<?php

require_once __DIR__ . "/conexionWordpress.php";
require_once __DIR__ . "/conexion.php";

/**
 * Persistencia del generador de etiquetas de contacto y garantía.
 *
 * Las tablas se crean de forma idempotente para que la función quede disponible
 * al desplegar el código. El mismo esquema también se documenta en sql/.
 */
class ModeloEtiquetas
{
    private static $tablasListas = false;

    private static function asegurarTablas()
    {
        if (self::$tablasListas) {
            return;
        }

        $pdo = ConexionWP::conectarWP();
        try {
            // Evita ejecutar DDL en cada lectura una vez que la migración existe.
            $pdo->query("SELECT 1 FROM egs_etiquetas_config c LEFT JOIN egs_etiquetas_garantia g ON 1 = 0 LIMIT 1");
            self::$tablasListas = true;
            return;
        } catch (Exception $e) {
            // Primer despliegue: crear las tablas de forma idempotente.
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS egs_etiquetas_config (
            id_empresa INT UNSIGNED NOT NULL,
            nombre_comercial VARCHAR(120) NOT NULL,
            lema VARCHAR(180) NOT NULL,
            direccion VARCHAR(300) NOT NULL,
            whatsapp VARCHAR(30) NOT NULL,
            telefono_1 VARCHAR(30) NOT NULL,
            telefono_2 VARCHAR(30) NOT NULL,
            telefono_3 VARCHAR(30) NOT NULL,
            sitio_web VARCHAR(180) NOT NULL,
            actualizado_por INT UNSIGNED DEFAULT NULL,
            actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id_empresa)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $pdo->exec("CREATE TABLE IF NOT EXISTS egs_etiquetas_garantia (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            id_orden INT UNSIGNED NOT NULL,
            token CHAR(64) NOT NULL,
            fac_rem VARCHAR(80) NOT NULL DEFAULT '',
            tecnico VARCHAR(160) NOT NULL DEFAULT '',
            clave_cliente VARCHAR(100) NOT NULL DEFAULT '',
            nombre_cliente VARCHAR(180) NOT NULL DEFAULT '',
            equipo VARCHAR(220) NOT NULL DEFAULT '',
            numero_serie VARCHAR(160) NOT NULL DEFAULT '',
            fecha_entrega DATE NOT NULL,
            fecha_vencimiento DATE NOT NULL,
            proximo_servicio DATE DEFAULT NULL,
            creado_por INT UNSIGNED DEFAULT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_egs_etiqueta_orden (id_orden),
            UNIQUE KEY uq_egs_etiqueta_token (token),
            KEY idx_egs_etiqueta_vencimiento (fecha_vencimiento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        self::$tablasListas = true;
    }

    public static function mdlObtenerConfiguracion($idEmpresa)
    {
        self::asegurarTablas();
        $stmt = ConexionWP::conectarWP()->prepare("SELECT * FROM egs_etiquetas_config WHERE id_empresa = :empresa LIMIT 1");
        $stmt->execute(array(":empresa" => intval($idEmpresa)));
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($config) {
            return $config;
        }

        return array(
            "nombre_comercial" => "COMERCIALIZADORA EGS",
            "lema" => "Somos tu proveedor local de confianza",
            "direccion" => "Pino Suárez Nte. 308, Col. Santa Clara, Toluca, México, 50090 Toluca de Lerdo, Méx.",
            "whatsapp" => "729-133-9897",
            "telefono_1" => "722-283-1159",
            "telefono_2" => "722-214-4416",
            "telefono_3" => "722-167-1684",
            "sitio_web" => "https://comercializadoraegs.shop"
        );
    }

    public static function mdlGuardarConfiguracion($datos)
    {
        self::asegurarTablas();
        $sql = "INSERT INTO egs_etiquetas_config
                    (id_empresa, nombre_comercial, lema, direccion, whatsapp, telefono_1, telefono_2, telefono_3, sitio_web, actualizado_por)
                VALUES
                    (:empresa, :nombre, :lema, :direccion, :whatsapp, :telefono1, :telefono2, :telefono3, :sitio, :usuario)
                ON DUPLICATE KEY UPDATE
                    nombre_comercial = VALUES(nombre_comercial), lema = VALUES(lema),
                    direccion = VALUES(direccion), whatsapp = VALUES(whatsapp),
                    telefono_1 = VALUES(telefono_1), telefono_2 = VALUES(telefono_2),
                    telefono_3 = VALUES(telefono_3), sitio_web = VALUES(sitio_web),
                    actualizado_por = VALUES(actualizado_por)";
        $stmt = ConexionWP::conectarWP()->prepare($sql);
        return $stmt->execute(array(
            ":empresa" => intval($datos["id_empresa"]),
            ":nombre" => $datos["nombre_comercial"],
            ":lema" => $datos["lema"],
            ":direccion" => $datos["direccion"],
            ":whatsapp" => $datos["whatsapp"],
            ":telefono1" => $datos["telefono_1"],
            ":telefono2" => $datos["telefono_2"],
            ":telefono3" => $datos["telefono_3"],
            ":sitio" => $datos["sitio_web"],
            ":usuario" => intval($datos["actualizado_por"])
        ));
    }

    public static function mdlOrdenesRecientes($idEmpresa, $limite)
    {
        self::asegurarTablas();
        $limite = max(20, min(300, intval($limite)));
        $sql = "SELECT o.id, o.id_usuario, o.id_tecnico, o.estado, o.fecha_ingreso, o.fecha_Salida,
                       o.marcaDelEquipo, o.modeloDelEquipo, o.numeroDeSerieDelEquipo,
                       g.token AS garantia_token, g.fac_rem AS garantia_fac_rem,
                       g.tecnico AS garantia_tecnico, g.clave_cliente AS garantia_clave_cliente,
                       g.nombre_cliente AS garantia_nombre_cliente, g.equipo AS garantia_equipo,
                       g.numero_serie AS garantia_numero_serie, g.fecha_entrega AS garantia_fecha_entrega,
                       g.fecha_vencimiento AS garantia_fecha_vencimiento,
                       g.proximo_servicio AS garantia_proximo_servicio
                FROM ordenes o
                LEFT JOIN egs_etiquetas_garantia g ON g.id_orden = o.id
                WHERE o.id_empresa = :empresa
                ORDER BY o.id DESC LIMIT " . $limite;
        $stmt = ConexionWP::conectarWP()->prepare($sql);
        $stmt->bindValue(":empresa", intval($idEmpresa), PDO::PARAM_INT);
        $stmt->execute();
        $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$ordenes) {
            return array();
        }

        $clientesIds = array();
        $tecnicosIds = array();
        foreach ($ordenes as $orden) {
            if (!empty($orden["id_usuario"])) $clientesIds[] = intval($orden["id_usuario"]);
            if (!empty($orden["id_tecnico"])) $tecnicosIds[] = intval($orden["id_tecnico"]);
        }
        $clientes = self::obtenerPersonasPorIds("clientesTienda", $clientesIds);
        $tecnicos = self::obtenerPersonasPorIds("tecnicos", $tecnicosIds);

        foreach ($ordenes as &$orden) {
            $idCliente = intval($orden["id_usuario"]);
            $idTecnico = intval($orden["id_tecnico"]);
            $orden["cliente_nombre"] = isset($clientes[$idCliente]) ? $clientes[$idCliente] : "";
            $orden["tecnico_nombre"] = isset($tecnicos[$idTecnico]) ? $tecnicos[$idTecnico] : "";
        }
        unset($orden);

        return $ordenes;
    }

    private static function obtenerPersonasPorIds($tabla, $ids)
    {
        $ids = array_values(array_unique(array_filter(array_map("intval", $ids))));
        if (!$ids) return array();

        $marcas = implode(",", array_fill(0, count($ids), "?"));
        $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM " . $tabla . " WHERE id IN (" . $marcas . ")");
        foreach ($ids as $i => $id) $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = array();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $resultado[intval($fila["id"])] = $fila["nombre"];
        }
        return $resultado;
    }

    public static function mdlGuardarGarantia($datos)
    {
        self::asegurarTablas();
        $pdo = ConexionWP::conectarWP();

        $verificar = $pdo->prepare("SELECT id FROM ordenes WHERE id = :orden AND id_empresa = :empresa LIMIT 1");
        $verificar->execute(array(":orden" => intval($datos["id_orden"]), ":empresa" => intval($datos["id_empresa"])));
        if (!$verificar->fetchColumn()) {
            throw new RuntimeException("La orden seleccionada no existe o pertenece a otra empresa.");
        }

        $existente = $pdo->prepare("SELECT token FROM egs_etiquetas_garantia WHERE id_orden = :orden LIMIT 1");
        $existente->execute(array(":orden" => intval($datos["id_orden"])));
        $token = $existente->fetchColumn();
        if (!$token) $token = bin2hex(random_bytes(32));

        $sql = "INSERT INTO egs_etiquetas_garantia
                    (id_orden, token, fac_rem, tecnico, clave_cliente, nombre_cliente, equipo,
                     numero_serie, fecha_entrega, fecha_vencimiento, proximo_servicio, creado_por)
                VALUES
                    (:orden, :token, :fac, :tecnico, :clave, :cliente, :equipo,
                     :serie, :entrega, :vencimiento, :servicio, :usuario)
                ON DUPLICATE KEY UPDATE
                    fac_rem = VALUES(fac_rem), tecnico = VALUES(tecnico),
                    clave_cliente = VALUES(clave_cliente), nombre_cliente = VALUES(nombre_cliente),
                    equipo = VALUES(equipo), numero_serie = VALUES(numero_serie),
                    fecha_entrega = VALUES(fecha_entrega), fecha_vencimiento = VALUES(fecha_vencimiento),
                    proximo_servicio = VALUES(proximo_servicio), creado_por = VALUES(creado_por)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ":orden" => intval($datos["id_orden"]), ":token" => $token,
            ":fac" => $datos["fac_rem"], ":tecnico" => $datos["tecnico"],
            ":clave" => $datos["clave_cliente"], ":cliente" => $datos["nombre_cliente"],
            ":equipo" => $datos["equipo"], ":serie" => $datos["numero_serie"],
            ":entrega" => $datos["fecha_entrega"], ":vencimiento" => $datos["fecha_vencimiento"],
            ":servicio" => $datos["proximo_servicio"] !== "" ? $datos["proximo_servicio"] : null,
            ":usuario" => intval($datos["creado_por"])
        ));
        return $token;
    }

    public static function mdlGarantiaPorToken($token)
    {
        self::asegurarTablas();
        $stmt = ConexionWP::conectarWP()->prepare(
            "SELECT g.*, o.id_empresa, o.estado AS estado_orden, o.marcaDelEquipo AS marca_actual,
                    o.modeloDelEquipo AS modelo_actual, o.numeroDeSerieDelEquipo AS serie_actual
             FROM egs_etiquetas_garantia g
             INNER JOIN ordenes o ON o.id = g.id_orden
             WHERE g.token = :token LIMIT 1"
        );
        $stmt->execute(array(":token" => $token));
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $fila : null;
    }
}
