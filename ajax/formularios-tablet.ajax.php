<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Content-Type-Options: nosniff");

require_once "../modelos/conexion.php";
require_once "../modelos/conexionWordpress.php";
require_once "../modelos/observacionOrdenes.modelo.php";

function tabletJsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function tabletStrLength(string $value): int
{
    return function_exists("mb_strlen") ? mb_strlen($value, "UTF-8") : strlen($value);
}

function tabletSubstr(string $value, int $length): string
{
    return function_exists("mb_substr") ? mb_substr($value, 0, $length, "UTF-8") : substr($value, 0, $length);
}

function tabletNormalizeText($value, int $maxLength = 500, bool $allowNewLines = false): string
{
    $value = is_scalar($value) ? (string) $value : "";
    $value = strip_tags($value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
    $value = str_replace(["\r\n", "\r"], "\n", $value);

    if ($allowNewLines) {
        $value = preg_replace("/[ \t]+/u", " ", $value);
        $value = preg_replace("/\n{3,}/u", "\n\n", $value);
    } else {
        $value = preg_replace("/\s+/u", " ", $value);
    }

    $value = trim($value);

    if (tabletStrLength($value) > $maxLength) {
        $value = tabletSubstr($value, $maxLength);
    }

    return trim($value);
}

function tabletPostedString(string $key, int $maxLength = 500): string
{
    return tabletNormalizeText($_POST[$key] ?? "", $maxLength, false);
}

function tabletValidateAjaxRequest(): void
{
    if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
        tabletJsonResponse(["error" => "Método no permitido."], 405);
    }

    $requestedWith = $_SERVER["HTTP_X_REQUESTED_WITH"] ?? "";
    if (strcasecmp($requestedWith, "XMLHttpRequest") !== 0) {
        tabletJsonResponse(["error" => "Solicitud inválida."], 400);
    }

    $serverHost = strtolower((string) ($_SERVER["HTTP_HOST"] ?? ""));
    $origin = (string) ($_SERVER["HTTP_ORIGIN"] ?? "");
    $referer = (string) ($_SERVER["HTTP_REFERER"] ?? "");

    foreach ([$origin, $referer] as $headerUrl) {
        if ($headerUrl === "") {
            continue;
        }

        $headerHost = strtolower((string) parse_url($headerUrl, PHP_URL_HOST));
        if ($headerHost !== "" && $serverHost !== "" && $headerHost !== $serverHost) {
            tabletJsonResponse(["error" => "Origen no permitido."], 403);
        }
    }
}

function tabletRequireCsrfToken(): void
{
    $requestToken = (string) ($_POST["csrfToken"] ?? "");
    $sessionToken = (string) ($_SESSION["formularios_tablet_csrf"] ?? "");

    if ($requestToken === "" || $sessionToken === "" || !hash_equals($sessionToken, $requestToken)) {
        tabletJsonResponse(["error" => "La sesión del formulario ya no es válida. Recargue la página."], 403);
    }
}

function tabletValidateBotGuard(bool $requireElapsed = false): void
{
    if (tabletPostedString("tabletGuard", 50) !== "") {
        tabletJsonResponse(["error" => "Solicitud bloqueada."], 403);
    }

    if ($requireElapsed) {
        $elapsedMs = isset($_POST["elapsedMs"]) ? (int) $_POST["elapsedMs"] : 0;
        if ($elapsedMs < 3000) {
            tabletJsonResponse(["error" => "Tiempo de captura insuficiente. Intente nuevamente."], 429);
        }
    }
}

