<?php

require_once "conexion.php";

class ModeloVisitas{

	static public function mdlMostrarTotalVisitas($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(cantidad) as total FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarPaises($tabla, $orden){
		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();
	
	}

	static public function mdlMostrarVisitas($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	AUDITORÍA DE TRACKING LOCAL
	=============================================*/

	static public function mdlAuditarTracking(){

		$pdo = Conexion::conectar();

		$audit = array(
			"total_registros" => 0,
			"ultima_fecha"    => null,
			"visitas_7d"      => 0,
			"visitas_30d"     => 0,
			"tracking_activo" => false,
		);

		try {
			$stmt = $pdo->query("SELECT COUNT(*) AS c, MAX(fecha) AS ultima FROM visitasPersonas");
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				$audit["total_registros"] = intval($row["c"]);
				$audit["ultima_fecha"]    = $row["ultima"];
			}

			$stmt7 = $pdo->query("SELECT COUNT(*) AS c FROM visitasPersonas WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
			$r7 = $stmt7->fetch(PDO::FETCH_ASSOC);
			$audit["visitas_7d"] = $r7 ? intval($r7["c"]) : 0;

			$stmt30 = $pdo->query("SELECT COUNT(*) AS c FROM visitasPersonas WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
			$r30 = $stmt30->fetch(PDO::FETCH_ASSOC);
			$audit["visitas_30d"] = $r30 ? intval($r30["c"]) : 0;

			if ($audit["ultima_fecha"]) {
				$ultima = strtotime($audit["ultima_fecha"]);
				$audit["tracking_activo"] = ($ultima !== false && $ultima >= strtotime("-14 days"));
			}
		} catch (Exception $e) {
			$audit["error"] = $e->getMessage();
		}

		return $audit;
	}

	/*=============================================
	CONTEO VISITAS POR RANGO (días)
	=============================================*/

	static public function mdlContarVisitasRango($dias){

		$dias = max(1, intval($dias));
		$stmt = Conexion::conectar()->prepare(
			"SELECT COUNT(*) AS total FROM visitasPersonas WHERE fecha >= DATE_SUB(NOW(), INTERVAL :dias DAY)"
		);
		$stmt->bindParam(":dias", $dias, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row ? intval($row["total"]) : 0;
	}

	/*=============================================
	TENDENCIA DIARIA DE VISITAS
	=============================================*/

	static public function mdlTendenciaVisitas($dias = 30){

		$dias = max(7, min(90, intval($dias)));
		$stmt = Conexion::conectar()->prepare(
			"SELECT DATE(fecha) AS dia, COUNT(*) AS total
			 FROM visitasPersonas
			 WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
			 GROUP BY DATE(fecha)
			 ORDER BY dia ASC"
		);
		$stmt->bindParam(":dias", $dias, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	TOP PAÍSES (tabla visitasPaises o agregado personas)
	=============================================*/

	static public function mdlTopPaises($limite = 8){

		$limite = max(1, min(20, intval($limite)));
		$pdo = Conexion::conectar();

		try {
			$stmt = $pdo->prepare("SELECT pais, SUM(cantidad) AS total FROM visitasPaises GROUP BY pais ORDER BY total DESC LIMIT :lim");
			$stmt->bindValue(":lim", $limite, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!empty($rows)) {
				return $rows;
			}
		} catch (Exception $e) {}

		$stmt2 = $pdo->prepare(
			"SELECT pais, COUNT(*) AS total FROM visitasPersonas
			 WHERE pais IS NOT NULL AND pais != ''
			 GROUP BY pais ORDER BY total DESC LIMIT :lim"
		);
		$stmt2->bindValue(":lim", $limite, PDO::PARAM_INT);
		$stmt2->execute();
		return $stmt2->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	PROMEDIO DE VISITAS POR IP
	=============================================*/

	static public function mdlPromedioVisitasPorIp(){

		$stmt = Conexion::conectar()->query(
			"SELECT AVG(visitas) AS promedio, COUNT(DISTINCT ip) AS ips
			 FROM visitasPersonas WHERE ip IS NOT NULL AND ip != ''"
		);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return array(
			"promedio" => $row && $row["promedio"] !== null ? round(floatval($row["promedio"]), 2) : 0,
			"ips"      => $row ? intval($row["ips"]) : 0,
		);
	}

	/*=============================================
	VISITANTES RECURRENTES (muchas visitas, misma IP)
	=============================================*/

	static public function mdlVisitantesRecurrentes($minVisitas = 5, $limite = 5){

		$minVisitas = max(2, intval($minVisitas));
		$limite = max(1, min(20, intval($limite)));
		$stmt = Conexion::conectar()->prepare(
			"SELECT ip, pais, SUM(visitas) AS total_visitas, MAX(fecha) AS ultima
			 FROM visitasPersonas
			 GROUP BY ip, pais
			 HAVING total_visitas >= :min
			 ORDER BY total_visitas DESC
			 LIMIT :lim"
		);
		$stmt->bindValue(":min", $minVisitas, PDO::PARAM_INT);
		$stmt->bindValue(":lim", $limite, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	SUMA PAÍSES EXTRANJEROS (% no México)
	=============================================*/

	static public function mdlPorcentajeExtranjero(){

		$paises = self::mdlTopPaises(50);
		if (empty($paises)) {
			return array("pct" => 0, "top_extranjero" => null);
		}

		$total = 0;
		$extranjero = 0;
		$topExt = null;

		foreach ($paises as $p) {
			$c = intval(isset($p["total"]) ? $p["total"] : 0);
			$total += $c;
			$nombre = isset($p["pais"]) ? trim($p["pais"]) : "";
			$esMexico = (stripos($nombre, "mex") !== false || stripos($nombre, "méx") !== false);
			if (!$esMexico) {
				$extranjero += $c;
				if ($topExt === null || $c > $topExt["total"]) {
					$topExt = array("pais" => $nombre, "total" => $c);
				}
			}
		}

		$pct = $total > 0 ? round($extranjero * 100 / $total) : 0;
		return array("pct" => $pct, "top_extranjero" => $topExt);
	}

}
