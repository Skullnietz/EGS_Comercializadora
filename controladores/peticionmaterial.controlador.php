<?php

class ControladorPeticionM{

	
	/*=============================================

	MOSTRAR  Peticiones

	static public function ctrMostrarPeticiones($tabla){



		$tabla = "peticionesmaterial";



		$respuesta = ModeloPeticionM::mdlMostrarPeticiones($tabla);



		return $respuesta;



	}
	=============================================*/




		/*======================================
	      METODO PARA REGISTRAR peticion
	=======================================*/
	public function ctrlCrearPeticion(){
		
		if (isset($_POST["material_orden"])) {
		    
		    	date_default_timezone_set("America/Mexico_City");



				$fechaDeIngreso = date('Y-m-d H:i:s');
				
		    $tabla = "peticionesmaterial";
			
			$datos = array("material_orden" => $_POST["material_orden"],
			               "tecnico_solicita" => $_POST["tecnico_solicita"],
						   "material_solicitado" => $_POST["material_solicitado"],
						   "entrega" =>$fechaDeIngreso,
						    "entregado" => $_POST["entregado"],
						   "devolucion" =>$fechaDeDevolucion,
						   "devuelto" => $_POST["devuelto"],);

		$respuesta =ModeloPeticionM::mdlCrearPeticion($tabla,$datos);

		if($respuesta == "ok"){
		    if($respuesta["entregado"] == 1 &&
$respuesta["devuelto"] == 0){
						
						$_SESSION["id_peticionM"] = $respuesta["id_peticionM"];
						$_SESSION["material_orden"] = $respuesta["material_orden"];
						$_SESSION["tecnico_solicita"] = $respuesta["tecnico_solicita"];
						$_SESSION["material_solicitado"] = $respuesta["material_solicitado"];
						$_SESSION["entrega"] = $respuesta["entrega"];
						
						$_SESSION["devolucion"] = $respuesta["devolucion"];

						echo '<script>

							window.location = "index.php?ruta=inicio";

						</script>';

						$tabla="peticionesmaterial";
						$Peticionentregada=1;
						$entregapeticion = ModeloPeticionM::mdlActivarPeticion($tabla,$_SESSION["id_peticionM"],$Peticionentregada);

						}

				echo'<script>

				swal({
					  type: "success",
					  title: "La Peticion ha sido Guardada correctamente",
					  showConfirmButton: true,						confirmButtonText: "Cerrar"
					  
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=peticionmaterial";

								}
							});

				</script>';

		}else{

			echo '<script>
						swal({
		      				title: "ERROR",
		      				text: "¡La peticion no ha sido guardada correctamente!",
		      				type: "error",
		      				confirmButtonText: "¡Cerrar!"
		    			});

				</script>';
		}



		}
	
	}

		/*======================================
	SELECCIONAR CREADOR DE LA Peticion
	=======================================*/
		public function ctrMostrarCreadorPeticion($tabla, $idperfil){
		
		$tabla = "administradores";

		$respuesta = ModeloPeticionM::mdlMostrarCreadorPeticion($tabla, $idperfil);

		return $respuesta;
	}
		/*======================================
	SELECCIONAR ULTIMAS 5 PETICONES
	=======================================*/
		public function ctrMostrarUltimasPeticiones($tabla){
		
		$tabla =  'peticionesmaterial';

		$respuesta = ModeloPeticionM::mdlMostrarUltimasPeticiones($tabla);

		return $respuesta;
	}
		/*======================================
	fecha de entrega
	=======================================*/
		static public function ctrFechaEntrega($tabla){

		
		if (isset($_POST["entrega"])) {
		    
		    
		    $tabla = "peticionesmaterial";
			
			$datos = array("entrega" => $_POST["entrega"]
			              
			);
				$respuesta = ModeloPeticionM::mdlFechaEntrega($tabla);



		return $respuesta;



	}}
	/*======================================
	fecha de devolucio
	=======================================*/
		static public function ctrFechaDevolucion($tabla){

		
		if (isset($_POST["devolucion"])) {
		    
		   
		    $tabla = "peticionesmaterial";
			
			$datos = array("devolucion" => $_POST["devolucion"]
			              
			);
				$respuesta = ModeloPeticionM::mdlFechaEntrega($tabla);



		return $respuesta;



	}
               	$item = "id";

      			$valor = $value["id_tecnico"];

      			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

      			$NombreTecnico = $tecnico["nombre"];
	//TRAER TECNICO

      			$item = "id";

      			$valor = $value["id_tecnico"];



      			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);



      			$NombreTecnico = $tecnico["nombre"];
		}
			

}
    

 

