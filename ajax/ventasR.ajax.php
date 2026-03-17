<?php

require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";


class AjaxVentasR{

	/*=============================================
  	IMPRIMIR TIKET
  	=============================================*/ 

    public $idventa;

    public function ajaxImprimirTiket(){

      $item = "id";
      $valor = $this->idventa;

      $respuesta = ControladorVentas::ctrMostrarVentasParaTiketimp($item, $valor);

      echo json_encode($respuesta);

    }

}

/*=============================================
VENTAS PARA TIKET
=============================================*/
if(isset($_POST["idventa"])){

  $editar = new AjaxVentasR();
  $editar -> idventa = $_POST["idventa"];
  $editar -> ajaxImprimirTiket();

}