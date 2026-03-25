<?php
/**
 * webhook/mercadolibre.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Endpoint público para notificaciones (Webhooks) de MercadoLibre.
 *
 * URL que debes registrar en:
 *   https://developers.mercadolibre.com.mx  →  Tu App  →  Notificaciones
 *   URL de notificación: https://TUDOMINIO.COM/webhook/mercadolibre.php
 *
 * Tópicos recomendados: orders_v2, payments
 *
 * Flujo:
 *   1. ML hace GET para verificar la URL  → respondemos 200.
 *   2. ML hace POST con la notificación   → validamos firma, guardamos en BD,
 *      consultamos la orden en la API y actualizamos la caché.
 * ─────────────────────────────────────────────────────────────────────────────
 */

/* ── 0. Respuesta inmediata (ML espera < 5 s) ───────────────────────────── */
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');

/* ── 1. Verificación GET (ML comprueba que la URL existe) ───────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status' => 'ok', 'service' => 'EGS-ML-Webhook']);
    exit;
}

/* ── 2. Solo aceptamos POST a partir de aquí ────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

/* ── 3. Cargar configuración y dependencias ─────────────────────────────── */
$root       = realpath(__DIR__ . '/..');
$configPath = $root . '/config/mercadolibre.config.php';
$config     = file_exists($configPath) ? (require $configPath) : [];

// Necesitamos Database para guardar en BD
require_once $root . '/config/env.php';
require_once $root . '/config/global.php';
require_once $root . '/config/Database.php';

/* ── 4. Leer el cuerpo de la notificación ───────────────────────────────── */
$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (empty($payload) || !is_array($payload)) {
    // Cuerpo inválido — igual respondemos 200 para que ML no reintente
    mlLog('WARN', 'Payload vacío o inválido', $rawBody);
    exit;
}

