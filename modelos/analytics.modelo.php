<?php

class ModeloAnalytics
{
	/**
	 * Lee caché de métricas GA4 en disco.
	 */
	public static function mdlLeerCache($key, $ttl)
	{
		$dir = __DIR__ . '/../storage/cache';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		$file = $dir . '/ga4_' . preg_replace('/[^a-z0-9_-]/i', '', $key) . '.json';
		if (!is_file($file)) {
			return null;
		}
		if (filemtime($file) + intval($ttl) < time()) {
			return null;
		}
		$raw = @file_get_contents($file);
		if ($raw === false) {
			return null;
		}
		$data = json_decode($raw, true);
		return is_array($data) ? $data : null;
	}

	public static function mdlGuardarCache($key, $data)
	{
		$dir = __DIR__ . '/../storage/cache';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		$file = $dir . '/ga4_' . preg_replace('/[^a-z0-9_-]/i', '', $key) . '.json';
		@file_put_contents($file, json_encode($data));
	}

	/**
	 * Consulta GA4 Data API v1 (requiere ext-curl y credenciales válidas).
	 */
	public static function mdlConsultarGA4($propertyId, $credentialsPath)
	{
		if (!is_file($credentialsPath)) {
			return null;
		}

		$creds = json_decode(file_get_contents($credentialsPath), true);
		if (!is_array($creds) || empty($creds['client_email']) || empty($creds['private_key'])) {
			return null;
		}

		$token = self::obtenerAccessToken($creds);
		if (!$token) {
			return null;
		}

		$body = array(
			'dateRanges' => array(array('startDate' => '30daysAgo', 'endDate' => 'today')),
			'metrics'    => array(
				array('name' => 'sessions'),
				array('name' => 'totalUsers'),
				array('name' => 'screenPageViews'),
				array('name' => 'bounceRate'),
			),
			'dimensions' => array(
				array('name' => 'pageTitle'),
			),
			'orderBys'   => array(
				array('metric' => array('metricName' => 'screenPageViews'), 'desc' => true),
			),
			'limit'      => 10,
		);

		$url = 'https://analyticsdata.googleapis.com/v1beta/properties/' . rawurlencode($propertyId) . ':runReport';
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json',
			),
			CURLOPT_POSTFIELDS     => json_encode($body),
			CURLOPT_TIMEOUT        => 15,
		));
		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($code !== 200 || !$response) {
			return null;
		}

		return json_decode($response, true);
	}

	private static function obtenerAccessToken($creds)
	{
		$now = time();
		$header = self::base64url(json_encode(array('alg' => 'RS256', 'typ' => 'JWT')));
		$claim = self::base64url(json_encode(array(
			'iss'   => $creds['client_email'],
			'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
			'aud'   => 'https://oauth2.googleapis.com/token',
			'iat'   => $now,
			'exp'   => $now + 3600,
		)));
		$unsigned = $header . '.' . $claim;
		$sig = '';
		$key = openssl_pkey_get_private($creds['private_key']);
		if (!$key) {
			return null;
		}
		openssl_sign($unsigned, $sig, $key, OPENSSL_ALGO_SHA256);
		$jwt = $unsigned . '.' . self::base64url($sig);

		$ch = curl_init('https://oauth2.googleapis.com/token');
		curl_setopt_array($ch, array(
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => http_build_query(array(
				'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
				'assertion'  => $jwt,
			)),
			CURLOPT_TIMEOUT        => 10,
		));
		$res = curl_exec($ch);
		curl_close($ch);
		if (!$res) {
			return null;
		}
		$data = json_decode($res, true);
		return isset($data['access_token']) ? $data['access_token'] : null;
	}

	private static function base64url($data)
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
}
