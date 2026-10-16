<?php
/**
 * Database — Configuración centralizada de las tres conexiones PDO del sistema EGS.
 *
 * Lee credenciales desde variables de entorno (.env cargado por config/env.php).
 * Las credenciales deben configurarse en un .env privado o en el entorno del servidor.
 *
 * Uso directo:
 *   $pdo = Database::conectar(Database::SISTEMA);
 *   $pdo = Database::conectar(Database::ECOMMERCE);
 *   $pdo = Database::conectar(Database::WORDPRESS);
 *
 * Los wrappers backward-compatible (Conexion, ConexionWP) delegan aquí.
 */

// Los endpoints de ajax/ no pasan por index.php: cargar aquí el .env garantiza que usen la misma base.
require_once __DIR__ . '/env.php';

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
     * Credenciales de entorno; sólo el host y puerto tienen valores predeterminados.
     *
     * @param  string $key
     * @return array
     * @throws RuntimeException
     */
    private static function env($name, $default='')
    {
        $value=getenv($name);return $value===false?$default:$value;
    }

    private static function config($key)
    {
        $map = [
            /* ── Sistema de ventas (egsequip_dbsistema) ── */
            self::SISTEMA => [
                'host'    => self::env('DB_SISTEMA_HOST', 'localhost'),
                'dbname'  => self::env('DB_SISTEMA_NAME', ''),
                'user'    => self::env('DB_SISTEMA_USER', ''),
                'pass'    => self::env('DB_SISTEMA_PASS', ''),
                'charset' => 'utf8',
            ],
            /* ── E-commerce / productos (egsequip_ecomerce) ── */
            self::ECOMMERCE => [
                'host'    => self::env('DB_ECOMMERCE_HOST', 'localhost'),
                'dbname'  => self::env('DB_ECOMMERCE_NAME', ''),
                'user'    => self::env('DB_ECOMMERCE_USER', ''),
                'pass'    => self::env('DB_ECOMMERCE_PASS', ''),
                'charset' => 'utf8',
            ],
            /* ── Órdenes / pedidos / comisiones (egsequip_respaldo) ── */
            self::WORDPRESS => [
                'host'    => self::env('DB_WORDPRESS_HOST', 'localhost'),
                'dbname'  => self::env('DB_WORDPRESS_NAME', ''),
                'user'    => self::env('DB_WORDPRESS_USER', ''),
                'pass'    => self::env('DB_WORDPRESS_PASS', ''),
                'charset' => 'utf8',
            ],
        ];

        if (!isset($map[$key])) {
            throw new RuntimeException("Database: identificador de conexión desconocido '$key'.");
        }

        if($map[$key]['dbname']==='' || $map[$key]['user']==='')throw new RuntimeException('Faltan las variables DB_ de la conexión '.$key.'.');
        $prefix='DB_'.strtoupper($key);$map[$key]['port']=(int)(getenv($prefix.'_PORT') ?: 3306);
        if($map[$key]['port']<1 || $map[$key]['port']>65535)throw new RuntimeException('Puerto de base de datos inválido.');
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
            $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['dbname']};charset={$c['charset']}";

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
