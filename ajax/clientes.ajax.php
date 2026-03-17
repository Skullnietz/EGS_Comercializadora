<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";


class AjxClientes{
	
	/*=============================================
  	TRAER DATOS CLIENTE
 	=============================================*/ 
 	public $idCliente;
	public function ajaxEditarCliente(){
		
		$item = "id";
		$valor = $this->idCliente;

		$respuesta = ControladorClientes::ctrMostrarClientes($item, $valor);

		echo json_encode($respuesta);
	}

	/*=============================================
	VALIDAR NO REPETIR CLIENTE
	=============================================*/	
	public $nombreCliente;

	public function ajaxValidarNorepetirCliente(){
		
		$item = "nombre";
		$valor = $this->nombreCliente;

		$respuesta = ControladorClientes::ctrMostrarClientesTabla($item, $valor);

		echo json_encode($respuesta);
	}
}

/*=============================================
TRAER DATOS CLIENTE
=============================================*/
if(isset($_POST["idCliente"])){

	$editar = new AjxClientes();
	$editar -> idCliente = $_POST["idCliente"];
	$editar -> ajaxEditarCliente();

}

/*=============================================
	VALIDAR NO REPETIR CLIENTE
=============================================*/
if(isset($_POST["nombreCliente"])){

	$valProducto = new AjxClientes();
	$valProducto -> nombreCliente = $_POST["nombreCliente"];
	$valProducto -> ajaxValidarNorepetirCliente();

}	