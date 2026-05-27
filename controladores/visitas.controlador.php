<?php

class ControladorVisitas{

	static public function ctrMostrarTotalVisitas(){

		$tabla = "visitasPaises";

		$respuesta = ModeloVisitas::mdlMostrarTotalVisitas($tabla);

		return $respuesta;

	}

	static public function ctrMostrarPaises($orden){

		$tabla = "visitasPaises";
	
		$respuesta = ModeloVisitas::mdlMostrarPaises($tabla, $orden);
		
		return $respuesta;
	}

	static public function ctrMostrarVisitas(){

		ModeloVisitas::mdlCrearTablasVisitas();

		$tabla = "visitasPersonas";
	
		$respuesta = ModeloVisitas::mdlMostrarVisitas($tabla);
		
		return $respuesta;
	}

	static public function ctrInstalarTablasVisitas(){
		return ModeloVisitas::mdlCrearTablasVisitas();
	}

	static private function panoramaVacio()
	{
		return array(
			"audit"              => array(
				"total_registros" => 0,
				"ultima_fecha"    => null,
				"visitas_7d"      => 0,
				"visitas_30d"     => 0,
				"tracking_activo" => false,
				"error"           => "No se pudo cargar el panorama",
			),
			"total_historico"    => 0,
			"visitas_7d"         => 0,
			"visitas_30d"        => 0,
			"nuevas_visitas"     => 0,
			"ventas_mes"         => 0,
			"pedidos_mes"        => 0,
			"prospectos_mes"     => 0,
			"ordenes_ingresadas" => 0,
			"tendencia"          => array(),
			"top_paises"         => array(),
			"promedio_ip"        => array("promedio" => 0, "ips" => 0),
			"recurrentes"        => array(),
			"extranjero"         => array("pct" => 0, "top_extranjero" => null),
			"productos_top"      => array(),
			"total_prod_ventas"  => 1,
			"ga4"                => array("activo" => false, "metricas" => array()),
		);
	}

	static public function ctrObtenerPanoramaWeb(){

		try {

		$panorama = array(
			"audit"              => ModeloVisitas::mdlAuditarTracking(),
			"total_historico"    => 0,
			"visitas_7d"         => 0,
			"visitas_30d"        => 0,
			"nuevas_visitas"     => 0,
			"ventas_mes"         => 0,
			"pedidos_mes"        => 0,
			"prospectos_mes"     => 0,
			"ordenes_ingresadas" => 0,
			"tendencia"          => array(),
			"top_paises"         => array(),
			"promedio_ip"        => array("promedio" => 0, "ips" => 0),
			"recurrentes"        => array(),
			"extranjero"         => array("pct" => 0, "top_extranjero" => null),
			"productos_top"      => array(),
			"total_prod_ventas"  => 1,
			"ga4"                => array("activo" => false, "metricas" => array()),
		);

		$totalRow = self::ctrMostrarTotalVisitas();
		if (is_array($totalRow) && isset($totalRow["total"])) {
			$panorama["total_historico"] = intval($totalRow["total"]);
		}

		$panorama["visitas_7d"]  = ModeloVisitas::mdlContarVisitasRango(7);
		$panorama["visitas_30d"] = ModeloVisitas::mdlContarVisitasRango(30);
		$panorama["tendencia"]   = ModeloVisitas::mdlTendenciaVisitas(30);
		$panorama["top_paises"]  = ModeloVisitas::mdlTopPaises(8);
		$panorama["promedio_ip"] = ModeloVisitas::mdlPromedioVisitasPorIp();
		$panorama["recurrentes"] = ModeloVisitas::mdlVisitantesRecurrentes(5, 5);
		$panorama["extranjero"]  = ModeloVisitas::mdlPorcentajeExtranjero();

		if (class_exists("ControladorNotificaciones")) {
			try {
				$notif = ControladorNotificaciones::ctrMostrarNotificaciones();
				if (is_array($notif) && isset($notif["nuevasVisitas"])) {
					$panorama["nuevas_visitas"] = intval($notif["nuevasVisitas"]);
				}
			} catch (Exception $e) {}
		}

		if (class_exists("ControladorVentas")) {
			try {
				$ventas = ControladorVentas::ctrMostrarTotalVentasMes();
				if (is_array($ventas) && isset($ventas["total"])) {
					$panorama["ventas_mes"] = floatval($ventas["total"]);
				}
			} catch (Exception $e) {}
		}

		if (class_exists("ControladorPedidos")) {
			try {
				$pedidos = ControladorPedidos::ctrMostrarTotalPedidosMes("id");
				if (is_array($pedidos)) {
					foreach ($pedidos as $p) {
						$panorama["pedidos_mes"] += floatval(isset($p["total"]) ? $p["total"] : 0);
					}
				}
			} catch (Exception $e) {}
		}

		if (class_exists("ControladorUsuarios")) {
			try {
				$usuarios = ControladorUsuarios::ctrMostrarTotalUsuariosMes("id");
				if (is_array($usuarios)) {
					$panorama["prospectos_mes"] = count($usuarios);
				}
			} catch (Exception $e) {}
		}

		if (class_exists("controladorOrdenes")) {
			try {
				$entradas = @controladorOrdenes::ctrMostrarOrdenesEntrada();
				if (is_array($entradas)) {
					$panorama["ordenes_ingresadas"] = count($entradas);
				}
			} catch (Exception $e) {}
		}

		if (class_exists("ControladorProductos")) {
			try {
				$productos = ControladorProductos::ctrMostrarTotalProductos("ventas");
				if (is_array($productos)) {
					$panorama["productos_top"] = array_slice($productos, 0, 5);
				}
				$tv = ControladorProductos::ctrMostrarSumaVentas();
				if (is_array($tv) && isset($tv["total"]) && floatval($tv["total"]) > 0) {
					$panorama["total_prod_ventas"] = floatval($tv["total"]);
				}
			} catch (Exception $e) {}
		}

		if (!class_exists("ControladorAnalytics", false)) {
			$analyticsCtrl = __DIR__ . '/analytics.controlador.php';
			if (is_file($analyticsCtrl)) {
				require_once $analyticsCtrl;
			}
		}
		if (class_exists("ControladorAnalytics")) {
			try {
				$ga = ControladorAnalytics::ctrObtenerResumen();
				if (is_array($ga)) {
					$panorama["ga4"] = $ga;
				}
			} catch (Exception $e) {}
		}

		return $panorama;

		} catch (Exception $e) {
			$p = self::panoramaVacio();
			$p["audit"]["error"] = $e->getMessage();
			return $p;
		}
	}

}
