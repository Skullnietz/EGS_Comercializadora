<?php

require_once "conexion.php";

class ModeloCitas
{

	/*=============================================
	MOSTRAR CITAS
	=============================================*/
	static public function mdlMostrarCitas($tabla, $item, $valor)
	{

		if ($item != null) {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch();

		} else {

			$stmt = Conexion::conectar()->prepare("SELECT c.id, c.title, c.description, c.start, c.end, c.color, c.id_orden, o.descripcion AS equipo, cl.nombre AS cliente_nombre FROM $tabla c LEFT JOIN ordenes o ON c.id_orden = o.id LEFT JOIN clientes cl ON o.id_usuario = cl.id");
			$stmt->execute();
			return $stmt->fetchAll();

		}

		$stmt = null;

	}

	/*=============================================
	CREAR CITA
	=============================================*/
	static public function mdlIngresarCita($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(title, description, start, end, color, id_orden) VALUES (:title, :description, :start, :end, :color, :id_orden)");

		$stmt->bindParam(":title", $datos["title"], PDO::PARAM_STR);
		$stmt->bindParam(":description", $datos["description"], PDO::PARAM_STR);
		$stmt->bindParam(":start", $datos["start"], PDO::PARAM_STR);
		$stmt->bindParam(":end", $datos["end"], PDO::PARAM_STR);
		$stmt->bindParam(":color", $datos["color"], PDO::PARAM_STR);
		$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);

		if ($stmt->execute()) {
			$stmt = null;
			return "ok";
		} else {
			$errorInfo = $stmt->errorInfo();
			$stmt = null;
			return "error: " . implode(" - ", $errorInfo);
		}

	}

	/*=============================================
	CITAS POR RANGO DE FECHAS
	=============================================*/
	static public function mdlCitasPorRango($tabla, $inicio, $fin)
	{
		$stmt = Conexion::conectar()->prepare("SELECT c.id, c.title, c.description, c.start, c.end, c.color, c.id_orden, o.descripcion AS equipo, cl.nombre AS cliente_nombre FROM $tabla c LEFT JOIN ordenes o ON c.id_orden = o.id LEFT JOIN clientes cl ON o.id_usuario = cl.id WHERE c.start BETWEEN :inicio AND :fin ORDER BY c.start ASC");
		$stmt->bindParam(":inicio", $inicio, PDO::PARAM_STR);
		$stmt->bindParam(":fin", $fin, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	VERIFICAR DUPLICADO DE CITA (misma orden + misma hora)
	=============================================*/
	static public function mdlVerificarDuplicado($tabla, $fecha, $idOrden)
	{
		if (!$idOrden) {
			return array("duplicado" => false);
		}

		// Verificar si ya existe una cita para la misma orden a la misma hora exacta
		$stmt = Conexion::conectar()->prepare(
			"SELECT id, title, start FROM $tabla WHERE id_orden = :idOrden AND start = :fecha LIMIT 1"
		);
		$stmt->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
		$stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
		$stmt->execute();
		$porHora = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = null;

		if ($porHora) {
			return array("duplicado" => true, "tipo" => "orden_hora", "cita" => $porHora);
		}

		// Verificar si ya existe una cita para la misma orden en el mismo día
		$soloFecha = substr($fecha, 0, 10);
		$stmt2 = Conexion::conectar()->prepare(
			"SELECT id, title, start FROM $tabla WHERE id_orden = :idOrden AND DATE(start) = :fecha LIMIT 1"
		);
		$stmt2->bindParam(":idOrden", $idOrden, PDO::PARAM_INT);
		$stmt2->bindParam(":fecha", $soloFecha, PDO::PARAM_STR);
		$stmt2->execute();
		$porOrdenDia = $stmt2->fetch(PDO::FETCH_ASSOC);
		$stmt2 = null;

		if ($porOrdenDia) {
			return array("duplicado" => true, "tipo" => "orden_dia", "cita" => $porOrdenDia);
		}

		return array("duplicado" => false);
	}

	/*=============================================
	MOSTRAR CITAS FILTRADAS POR ORDENES (para técnicos)
	=============================================*/
	static public function mdlMostrarCitasPorOrdenes($tabla, $ordenIds)
	{
		if (empty($ordenIds)) {
			return array();
		}
		$in = implode(',', array_map('intval', $ordenIds));
		$stmt = Conexion::conectar()->prepare("SELECT c.id, c.title, c.description, c.start, c.end, c.color, c.id_orden, o.descripcion AS equipo, cl.nombre AS cliente_nombre FROM $tabla c LEFT JOIN ordenes o ON c.id_orden = o.id LEFT JOIN clientes cl ON o.id_usuario = cl.id WHERE c.id_orden IN ($in)");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	/*=============================================
	CITAS POR RANGO FILTRADAS POR ORDENES (para técnicos)
	=============================================*/
	static public function mdlCitasPorRangoYOrdenes($tabla, $inicio, $fin, $ordenIds)
	{
		if (empty($ordenIds)) {
			return array();
		}
		$in = implode(',', array_map('intval', $ordenIds));
		$stmt = Conexion::conectar()->prepare("SELECT c.id, c.title, c.description, c.start, c.end, c.color, c.id_orden, o.descripcion AS equipo, cl.nombre AS cliente_nombre FROM $tabla c LEFT JOIN ordenes o ON c.id_orden = o.id LEFT JOIN clientes cl ON o.id_usuario = cl.id WHERE c.start BETWEEN :inicio AND :fin AND c.id_orden IN ($in) ORDER BY c.start ASC");
		$stmt->bindParam(":inicio", $inicio, PDO::PARAM_STR);
		$stmt->bindParam(":fin", $fin, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	ELIMINAR CITA
	=============================================*/
	static public function mdlEliminarCita($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
		$stmt->bindParam(":id", $datos, PDO::PARAM_INT);

		if ($stmt->execute()) {
			$stmt = null;
			return "ok";
		} else {
			$stmt = null;
			return "error";
		}

	}

}
