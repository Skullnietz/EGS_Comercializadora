<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../modelos/ordenes.diagnostico.php';
try {
    $r = OrdenesDiagnostico::ejecutar(Database::conectar(Database::WORDPRESS));
    echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit($r['correcto'] ? 0 : 1);
} catch (Throwable $e) {
    $codigo = $e instanceof PDOException ? ($e->errorInfo[1] ?? $e->getCode()) : null;
    echo json_encode(['correcto' => false, 'codigo' => $codigo,
        'indicacion' => 'No se pudo diagnosticar. Ejecuta en el hosting con PHP PDO MySQL y la configuración de la aplicación.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit(2);
}
