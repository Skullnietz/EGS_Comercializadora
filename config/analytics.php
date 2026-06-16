<?php
/**
 * Configuración de analítica web (GA4).
 * Copia config/analytics.example.php → config/analytics.local.php (gitignored) con tus valores.
 */
$analyticsLocal = __DIR__ . '/analytics.local.php';
if (is_file($analyticsLocal)) {
	return require $analyticsLocal;
}

return array(
	'ga4_property_id'     => getenv('GA4_PROPERTY_ID') ?: '',
	'ga4_credentials'     => getenv('GA4_CREDENTIALS_JSON') ?: '',
	'clarity_project_id'  => getenv('CLARITY_PROJECT_ID') ?: '',
	'cache_ttl_seconds'   => 900,
	'site_url'            => 'https://comercializadoraegs.com/',
	'tracking_endpoint'   => 'https://backend.comercializadoraegs.com/extensiones/tracking/registrar-visita.php',
);
