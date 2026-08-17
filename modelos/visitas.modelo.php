<?php

require_once "conexion.php";

class ModeloVisitas{

	private static function conectarSeguro()
	{
		try {
			return Conexion::conectar();
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * Verifica si una tabla existe en la BD e-commerce.
	 */
	static private function tablaExiste($pdo, $nombreTabla)
	{
		try {
			$nombreTabla = preg_replace('/[^a-zA-Z0-9_]/', '', $nombreTabla);
			$stmt = $pdo->query("SHOW TABLES LIKE '" . $nombreTabla . "'");
			return $stmt && $stmt->rowCount() > 0;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Crea visitasPersonas y visitasPaises si no existen (idempotente).
	 * @return array{ok: bool, creadas: string[], mensaje: string}
	 */
	static public function mdlCrearTablasVisitas()
	{
		$resultado = array(
			"ok"       => false,
			"creadas"  => array(),
			"mensaje"  => "",
		);

		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) {
				$resultado["mensaje"] = "Sin conexión a base de datos e-commerce";
				return $resultado;
			}

			// Ruta normal: validar con lecturas livianas y evitar DDL/SHOW TABLES en cada carga.
			try {
				$pdo->query("SELECT 1 FROM visitasPersonas LIMIT 0");
				$pdo->query("SELECT 1 FROM visitasPaises LIMIT 0");
				$resultado["ok"] = true;
				$resultado["mensaje"] = "Tablas de visitas disponibles";
				return $resultado;
			} catch (Exception $e) {
				// Instalación inicial: continuar con la creación idempotente de las tablas.
			}

			if (!self::tablaExiste($pdo, "visitasPersonas")) {
				$pdo->exec("CREATE TABLE IF NOT EXISTS `visitasPersonas` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`ip` VARCHAR(45) NOT NULL,
					`pais` VARCHAR(80) NOT NULL DEFAULT 'Desconocido',
					`visitas` INT(11) NOT NULL DEFAULT 1,
					`fecha` DATETIME NOT NULL,
					PRIMARY KEY (`id`),
					KEY `idx_visitas_fecha` (`fecha`),
					KEY `idx_visitas_ip` (`ip`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8");
				$resultado["creadas"][] = "visitasPersonas";
			}

			if (!self::tablaExiste($pdo, "visitasPaises")) {
				$pdo->exec("CREATE TABLE IF NOT EXISTS `visitasPaises` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`pais` VARCHAR(80) NOT NULL,
					`cantidad` INT(11) NOT NULL DEFAULT 0,
					PRIMARY KEY (`id`),
					UNIQUE KEY `uk_visitas_pais` (`pais`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8");
				$resultado["creadas"][] = "visitasPaises";
			}

			$resultado["ok"] = self::tablaExiste($pdo, "visitasPersonas")
				&& self::tablaExiste($pdo, "visitasPaises");

			if (!empty($resultado["creadas"])) {
				$resultado["mensaje"] = "Tablas creadas: " . implode(", ", $resultado["creadas"]);
			} else {
				$resultado["mensaje"] = "Tablas de visitas ya existían";
			}
		} catch (Exception $e) {
			$resultado["mensaje"] = $e->getMessage();
		}

		return $resultado;
	}

	static public function mdlMostrarTotalVisitas($tabla){

		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array("total" => 0);
			$stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM $tabla");
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row ? $row : array("total" => 0);
		} catch (Exception $e) {
			return array("total" => 0);
		}
	}

	static public function mdlMostrarPaises($tabla, $orden){
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array();
			$orden = preg_replace('/[^a-zA-Z0-9_]/', '', $orden);
			$stmt = $pdo->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return array();
		}
	}

	static public function mdlMostrarVisitas($tabla){
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array();
			$stmt = $pdo->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return array();
		}
	}

	static public function mdlAuditarTracking(){

		$audit = array(
			"total_registros" => 0,
			"ultima_fecha"    => null,
			"visitas_7d"      => 0,
			"visitas_30d"     => 0,
			"tracking_activo" => false,
		);

		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) {
				$audit["error"] = "Sin conexión a base de datos e-commerce";
				return $audit;
			}

			$instalacion = self::mdlCrearTablasVisitas();
			$audit["instalacion"] = $instalacion;

			if (!$instalacion["ok"]) {
				$audit["error"] = isset($instalacion["mensaje"]) ? $instalacion["mensaje"] : "No se pudieron crear las tablas de visitas";
				return $audit;
			}

			$stmt = $pdo->query("SELECT COUNT(*) AS c, MAX(fecha) AS ultima FROM visitasPersonas");
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				$audit["total_registros"] = intval($row["c"]);
				$audit["ultima_fecha"]    = $row["ultima"];
			}

			$audit["visitas_7d"]  = self::mdlContarVisitasRango(7);
			$audit["visitas_30d"] = self::mdlContarVisitasRango(30);

			if ($audit["ultima_fecha"]) {
				$ultima = strtotime($audit["ultima_fecha"]);
				$audit["tracking_activo"] = ($ultima !== false && $ultima >= strtotime("-14 days"));
			}
		} catch (Exception $e) {
			$audit["error"] = $e->getMessage();
		}

		return $audit;
	}