/* ── 5. Validar firma HMAC-SHA256 (x-signature) ─────────────────────────── */
// Solo si tenemos client_secret configurado
if (!empty($config['client_secret'])) {
    $xSignature = $_SERVER['HTTP_X_SIGNATURE']  ?? '';
    $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

    if (!empty($xSignature)) {
        // Extraer ts y v1 del header: "ts=1700000000,v1=abc123..."
        $ts = '';
        $v1 = '';
        foreach (explode(',', $xSignature) as $part) {
            [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($k === 'ts') $ts = $v;
            if ($k === 'v1') $v1 = $v;
        }

        $notifId  = $payload['_id'] ?? '';
        $manifest = "id:{$notifId};request-id:{$xRequestId};ts:{$ts}";
        $expected = hash_hmac('sha256', $manifest, $config['client_secret']);

        if (!hash_equals($expected, $v1)) {
            mlLog('ERROR', 'Firma inválida', [
                'manifest' => $manifest,
                'received' => $v1,
            ]);
            http_response_code(401);
            exit;
        }
    }
}

/* ── 6. Guardar notificación en BD y procesar ───────────────────────────── */
$topic    = $payload['topic']    ?? $payload['type'] ?? '';
$resource = $payload['resource'] ?? '';
$userId   = $payload['user_id']  ?? '';

mlLog('INFO', "Notificación recibida: topic={$topic}", $payload);

// Guardar en tabla de log
mlGuardarNotificacion($topic, $resource, $userId, $rawBody);

// Procesar órdenes de compra
if (in_array($topic, ['orders_v2', 'orders', 'payments'])) {
    mlProcesarOrden($resource, $config, $configPath);
}

echo json_encode(['status' => 'received']);
exit;

/* ══════════════════════════════════════════════════════════════════════════ */
/*  HELPERS                                                                   */
/* ══════════════════════════════════════════════════════════════════════════ */

/**
 * Guarda la notificación en la tabla ml_webhook_log.
 */
function mlGuardarNotificacion($topic, $resource, $userId, $rawBody)
{
    try {
        $pdo  = Database::conectar(Database::SISTEMA);

        // Crear tabla si no existe (primera vez)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS ml_webhook_log (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                topic       VARCHAR(50)  NOT NULL,
                resource    VARCHAR(255) NOT NULL,
                ml_user_id  VARCHAR(50)  DEFAULT NULL,
                payload     TEXT         NOT NULL,
                procesado   TINYINT(1)   NOT NULL DEFAULT 0,
                created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_topic (topic),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $stmt = $pdo->prepare("
            INSERT INTO ml_webhook_log (topic, resource, ml_user_id, payload)
            VALUES (:topic, :resource, :user_id, :payload)
        ");
        $stmt->execute([
            ':topic'    => $topic,
            ':resource' => $resource,
            ':user_id'  => $userId,
            ':payload'  => $rawBody,
        ]);
    } catch (Exception $e) {
        mlLog('DB_ERROR', $e->getMessage());
    }
}

/**
 * Consulta la orden en la API de ML y la guarda/actualiza en ml_ordenes_cache.
 */
function mlProcesarOrden($resource, $config, $configPath)
{
    if (empty($config['access_token'])) {
        mlLog('WARN', 'Sin access_token para procesar orden');
        return;
    }

    // El resource puede ser "/orders/12345678" o "/payments/987654"
    // Extraemos el ID numérico
    preg_match('/(\d+)$/', $resource, $m);
    $resourceId = $m[1] ?? '';
    if (!$resourceId) {
        mlLog('WARN', 'No se pudo extraer ID del resource: ' . $resource);
        return;
    }

    // Si es un pago, obtenemos la orden asociada
    if (strpos($resource, '/payments/') !== false) {
        $url    = "https://api.mercadolibre.com/collections/{$resourceId}";
        $result = mlGet($url, $config['access_token']);

        if ($result['code'] === 401) {
            $newToken = mlRefreshToken($config, $configPath);
            if ($newToken) {
                $result = mlGet($url, $newToken);
            }
        }

        $orderId = $result['body']['collection']['order_id'] ?? '';
        if (!$orderId) {
            mlLog('WARN', 'No se encontró order_id para el payment ' . $resourceId);
            return;
        }
        $resourceId = $orderId;
    }

    // Consultar la orden
    $url    = "https://api.mercadolibre.com/orders/{$resourceId}";
    $result = mlGet($url, $config['access_token']);

    if ($result['code'] === 401) {
        $newToken = mlRefreshToken($config, $configPath);
        if ($newToken) {
            $result = mlGet($url, $newToken);
        }
    }

    if ($result['code'] !== 200 || empty($result['body']['id'])) {
        mlLog('WARN', "No se pudo obtener la orden {$resourceId}. HTTP {$result['code']}");
        return;
    }

    mlGuardarOrdenCache($result['body'], $configPath);
}

/**
 * Guarda o actualiza la orden en la tabla ml_ordenes_cache.
 */
function mlGuardarOrdenCache($orden, $configPath)
{
    try {
        $pdo = Database::conectar(Database::SISTEMA);

        // Crear tabla caché si no existe
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS ml_ordenes_cache (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ml_order_id     BIGINT UNSIGNED NOT NULL UNIQUE,
                status          VARCHAR(50)  DEFAULT NULL,
                total_amount    DECIMAL(12,2) DEFAULT NULL,
                currency_id     VARCHAR(10)  DEFAULT NULL,
                date_created    DATETIME     DEFAULT NULL,
                date_closed     DATETIME     DEFAULT NULL,
                buyer_id        BIGINT       DEFAULT NULL,
                buyer_nickname  VARCHAR(120) DEFAULT NULL,
                seller_id       BIGINT       DEFAULT NULL,
                seller_nickname VARCHAR(120) DEFAULT NULL,
                items_json      TEXT         DEFAULT NULL,
                payload_json    LONGTEXT     DEFAULT NULL,
                updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status     (status),
                INDEX idx_date       (date_created),
                INDEX idx_buyer      (buyer_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $items   = $orden['order_items'] ?? [];
        $buyer   = $orden['buyer']  ?? [];
        $seller  = $orden['seller'] ?? [];

        $stmt = $pdo->prepare("
            INSERT INTO ml_ordenes_cache
                (ml_order_id, status, total_amount, currency_id,
                 date_created, date_closed,
                 buyer_id, buyer_nickname,
                 seller_id, seller_nickname,
                 items_json, payload_json)
            VALUES
                (:ml_order_id, :status, :total_amount, :currency_id,
                 :date_created, :date_closed,
                 :buyer_id, :buyer_nickname,
                 :seller_id, :seller_nickname,
                 :items_json, :payload_json)
            ON DUPLICATE KEY UPDATE
                status          = VALUES(status),
                total_amount    = VALUES(total_amount),
                date_closed     = VALUES(date_closed),
                buyer_id        = VALUES(buyer_id),
                buyer_nickname  = VALUES(buyer_nickname),
                seller_id       = VALUES(seller_id),
                seller_nickname = VALUES(seller_nickname),
                items_json      = VALUES(items_json),
                payload_json    = VALUES(payload_json),
                updated_at      = NOW()
        ");

        $stmt->execute([
            ':ml_order_id'     => $orden['id'],
            ':status'          => $orden['status'] ?? '',
            ':total_amount'    => $orden['total_amount'] ?? 0,
            ':currency_id'     => $orden['currency_id'] ?? 'MXN',
            ':date_created'    => isset($orden['date_created'])
                                    ? date('Y-m-d H:i:s', strtotime($orden['date_created']))
                                    : null,
            ':date_closed'     => isset($orden['date_closed'])
                                    ? date('Y-m-d H:i:s', strtotime($orden['date_closed']))
                                    : null,
            ':buyer_id'        => $buyer['id']       ?? null,
            ':buyer_nickname'  => $buyer['nickname']  ?? '',
            ':seller_id'       => $seller['id']       ?? null,
            ':seller_nickname' => $seller['nickname'] ?? '',
            ':items_json'      => json_encode($items, JSON_UNESCAPED_UNICODE),
            ':payload_json'    => json_encode($orden, JSON_UNESCAPED_UNICODE),
        ]);

        mlLog('INFO', "Orden {$orden['id']} guardada/actualizada en caché.");

    } catch (Exception $e) {
        mlLog('DB_ERROR', $e->getMessage());
    }
}

/**
 * GET a la API de ML.
 */
function mlGet($url, $token)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $err ? 0 : $code,
        'body' => json_decode($body, true) ?? [],
    ];
}

/**
 * Renueva el access_token con el refresh_token.
 */
function mlRefreshToken(&$config, $configPath)
{
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
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_TIMEOUT => 10,
    ]);
    $data = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!empty($data['access_token'])) {
        $config['access_token']  = $data['access_token'];
        $config['refresh_token'] = $data['refresh_token'] ?? $config['refresh_token'];

        // Guardar nuevo token
        file_put_contents(
            realpath(dirname($configPath)) . '/' . basename($configPath),
            "<?php\nreturn " . var_export($config, true) . ";\n"
        );
        return $config['access_token'];
    }
    return false;
}

/**
 * Log simple a archivo.
 */
function mlLog($level, $msg, $data = null)
{
    $logPath = __DIR__ . '/ml_webhook.log';
    $line    = date('Y-m-d H:i:s') . " [{$level}] {$msg}";
    if ($data !== null) {
        $line .= ' | ' . (is_array($data) ? json_encode($data) : $data);
    }
    file_put_contents($logPath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}
