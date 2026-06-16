<?php

require_once "conexion.php";

class ModeloObservaciones
{

	/*=============================================
	MOSTRAR OBSERVACIONES
	=============================================*/

	static public function mdlMostrarobservaciones($tabla, $itemobs)
	{

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.*, a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 WHERE o.id_orden = :idOrden
			 ORDER BY o.fecha DESC"
		);

		$stmt->bindParam(":idOrden", $itemobs, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();

		$stmt->close();

		$stmt = null;


	}

	/*=============================================
	MOSTRAR OBSERVACIONES INFO USUARIO
	=============================================*/

	static public function mdlMostrarInfoUser($tabla, $idadmin)
	{



		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id = $idadmin");

		$stmt->execute();

		return $stmt->fetchAll();



		$stmt->close();

		$stmt = null;


	}

	/*=============================================
	MOSTRAR ÚLTIMAS OBSERVACIONES (GLOBAL)
	=============================================*/

	static public function mdlUltimasObservaciones($tabla, $limite)
	{

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
			        a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 ORDER BY o.fecha DESC
			 LIMIT :limite"
		);

		$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	OBSERVACIONES DE HOY (GLOBAL)
	=============================================*/

	static public function mdlObservacionesHoy($tabla)
	{

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
			        a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 WHERE DATE(o.fecha) = CURDATE()
			 ORDER BY o.fecha DESC"
		);

		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	CREAR OBSERVACIONES (con protección anti-duplicado)
	=============================================*/
	static public function mdlCrearObservacion($tabla, $datos)
	{

		$pdo = Conexion::conectar();

		// ── Verificar duplicado: misma observación, orden y creador en los últimos 60 seg ──
		$chk = $pdo->prepare(
			"SELECT COUNT(*) FROM $tabla
			 WHERE id_creador = :id_creador
			   AND id_orden   = :id_orden
			   AND observacion = :observacion
			   AND fecha >= DATE_SUB(NOW(), INTERVAL 60 SECOND)"
		);
		$chk->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
		$chk->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
		$chk->bindParam(":observacion", $datos["observacion"], PDO::PARAM_STR);
		$chk->execute();

		// Si ya existe una idéntica reciente, devolver su id (para poder adjuntar fotos a ella)
		$existente = $chk->fetchColumn();
		if ($existente > 0) {
			$sel = $pdo->prepare(
				"SELECT id FROM $tabla
				 WHERE id_creador = :id_creador
				   AND id_orden   = :id_orden
				   AND observacion = :observacion
				   AND fecha >= DATE_SUB(NOW(), INTERVAL 60 SECOND)
				 ORDER BY fecha DESC LIMIT 1"
			);
			$sel->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
			$sel->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$sel->bindParam(":observacion", $datos["observacion"], PDO::PARAM_STR);
			$sel->execute();
			$idExistente = $sel->fetchColumn();
			return $idExistente ? intval($idExistente) : "ok";
		}

		$fecha = date("Y-m-d H:i:s");

		$stmt = $pdo->prepare("INSERT INTO $tabla(id_creador, id_orden, observacion, fecha) VALUES (:id_creador, :id_orden, :observacion, :fecha)");

		$stmt->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
		$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
		$stmt->bindParam(":observacion", $datos["observacion"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

		if ($stmt->execute()) {

			return intval($pdo->lastInsertId());

		} else {

			return "error";

		}

		$stmt->close();

		$stmt = null;
	}

	/*=============================================
	CREAR FOTO DE OBSERVACIÓN
	=============================================*/
	static public function mdlCrearFotoObservacion($tabla, $datos)
	{
		try {
			$pdo = Conexion::conectar();
			$fecha = date("Y-m-d H:i:s");

			$stmt = $pdo->prepare("INSERT INTO $tabla(id_observacion, id_orden, ruta, fecha) VALUES (:id_observacion, :id_orden, :ruta, :fecha)");
			$stmt->bindParam(":id_observacion", $datos["id_observacion"], PDO::PARAM_INT);
			$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
			$stmt->bindParam(":ruta", $datos["ruta"], PDO::PARAM_STR);
			$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);

			return $stmt->execute() ? "ok" : "error";
		} catch (Exception $e) {
			return "error";
		}
	}

	/*=============================================
	MOSTRAR FOTOS DE TODAS LAS OBSERVACIONES DE UNA ORDEN
	(una sola consulta; se agrupa por id_observacion en la vista)
	=============================================*/
	static public function mdlMostrarFotosPorOrden($tabla, $idOrden)
	{
		try {
			$stmt = Conexion::conectar()->prepare(
				"SELECT id, id_observacion, id_orden, ruta, fecha
				 FROM $tabla
				 WHERE id_orden = :idOrden
				 ORDER BY id ASC"
			);
			$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			// La tabla puede no existir todavía: degradar sin romper la vista
			return array();
		}
	}

	/*=============================================
	ELIMINAR FOTOS DE UNA OBSERVACIÓN
	(devuelve las rutas eliminadas para borrar los archivos físicos)
	=============================================*/
	static public function mdlEliminarFotosPorObservacion($tabla, $idObservacion)
	{
		try {
			$pdo = Conexion::conectar();

			$sel = $pdo->prepare("SELECT ruta FROM $tabla WHERE id_observacion = :id");
			$sel->bindParam(":id", $idObservacion, PDO::PARAM_INT);
			$sel->execute();
			$rutas = $sel->fetchAll(PDO::FETCH_COLUMN);

			$del = $pdo->prepare("DELETE FROM $tabla WHERE id_observacion = :id");
			$del->bindParam(":id", $idObservacion, PDO::PARAM_INT);
			$del->execute();

			return is_array($rutas) ? $rutas : array();
		} catch (Exception $e) {
			return array();
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

	/*=============================================
	OBSERVACIONES RECIENTES PARA NOTIFICACIÓN
	(Hoy, no creadas por el usuario actual)
	=============================================*/
	static public function mdlObservacionesRecientesNotif($tabla, $idUsuario, $limite = 15, $ordenIds = null)
	{

		$filtroOrden = "";
		if (is_array($ordenIds) && !empty($ordenIds)) {
			$placeholders = implode(',', array_map('intval', $ordenIds));
			$filtroOrden = " AND o.id_orden IN ($placeholders)";
		}

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
			        a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 WHERE DATE(o.fecha) = CURDATE()
			   AND o.id_creador != :idUsuario
			   $filtroOrden
			 ORDER BY o.fecha DESC
			 LIMIT :limite"
		);

		$stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
		$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}
	/*=============================================
	ELIMINAR OBSERVACIONES
	=============================================*/
	static public function mdlEliminarObservacion($tabla, $datos)
	{



		$stmt = ConexionWP::conectarWP()->prepare("DELETE FROM $tabla WHERE id = :id");



		$stmt->bindParam(":id", $datos, PDO::PARAM_INT);



		if ($stmt->execute()) {



			return "ok";



		} else {



			return "error";



		}



		$stmt->close();



		$stmt = null;



	}

	/*=============================================
	ÚLTIMAS OBSERVACIONES FILTRADAS POR ÓRDENES
	=============================================*/

	static public function mdlUltimasObservacionesPorOrdenes($tabla, $idsOrdenes, $limite)
	{
		if (empty($idsOrdenes)) return array();

		$idsOrdenes = array_values(array_unique(array_map('intval', $idsOrdenes)));
		$placeholders = implode(',', array_fill(0, count($idsOrdenes), '?'));

		$sql = "SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
				       a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
				FROM $tabla o
				LEFT JOIN administradores a ON a.id = o.id_creador
				WHERE o.id_orden IN ($placeholders)
				ORDER BY o.fecha DESC
				LIMIT " . intval($limite);

		$stmt = Conexion::conectar()->prepare($sql);

		for ($i = 0; $i < count($idsOrdenes); $i++) {
			$stmt->bindValue($i + 1, $idsOrdenes[$i], PDO::PARAM_INT);
		}

		$stmt->execute();
		return $stmt->fetchAll();
	}
}