function tabletRateLimit(string $bucket, int $limit, int $windowSeconds): void
{
    $now = time();
    if (!isset($_SESSION["formularios_tablet_rate_limit"])) {
        $_SESSION["formularios_tablet_rate_limit"] = [];
    }

    $state = $_SESSION["formularios_tablet_rate_limit"][$bucket] ?? null;
    if (!is_array($state) || !isset($state["reset_at"]) || $state["reset_at"] <= $now) {
        $state = [
            "count" => 0,
            "reset_at" => $now + $windowSeconds
        ];
    }

    $state["count"]++;
    $_SESSION["formularios_tablet_rate_limit"][$bucket] = $state;

    if ($state["count"] > $limit) {
        tabletJsonResponse(["error" => "Demasiadas solicitudes. Espere un momento e intente de nuevo."], 429);
    }
}

function tabletOrderStateMap(): array
{
    return [
        "REV" => [
            "like" => "%REV%",
            "label" => "INGRESO DE EQUIPO"
        ],
        "Entregado" => [
            "like" => "%Entregado%",
            "label" => "SALIDA DE EQUIPO"
        ]
    ];
}

function tabletBuildOrderPayload(array $orden): array
{
    return [
        "id" => isset($orden["id"]) ? (int) $orden["id"] : 0,
        "id_usuario" => isset($orden["id_usuario"]) ? (int) $orden["id_usuario"] : 0,
        "marcaDelEquipo" => tabletNormalizeText($orden["marcaDelEquipo"] ?? "", 120),
        "modeloDelEquipo" => tabletNormalizeText($orden["modeloDelEquipo"] ?? "", 120),
        "numeroDeSerieDelEquipo" => tabletNormalizeText($orden["numeroDeSerieDelEquipo"] ?? "", 120),
        "nombre_cliente" => tabletNormalizeText($orden["nombre_cliente"] ?? "Desconocido", 140),
        "fecha" => !empty($orden["fecha"]) ? date("d/m/Y", strtotime((string) $orden["fecha"])) : ""
    ];
}

function tabletFindLatestOrderByFlow(string $flow): ?array
{
    $stateMap = tabletOrderStateMap();
    if (!isset($stateMap[$flow])) {
        tabletJsonResponse(["error" => "Estado solicitado no permitido."], 400);
    }

    $pdoWP = ConexionWP::conectarWP();
    $stmt = $pdoWP->prepare(
        "SELECT id, id_usuario, marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo, fecha
         FROM ordenes
         WHERE estado LIKE :estado
         ORDER BY id DESC
         LIMIT 1"
    );
    $stmt->bindValue(":estado", $stateMap[$flow]["like"], PDO::PARAM_STR);
    $stmt->execute();

    $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$orden) {
        return null;
    }

    $pdo = Conexion::conectar();
    $stmtCliente = $pdo->prepare("SELECT nombre FROM clientesTienda WHERE id = :id LIMIT 1");
    $stmtCliente->bindValue(":id", (int) ($orden["id_usuario"] ?? 0), PDO::PARAM_INT);
    $stmtCliente->execute();
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    $orden["nombre_cliente"] = $cliente["nombre"] ?? "Desconocido";

    return tabletBuildOrderPayload($orden);
}

function tabletFindOrderById(int $orderId): ?array
{
    $pdoWP = ConexionWP::conectarWP();
    $stmt = $pdoWP->prepare(
        "SELECT id, id_usuario, estado, marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo, fecha
         FROM ordenes
         WHERE id = :id
         LIMIT 1"
    );
    $stmt->bindValue(":id", $orderId, PDO::PARAM_INT);
    $stmt->execute();

    $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$orden) {
        return null;
    }

    $pdo = Conexion::conectar();
    $stmtCliente = $pdo->prepare("SELECT nombre FROM clientesTienda WHERE id = :id LIMIT 1");
    $stmtCliente->bindValue(":id", (int) ($orden["id_usuario"] ?? 0), PDO::PARAM_INT);
    $stmtCliente->execute();
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    $orden["nombre_cliente"] = $cliente["nombre"] ?? "Desconocido";

    return $orden;
}

function tabletValueFromKeys(array $source, array $keys)
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $source)) {
            return $source[$key];
        }
    }

    return null;
}