	static public function mdlContarVisitasRango($dias){

		$dias = max(1, intval($dias));
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return 0;
			$sql = "SELECT COUNT(*) AS total FROM visitasPersonas
			        WHERE fecha >= DATE_SUB(NOW(), INTERVAL " . $dias . " DAY)";
			$stmt = $pdo->query($sql);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row ? intval($row["total"]) : 0;
		} catch (Exception $e) {
			return 0;
		}
	}

	static public function mdlTendenciaVisitas($dias = 30){

		$dias = max(7, min(90, intval($dias)));
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array();
			$sql = "SELECT DATE(fecha) AS dia, COUNT(*) AS total
			        FROM visitasPersonas
			        WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL " . $dias . " DAY)
			        GROUP BY DATE(fecha)
			        ORDER BY dia ASC";
			$stmt = $pdo->query($sql);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return array();
		}
	}

	static public function mdlTopPaises($limite = 8){

		$limite = max(1, min(20, intval($limite)));
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array();

			try {
				$sql = "SELECT pais, SUM(cantidad) AS total FROM visitasPaises
				        GROUP BY pais ORDER BY total DESC LIMIT " . $limite;
				$stmt = $pdo->query($sql);
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if (!empty($rows)) {
					return $rows;
				}
			} catch (Exception $e) {}

			$sql2 = "SELECT pais, COUNT(*) AS total FROM visitasPersonas
			         WHERE pais IS NOT NULL AND pais != ''
			         GROUP BY pais ORDER BY total DESC LIMIT " . $limite;
			$stmt2 = $pdo->query($sql2);
			return $stmt2->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return array();
		}
	}

	static public function mdlPromedioVisitasPorIp(){

		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array("promedio" => 0, "ips" => 0);
			$stmt = $pdo->query(
				"SELECT AVG(visitas) AS promedio, COUNT(DISTINCT ip) AS ips
				 FROM visitasPersonas WHERE ip IS NOT NULL AND ip != ''"
			);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return array(
				"promedio" => $row && $row["promedio"] !== null ? round(floatval($row["promedio"]), 2) : 0,
				"ips"      => $row ? intval($row["ips"]) : 0,
			);
		} catch (Exception $e) {
			return array("promedio" => 0, "ips" => 0);
		}
	}

	static public function mdlVisitantesRecurrentes($minVisitas = 5, $limite = 5){

		$minVisitas = max(2, intval($minVisitas));
		$limite = max(1, min(20, intval($limite)));
		try {
			$pdo = self::conectarSeguro();
			if (!$pdo) return array();
			$sql = "SELECT ip, pais, SUM(visitas) AS total_visitas, MAX(fecha) AS ultima
			        FROM visitasPersonas
			        GROUP BY ip, pais
			        HAVING SUM(visitas) >= " . $minVisitas . "
			        ORDER BY total_visitas DESC
			        LIMIT " . $limite;
			$stmt = $pdo->query($sql);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return array();
		}
	}

	static public function mdlPorcentajeExtranjero(){

		try {
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
		} catch (Exception $e) {
			return array("pct" => 0, "top_extranjero" => null);
		}
	}

}
