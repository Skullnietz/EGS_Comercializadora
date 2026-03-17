<?php
require_once "../controladores/tecnicos.controlador.php";
require_once "../modelos/tecnicos.modelo.php";

class TecnicosAjax{
  
  	/*=============================================
  	ACTIVAR USUARIOS
  	=============================================*/	
  	public $activarTecnico;
 	public $activarId;
	
	public function ActivarTecnico(){
	
		$respuesta = ModeloTecnicos::mdlActualizarTecnico("tecnicos", "estado", $this->activarTecnico, "id", $this->activarId);

  		echo $respuesta;

	}
}
/*=============================================
ACTIVAR CATEGORIA
=============================================*/

if(isset($_POST["activarTecnico"])){

	$activarTecnico = new TecnicosAjax();
	$activarTecnico -> activarTecnico = $_POST["activarTecnico"];
	$activarTecnico -> activarId = $_POST["activarId"];
	$activarTecnico -> ActivarTecnico();

}