function tabletSanitizeIngresoAnswers(array $answers): array
{
    $urgencia = tabletNormalizeText((string) tabletValueFromKeys($answers, ["urgencia", "Urgencia"]), 30);
    $urgenciasPermitidas = ["Hoy", "1-2 dias", "+ de 2 dias"];

    if (!in_array($urgencia, $urgenciasPermitidas, true)) {
        tabletJsonResponse(["error" => "La urgencia seleccionada no es válida."], 422);
    }

    $problema = tabletNormalizeText((string) tabletValueFromKeys($answers, ["problema_o_falla", "Problema_o_falla"]), 500, true);
    if ($problema === "") {
        tabletJsonResponse(["error" => "Debe capturar la falla o problema del equipo."], 422);
    }

    return [
        "Problema_o_falla" => $problema,
        "Contrasena" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["contrasena", "Contraseña", "ContraseÃ±a"]), 180, false),
        "Respaldo_Info" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["respaldo_info", "Respaldo_Info"]), 300, true),
        "Sistema_Operativo" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["sistema_operativo", "Sistema_Operativo"]), 180, true),
        "Info_Adicional" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["info_adicional", "Info_Adicional"]), 300, true),
        "Urgencia" => $urgencia
    ];
}

function tabletSanitizeSalidaAnswers(array $answers): array
{
    $yesNo = ["Si", "No"];
    $calificaciones = ["Bueno", "Regular", "Malo"];

    $map = [
        "Explicaron_reparacion" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["explicaron_reparacion", "Explicaron_reparacion"]), 20),
        "Mostro_funcionamiento" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["mostro_funcionamiento", "Mostro_funcionamiento"]), 20),
        "Explicaron_garantia" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["explicaron_garantia", "Explicaron_garantia"]), 20),
        "Entregaron_limpio" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["entregaron_limpio", "Entregaron_limpio"]), 20),
        "Calificacion_servicio" => tabletNormalizeText((string) tabletValueFromKeys($answers, ["calificacion_servicio", "Calificacion_servicio"]), 20)
    ];

    foreach (["Explicaron_reparacion", "Mostro_funcionamiento", "Explicaron_garantia", "Entregaron_limpio"] as $key) {
        if (!in_array($map[$key], $yesNo, true)) {
            tabletJsonResponse(["error" => "Hay respuestas de salida con formato inválido."], 422);
        }
    }

    if (!in_array($map["Calificacion_servicio"], $calificaciones, true)) {
        tabletJsonResponse(["error" => "La calificación del servicio no es válida."], 422);
    }

    return $map;
}

function tabletValidateSignature(string $signature): string
{
    $signature = trim($signature);

    if ($signature === "" || !preg_match('#^data:image/png;base64,[A-Za-z0-9+/=]+$#', $signature)) {
        tabletJsonResponse(["error" => "La firma digital no tiene un formato válido."], 422);
    }

    if (strlen($signature) > 800000) {
        tabletJsonResponse(["error" => "La firma digital excede el tamaño permitido."], 422);
    }

    return $signature;
}

function tabletValidateSignatureMeta($meta): array
{
    if (!is_array($meta)) {
        tabletJsonResponse(["error" => "La firma digital no contiene datos suficientes para validarse."], 422);
    }

    $strokeCount = isset($meta["strokeCount"]) ? (int) $meta["strokeCount"] : 0;
    $pointCount = isset($meta["pointCount"]) ? (int) $meta["pointCount"] : 0;
    $width = isset($meta["width"]) ? (int) $meta["width"] : 0;
    $height = isset($meta["height"]) ? (int) $meta["height"] : 0;

    if ($strokeCount < 1 || $pointCount < 18) {
        tabletJsonResponse(["error" => "La firma es demasiado simple. Debe dibujar un trazo más completo."], 422);
    }

    if ($width < 36 && $height < 18) {
        tabletJsonResponse(["error" => "La firma es demasiado pequeña para ser válida."], 422);
    }

    if (($width * $height) < 900) {
        tabletJsonResponse(["error" => "La firma es demasiado corta o pequeña. Intente firmar nuevamente."], 422);
    }

    return [
        "strokeCount" => $strokeCount,
        "pointCount" => $pointCount,
        "width" => $width,
        "height" => $height
    ];
}

