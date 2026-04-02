<?php

require_once "../config/Database.php";
require_once "../modelos/recompensas.modelo.php";
require_once "../controladores/recompensas.controlador.php";

class AjaxRecompensas
{
    /*=============================================
    OBTENER INFO DE RECOMPENSAS DE UN CLIENTE
    =============================================*/
    public function ajaxObtenerInfoRecompensas()
    {
        $idCliente = intval($this->idCliente);
        $info = ControladorRecompensas::ctrObtenerInfoRecompensas($idCliente);

        header('Content-Type: application/json');
        echo json_encode($info);
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO
    =============================================*/
    public function ajaxCanjearRecompensa()
    {
        $idCliente = intval($this->idClienteCanje);
        $idOrden = intval($this->idOrdenCanje);
        $montoCanje = floatval($this->montoCanje);

        $resultado = ControladorRecompensas::ctrCanjearRecompensa($idCliente, $idOrden, $montoCanje);

        header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(array("status" => "ok", "data" => $resultado));
        } else {
            echo json_encode(array("status" => "error", "mensaje" => "No se pudo realizar el canje. Verifica el saldo disponible."));
        }
    }

    /*=============================================
    ACUMULAR RECOMPENSA (se llama al entregar)
    =============================================*/
    public function ajaxAcumularRecompensa()
    {
        $idCliente = intval($this->idClienteAcumular);
        $idOrden = intval($this->idOrdenAcumular);
        $totalOrden = floatval($this->totalOrdenAcumular);

        $resultado = ControladorRecompensas::ctrAcumularRecompensa($idCliente, $idOrden, $totalOrden);

        header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(array("status" => "ok", "data" => $resultado));
        } else {
            echo json_encode(array("status" => "ya_acumulado", "mensaje" => "La recompensa ya fue acumulada para esta orden."));
        }
    }
}

/*=============================================
OBTENER INFO RECOMPENSAS
=============================================*/
if (isset($_POST["idClienteRecompensas"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idCliente = $_POST["idClienteRecompensas"];
    $recompensas->ajaxObtenerInfoRecompensas();
}

/*=============================================
CANJEAR RECOMPENSA
=============================================*/
if (isset($_POST["idClienteCanje"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idClienteCanje = $_POST["idClienteCanje"];
    $recompensas->idOrdenCanje = $_POST["idOrdenCanje"];
    $recompensas->montoCanje = $_POST["montoCanje"];
    $recompensas->ajaxCanjearRecompensa();
}

/*=============================================
ACUMULAR RECOMPENSA
=============================================*/
if (isset($_POST["idClienteAcumular"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idClienteAcumular = $_POST["idClienteAcumular"];
    $recompensas->idOrdenAcumular = $_POST["idOrdenAcumular"];
    $recompensas->totalOrdenAcumular = $_POST["totalOrdenAcumular"];
    $recompensas->ajaxAcumularRecompensa();
}
