<?php

class ControladorPeticionO{
	/*======================================
	CREAR PETICION DE ORDEN 
	=======================================*/
	public function ctrlCrearPeticionO(){
	
		if (isset($_POST["orden_mod"])) {
		    
		    	date_default_timezone_set("America/Mexico_City");

				$fechahoracambios = date('Y-m-d H:i:s');
				
		    $tabla = "peticionesordenes";
			
			$datos = array("orden_mod" => $_POST["orden_mod"],
			               "tecnico_solicita" => $_POST["tecnico_solicita"],
						   "dep_desarrollo" => $_POST["dep_desarrollo"],
						    "acciones" => $_POST["acciones"],
						   "motivos" => $_POST["motivos"],
						    "fecha_hora" =>$fechahoracambios,);

		$respuesta =ModeloPeticionO::mdlCrearPeticionO($tabla,$datos);
		
		}
	    
	}
			/*======================================
	SELECCIONAR ULTIMAS PETICIONES
	=======================================*/
		public function ctrMostrarUltimasPeticionesO($tabla){
		
		$tabla =  'peticionesordenes';

		$respuesta = ModeloPeticionO::mdlMostrarUltimasPeticionesO($tabla);

		return $respuesta;
	}
    
}