<?php
require_once "../controladores/observacionOrdenes.controlador.php";
require_once "../modelos/observacionOrdenes.modelo.php";


class AjaxObservaciones{
	
	/*=============================================
  	EDITAR DATOS TECNICO
 	=============================================*/ 

	public function ajaxMostrarObservacion(){
		
		$item = "id_orden";
		$valor = $this->idOrdenOv;
		
		$respuesta = controladorObservaciones::ctrMostrarobservaciones($item, $valor);

		echo json_encode($respuesta);

		
	}
}

/*=============================================
EDITAR TECNICO
=============================================*/
if(isset($_POST["idOrdenOv"])){

	$editar = new AjaxObservaciones();
	$editar -> idOrdenOv = $_POST["idOrdenOv"];
	$editar -> ajaxMostrarObservacion();

}