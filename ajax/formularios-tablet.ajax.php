<?php

require_once "../modelos/conexion.php";
require_once "../modelos/conexionWordpress.php";
require_once "../modelos/observacionOrdenes.modelo.php";

class AjaxFormulariosTablet {

    public $estado;

    public function ajaxObtenerUltimaOrden() {
        
        $estadoLike = "%" . $this->estado . "%";
        
        // La tabla ordenes está en la BD secundaria (WP / Respaldo)
        $pdoWP = ConexionWP::conectarWP();
        $stmt = $pdoWP->prepare("SELECT id, id_usuario, marcaDelEquipo, modeloDelEquipo, numeroDeSerieDelEquipo, fecha, estado 
                               FROM ordenes 
                               WHERE estado LIKE :estado 
                               ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(":estado", $estadoLike, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($orden) {
                // La tabla clientes está en la BD principal
                $pdo = Conexion::conectar();
                $stmtCliente = $pdo->prepare("SELECT nombre FROM clientes WHERE id = :id");
                $stmtCliente->bindParam(":id", $orden["id_usuario"], PDO::PARAM_INT);
                $stmtCliente->execute();
                $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);
                $orden["nombre_cliente"] = $cliente ? $cliente["nombre"] : "Desconocido";
                
                $orden["fecha"] = date("d/m/Y", strtotime($orden["fecha"]));
            }

            echo json_encode($orden);
        } catch (Exception $e) {
            echo json_encode(["error" => "Error de BD: " . $e->getMessage()]);
        }
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

        try {
            $respuesta = ModeloObservaciones::mdlCrearObservacion("observacionesOrdenes", $datos);
            echo json_encode(["status" => $respuesta]);
        } catch (Exception $e) {
            echo json_encode(["error" => "Error al guardar observación: " . $e->getMessage()]);
        }
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
