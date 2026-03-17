<?php

require_once "../config/controladores/citas.controlador.php";
require_once "../modelos/citas.modelo.php";

class AjaxCitas
{

    /*=============================================
    MOSTRAR CITAS (JSON)
    =============================================*/
    public function ajaxMostrarCitas()
    {

        $item = null;
        $valor = null;

        $respuesta = ControladorCitas::ctrMostrarCitas($item, $valor);

        echo json_encode($respuesta);

    }

    /*=============================================
    GUARDAR CITA (AJAX)
    =============================================*/
    public $titulo;
    public $start;
    public $color;
    public $idOrden;

    public function ajaxGuardarCita()
    {

        $tabla = "citas";

        $datos = array(
            "title" => $this->titulo,
            "start" => $this->start,
            "end" => $this->start,
            "description" => "",
            "color" => $this->color,
            "id_orden" => $this->idOrden
        );

        $respuesta = ModeloCitas::mdlIngresarCita($tabla, $datos);

        echo $respuesta;

    }

    /*=============================================
    ELIMINAR CITA (AJAX)
    =============================================*/
    public $idCita;

    public function ajaxEliminarCita()
    {
        $tabla = "citas";
        $datos = $this->idCita;
        $respuesta = ModeloCitas::mdlEliminarCita($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    OBTENER ORDEN (AJAX)
    =============================================*/
    public $obtenerOrden;

    public function ajaxObtenerOrden()
    {
        require_once "../modelos/ordenes.modelo.php";

        // Using existing model method? Or create a simple query here?
        // ModeloOrdenes::mdlMostrarordenesParaValidar takes ($tabla, $item, $valor)
        $tabla = "ordenes";
        $item = "id";
        $valor = $this->obtenerOrden;

        // Note: mdlMostrarordenesParaValidar returns fetchAll (array of arrays)
        $respuesta = ModeloOrdenes::mdlMostrarordenesParaValidar($tabla, $item, $valor);

        // Return first item or false
        echo json_encode(!empty($respuesta) ? $respuesta[0] : false);
    }

}

/*=============================================
ACCIONES
=============================================*/

// Mostrar Citas
if (isset($_POST["accion"]) && $_POST["accion"] == "mostrar") {
    $citas = new AjaxCitas();
    $citas->ajaxMostrarCitas();
}

// Guardar Cita
if (isset($_POST["tituloCita"])) {
    $guardar = new AjaxCitas();
    $guardar->titulo = $_POST["tituloCita"];
    $guardar->start = $_POST["fechaCita"];
    $guardar->color = $_POST["colorCita"];
    $guardar->idOrden = isset($_POST["idOrden"]) ? $_POST["idOrden"] : null;
    $guardar->ajaxGuardarCita();
}

// Eliminar Cita
if (isset($_POST["idCita"])) {
    $eliminar = new AjaxCitas();
    $eliminar->idCita = $_POST["idCita"];
    $eliminar->ajaxEliminarCita();
}

// Obtener Orden Info
if (isset($_POST["obtenerOrden"])) {
    $infoOrden = new AjaxCitas();
    $infoOrden->obtenerOrden = $_POST["obtenerOrden"];
    $infoOrden->ajaxObtenerOrden();
}
