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
    $fecha = $_POST["fechaCita"];
    $idOrden = isset($_POST["idOrden"]) ? $_POST["idOrden"] : null;

    // Verificar duplicados antes de guardar
    $dup = ModeloCitas::mdlVerificarDuplicado("citas", $fecha, $idOrden);
    if ($dup["duplicado"]) {
        if ($dup["tipo"] === "orden_hora") {
            echo "duplicado_hora|" . $dup["cita"]["title"] . "|" . $dup["cita"]["start"];
        } else {
            echo "duplicado_orden|" . $dup["cita"]["title"] . "|" . $dup["cita"]["start"];
        }
    } else {
        $guardar = new AjaxCitas();
        $guardar->titulo = $_POST["tituloCita"];
        $guardar->start = $fecha;
        $guardar->color = $_POST["colorCita"];
        $guardar->idOrden = $idOrden;
        $guardar->ajaxGuardarCita();
    }
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

// Obtener color automático basado en calificación del cliente de la orden
if (isset($_POST["accion"]) && $_POST["accion"] == "colorPorOrden") {
    $idOrden = isset($_POST["idOrden"]) ? intval($_POST["idOrden"]) : 0;

    if ($idOrden > 0) {
        require_once "../modelos/ordenes.modelo.php";
        $orden = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id", $idOrden);

        if (!empty($orden) && isset($orden[0]["id_usuario"])) {
            $idCliente = $orden[0]["id_usuario"];

            require_once "../config/clienteBadges.helper.php";
            if (!defined('EGS_ROOT')) define('EGS_ROOT', realpath(__DIR__ . '/..'));
            $bh = ClienteBadgesHelper::getInstance();
            $stats = $bh->getDetailedStats($idCliente);

            // Asignar color basado en calificación de entrega
            $color = '#3a87ad'; // Default azul
            if ($stats['es_nuevo']) {
                $color = '#8b5cf6'; // Morado - cliente nuevo
            } elseif ($stats['calif_entrega'] !== null) {
                if ($stats['calif_entrega'] >= 90)      $color = '#16a34a'; // Verde - excelente
                elseif ($stats['calif_entrega'] >= 70)   $color = '#2563eb'; // Azul - bueno
                elseif ($stats['calif_entrega'] >= 50)   $color = '#d97706'; // Ámbar - regular
                else                                     $color = '#dc2626'; // Rojo - pobre
            }

            echo json_encode([
                'ok' => true,
                'color' => $color,
                'calif' => $stats['calif_entrega'],
                'es_nuevo' => $stats['es_nuevo'],
                'total_ordenes' => $stats['total_ordenes'],
                'avg_recogida' => $stats['avg_recogida']
            ]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Orden no encontrada', 'color' => '#3a87ad']);
        }
    } else {
        echo json_encode(['ok' => false, 'error' => 'ID inválido', 'color' => '#3a87ad']);
    }
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
