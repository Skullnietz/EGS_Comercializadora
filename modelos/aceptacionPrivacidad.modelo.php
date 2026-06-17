<?php

require_once "conexion.php";

class ModeloAceptacionPrivacidad
{
    /*=============================================
    CREAR TABLA SI NO EXISTE (idempotente).
    También agrega la columna 'firma' a instalaciones previas que
    no la tenían (la columna se introdujo después).
    =============================================*/
    static public function mdlCrearTabla()
    {
        try {
            $pdo = Conexion::conectar();
            $pdo->exec("CREATE TABLE IF NOT EXISTS aceptaciones_privacidad (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_cliente INT NOT NULL,
                aceptado TINYINT(1) NOT NULL,
                firma MEDIUMTEXT DEFAULT NULL,
                fecha DATETIME NOT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                user_agent VARCHAR(255) DEFAULT NULL,
                UNIQUE KEY uk_cliente (id_cliente),
                INDEX idx_fecha (fecha)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Backfill de la columna firma para tablas creadas antes (sin IF NOT EXISTS portable).
            try {
                $check = $pdo->query("SHOW COLUMNS FROM aceptaciones_privacidad LIKE 'firma'");
                if ($check && !$check->fetch()) {
                    $pdo->exec("ALTER TABLE aceptaciones_privacidad ADD COLUMN firma MEDIUMTEXT DEFAULT NULL AFTER aceptado");
                }
            } catch (Exception $e) {
                // si SHOW COLUMNS falla, ignoramos: el INSERT lo dirá
            }
        } catch (Exception $e) {
            // silencio: si falla aquí, los métodos posteriores reportarán el error real
        }
    }

    /*=============================================
    OBTENER LA ÚLTIMA DECISIÓN DEL CLIENTE
    Devuelve fila con id_cliente, aceptado, firma, fecha o null.
    =============================================*/
    static public function mdlObtener($idCliente)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT id, id_cliente, aceptado, firma, fecha, ip, user_agent
                 FROM aceptaciones_privacidad
                 WHERE id_cliente = :idCliente
                 LIMIT 1"
            );
            $stmt->bindParam(":idCliente", $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? $fila : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /*=============================================
    GUARDAR LA DECISIÓN (upsert por id_cliente).
    'firma' es opcional (data URL image/png base64).
    =============================================*/
    static public function mdlGuardar($datos)
    {
        try {
            $pdo = Conexion::conectar();
            $fecha = date("Y-m-d H:i:s");

            $stmt = $pdo->prepare(
                "INSERT INTO aceptaciones_privacidad (id_cliente, aceptado, firma, fecha, ip, user_agent)
                 VALUES (:id_cliente, :aceptado, :firma, :fecha, :ip, :user_agent)
                 ON DUPLICATE KEY UPDATE
                    aceptado = VALUES(aceptado),
                    firma = VALUES(firma),
                    fecha = VALUES(fecha),
                    ip = VALUES(ip),
                    user_agent = VALUES(user_agent)"
            );
            $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
            $stmt->bindParam(":aceptado",   $datos["aceptado"],   PDO::PARAM_INT);
            $stmt->bindParam(":firma",      $datos["firma"],      PDO::PARAM_STR);
            $stmt->bindParam(":fecha",      $fecha,               PDO::PARAM_STR);
            $stmt->bindParam(":ip",         $datos["ip"],         PDO::PARAM_STR);
            $stmt->bindParam(":user_agent", $datos["user_agent"], PDO::PARAM_STR);

            return $stmt->execute() ? "ok" : "error";
        } catch (Exception $e) {
            return "error";
        }
    }
}