function tabletPendingOrderOrFail(int $orderId, string $tipoFormulario): array
{
    $pending = $_SESSION["formularios_tablet_pending_order"] ?? null;
    if (!is_array($pending)) {
        tabletJsonResponse(["error" => "No hay una orden pendiente para este formulario. Reinicie el flujo."], 409);
    }

    $stateMap = tabletOrderStateMap();
    $expectedFlow = (string) ($pending["flow"] ?? "");
    $expectedOrderId = isset($pending["id"]) ? (int) $pending["id"] : 0;
    $expectedLabel = $stateMap[$expectedFlow]["label"] ?? "";
    $createdAt = isset($pending["created_at"]) ? (int) $pending["created_at"] : 0;

    if ($createdAt > 0 && (time() - $createdAt) > 1800) {
        unset($_SESSION["formularios_tablet_pending_order"]);
        tabletJsonResponse(["error" => "La sesión del formulario expiró. Vuelva a iniciar el flujo."], 409);
    }

    if ($expectedOrderId !== $orderId || $expectedLabel !== $tipoFormulario) {
        tabletJsonResponse(["error" => "La orden o el tipo de formulario ya no coinciden con la sesión actual."], 409);
    }

    return $pending;
}

function tabletFindExistingFormObservationId(int $orderId, string $tipoFormulario): int
{
    $pdo = Conexion::conectar();
    $stmt = $pdo->prepare(
        "SELECT id, observacion
         FROM observacionesOrdenes
         WHERE id_orden = :id_orden
           AND observacion LIKE :prefix
         ORDER BY id DESC
         LIMIT 50"
    );
    $stmt->bindValue(":id_orden", $orderId, PDO::PARAM_INT);
    $stmt->bindValue(":prefix", "FORMULARIO_TABLET_JSON:%", PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $observacion = (string) ($row["observacion"] ?? "");
        if (strpos($observacion, "FORMULARIO_TABLET_JSON: ") !== 0) {
            continue;
        }

        $payload = json_decode(substr($observacion, 24), true);
        if (!is_array($payload)) {
            continue;
        }

        $savedType = tabletNormalizeText($payload["tipo_formulario"] ?? "", 40);
        if ($savedType === $tipoFormulario) {
            return (int) ($row["id"] ?? 0);
        }
    }

    return 0;
}

function tabletHandleFetchOrder(): void
{
    tabletRequireCsrfToken();
    tabletValidateBotGuard(false);
    tabletRateLimit("fetch_order", 12, 60);

    $flow = tabletPostedString("estado", 20);
    $orden = tabletFindLatestOrderByFlow($flow);

    if (!$orden) {
        unset($_SESSION["formularios_tablet_pending_order"]);
        tabletJsonResponse([]);
    }

    $_SESSION["formularios_tablet_pending_order"] = [
        "id" => (int) $orden["id"],
        "flow" => $flow,
        "created_at" => time()
    ];

    tabletJsonResponse($orden);
}

