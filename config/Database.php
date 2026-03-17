<?php
/**
 * Database — Configuración centralizada de las tres conexiones PDO del sistema EGS.
 *
 * Lee credenciales desde variables de entorno (.env cargado por config/env.php).
 * Si una variable no está definida, usa el valor de fallback hardcodeado
 * para garantizar compatibilidad sin .env (útil en desarrollo local).
 *
 * Uso directo:
 *   $pdo = Database::conectar(Database::SISTEMA);
 *   $pdo = Database::conectar(Database::ECOMMERCE);
 *   $pdo = Database::conectar(Database::WORDPRESS);
 *
 * Los wrappers backward-compatible (Conexion, ConexionWP) delegan aquí.
 */
class Database
{
    /* ── Identificadores de conexión ── */
    const SISTEMA   = 'sistema';
    const ECOMMERCE = 'ecommerce';
    const WORDPRESS = 'wordpress';

    /** Pool de instancias PDO reutilizables (Singleton por conexión) */
    private static $instances = [];

    /**
     * Devuelve la configuración de credenciales para la clave dada.
     * Prioridad: variable de entorno → valor de fallback.
     *
     * @param  string $key
     * @return array
     * @throws RuntimeException
     */
    private static function config($key)
    {
        $map = [
            /* ── Sistema de ventas (egsequip_dbsistema) ── */
            self::SISTEMA => [
                'host'    => getenv('DB_SISTEMA_HOST') ?: 'localhost',
                'dbname'  => getenv('DB_SISTEMA_NAME') ?: 'egsequip_dbsistema',
                'user'    => getenv('DB_SISTEMA_USER') ?: 'egsequip_sistema',
                'pass'    => getenv('DB_SISTEMA_PASS') ?: '{#k%ER.PJD0?',
                'charset' => 'utf8',
            ],
            /* ── E-commerce / productos (egsequip_ecomerce) ── */
            self::ECOMMERCE => [
                'host'    => getenv('DB_ECOMMERCE_HOST') ?: 'localhost',
                'dbname'  => getenv('DB_ECOMMERCE_NAME') ?: 'egsequip_ecomerce',
                'user'    => getenv('DB_ECOMMERCE_USER') ?: 'egsequip_bdecome',
                'pass'    => getenv('DB_ECOMMERCE_PASS') ?: 'Qlf24011607',
                'charset' => 'utf8',
            ],
            /* ── Órdenes / pedidos / comisiones (egsequip_respaldo) ── */
            self::WORDPRESS => [
                'host'    => getenv('DB_WORDPRESS_HOST') ?: 'localhost',
                'dbname'  => getenv('DB_WORDPRESS_NAME') ?: 'egsequip_respaldo',
                'user'    => getenv('DB_WORDPRESS_USER') ?: 'egsequip_wp54331',
                'pass'    => getenv('DB_WORDPRESS_PASS') ?: 'Qlf24011607',
                'charset' => 'utf8',
            ],
        ];

        if (!isset($map[$key])) {
            throw new RuntimeException("Database: identificador de conexión desconocido '$key'.");
        }

        return $map[$key];
    }

    /**
     * Retorna (o crea) la instancia PDO para la conexión indicada.
     * La conexión se reutiliza en el mismo request (Singleton).
     *
     * @param  string $key  Database::SISTEMA | ECOMMERCE | WORDPRESS
     * @return PDO
     */
    public static function conectar($key)
    {
        if (!isset(self::$instances[$key])) {
            $c   = self::config($key);
            $dsn = "mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}";

            self::$instances[$key] = new PDO(
                $dsn,
                $c['user'],
                $c['pass'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$c['charset']}",
                ]
            );
        }

        return self::$instances[$key];
    }
}
