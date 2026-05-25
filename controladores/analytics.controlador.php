<?php

require_once __DIR__ . '/../modelos/analytics.modelo.php';

class ControladorAnalytics
{
	public static function ctrObtenerConfig()
	{
		return require __DIR__ . '/../config/analytics.php';
	}

	/**
	 * Resumen GA4 para el panel (vacío si no hay credenciales).
	 */
	public static function ctrObtenerResumen()
	{
		$cfg = self::ctrObtenerConfig();
		$propertyId = trim(isset($cfg['ga4_property_id']) ? $cfg['ga4_property_id'] : '');
		$credsPath  = trim(isset($cfg['ga4_credentials']) ? $cfg['ga4_credentials'] : '');
		$ttl        = intval(isset($cfg['cache_ttl_seconds']) ? $cfg['cache_ttl_seconds'] : 900);

		$vacío = array(
			'activo'   => false,
			'metricas' => array(),
		);

		if ($propertyId === '' || $credsPath === '') {
			return $vacío;
		}

		$cached = ModeloAnalytics::mdlLeerCache('resumen', $ttl);
		if (is_array($cached)) {
			$cached['activo'] = true;
			return $cached;
		}

		$raw = ModeloAnalytics::mdlConsultarGA4($propertyId, $credsPath);
		if (!is_array($raw)) {
			return $vacío;
		}

		$metricas = self::parsearReporte($raw);
		$out = array(
			'activo'   => true,
			'metricas' => $metricas,
		);
		ModeloAnalytics::mdlGuardarCache('resumen', $out);

		return $out;
	}

	private static function parsearReporte($raw)
	{
		$out = array(
			'sesiones'     => 0,
			'usuarios'     => 0,
			'pageviews'    => 0,
			'rebote'       => 0,
			'rebote_alto'  => false,
			'paginas_top'  => array(),
		);

		if (empty($raw['rows'])) {
			return $out;
		}

		$totSes = 0;
		$totUsr = 0;
		$totPv  = 0;
		$rebSum = 0;
		$rebCnt = 0;
		$paginas = array();

		foreach ($raw['rows'] as $row) {
			$dim = isset($row['dimensionValues'][0]['value']) ? $row['dimensionValues'][0]['value'] : '';
			$vals = isset($row['metricValues']) ? $row['metricValues'] : array();
			$s = isset($vals[0]['value']) ? floatval($vals[0]['value']) : 0;
			$u = isset($vals[1]['value']) ? floatval($vals[1]['value']) : 0;
			$pv = isset($vals[2]['value']) ? floatval($vals[2]['value']) : 0;
			$br = isset($vals[3]['value']) ? floatval($vals[3]['value']) : 0;

			$totSes += $s;
			$totUsr += $u;
			$totPv  += $pv;
			if ($br > 0) {
				$rebSum += $br;
				$rebCnt++;
			}

			if ($dim !== '' && $dim !== '(not set)') {
				$paginas[] = array('titulo' => $dim, 'vistas' => intval($pv));
			}
		}

		usort($paginas, function ($a, $b) {
			return $b['vistas'] - $a['vistas'];
		});

		$out['sesiones']    = intval($totSes);
		$out['usuarios']    = intval($totUsr);
		$out['pageviews']   = intval($totPv);
		$out['rebote']      = $rebCnt > 0 ? round($rebSum / $rebCnt * 100, 1) : 0;
		$out['rebote_alto'] = $out['rebote'] >= 55;
		$out['paginas_top'] = array_slice($paginas, 0, 5);

		return $out;
	}
}
