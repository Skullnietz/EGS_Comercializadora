<?php
/**
 * Ejemplo de configuración GA4 — renombrar/copiar a analytics.local.php
 *
 * 1. Crear propiedad GA4 en https://analytics.google.com
 * 2. Crear service account con acceso a la propiedad
 * 3. Descargar JSON de credenciales y guardar fuera del repo
 */
return array(
	'ga4_property_id'    => '123456789',
	'ga4_credentials'    => __DIR__ . '/../storage/ga4-service-account.json',
	'clarity_project_id' => 'tu_project_id',
	'cache_ttl_seconds'  => 900,
	'site_url'           => 'https://comercializadoraegs.com/',
	'tracking_endpoint'  => 'https://backend.comercializadoraegs.com/extensiones/tracking/registrar-visita.php',
);
