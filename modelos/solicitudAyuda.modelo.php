<?php

require_once "conexion.php";

class ModeloSolicitudAyuda
{
    /*=============================================
    CREAR TABLA SI NO EXISTE (idempotente)
    =============================================*/
    static public function mdlCrearTabla()
    {
        try {
            $pdo = Conexion::conectar();
            $pdo->exec("CREATE TABLE IF NOT EXISTS solicitudes_ayuda (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_cliente INT NOT NULL,
                id_orden INT DEFAULT NULL,
                mensaje VARCHAR(2000) NOT NULL,
                estado ENUM('pendiente','en_proceso','resuelta') NOT NULL DEFAULT 'pendiente',
                fecha DATETIME NOT NULL,
                fecha_resolucion DATETIME DEFAULT NULL,
                notas_admin VARCHAR(2000) DEFAULT NULL,
                INDEX idx_cliente (id_cliente),
                INDEX idx_estado (estado),
                INDEX idx_fecha (fecha)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (Exception $e) {
            // silencio
        }
    }

    /*=============================================
    LISTAR SOLICITUDES DEL CLIENTE (más recientes primero)
    =============================================*/
    static public function mdlListarPorCliente($idCliente)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT id, id_cliente, id_orden, mensaje, estado, fecha, fecha_resolucion, notas_admin
                 FROM solicitudes_ayuda
                 WHERE id_cliente = :idCliente
                 ORDER BY fecha DESC
                 LIMIT 30"
            );
            $stmt->bindParam(":idCliente", $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array();
        }
    }

    /*=============================================
    DUPLICADO RECIENTE: misma solicitud en los últimos 60s
    =============================================*/
    static public function mdlDuplicadoReciente($idCliente, $mensaje)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT id FROM solicitudes_ayuda
                 WHERE id_cliente = :idCliente
                   AND mensaje = :mensaje
                   AND fecha >= (NOW() - INTERVAL 60 SECOND)
                 LIMIT 1"
            );
            $stmt->bindParam(":idCliente", $idCliente, PDO::PARAM_INT);
            $stmt->bindParam(":mensaje",   $mensaje,   PDO::PARAM_STR);
            $stmt->execute();
            return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    /*=============================================
    CREAR SOLICITUD
    =============================================*/
    static public function mdlCrear($datos)
    {
        try {
            $pdo = Conexion::conectar();
            $fecha = date("Y-m-d H:i:s");

            $stmt = $pdo->prepare(
                "INSERT INTO solicitudes_ayuda (id_cliente, id_orden, mensaje, estado, fecha)
                 VALUES (:id_cliente, :id_orden, :mensaje, 'pendiente', :fecha)"
            );
            $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
            if ($datos["id_orden"] !== null) {
                $stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":id_orden", null, PDO::PARAM_NULL);
            }
            $stmt->bindParam(":mensaje", $datos["mensaje"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha",   $fecha,            PDO::PARAM_STR);

            return $stmt->execute() ? "ok" : "error";
        } catch (Exception $e) {
            return "error";
        }
    }
}
