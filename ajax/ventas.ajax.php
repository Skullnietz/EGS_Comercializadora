<?php

require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";


class AjaxVentas{

	/*=============================================
	ACTUALIZAR PROCESO DE ENVÍO
	=============================================*/
	

  	public $idVenta;
  	public $etapa;

  	public function ajaxEnvioVenta(){

  		$respuesta = ModeloVentas::mdlActualizarVenta("compras", "envio", $this->etapa, "id", $this->idVenta);

  		echo $respuesta;

	}

	/*=============================================
  		IMPRIMIR TIKET
  	=============================================*/ 

    public $idventa;

    public function ajaxImprimirTiket(){

      $item = "id";
      $valor = $this->idventa;

      $respuesta = ControladorVentas::ctrMostrarVentasParaTiket($item, $valor);

      echo json_encode($respuesta);

    }

}

/*=============================================
ACTUALIZAR PROCESO DE ENVÍO
=============================================*/


if(isset($_POST["idVenta"])){

	$envioVenta = new AjaxVentas();
	$envioVenta -> idVenta = $_POST["idVenta"];
	$envioVenta -> etapa = $_POST["etapa"];
	$envioVenta -> ajaxEnvioVenta();

}

/*=============================================
VENTAS PARA TIKET
=============================================*/
if(isset($_POST["idventa"])){

  $editar = new AjaxVentas();
  $editar -> idventa = $_POST["idventa"];
  $editar -> ajaxImprimirTiket();

}