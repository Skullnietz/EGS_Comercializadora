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
    CONTAR ORDENES ENTREGADAS POR CLIENTE
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
    VERIFICAR SI YA SE ACUMULÓ RECOMPENSA PARA UNA ORDEN
    =============================================*/
    static public function mdlExisteAcumulacionOrden($idOrden)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM dinero_electronico_movimientos WHERE id_orden = :id_orden AND tipo = 'acumulacion'");
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result["total"]) > 0;
    }

    /*=============================================
    ACUMULAR DINERO ELECTRÓNICO
    =============================================*/
    static public function mdlAcumularRecompensa($idCliente, $idOrden, $monto, $porcentaje, $descripcion)
    {
        $pdo = ConexionWP::conectarWP();

        $monedero = self::mdlObtenerMonedero($idCliente);
        $saldoAnterior = floatval($monedero["saldo"]);
        $saldoNuevo = $saldoAnterior + $monto;
        $fechaExpiracion = date('Y-m-d', strtotime('+6 months'));

        $stmt = $pdo->prepare("INSERT INTO dinero_electronico_movimientos
            (id_cliente, id_orden, tipo, monto, porcentaje_aplicado, saldo_anterior, saldo_nuevo, fecha_expiracion, descripcion)
            VALUES (:id_cliente, :id_orden, 'acumulacion', :monto, :porcentaje, :saldo_anterior, :saldo_nuevo, :fecha_expiracion, :descripcion)");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $stmt->bindParam(":monto", $monto);
        $stmt->bindParam(":porcentaje", $porcentaje);
        $stmt->bindParam(":saldo_anterior", $saldoAnterior);
        $stmt->bindParam(":saldo_nuevo", $saldoNuevo);
        $stmt->bindParam(":fecha_expiracion", $fechaExpiracion, PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->execute();

        $stmtUp = $pdo->prepare("UPDATE dinero_electronico SET saldo = :saldo WHERE id_cliente = :id_cliente");
        $stmtUp->bindParam(":saldo", $saldoNuevo);
        $stmtUp->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmtUp->execute();

        return $saldoNuevo;
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO
    =============================================*/
    static public function mdlCanjearRecompensa($idCliente, $idOrden, $montoCanje, $descripcion)
    {
        $pdo = ConexionWP::conectarWP();

        $monedero = self::mdlObtenerMonedero($idCliente);
        $saldoAnterior = floatval($monedero["saldo"]);

        if ($montoCanje > $saldoAnterior) {
            return false;
        }

        $saldoNuevo = $saldoAnterior - $montoCanje;

        $stmt = $pdo->prepare("INSERT INTO dinero_electronico_movimientos
            (id_cliente, id_orden, tipo, monto, saldo_anterior, saldo_nuevo, descripcion)
            VALUES (:id_cliente, :id_orden, 'canje', :monto, :saldo_anterior, :saldo_nuevo, :descripcion)");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":id_orden", $idOrden, PDO::PARAM_INT);
        $montoNeg = -$montoCanje;
        $stmt->bindParam(":monto", $montoNeg);
        $stmt->bindParam(":saldo_anterior", $saldoAnterior);
        $stmt->bindParam(":saldo_nuevo", $saldoNuevo);
        $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->execute();

        $stmtUp = $pdo->prepare("UPDATE dinero_electronico SET saldo = :saldo WHERE id_cliente = :id_cliente");
        $stmtUp->bindParam(":saldo", $saldoNuevo);
        $stmtUp->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmtUp->execute();

        return $saldoNuevo;
    }

    /*=============================================
    EXPIRAR MOVIMIENTOS VENCIDOS
    =============================================*/
    static public function mdlExpirarMovimientos()
    {
        $pdo = ConexionWP::conectarWP();
        $hoy = date('Y-m-d');

        $stmt = $pdo->prepare("SELECT id, id_cliente, monto FROM dinero_electronico_movimientos
            WHERE tipo = 'acumulacion' AND expirado = 0 AND fecha_expiracion IS NOT NULL AND fecha_expiracion <= :hoy");
        $stmt->bindParam(":hoy", $hoy, PDO::PARAM_STR);
        $stmt->execute();
        $vencidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vencidos as $mov) {
            $monedero = self::mdlObtenerMonedero($mov["id_cliente"]);
            $saldoAnterior = floatval($monedero["saldo"]);
            $montoExpirar = min(floatval($mov["monto"]), $saldoAnterior);

            if ($montoExpirar > 0) {
                $saldoNuevo = $saldoAnterior - $montoExpirar;

                $stmtIns = $pdo->prepare("INSERT INTO dinero_electronico_movimientos
                    (id_cliente, tipo, monto, saldo_anterior, saldo_nuevo, descripcion)
                    VALUES (:id_cliente, 'expiracion', :monto, :saldo_anterior, :saldo_nuevo, :descripcion)");
                $stmtIns->bindParam(":id_cliente", $mov["id_cliente"], PDO::PARAM_INT);
                $montoNeg = -$montoExpirar;
                $stmtIns->bindParam(":monto", $montoNeg);
                $stmtIns->bindParam(":saldo_anterior", $saldoAnterior);
                $stmtIns->bindParam(":saldo_nuevo", $saldoNuevo);
                $desc = "Expiración automática del movimiento #" . $mov["id"];
                $stmtIns->bindParam(":descripcion", $desc, PDO::PARAM_STR);
                $stmtIns->execute();

                $stmtUp = $pdo->prepare("UPDATE dinero_electronico SET saldo = :saldo WHERE id_cliente = :id_cliente");
                $stmtUp->bindParam(":saldo", $saldoNuevo);
                $stmtUp->bindParam(":id_cliente", $mov["id_cliente"], PDO::PARAM_INT);
                $stmtUp->execute();
            }

            $stmtExp = $pdo->prepare("UPDATE dinero_electronico_movimientos SET expirado = 1 WHERE id = :id");
            $stmtExp->bindParam(":id", $mov["id"], PDO::PARAM_INT);
            $stmtExp->execute();
        }

        return count($vencidos);
    }

    /*=============================================
    OBTENER MOVIMIENTOS DEL CLIENTE
    =============================================*/
    static public function mdlObtenerMovimientos($idCliente, $limite = 20)
    {
        $pdo = ConexionWP::conectarWP();
        $stmt = $pdo->prepare("SELECT * FROM dinero_electronico_movimientos WHERE id_cliente = :id_cliente ORDER BY fecha DESC LIMIT :limite");
        $stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=============================================
    OBTENER SALDO DISPONIBLE (sin expirados)
    =============================================*/
    static public function mdlObtenerSaldoDisponible($idCliente)
    {
        $pdo = ConexionWP::conectarWP();
        $monedero = self::mdlObtenerMonedero($idCliente);
        return floatval($monedero["saldo"]);
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
        $pdo = Database::conectar(Database::SISTEMA);
        $stmt = $pdo->prepare("SELECT nombre FROM clientesTienda WHERE id = :id");
        $stmt->bindParam(":id", $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["nombre"] : "Cliente";
    }
}
