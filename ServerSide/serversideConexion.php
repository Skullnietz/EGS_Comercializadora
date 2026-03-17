<?php
class ConexionO{
    public static function Conectar(){
define("servidor","localhost");
define("nombre_bd","egsequip_respaldo");
define("usuario","egsequip_wp54331");
define("password","Qlf24011607");
$opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
try{
    $conexiono = new PDO("mysql:host=".servidor.";dbname=".nombre_bd, usuario, password, $opciones);
    return $conexiono;
}catch (Exception $e){
    die ("El error de la Conexion es: ". $e->getMessage());
}
}
}
?>