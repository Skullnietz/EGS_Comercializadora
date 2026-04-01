<?php

require_once "conexion.php";
require_once "conexionWordpress.php";

class ModeloCitas
{

	/*=============================================
	HELPER: Enriquecer citas con datos de orden y cliente
	(ordenes está en BD WordPress/respaldo, clientes en ecommerce)
	=============================================*/
	private static function enriquecerCitas($citas)
	{
		if (empty($citas)) return $citas;

		// Recolectar IDs de orden únicos
		$ordenIds = array();
		foreach ($citas as $c) {
			if (!empty($c["id_orden"])) {
				$ordenIds[] = intval($c["id_orden"]);
			}
		}
		if (empty($ordenIds)) return $citas;
		$ordenIds = array_unique($ordenIds);

		// Consultar ordenes (BD WordPress/respaldo)
		$in = implode(',', $ordenIds);
		try {
			$stmt = ConexionWP::conectarWP()->prepare("SELECT id, descripcion, id_usuario FROM ordenes WHERE id IN ($in)");
			$stmt->execute();
			$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = null;
		} catch (Exception $e) {
			return $citas;
		}

		// Mapear ordenes por ID
		$ordenMap = array();
		$clienteIds = array();
		foreach ($ordenes as $o) {
			$ordenMap[intval($o["id"])] = $o;
			if (!empty($o["id_usuario"])) {
				$clienteIds[] = intval($o["id_usuario"]);
			}
		}

		// Consultar clientes (BD ecommerce)
		$clienteMap = array();
		if (!empty($clienteIds)) {
			$clienteIds = array_unique($clienteIds);
			$inCl = implode(',', $clienteIds);
			try {
				$stmt2 = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes WHERE id IN ($inCl)");
				$stmt2->execute();
				$clientes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
				$stmt2 = null;
				foreach ($clientes as $cl) {
					$clienteMap[intval($cl["id"])] = $cl["nombre"];
				}
			} catch (Exception $e) {}
		}

		// Enriquecer cada cita
		foreach ($citas as &$c) {
			$c["equipo"] = "";
			$c["cliente_nombre"] = "";
			if (!empty($c["id_orden"]) && isset($ordenMap[intval($c["id_orden"])])) {
				$o = $ordenMap[intval($c["id_orden"])];
				$c["equipo"] = isset($o["descripcion"]) ? $o["descripcion"] : "";
				if (!empty($o["id_usuario"]) && isset($clienteMap[intval($o["id_usuario"])])) {
					$c["cliente_nombre"] = $clienteMap[intval($o["id_usuario"])];
				}
			}
		}
		unset($c);

		return $citas;
	}

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

			$stmt = Conexion::conectar()->prepare("SELECT id, title, description, start, end, color, id_orden FROM $tabla");
			$stmt->execute();
			$citas = $stmt->fetchAll();
			return self::enriquecerCitas($citas);

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
		$stmt = Conexion::conectar()->prepare("SELECT id, title, description, start, end, color, id_orden FROM $tabla WHERE start BETWEEN :inicio AND :fin ORDER BY start ASC");
		$stmt->bindParam(":inicio", $inicio, PDO::PARAM_STR);
		$stmt->bindParam(":fin", $fin, PDO::PARAM_STR);
		$stmt->execute();
		$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return self::enriquecerCitas($citas);
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
		$stmt = Conexion::conectar()->prepare("SELECT id, title, description, start, end, color, id_orden FROM $tabla WHERE id_orden IN ($in)");
		$stmt->execute();
		$citas = $stmt->fetchAll();
		return self::enriquecerCitas($citas);
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
		$stmt = Conexion::conectar()->prepare("SELECT id, title, description, start, end, color, id_orden FROM $tabla WHERE start BETWEEN :inicio AND :fin AND id_orden IN ($in) ORDER BY start ASC");
		$stmt->bindParam(":inicio", $inicio, PDO::PARAM_STR);
		$stmt->bindParam(":fin", $fin, PDO::PARAM_STR);
		$stmt->execute();
		$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return self::enriquecerCitas($citas);
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
