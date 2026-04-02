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
    CANJEAR DINERO ELECTRÓNICO EN ORDEN
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
    CANJEAR DINERO ELECTRÓNICO EN PEDIDO
    =============================================*/
    public function ajaxCanjearRecompensaPedido()
    {
        $idCliente = intval($this->idClienteCanjePedido);
        $idPedido = intval($this->idPedidoCanje);
        $montoCanje = floatval($this->montoCanjePedido);

        $resultado = ControladorRecompensas::ctrCanjearRecompensaPedido($idCliente, $idPedido, $montoCanje);

        header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(array("status" => "ok", "data" => $resultado));
        } else {
            echo json_encode(array("status" => "error", "mensaje" => "No se pudo realizar el canje. Verifica el saldo disponible."));
        }
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO EN VENTA RÁPIDA
    =============================================*/
    public function ajaxCanjearRecompensaVenta()
    {
        $idCliente = intval($this->idClienteCanjeVenta);
        $idVenta = intval($this->idVentaCanje);
        $montoCanje = floatval($this->montoCanjeVenta);

        $resultado = ControladorRecompensas::ctrCanjearRecompensaVenta($idCliente, $idVenta, $montoCanje);

        header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(array("status" => "ok", "data" => $resultado));
        } else {
            echo json_encode(array("status" => "error", "mensaje" => "No se pudo realizar el canje. Verifica el saldo disponible."));
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
CANJEAR RECOMPENSA (ORDEN)
=============================================*/
if (isset($_POST["idClienteCanje"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idClienteCanje = $_POST["idClienteCanje"];
    $recompensas->idOrdenCanje = $_POST["idOrdenCanje"];
    $recompensas->montoCanje = $_POST["montoCanje"];
    $recompensas->ajaxCanjearRecompensa();
}

/*=============================================
CANJEAR RECOMPENSA (PEDIDO)
=============================================*/
if (isset($_POST["idClienteCanjePedido"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idClienteCanjePedido = $_POST["idClienteCanjePedido"];
    $recompensas->idPedidoCanje = $_POST["idPedidoCanje"];
    $recompensas->montoCanjePedido = $_POST["montoCanjePedido"];
    $recompensas->ajaxCanjearRecompensaPedido();
}

/*=============================================
CANJEAR RECOMPENSA (VENTA RÁPIDA)
=============================================*/
if (isset($_POST["idClienteCanjeVenta"])) {
    $recompensas = new AjaxRecompensas();
    $recompensas->idClienteCanjeVenta = $_POST["idClienteCanjeVenta"];
    $recompensas->idVentaCanje = $_POST["idVentaCanje"];
    $recompensas->montoCanjeVenta = $_POST["montoCanjeVenta"];
    $recompensas->ajaxCanjearRecompensaVenta();
}
