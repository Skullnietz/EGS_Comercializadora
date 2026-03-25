<?php
/**
 * ir.php — Redirect limpio hacia URLs externas sin enviar Referer ni Origin.
 * Uso: ir.php?a=https://myaccount.mercadolibre.com.mx/...
 */

$url = $_GET['a'] ?? '';

// Solo permitir dominios de MercadoLibre
$permitidos = [
    'myaccount.mercadolibre.com.mx',
    'myorders.mercadolibre.com.mx',
    'www.mercadolibre.com.mx',
    'articulo.mercadolibre.com.mx',
    'www.mercadolibre.com',
];

$host = parse_url($url, PHP_URL_HOST);

if (!$url || !$host || !in_array($host, $permitidos)) {
    http_response_code(400);
    exit('URL no permitida.');
}

// Quitar cualquier Referer y Origin que pudiera ir en headers
header('Referrer-Policy: no-referrer');
header('Location: ' . $url, true, 302);
exit;
