<?php
require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";

class AjaxAsesores{

  /*=============================================
  EDITAR DATOS ASESOR
  =============================================*/ 

    public $idAsesor;

    public function ajaxEditarAsesor(){

      $item = "id";
      $valor = $this->idAsesor;

      $respuesta = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);
      //$asesores = Controladorasesores::ctrMostrarAsesores();
      echo json_encode($respuesta);

    }
	


}


/*=============================================
EDITAR PERFIL
=============================================*/
if(isset($_POST["idAsesor"])){

	$editar = new AjaxAsesores();
	$editar -> idAsesor = $_POST["idAsesor"];
	$editar -> ajaxEditarAsesor();

}