<?php
/**
 * webhook/mercadolibre-oauth.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Redirect URI para el flujo OAuth 2.0 de MercadoLibre.
 *
 * Registra esta URL en:
 *   developers.mercadolibre.com.mx  →  Tu App  →  Redirect URIs
 *   https://TUDOMINIO.COM/webhook/mercadolibre-oauth.php
 *
 * Flujo:
 *   1. El usuario pulsa "Conectar con MercadoLibre" en el modal de pedidos.
 *   2. Se le redirige a ML para que autorice la app.
 *   3. ML redirige aquí con ?code=TG-xxxxxxxx  (o ?error=...)
 *   4. Este script canjea el code por access_token + refresh_token.
 *   5. Guarda los tokens en config/mercadolibre.config.php.
 *   6. Redirige de vuelta a index.php?ruta=pedidos con un mensaje de estado.
 * ─────────────────────────────────────────────────────────────────────────────
 */

session_start();

$root       = realpath(__DIR__ . '/..');
$configPath = $root . '/config/mercadolibre.config.php';
$config     = file_exists($configPath) ? (require $configPath) : [];
$pedidosUrl = '../index.php?ruta=pedidos';

/* ── Verificar que el usuario tenga sesión activa ───────────────────────── */
if (!isset($_SESSION['perfil'])) {
    header('Location: ' . $pedidosUrl . '&ml_status=no_session');
    exit;
}

/* ── Error devuelto por ML ──────────────────────────────────────────────── */
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    header("Location: {$pedidosUrl}&ml_status=error&ml_msg=" . urlencode($error));
    exit;
}

/* ── Sin código de autorización ─────────────────────────────────────────── */
if (empty($_GET['code'])) {
    header("Location: {$pedidosUrl}&ml_status=no_code");
    exit;
}

/* ── Verificar state (CSRF) ─────────────────────────────────────────────── */
$stateRecibido = $_GET['state'] ?? '';
$stateEsperado = $_SESSION['ml_oauth_state'] ?? '';

if ($stateRecibido !== $stateEsperado || empty($stateEsperado)) {
    header("Location: {$pedidosUrl}&ml_status=csrf_error");
    exit;
}
unset($_SESSION['ml_oauth_state']);

/* ── Necesitamos client_id y client_secret ──────────────────────────────── */
if (empty($config['client_id']) || empty($config['client_secret'])) {
    header("Location: {$pedidosUrl}&ml_status=no_credentials");
    exit;
}

/* ── Construir la Redirect URI (debe ser exacta a la registrada en ML) ──── */
// SCRIPT_NAME = /EGS_Comercializadora/webhook/mercadolibre-oauth.php
$protocol    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host        = $_SERVER['HTTP_HOST'];
$redirectUri = $protocol . '://' . $host . $_SERVER['SCRIPT_NAME'];

/* ── Canjear code por tokens ─────────────────────────────────────────────── */
$ch = curl_init('https://api.mercadolibre.com/oauth/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'grant_type'   => 'authorization_code',
        'client_id'    => $config['client_id'],
        'client_secret'=> $config['client_secret'],
        'code'         => $_GET['code'],
        'redirect_uri' => $redirectUri,
    ]),
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
    ],
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$response = json_decode(curl_exec($ch), true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || empty($response['access_token'])) {
    $mlError = $response['message'] ?? $response['error'] ?? 'unknown';
    header("Location: {$pedidosUrl}&ml_status=token_error&ml_msg=" . urlencode($mlError));
    exit;
}

/* ── Obtener el user_id del token ────────────────────────────────────────── */
$userId = $response['user_id'] ?? '';

if (empty($userId)) {
    // Consultamos /users/me para obtener el ID
    $ch2 = curl_init('https://api.mercadolibre.com/users/me');
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $response['access_token']],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $meData = json_decode(curl_exec($ch2), true);
    curl_close($ch2);
    $userId = $meData['id'] ?? '';
}

/* ── Guardar tokens en config ────────────────────────────────────────────── */
$newConfig = array_merge($config, [
    'access_token'  => $response['access_token'],
    'refresh_token' => $response['refresh_token'] ?? ($config['refresh_token'] ?? ''),
    'seller_id'     => (string) $userId,
    'site_id'       => $response['scope'] ? 'MLM' : ($config['site_id'] ?? 'MLM'),
]);

require_once $root . '/config/env.php';

file_put_contents(
    $configPath,
    "<?php\nreturn " . var_export($newConfig, true) . ";\n"
);

/* ── Redirigir con éxito ─────────────────────────────────────────────────── */
header("Location: {$pedidosUrl}&ml_status=success&ml_user=" . urlencode($userId));
exit;
