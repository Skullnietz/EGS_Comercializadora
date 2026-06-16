<?php

require_once "conexion.php";

class ModeloReporteEquipo
{

	/*=============================================
	MOSTRAR REPORTES DE UNA ORDEN
	=============================================*/
	static public function mdlMostrarReportes($tabla, $idOrden)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT r.*, a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
				 FROM $tabla r
				 LEFT JOIN administradores a ON a.id = r.id_creador
				 WHERE r.id_orden = :idOrden
				 ORDER BY r.fecha DESC"
			);
			$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return array();
		}
	}

	/*=============================================
	CREAR REPORTE (con protección anti-duplicado)
	Devuelve el id insertado, "ok" (duplicado) o "error".
	=============================================*/
	static public function mdlCrearReporte($tabla, $datos)
	{
		try {
			$pdo = Conexion::conectar();

			// Anti-duplicado: misma descripción, orden y creador en los últimos 60 seg
			$chk = $pdo->prepare(
				"SELECT id FROM $tabla
				 WHERE id_creador = :id_creador
				   AND id_orden   = :id_orden
				   AND descripcion = :descripcion
				   AND fecha >= DATE_SUB(NOW(), INTERVAL 60 SECOND)
				 ORDER BY fecha DESC LIMIT 1"
			);
			$chk->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
			$chk->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$chk->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
			$chk->execute();
			$idExistente = $chk->fetchColumn();
			if ($idExistente) {
				return intval($idExistente);
			}

			$fecha = date("Y-m-d H:i:s");
			$stmt = $pdo->prepare("INSERT INTO $tabla(id_orden, id_creador, descripcion, fecha) VALUES (:id_orden, :id_creador, :descripcion, :fecha)");
			$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$stmt->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
			$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
			$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

			return $stmt->execute() ? intval($pdo->lastInsertId()) : "error";
		} catch (Exception $e) {
			return "error";
		}
	}

	/*=============================================
	CREAR FOTO DE REPORTE
	=============================================*/
	static public function mdlCrearFotoReporte($tabla, $datos)
	{
		try {
			$pdo = Conexion::conectar();
			$fecha = date("Y-m-d H:i:s");

			$stmt = $pdo->prepare("INSERT INTO $tabla(id_reporte, id_orden, ruta, fecha) VALUES (:id_reporte, :id_orden, :ruta, :fecha)");
			$stmt->bindParam(":id_reporte", $datos["id_reporte"], PDO::PARAM_INT);
			$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$stmt->bindParam(":ruta", $datos["ruta"], PDO::PARAM_STR);
			$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

			return $stmt->execute() ? "ok" : "error";
		} catch (Exception $e) {
			return "error";
		}
	}

	/*=============================================
	MOSTRAR FOTOS DE TODOS LOS REPORTES DE UNA ORDEN
	(una sola consulta; se agrupa por id_reporte en la vista)
	=============================================*/
	static public function mdlMostrarFotosPorOrden($tabla, $idOrden)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT id, id_reporte, id_orden, ruta, fecha
				 FROM $tabla
				 WHERE id_orden = :idOrden
				 ORDER BY id ASC"
			);
			$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return array();
		}
	}

	/*=============================================
	ELIMINAR FOTOS DE UN REPORTE
	(devuelve las rutas eliminadas para borrar los archivos físicos)
	=============================================*/
	static public function mdlEliminarFotosPorReporte($tabla, $idReporte)
	{
		try {
			$pdo = Conexion::conectar();

			$sel = $pdo->prepare("SELECT ruta FROM $tabla WHERE id_reporte = :id");
			$sel->bindParam(":id", $idReporte, PDO::PARAM_INT);
			$sel->execute();
			$rutas = $sel->fetchAll(PDO::FETCH_COLUMN);

			$del = $pdo->prepare("DELETE FROM $tabla WHERE id_reporte = :id");
			$del->bindParam(":id", $idReporte, PDO::PARAM_INT);
			$del->execute();

			return is_array($rutas) ? $rutas : array();
		} catch (Exception $e) {
			return array();
		}
	}

	/*=============================================
	ELIMINAR REPORTE
	=============================================*/
	static public function mdlEliminarReporte($tabla, $idReporte)
	{
		try {
			$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
			$stmt->bindParam(":id", $idReporte, PDO::PARAM_INT);
			return $stmt->execute() ? "ok" : "error";
		} catch (Exception $e) {
			return "error";
		}
	}

	/*=============================================
	FOTOS ANTERIORES A UNA FECHA (para mantenimiento por espacio)
	=============================================*/
	static public function mdlFotosAnterioresA($tabla, $fechaCorte)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT id, ruta, fecha FROM $tabla WHERE fecha < :corte ORDER BY fecha ASC"
			);
			$stmt->bindParam(":corte", $fechaCorte, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return array();
		}
	}

	/*=============================================
	ELIMINAR FOTOS POR IDS (para mantenimiento por espacio)
	=============================================*/
	static public function mdlEliminarFotosPorIds($tabla, $ids)
	{
		if (empty($ids)) return "ok";
		try {
			$ids = array_values(array_unique(array_map('intval', $ids)));
			$placeholders = implode(',', array_fill(0, count($ids), '?'));
			$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id IN ($placeholders)");
			for ($i = 0; $i < count($ids); $i++) {
				$stmt->bindValue($i + 1, $ids[$i], PDO::PARAM_INT);
			}
			return $stmt->execute() ? "ok" : "error";
		} catch (Exception $e) {
			return "error";
		}
	}
}
