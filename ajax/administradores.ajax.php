<?php

require_once "../controladores/administradores.controlador.php";
require_once "../modelos/administradores.modelo.php";
require_once "../modelos/tecnicos.modelo.php";
require_once "../modelos/modelo.asesores.php";

class AjaxAdministradores{

	/*=============================================
	ACTIVAR PERFIL
	=============================================*/	

	public $activarPerfil;
	public $activarId;

	public function ajaxActivarPerfil(){

		$tabla = "administradores";

		$item1 = "estado";
		$valor1 = $this->activarPerfil;

		$item2 = "id";
		$valor2 = $this->activarId;

		$profile = ModeloAdministradores::mdlMostrarAdministradores($tabla, "id", $this->activarId);

		$respuesta = ModeloAdministradores::mdlActualizarPerfil($tabla, $item1, $valor1, $item2, $valor2);

		if($respuesta == "ok" && $profile){
			$estadoStr = ($valor1 == 1 || $valor1 == "1") ? "Activo" : "Inactivo";
			$email = $profile["email"];
			
			if($profile["perfil"] == "tecnico"){
				$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $email);
				if($tec){
					ModeloTecnicos::mdlActualizarTecnico("tecnicos", "estado", $estadoStr, "id", $tec["id"]);
				}
			} elseif($profile["perfil"] == "vendedor"){
				$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $email);
				if($ase){
					$datosAse = array("id" => $ase["id"], "nombre" => $ase["nombre"], "correo" => $ase["correo"], "numerodeCelular" => $ase["numerodeCelular"], "numeroTelefono" => $ase["numeroTelefono"], "porcentajeComision" => $ase["porcentajeComision"], "estado" => $estadoStr);
					ModeloAsesores::mdlEditarAsesor("asesores", $datosAse);
				}
			}
		}

		echo $respuesta;

	}

	/*=============================================
	EDITAR PERFIL
	=============================================*/	

	public $idPerfil;

	public function ajaxEditarPerfil(){

		$item = "id";
		$valor = $this->idPerfil;

		$respuesta = ControladorAdministradores::ctrMostrarAdministradores($item, $valor);

		echo json_encode($respuesta);

	}



}

/*=============================================
ACTIVAR PERFIL
=============================================*/	

if(isset($_POST["activarPerfil"])){

	$activarPerfil = new AjaxAdministradores();
	$activarPerfil -> activarPerfil = $_POST["activarPerfil"];
	$activarPerfil -> activarId = $_POST["activarId"];
	$activarPerfil -> ajaxActivarPerfil();

}

/*=============================================
EDITAR PERFIL
=============================================*/
if(isset($_POST["idPerfil"])){

	$editar = new AjaxAdministradores();
	$editar -> idPerfil = $_POST["idPerfil"];
	$editar -> ajaxEditarPerfil();

}