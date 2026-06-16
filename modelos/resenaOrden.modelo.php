<?php

require_once "conexion.php";

class ModeloResenaOrden
{

	/*=============================================
	MOSTRAR LA RESEÑA DE UNA ORDEN (única o null)
	=============================================*/
	static public function mdlMostrarPorOrden($tabla, $idOrden)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT id, id_orden, calificacion, comentario, fecha
				 FROM $tabla
				 WHERE id_orden = :idOrden
				 LIMIT 1"
			);
			$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
			$stmt->execute();
			$fila = $stmt->fetch(PDO::FETCH_ASSOC);
			return $fila ? $fila : null;
		} catch (Exception $e) {
			return null;
		}
	}

	/*=============================================
	CREAR RESEÑA (envío único por orden)
	El índice UNIQUE en id_orden evita duplicados; si ya existe el INSERT
	falla y se devuelve "duplicate".
	Devuelve "ok", "duplicate" o "error".
	=============================================*/
	static public function mdlCrear($tabla, $datos)
	{
		try {
			$pdo = Conexion::conectar();
			$fecha = date("Y-m-d H:i:s");

			$stmt = $pdo->prepare("INSERT INTO $tabla(id_orden, calificacion, comentario, fecha) VALUES (:id_orden, :calificacion, :comentario, :fecha)");
			$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$stmt->bindParam(":calificacion", $datos["calificacion"], PDO::PARAM_INT);
			$stmt->bindParam(":comentario", $datos["comentario"], PDO::PARAM_STR);
			$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

			return $stmt->execute() ? "ok" : "error";
		} catch (Exception $e) {
			// Violación de UNIQUE (ya hay reseña) u otro error de BD.
			return "duplicate";
		}
	}
}
