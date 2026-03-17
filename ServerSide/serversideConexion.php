<?php
/**
 * Conexión PDO para ServerSide (DataTables legacy).
 * Usa variables de entorno cargadas por config/env.php.
 * Conecta a egsequip_respaldo (misma BD que ConexionWP / Database::WORDPRESS).
 */
class ConexionO{
    public static function Conectar(){
        $host     = getenv('DB_WORDPRESS_HOST') ?: 'localhost';
        $database = getenv('DB_WORDPRESS_NAME') ?: 'egsequip_respaldo';
        $user     = getenv('DB_WORDPRESS_USER') ?: 'egsequip_wp54331';
        $passwd   = getenv('DB_WORDPRESS_PASS') ?: 'Qlf24011607';

        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try{
            $conexiono = new PDO("mysql:host=".$host.";dbname=".$database, $user, $passwd, $opciones);
            return $conexiono;
        }catch (Exception $e){
            error_log("ServerSide ConexionO error: ". $e->getMessage());
            die("Error de conexión");
        }
    }
}
