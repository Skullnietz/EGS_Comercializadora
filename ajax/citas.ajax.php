<?php

require_once "../controladores/citas.controlador.php";
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

// Obtener citas por rango de fechas (para navbar calendar)
if (isset($_POST["accion"]) && $_POST["accion"] == "citasPorRango") {
    $rango = isset($_POST["rango"]) ? $_POST["rango"] : "hoy";

    $hoy = date('Y-m-d');

    switch ($rango) {
        case 'hoy':
            $inicio = $hoy . ' 00:00:00';
            $fin = $hoy . ' 23:59:59';
            break;
        case 'semana':
            $inicioSemana = date('Y-m-d', strtotime('monday this week'));
            $finSemana = date('Y-m-d', strtotime('sunday this week'));
            $inicio = $inicioSemana . ' 00:00:00';
            $fin = $finSemana . ' 23:59:59';
            break;
        case 'mes':
            $inicio = date('Y-m-01') . ' 00:00:00';
            $fin = date('Y-m-t') . ' 23:59:59';
            break;
        default:
            $inicio = $hoy . ' 00:00:00';
            $fin = $hoy . ' 23:59:59';
    }

    $respuesta = ModeloCitas::mdlCitasPorRango("citas", $inicio, $fin);
    echo json_encode($respuesta);
}
