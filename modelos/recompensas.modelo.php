<?php

if (!class_exists('ConexionWP')) {
    require_once __DIR__ . "/conexionWordpress.php";
}
if (!class_exists('Database')) {
    require_once __DIR__ . "/../config/Database.php";
}

class ModeloRecompensas
{
    /*=============================================
    CREAR TABLAS SI NO EXISTEN
    =============================================*/
    static public function mdlCrearTablas()
    {
        $pdo = ConexionWP::conectarWP();

        $pdo->exec("CREATE TABLE IF NOT EXISTS `dinero_electronico` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `id_cliente` INT(11) NOT NULL,
            `saldo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `token` VARCHAR(64) NOT NULL,
            `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_cliente` (`id_cliente`),
            UNIQUE KEY `uk_token` (`token`),
            KEY `idx_token` (`token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `dinero_electronico_movimientos` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `id_cliente` INT(11) NOT NULL,
            `id_orden` INT(11) DEFAULT NULL,
            `tipo` ENUM('acumulacion','canje','expiracion') NOT NULL,
            `monto` DECIMAL(10,2) NOT NULL,
            `porcentaje_aplicado` DECIMAL(5,2) DEFAULT NULL,
            `saldo_anterior` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `saldo_nuevo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `fecha_expiracion` DATE DEFAULT NULL,
            `expirado` TINYINT(1) NOT NULL DEFAULT 0,
            `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `descripcion` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_cliente` (`id_cliente`),
            KEY `idx_orden` (`id_orden`),
            KEY `idx_tipo` (`tipo`),
            KEY `idx_expiracion` (`fecha_expiracion`, `expirado`),
            KEY `idx_fecha` (`fecha`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    /*=============================================
    OBTENER O CREAR MONEDERO DEL CLIENTE
    (solo para el token y registro, el saldo se calcula dinámicamente)
    =============================================*/
    static public function mdlObtenerMonedero($idCliente)
    {
        $pdo = ConexionWP::conectarWP();

        $stmt = $pdo->prepare("SELECT * FROM dinero_electronico WHERE id_cliente = :id_cliente");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        $monedero = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$monedero) {
            $token = bin2hex(random_bytes(32));
            $stmt2 = $pdo->prepare("INSERT INTO dinero_electronico (id_cliente, saldo, token) VALUES (:id_cliente, 0.00, :token)");
            $stmt2->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
            $stmt2->bindParam(":token", $token, PDO::PARAM_STR);
            $stmt2->execute();

            $stmt3 = $pdo->prepare("SELECT * FROM dinero_electronico WHERE id_cliente = :id_cliente");
            $stmt3->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
            $stmt3->execute();
            $monedero = $stmt3->fetch(PDO::FETCH_ASSOC);
        }

        return $monedero;
    }

    /*=============================================
    OBTENER MONEDERO POR TOKEN (acceso público)
    =============================================*/
    static public function mdlObtenerMonederoPorToken($token)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT * FROM dinero_electronico WHERE token = :token");
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*=============================================
    CONTAR ORDENES ENTREGADAS POR CLIENTE (todas)
    =============================================*/
    static public function mdlContarOrdenesEntregadas($idCliente)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ordenes WHERE id_usuario = :id_cliente AND estado LIKE '%Ent%'");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result["total"]);
    }

    /*=============================================
    FECHA DE INICIO DEL PROGRAMA DE RECOMPENSAS
    Solo se contabilizan órdenes entregadas desde esta fecha.
    Cada recompensa vence 6 meses después de la entrega.
    =============================================*/
    const FECHA_INICIO_PROGRAMA = '2026-04-01';

    /*=============================================
    CALCULAR SALDO DINÁMICO
    Suma el % correspondiente del total de cada orden
    entregada desde el inicio del programa (abril 2026)
    cuya recompensa no haya vencido (máx 6 meses) y
    resta los canjes del mismo periodo.
    =============================================*/
    static public function mdlCalcularSaldoDinamico($idCliente, $porcentaje)
    {
        $pdo = ConexionWP::conectarWP();
        $hace6meses = date('Y-m-d', strtotime('-6 months'));
        // La ventana inicia en la fecha más reciente entre hace 6 meses y el inicio del programa
        $fechaDesde = max($hace6meses, self::FECHA_INICIO_PROGRAMA);

        // 1) Sumar recompensas de órdenes entregadas en la ventana vigente
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(total), 0) as suma_totales
            FROM ordenes
            WHERE id_usuario = :id_cliente
              AND estado LIKE '%Ent%'
              AND fecha_Salida IS NOT NULL
              AND fecha_Salida >= :fechaDesde
        ");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":fechaDesde", $fechaDesde, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $sumaTotales = floatval($row["suma_totales"]);

        $acumulado = round($sumaTotales * ($porcentaje / 100), 2);

        // 2) Restar canjes realizados desde el inicio del programa
        $stmtCanjes = $pdo->prepare("
            SELECT COALESCE(SUM(ABS(monto)), 0) as total_canjes
            FROM dinero_electronico_movimientos
            WHERE id_cliente = :id_cliente
              AND tipo = 'canje'
              AND fecha >= :fechaDesde
        ");
        $stmtCanjes->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmtCanjes->bindParam(":fechaDesde", $fechaDesde, PDO::PARAM_STR);
        $stmtCanjes->execute();
        $rowCanjes = $stmtCanjes->fetch(PDO::FETCH_ASSOC);
        $totalCanjes = floatval($rowCanjes["total_canjes"]);

        $saldo = $acumulado - $totalCanjes;
        if ($saldo < 0) $saldo = 0;

        return $saldo;
    }

    /*=============================================
    OBTENER DESGLOSE DE ÓRDENES QUE GENERAN SALDO
    (para la vista del monedero público)
    =============================================*/
    static public function mdlObtenerOrdenesConRecompensa($idCliente, $porcentaje)
    {
        $pdo = ConexionWP::conectarWP();
        $hace6meses = date('Y-m-d', strtotime('-6 months'));
        $fechaDesde = max($hace6meses, self::FECHA_INICIO_PROGRAMA);

        $stmt = $pdo->prepare("
            SELECT id, total, fecha_Salida
            FROM ordenes
            WHERE id_usuario = :id_cliente
              AND estado LIKE '%Ent%'
              AND fecha_Salida IS NOT NULL
              AND fecha_Salida >= :fechaDesde
            ORDER BY fecha_Salida DESC
        ");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":fechaDesde", $fechaDesde, PDO::PARAM_STR);
        $stmt->execute();
        $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = array();
        foreach ($ordenes as $ord) {
            $recompensa = round(floatval($ord["total"]) * ($porcentaje / 100), 2);
            $resultado[] = array(
                "id_orden" => $ord["id"],
                "total_orden" => floatval($ord["total"]),
                "recompensa" => $recompensa,
                "fecha_entrega" => $ord["fecha_Salida"]
            );
        }
        return $resultado;
    }

    /*=============================================
    CONTAR ORDENES ENTREGADAS DENTRO DEL PROGRAMA
    (desde abril 2026 y dentro de la ventana de 6 meses)
    =============================================*/
    static public function mdlContarOrdenesEnPrograma($idCliente)
    {
        $pdo = ConexionWP::conectarWP();
        $hace6meses = date('Y-m-d', strtotime('-6 months'));
        $fechaDesde = max($hace6meses, self::FECHA_INICIO_PROGRAMA);

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM ordenes
            WHERE id_usuario = :id_cliente
              AND estado LIKE '%Ent%'
              AND fecha_Salida IS NOT NULL
              AND fecha_Salida >= :fechaDesde
        ");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":fechaDesde", $fechaDesde, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result["total"]);
    }

    /*=============================================
    VERIFICAR SI YA SE REGISTRÓ CANJE PARA UNA ORDEN
    =============================================*/
    static public function mdlExisteCanjeOrden($idOrden)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM dinero_electronico_movimientos WHERE id_orden = :id_orden AND tipo = 'canje'");
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result["total"]) > 0;
    }

    /*=============================================
    REGISTRAR CANJE DE DINERO ELECTRÓNICO
    (solo inserta el movimiento, el saldo se recalcula dinámicamente)
    =============================================*/
    static public function mdlCanjearRecompensa($idCliente, $idOrden, $montoCanje, $descripcion)
    {
        $pdo = ConexionWP::conectarWP();

        $montoNeg = -$montoCanje;
        $stmt = $pdo->prepare("INSERT INTO dinero_electronico_movimientos
            (id_cliente, id_orden, tipo, monto, saldo_anterior, saldo_nuevo, descripcion)
            VALUES (:id_cliente, :id_orden, 'canje', :monto, 0, 0, :descripcion)");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $stmt->bindParam(":monto", $montoNeg);
        $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

    /*=============================================
    OBTENER CANJES DEL CLIENTE (para historial)
    =============================================*/
    static public function mdlObtenerCanjes($idCliente, $limite = 20)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT * FROM dinero_electronico_movimientos WHERE id_cliente = :id_cliente AND tipo = 'canje' ORDER BY fecha DESC LIMIT :limite");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=============================================
    OBTENER CANJE DE UNA ORDEN ESPECÍFICA
    =============================================*/
    static public function mdlObtenerCanjeOrden($idOrden)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT * FROM dinero_electronico_movimientos WHERE id_orden = :id_orden AND tipo = 'canje' LIMIT 1");
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*=============================================
    OBTENER NOMBRE DE CLIENTE POR ID
    =============================================*/
    static public function mdlObtenerNombreCliente($idCliente)
    {
        try {
            $pdo = Database::conectar(Database::SISTEMA);
            $stmt = $pdo->prepare("SELECT nombre FROM clientesTienda WHERE id = :id");
            $stmt->bindParam(":id", $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result["nombre"] : "Cliente";
        } catch (Exception $e) {
            // Fallback: intentar con cross-database query vía conexión WORDPRESS
            try {
                $pdo = ConexionWP::conectarWP();
                $dbSistema = getenv('DB_SISTEMA_NAME') ?: 'egsequip_dbsistema';
                $stmt = $pdo->prepare("SELECT nombre FROM `" . $dbSistema . "`.clientesTienda WHERE id = :id");
                $stmt->bindParam(":id", $idCliente, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result["nombre"] : "Cliente";
            } catch (Exception $e2) {
                return "Cliente";
            }
        }
    }
}
