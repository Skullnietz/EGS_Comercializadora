<?php
/**
 * Motor de recomendaciones y focos de mejora para el panel de Inteligencia Web.
 */
class VisitasInsightsHelper
{
	/**
	 * @param array $p Panorama de ControladorVisitas::ctrObtenerPanoramaWeb()
	 * @return array{recomendaciones: array, focos: array}
	 */
	public static function generar($p)
	{
		$recs = array();
		$focos = array(
			"conversion"  => array(),
			"contenido"   => array(),
			"geografia"   => array(),
			"catalogo"    => array(),
		);

		$v7   = intval(isset($p["visitas_7d"]) ? $p["visitas_7d"] : 0);
		$v30  = intval(isset($p["visitas_30d"]) ? $p["visitas_30d"] : 0);
		$ventas = floatval(isset($p["ventas_mes"]) ? $p["ventas_mes"] : 0);
		$nuevas = intval(isset($p["nuevas_visitas"]) ? $p["nuevas_visitas"] : 0);
		$prospectos = intval(isset($p["prospectos_mes"]) ? $p["prospectos_mes"] : 0);
		$ordenes = intval(isset($p["ordenes_ingresadas"]) ? $p["ordenes_ingresadas"] : 0);
		$promIp = isset($p["promedio_ip"]["promedio"]) ? floatval($p["promedio_ip"]["promedio"]) : 0;
		$pctExt = isset($p["extranjero"]["pct"]) ? intval($p["extranjero"]["pct"]) : 0;
		$audit = isset($p["audit"]) ? $p["audit"] : array();
		$trackingActivo = !empty($audit["tracking_activo"]);
		$ga4 = isset($p["ga4"]) ? $p["ga4"] : array();
		$gaActivo = !empty($ga4["activo"]);

		if (!$trackingActivo && empty($audit["total_registros"])) {
			$recs[] = self::item(
				"alta",
				"fa-satellite-dish",
				"Sin datos de tracking local",
				"Activa el script de visitas en comercializadoraegs.com o configura GA4 para medir tráfico real.",
				"Ver guía de analítica",
				"#visitas-config",
				"contenido"
			);
			$focos["contenido"][] = "Implementar medición web (GA4 + tracking propio)";
		} elseif (!$trackingActivo) {
			$recs[] = self::item(
				"media",
				"fa-clock",
				"Tracking local inactivo",
				"La última visita registrada tiene más de 14 días. Reactiva el endpoint de tracking o migra a GA4.",
				"Configurar analítica",
				"#visitas-config",
				"contenido"
			);
		}

		if (!$gaActivo) {
			$recs[] = self::item(
				"media",
				"fa-chart-line",
				"GA4 no configurado",
				"Conecta Google Analytics 4 para ver páginas más visitadas, dispositivos y embudos de conversión.",
				"Configurar GA4",
				"#visitas-config",
				"contenido"
			);
		}

		if ($nuevas >= 5) {
			$recs[] = self::item(
				"alta",
				"fa-bell",
				"Pico de tráfico reciente",
				"Tienes {$nuevas} nuevas visitas sin revisar. Identifica qué página o campaña las generó.",
				"Ver detalle de visitas",
				"#visitas-detalle",
				"contenido"
			);
		}

		if ($v7 >= 10 && $ventas < 1) {
			$recs[] = self::item(
				"alta",
				"fa-cart-shopping",
				"Tráfico sin conversión en tienda",
				"Hay {$v7} visitas en 7 días pero pocas ventas e-commerce este mes. Revisa precios, CTA y proceso de checkout.",
				"Gestionar productos",
				"index.php?ruta=productos",
				"conversion"
			);
			$focos["conversion"][] = "Mejorar embudo de compra en la tienda online";
		} elseif ($v7 >= 20 && $ventas > 0 && $v7 > 0) {
			$tasa = round(($ventas / max($v7, 1)) * 100, 1);
			if ($tasa < 5) {
				$recs[] = self::item(
					"media",
					"fa-filter",
					"Tasa de conversión baja",
					"Muchas visitas ({$v7}/7d) frente a ventas del mes. Optimiza landings y formularios de contacto.",
					"Ver ventas",
					"index.php?ruta=ventas",
					"conversion"
				);
			}
		}

		if ($v7 >= 5 && $prospectos === 0) {
			$recs[] = self::item(
				"media",
				"fa-user-plus",
				"Visitas sin nuevos registros",
				"Hay tráfico pero no hay prospectos nuevos este mes. Facilita registro y cotización en la web.",
				"Ver clientes",
				"index.php?ruta=clientes",
				"conversion"
			);
			$focos["conversion"][] = "Facilitar registro y formularios de cotización";
		}

		if ($pctExt >= 40) {
			$top = isset($p["extranjero"]["top_extranjero"]["pais"])
				? $p["extranjero"]["top_extranjero"]["pais"] : "extranjero";
			$recs[] = self::item(
				"media",
				"fa-globe",
				"Audiencia internacional ({$pctExt}%)",
				"Buena parte del tráfico viene de fuera de México (destacado: {$top}). Evalúa envíos, idioma y SEO regional.",
				"Revisar catálogo",
				"index.php?ruta=productos",
				"geografia"
			);
			$focos["geografia"][] = "Adaptar contenido y logística para audiencia internacional";
		}

		if ($promIp > 0 && $promIp < 1.5 && $v30 >= 15) {
			$recs[] = self::item(
				"media",
				"fa-book-open",
				"Bajo engagement por visitante",
				"Promedio de {$promIp} visitas por IP. Mejora contenido, velocidad y navegación (usa Clarity para heatmaps).",
				"Microsoft Clarity",
				"https://clarity.microsoft.com/",
				"contenido"
			);
			$focos["contenido"][] = "Mejorar contenido, velocidad y UX (heatmaps)";
		}

		if (!empty($p["recurrentes"])) {
			$topR = $p["recurrentes"][0];
			$ip = isset($topR["ip"]) ? $topR["ip"] : "";
			$tv = isset($topR["total_visitas"]) ? intval($topR["total_visitas"]) : 0;
			if ($tv >= 10) {
				$recs[] = self::item(
					"baja",
					"fa-repeat",
					"Visitante recurrente sin cerrar",
					"La IP {$ip} acumula {$tv} visitas. Considera retargeting, cupón o contacto comercial.",
					"CRM clientes",
					"index.php?ruta=clientes",
					"conversion"
				);
			}
		}

		if ($ordenes >= 10 && $v30 < 5) {
			$recs[] = self::item(
				"baja",
				"fa-screwdriver-wrench",
				"Órdenes de servicio sin tráfico web",
				"El canal de reparación crece pero la web registra poco tráfico. Refuerza SEO local y landing de servicios.",
				"Ver órdenes",
				"index.php?ruta=ordenes",
				"contenido"
			);
		}

		if (!empty($p["productos_top"])) {
			$topProd = $p["productos_top"][0];
			$nombre = isset($topProd["descripcion"]) ? $topProd["descripcion"] : "producto líder";
			$ventasProd = isset($topProd["ventas"]) ? intval($topProd["ventas"]) : 0;
			$totalV = floatval(isset($p["total_prod_ventas"]) ? $p["total_prod_ventas"] : 1);
			$share = $totalV > 0 ? round($ventasProd * 100 / $totalV) : 0;
			if ($share >= 35) {
				$recs[] = self::item(
					"media",
					"fa-star",
					"Catálogo concentrado en un producto",
					"\"{$nombre}\" representa ~{$share}% de ventas. Destácalo en home y banners de la tienda.",
					"Editar productos",
					"index.php?ruta=productos",
					"catalogo"
				);
				$focos["catalogo"][] = "Destacar productos estrella en página principal";
			}
		}

		if ($gaActivo && !empty($ga4["metricas"]["paginas_top"])) {
			$pag = $ga4["metricas"]["paginas_top"][0];
			$titulo = isset($pag["titulo"]) ? $pag["titulo"] : "";
			$recs[] = self::item(
				"baja",
				"fa-file-lines",
				"Página más visitada (GA4)",
				"\"{$titulo}\" lidera vistas. Asegura CTA claro hacia cotización o compra en esa página.",
				"Abrir tienda",
				"https://comercializadoraegs.com/",
				"contenido"
			);
		}

		if ($gaActivo && !empty($ga4["metricas"]["rebote_alto"])) {
			$recs[] = self::item(
				"alta",
				"fa-arrow-right-from-bracket",
				"Alta tasa de rebote (GA4)",
				"Muchos usuarios salen sin interactuar. Revisa velocidad, mensaje principal y enlaces internos.",
				"Clarity heatmaps",
				"https://clarity.microsoft.com/",
				"contenido"
			);
		}

		if (empty($recs)) {
			$recs[] = self::item(
				"baja",
				"fa-circle-check",
				"Panorama estable",
				"Sin alertas críticas. Sigue monitoreando tendencia semanal y comparando con ventas del mes.",
				"Ver gráficos",
				"#visitas-resumen",
				"contenido"
			);
		}

		usort($recs, function ($a, $b) {
			$ord = array("alta" => 0, "media" => 1, "baja" => 2);
			$pa = isset($ord[$a["prioridad"]]) ? $ord[$a["prioridad"]] : 9;
			$pb = isset($ord[$b["prioridad"]]) ? $ord[$b["prioridad"]] : 9;
			return $pa - $pb;
		});

		if (empty($focos["conversion"]) && $v7 > 0) {
			$focos["conversion"][] = "Mantener relación visitas vs ventas del mes";
		}
		if (empty($focos["contenido"])) {
			$focos["contenido"][] = "Revisar páginas clave y tiempo en sitio (GA4/Clarity)";
		}
		if (empty($focos["geografia"]) && !empty($p["top_paises"])) {
			$focos["geografia"][] = "Monitorear distribución geográfica del tráfico";
		}
		if (empty($focos["catalogo"])) {
			$focos["catalogo"][] = "Rotar productos destacados según ventas reales";
		}

		return array(
			"recomendaciones" => $recs,
			"focos"           => $focos,
		);
	}

	private static function item($prioridad, $icono, $titulo, $mensaje, $accionLabel, $accionUrl, $categoria)
	{
		return array(
			"prioridad"    => $prioridad,
			"icono"        => $icono,
			"titulo"       => $titulo,
			"mensaje"      => $mensaje,
			"accion_label" => $accionLabel,
			"accion_url"   => $accionUrl,
			"categoria"    => $categoria,
		);
	}
}
