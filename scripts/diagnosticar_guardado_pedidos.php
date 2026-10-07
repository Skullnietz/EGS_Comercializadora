<?php
// Ejecutar desde la terminal del hosting. No ofrece un endpoint público de diagnóstico.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../modelos/pedidos.diagnostico.php';
try {
    $resultado = PedidosDiagnostico::ejecutar(Database::conectar(Database::ECOMMERCE), Database::conectar(Database::WORDPRESS));
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit($resultado['correcto'] ? 0 : 1);
} catch (Throwable $e) {
    // No imprimir configuraciones, credenciales ni mensajes del conector.
    $codigo = $e instanceof PDOException ? ($e->errorInfo[1] ?? $e->getCode()) : null;
    echo json_encode(['correcto' => false, 'codigo' => $codigo,
        'indicacion' => 'No se pudo conectar para diagnosticar. Ejecuta este comando en el hosting con PHP PDO MySQL y la misma configuración que usa la aplicación.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit(2);
}
