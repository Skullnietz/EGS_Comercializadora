<?php
require_once "../controladores/empresas.controlador.php";
require_once "../modelos/empresas.modelo.php";

class AjaxEmpresas{

  /*=============================================
  EDITAR DATOS ASESOR
  =============================================*/ 

    public $idEmpresa;

    public function ajaxEditarEmpresa(){

      $item = "id";
      $valor = $this->idEmpresa;

      $respuesta = ControladorEmpresas::ctrMostrarEmpresas($item, $valor);
      echo json_encode($respuesta);

    }
	


}


/*=============================================
EDITAR PERFIL
=============================================*/
if(isset($_POST["idEmpresa"])){

	$editar = new AjaxEmpresas();
	$editar -> idEmpresa = $_POST["idEmpresa"];
	$editar -> ajaxEditarEmpresa();

}