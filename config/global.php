<?php
/**
 * global.php — Constantes globales del sistema EGS.
 *
 * Lee los valores desde variables de entorno (.env cargado en index.php).
 * Las credenciales requieren configuración; no se incluyen contraseñas heredadas.
 */

require_once __DIR__.'/env.php';

// ── Base de datos del sistema de ventas (usado por config/Conexion.php — mysqli) ──
define('DB_HOST',     (getenv('DB_SISTEMA_HOST') === false ? 'localhost' : getenv('DB_SISTEMA_HOST')));
define('DB_NAME',     (getenv('DB_SISTEMA_NAME') === false ? '' : getenv('DB_SISTEMA_NAME')));
define('DB_USERNAME', (getenv('DB_SISTEMA_USER') === false ? '' : getenv('DB_SISTEMA_USER')));
define('DB_PASSWORD', (getenv('DB_SISTEMA_PASS') === false ? '' : getenv('DB_SISTEMA_PASS')));
define('DB_PORT', (int)(getenv('DB_SISTEMA_PORT') ?: 3306));
define('DB_ENCODE',   'utf8');

// ── Nombre del proyecto ────────────────────────────────────────────────────────
define('PRO_NOMBRE', getenv('APP_NAME') ?: 'ITVentas');
