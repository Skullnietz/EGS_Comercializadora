<?php

require_once "conexion.php";

class ModeloComentarioCliente
{

	/*=============================================
	MOSTRAR COMENTARIOS DE UNA ORDEN
	=============================================*/
	static public function mdlMostrarPorOrden($tabla, $idOrden)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT id, id_orden, comentario, fecha
				 FROM $tabla
				 WHERE id_orden = :idOrden
				 ORDER BY fecha DESC"
			);
			$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return array();
		}
	}

	/*=============================================
	CREAR COMENTARIO (con protección anti-duplicado)
	Devuelve el id insertado, "duplicate" o "error".
	=============================================*/
	static public function mdlCrear($tabla, $datos)
	{
		try {
			$pdo = Conexion::conectar();

			// Anti-duplicado: mismo comentario y orden en los últimos 60 seg
			$chk = $pdo->prepare(
				"SELECT id FROM $tabla
				 WHERE id_orden = :id_orden
				   AND comentario = :comentario
				   AND fecha >= DATE_SUB(NOW(), INTERVAL 60 SECOND)
				 ORDER BY fecha DESC LIMIT 1"
			);
			$chk->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$chk->bindParam(":comentario", $datos["comentario"], PDO::PARAM_STR);
			$chk->execute();
			if ($chk->fetchColumn()) {
				return "duplicate";
			}

			$fecha = date("Y-m-d H:i:s");
			$stmt = $pdo->prepare("INSERT INTO $tabla(id_orden, comentario, fecha) VALUES (:id_orden, :comentario, :fecha)");
			$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$stmt->bindParam(":comentario", $datos["comentario"], PDO::PARAM_STR);
			$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

			return $stmt->execute() ? intval($pdo->lastInsertId()) : "error";
		} catch (Exception $e) {
			return "error";
		}
	}
}
