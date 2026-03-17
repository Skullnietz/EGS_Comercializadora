<?php

//require_once "../controladores/peticionmaterial.controlador.php";
//require_once "../modelos/peticionmaterial.modelo.php";

class AjaxPeticionMaterial{

	/*=============================================
	entrega y devolucion material
	=============================================*/	

	public $activarPeticion;
	public $activarid_peticionM;

	public function ajaxactivarPeticion(){

		$tabla = "peticionesmaterial";

		$item1 = "entregado";
		$valor1 = $this->activarPeticion;

		$item2 = "id_peticionM";
		$valor2 = $this->activarid_peticionM;

		$respuesta = ModeloPeticionM::mdlActualizarPeticion($tabla, $item1, $valor1, $item2, $valor2);

		echo $respuesta;

	}
    
}
/*=============================================
ACTIVAR PETICION
=============================================*/	

if(isset($_POST["activarPeticion"])){

	$activarPeticion = new AjaxAdministradores();
	$activarPeticion -> activarPeticion = $_POST["activarPeticion"];
	$activarPeticion -> activarid_peticionM = $_POST["activarid_peticionM"];
	$activarPeticion -> ajaxactivarPeticion();

}