<?php
/**
 * env.php — Cargador de variables de entorno sin dependencias externas.
 *
 * Lee el archivo .env de la raíz del proyecto y registra cada variable en:
 *   - $_ENV['NOMBRE']
 *   - getenv('NOMBRE')
 *
 * Características:
 *   - Ignora líneas vacías y comentarios (# ...)
 *   - Soporta comillas simples y dobles en los valores
 *   - No sobreescribe variables que ya existan en el entorno del servidor
 *     (las del cPanel / virtualhost tienen prioridad)
 *   - Si .env no existe, no lanza error — las conexiones requieren configuración explícita
 *
 * Uso: require_once __DIR__ . '/../config/env.php';  (se llama en index.php)
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * IMPORTANTE PARA PRODUCCIÓN
 * ─────────────────────────────────────────────────────────────────────────────
 *  1. Sube el archivo .env a la raíz del proyecto por FTP.
 *  2. Verifica que .env NO sea accesible desde el navegador
 *     (el .htaccess de la raíz lo bloquea automáticamente).
 *  3. Nunca subas .env al repositorio — está en .gitignore.
 * ─────────────────────────────────────────────────────────────────────────────
 */

(static function () {
    $pointer=__DIR__.'/environment.php';
    $envPath = getenv('EGS_ENV_FILE') ?: (is_file($pointer)?require $pointer:__DIR__.'/../.env');

    if (!file_exists($envPath) || !is_readable($envPath)) {
        return; // Sin .env: configura las conexiones antes de utilizarlas
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Ignorar comentarios y líneas vacías
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Separar nombre = valor (solo en el primer '=')
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $name  = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        // Eliminar comillas envolventes  "valor"  o  'valor'
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Solo registrar si el nombre es válido y no existe ya
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) && getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name]    = $value;
            $_SERVER[$name] = $value;
        }
    }
})();