function tabletHandleSaveForm(): void
{
    tabletRequireCsrfToken();
    tabletValidateBotGuard(true);
    tabletRateLimit("save_form", 6, 600);

    $orderId = isset($_POST["idOrden"]) ? (int) $_POST["idOrden"] : 0;
    $creatorId = isset($_POST["idCreador"]) ? max(1, (int) $_POST["idCreador"]) : 1;
    $formDataRaw = (string) ($_POST["formData"] ?? "");

    if ($orderId <= 0 || $formDataRaw === "") {
        tabletJsonResponse(["error" => "Faltan datos obligatorios para guardar el formulario."], 422);
    }

    $decoded = json_decode($formDataRaw, true);
    if (!is_array($decoded)) {
        tabletJsonResponse(["error" => "El formato de los datos enviados no es válido."], 422);
    }

    $tipoFormulario = tabletNormalizeText($decoded["tipo_formulario"] ?? "", 40);
    if (!in_array($tipoFormulario, ["INGRESO DE EQUIPO", "SALIDA DE EQUIPO"], true)) {
        tabletJsonResponse(["error" => "Tipo de formulario no permitido."], 422);
    }

    $pendingOrder = tabletPendingOrderOrFail($orderId, $tipoFormulario);

    $orden = tabletFindOrderById($orderId);
    if (!$orden) {
        tabletJsonResponse(["error" => "La orden ya no existe o no se puede consultar."], 404);
    }

    $expectedFlow = (string) ($pendingOrder["flow"] ?? "");
    if ($expectedFlow !== "" && stripos((string) ($orden["estado"] ?? ""), $expectedFlow) === false) {
        tabletJsonResponse(["error" => "La orden cambió de estado y ya no corresponde a este formulario."], 409);
    }

    $respuestas = isset($decoded["respuestas"]) && is_array($decoded["respuestas"]) ? $decoded["respuestas"] : [];
    $firma = tabletValidateSignature((string) ($decoded["firma"] ?? ""));
    $firmaMeta = tabletValidateSignatureMeta($decoded["firma_meta"] ?? null);

    $respuestasLimpias = $tipoFormulario === "INGRESO DE EQUIPO"
        ? tabletSanitizeIngresoAnswers($respuestas)
        : tabletSanitizeSalidaAnswers($respuestas);

    $payload = [
        "tipo_formulario" => $tipoFormulario,
        "nombre_cliente" => tabletNormalizeText($orden["nombre_cliente"] ?? "Desconocido", 140),
        "orden_id" => $orderId,
        "marcaModelo" => trim(
            tabletNormalizeText(($orden["marcaDelEquipo"] ?? "") . " " . ($orden["modeloDelEquipo"] ?? ""), 160)
        ),
        "respuestas" => $respuestasLimpias,
        "firma" => $firma,
        "firma_meta" => $firmaMeta
    ];

    $existingObservationId = tabletFindExistingFormObservationId($orderId, $tipoFormulario);
    if ($existingObservationId > 0) {
        unset($_SESSION["formularios_tablet_pending_order"]);
        tabletJsonResponse([
            "status" => "ok",
            "duplicate" => true,
            "message" => "Este formulario ya estaba registrado para la orden #" . $orderId . "."
        ]);
    }

    $observacion = "FORMULARIO_TABLET_JSON: " . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $datos = [
        "id_creador" => $creatorId,
        "id_orden" => $orderId,
        "observacion" => $observacion
    ];

    try {
        $respuesta = ModeloObservaciones::mdlCrearObservacion("observacionesOrdenes", $datos);
        unset($_SESSION["formularios_tablet_pending_order"]);

        if ($respuesta !== "ok") {
            tabletJsonResponse(["error" => "No fue posible guardar la observación del formulario."], 500);
        }

        tabletJsonResponse(["status" => "ok"]);
    } catch (Exception $e) {
        tabletJsonResponse(["error" => "Error al guardar observación: " . $e->getMessage()], 500);
    }
}

tabletValidateAjaxRequest();

try {
    if (isset($_POST["estado"])) {
        tabletHandleFetchOrder();
    }

    if (isset($_POST["guardarFormulario"])) {
        tabletHandleSaveForm();
    }

    tabletJsonResponse(["error" => "Acción no reconocida."], 400);
} catch (Exception $e) {
    tabletJsonResponse(["error" => "Error interno: " . $e->getMessage()], 500);
}
