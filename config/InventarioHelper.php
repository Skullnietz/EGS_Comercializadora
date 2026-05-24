<?php
/**
 * InventarioHelper — utilidades de moneda y configuración del inventario IT.
 */
require_once __DIR__ . '/../modelos/conexion.php';

class InventarioHelper
{
    const CLAVE_TIPO_CAMBIO = 'tipo_cambio_usd';
    const DEFAULT_TIPO_CAMBIO = 17.50;

    /** Asegura que exista la tabla config_sistema y el valor por defecto. */
    public static function ensureConfigTable()
    {
        try {
            $pdo = Conexion::conectar();
            $pdo->exec("CREATE TABLE IF NOT EXISTS config_sistema (
                clave VARCHAR(64) PRIMARY KEY,
                valor VARCHAR(255) NOT NULL,
                actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $stmt = $pdo->prepare("INSERT IGNORE INTO config_sistema (clave, valor) VALUES (:clave, :valor)");
            $stmt->execute([
                ':clave' => self::CLAVE_TIPO_CAMBIO,
                ':valor' => (string) self::DEFAULT_TIPO_CAMBIO,
            ]);
        } catch (Exception $e) {
            // Silencioso: el módulo sigue con valor por defecto en memoria.
        }
    }

    public static function getTipoCambioUsd()
    {
        self::ensureConfigTable();
        try {
            $pdo = Conexion::conectar();
            $stmt = $pdo->prepare("SELECT valor FROM config_sistema WHERE clave = :clave LIMIT 1");
            $stmt->execute([':clave' => self::CLAVE_TIPO_CAMBIO]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && is_numeric($row['valor']) && floatval($row['valor']) > 0) {
                return floatval($row['valor']);
            }
        } catch (Exception $e) {
            // fallback
        }
        return self::DEFAULT_TIPO_CAMBIO;
    }

    public static function setTipoCambioUsd($valor)
    {
        $valor = floatval($valor);
        if ($valor <= 0) {
            return false;
        }
        self::ensureConfigTable();
        $pdo = Conexion::conectar();
        $stmt = $pdo->prepare("INSERT INTO config_sistema (clave, valor) VALUES (:clave, :valor)
            ON DUPLICATE KEY UPDATE valor = :valor2");
        return $stmt->execute([
            ':clave'  => self::CLAVE_TIPO_CAMBIO,
            ':valor'  => (string) $valor,
            ':valor2' => (string) $valor,
        ]);
    }

    public static function formatPrecioMxn($monto)
    {
        return '$' . number_format(floatval($monto), 2, '.', ',') . ' MXN';
    }

    public static function calcularUsd($montoMxn, $tipoCambio = null)
    {
        $tc = $tipoCambio !== null ? floatval($tipoCambio) : self::getTipoCambioUsd();
        if ($tc <= 0) {
            return 0;
        }
        return floatval($montoMxn) / $tc;
    }

    public static function formatPrecioUsd($montoMxn, $tipoCambio = null)
    {
        $usd = self::calcularUsd($montoMxn, $tipoCambio);
        return 'US$' . number_format($usd, 2, '.', ',');
    }

    public static function stockBadgeClass($disponibilidad)
    {
        $d = intval($disponibilidad);
        if ($d <= 10) {
            return 'inv-badge-danger';
        }
        if ($d <= 15) {
            return 'inv-badge-warning';
        }
        return 'inv-badge-success';
    }
}
