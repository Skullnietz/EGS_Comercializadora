<?php 
require_once "C:/Users/CarlosGuizar/Desktop/Proyectos/EGS/EGS_Comercializadora/modelos/conexion.php"; 
try {
    $stmt = Conexion::conectar()->prepare("DESCRIBE pedidos"); 
    $stmt->execute(); 
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
