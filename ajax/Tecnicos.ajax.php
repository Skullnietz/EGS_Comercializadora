<?php
require_once "../controladores/tecnicos.controlador.php";
require_once "../modelos/tecnicos.modelo.php";

class AjaxTecnicos{
	
  /*=============================================
  EDITAR TECNICO
  =============================================*/ 

  public $idTecnico;

  public function ajaxEditarTecnico(){

    $item = "id";
    $valor = $this->idTecnico;

    $respuesta = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

    echo json_encode($respuesta);

  }
}
/*=============================================
EDITAR TECNICO
=============================================*/
if(isset($_POST["idTecnico"])){

  $editar = new AjaxTecnicos();
  $editar -> idTecnico = $_POST["idTecnico"];
  $editar -> ajaxEditarTecnico();

}