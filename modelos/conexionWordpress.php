<?php
/**
 * ConexionWP — wrapper backward-compatible para la BD principal (órdenes/pedidos).
 *
 * Delega a Database::conectar(Database::WORDPRESS) para mantener
 * un único punto de configuración de credenciales.
 *
 * Todos los modelos que llaman ConexionWP::conectarWP() siguen funcionando sin cambios.
 *
 * Nota: el nombre "WordPress" es histórico (migración desde WP nunca completada).
 * La BD real es egsequip_respaldo y contiene órdenes, pedidos y comisiones.
 */
require_once __DIR__ . '/../config/Database.php';

class ConexionWP
{
    /**
     * Retorna la conexión PDO a egsequip_respaldo (órdenes, pedidos, comisiones).
     * @return PDO
     */
    public static function conectarWP()
    {
        return Database::conectar(Database::WORDPRESS);
    }
}
