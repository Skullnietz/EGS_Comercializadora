<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

/*=============================================
  AUTH CHECK
=============================================*/
$perfilesPermitidos = ['administrador', 'vendedor', 'Super-Administrador', 'tecnico'];
if (!isset($_SESSION['perfil']) || !in_array($_SESSION['perfil'], $perfilesPermitidos)) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$configPath = realpath(__DIR__ . '/../config') . '/mercadolibre.config.php';
$config     = file_exists($configPath) ? (require $configPath) : [];

/*=============================================
  HELPERS
=============================================*/

/**
 * Realiza un GET a la API de MercadoLibre con el Bearer token indicado.
 */
function mlGet($url, $token) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['code' => 0, 'body' => ['error' => 'cURL error: ' . $err]];
    }
    return ['code' => $code, 'body' => json_decode($body, true)];
}

/**
 * Intenta renovar el access_token usando el refresh_token.
 * Si tiene éxito guarda el nuevo token en el archivo de config y retorna el nuevo token.
 * Retorna false si no se puede renovar.
 */
function mlRefreshToken(&$config, $configPath) {
    if (empty($config['client_id']) || empty($config['refresh_token'])) {
        return false;
    }

    $ch = curl_init('https://api.mercadolibre.com/oauth/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type'    => 'refresh_token',
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'refresh_token' => $config['refresh_token'],
        ]),
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $data = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!empty($data['access_token'])) {
        $config['access_token'] = $data['access_token'];
        if (!empty($data['refresh_token'])) {
            $config['refresh_token'] = $data['refresh_token'];
        }
        mlSaveConfig($config, $configPath);
        return $config['access_token'];
    }
    return false;
}

/**
 * Guarda el array de config como archivo PHP.
 */
function mlSaveConfig($config, $path) {
    $php = "<?php\nreturn " . var_export($config, true) . ";\n";
    return file_put_contents($path, $php) !== false;
}

/**
 * Ejecuta un GET a ML; si recibe 401 intenta renovar token y reintenta.
 */
function mlCallWithRefresh($url, &$config, $configPath) {
    $result = mlGet($url, $config['access_token']);
    if ($result['code'] === 401) {
        $newToken = mlRefreshToken($config, $configPath);
        if ($newToken) {
            $result = mlGet($url, $newToken);
        }
    }
    return $result;
}

/*=============================================
  ACCIONES
=============================================*/
$accion = $_POST['accion'] ?? '';

// ── Obtener lista de pedidos ──────────────────────────────────────────────
if ($accion === 'obtenerOrdenes') {

    if (empty($config['access_token']) || empty($config['seller_id'])) {
        echo json_encode([
            'error'   => 'config_missing',
            'message' => 'Configura el Access Token y el Seller ID de MercadoLibre.',
        ]);
        exit;
    }

    $offset = max(0, intval($_POST['offset'] ?? 0));
    $limit  = 50;
    $url    = "https://api.mercadolibre.com/orders/search"
            . "?buyer={$config['seller_id']}"
            . "&sort=date_desc"
            . "&offset={$offset}"
            . "&limit={$limit}";

    $result = mlCallWithRefresh($url, $config, $configPath);
    echo json_encode($result['body']);
    exit;
}

// ── Obtener detalle de una orden ──────────────────────────────────────────
if ($accion === 'obtenerOrden') {

    if (empty($config['access_token'])) {
        echo json_encode([
            'error'   => 'config_missing',
            'message' => 'Configura el Access Token de MercadoLibre.',
        ]);
        exit;
    }

    $orderId = intval($_POST['order_id'] ?? 0);
    if (!$orderId) {
        echo json_encode(['error' => 'ID de orden inválido.']);
        exit;
    }

    $url    = "https://api.mercadolibre.com/orders/{$orderId}";
    $result = mlCallWithRefresh($url, $config, $configPath);
    echo json_encode($result['body']);
    exit;
}

// ── Guardar configuración (solo administradores) ──────────────────────────
if ($accion === 'guardarConfig') {

    if (!in_array($_SESSION['perfil'], ['administrador', 'Super-Administrador', 'vendedor'])) {
        echo json_encode(['error' => 'No tienes permisos para cambiar esta configuración.']);
        exit;
    }

    $newCfg = [
        'access_token'  => trim($_POST['access_token']  ?? ''),
        'refresh_token' => trim($_POST['refresh_token'] ?? ''),
        'seller_id'     => trim($_POST['seller_id']     ?? ''),
        'client_id'     => trim($_POST['client_id']     ?? ''),
        'client_secret' => trim($_POST['client_secret'] ?? ''),
        'site_id'       => 'MLM',
    ];

    if (mlSaveConfig($newCfg, $configPath)) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['error' => 'No se pudo guardar. Verifica los permisos del archivo de configuración.']);
    }
    exit;
}

// ── Verificar si la configuración está activa ─────────────────────────────
if ($accion === 'verificarConfig') {

    $configurado = !empty($config['access_token']) && !empty($config['seller_id']);
    $datos = [
        'configurado'    => $configurado,
        'seller_id'      => $config['seller_id']     ?? '',
        'tiene_refresh'  => !empty($config['refresh_token']),
        'tiene_client'   => !empty($config['client_id']),
        'access_preview' => !empty($config['access_token'])
            ? substr($config['access_token'], 0, 8) . '...'
            : '',
    ];

    if (in_array($_SESSION['perfil'], ['administrador', 'Super-Administrador'])) {
        $datos['client_id']         = $config['client_id'] ?? '';
        $datos['client_secret_set'] = !empty($config['client_secret']);
    }

    echo json_encode($datos);
    exit;
}

echo json_encode(['error' => 'Acción no reconocida.']);
