<?php

require_once "../config/Database.php";
require_once "../controladores/ordenes.controlador.php";
require_once "../modelos/ordenes.modelo.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../modelos/observacionOrdenes.modelo.php";

class AjaxFormulariosTablet {

    public $estado;

    public function ajaxObtenerUltimaOrden() {
        $pdo = Conexion::conectar();
        
        $estadoLike = "%" . $this->estado . "%";
        
        $stmt = $pdo->prepare("SELECT id, id_cliente, marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo, fecha, estado 
                               FROM ordenes 
                               WHERE estado LIKE :estado 
                               ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(":estado", $estadoLike, PDO::PARAM_STR);
        $stmt->execute();
        
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($orden){
            // Traer el nombre del cliente
            $stmtCliente = $pdo->prepare("SELECT nombre FROM clientes WHERE id = :id");
            $stmtCliente->bindParam(":id", $orden["id_cliente"], PDO::PARAM_INT);
            $stmtCliente->execute();
            $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);
            $orden["nombre_cliente"] = $cliente ? $cliente["nombre"] : "Desconocido";
            
            // Format fecha
            $orden["fecha"] = date("d/m/Y", strtotime($orden["fecha"]));
        }

        echo json_encode($orden);
    }

    public $idOrden;
    public $formData;
    public $idCreador;

    public function ajaxGuardarFormulario() {
        $observacion = "FORMULARIO_TABLET_JSON: " . $this->formData;
        
        $datos = array(
            "id_creador" => $this->idCreador,
            "id_orden" => $this->idOrden,
            "observacion" => $observacion
        );

        $respuesta = ModeloObservaciones::mdlCrearObservacion("observacionesOrdenes", $datos);
        echo json_encode(["status" => $respuesta]);
    }
}

if(isset($_POST["estado"])){
    $ultimaOrden = new AjaxFormulariosTablet();
    $ultimaOrden->estado = $_POST["estado"];
    $ultimaOrden->ajaxObtenerUltimaOrden();
}

if(isset($_POST["guardarFormulario"])){
    $guardar = new AjaxFormulariosTablet();
    $guardar->idOrden = $_POST["idOrden"];
    $guardar->formData = $_POST["formData"];
    $guardar->idCreador = $_POST["idCreador"];
    $guardar->ajaxGuardarFormulario();
}
