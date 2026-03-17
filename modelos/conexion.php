<?php
/**
 * Conexion — wrapper backward-compatible para la BD de e-commerce.
 *
 * Delega a Database::conectar(Database::ECOMMERCE) para mantener
 * un único punto de configuración de credenciales.
 *
 * Todos los modelos que llaman Conexion::conectar() siguen funcionando sin cambios.
 */
require_once __DIR__ . '/../config/Database.php';

class Conexion
{
    /**
     * Retorna la conexión PDO a egsequip_ecomerce (e-commerce / productos).
     * @return PDO
     */
    public static function conectar()
    {
        return Database::conectar(Database::ECOMMERCE);
    }
}
