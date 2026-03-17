<?php
/**
 * global.php — Constantes globales del sistema EGS.
 *
 * Lee los valores desde variables de entorno (.env cargado en index.php).
 * Si la variable no está disponible, usa el valor de fallback.
 */

// ── Base de datos del sistema de ventas (usado por config/Conexion.php — mysqli) ──
define('DB_HOST',     getenv('DB_SISTEMA_HOST') ?: 'localhost');
define('DB_NAME',     getenv('DB_SISTEMA_NAME') ?: 'egsequip_dbsistema');
define('DB_USERNAME', getenv('DB_SISTEMA_USER') ?: 'egsequip_sistema');
define('DB_PASSWORD', getenv('DB_SISTEMA_PASS') ?: '{#k%ER.PJD0?');
define('DB_ENCODE',   'utf8');

// ── Nombre del proyecto ────────────────────────────────────────────────────────
define('PRO_NOMBRE', getenv('APP_NAME') ?: 'ITVentas');
