<?php
class controladorObservaciones{
	
	/*=============================================
	MOSTRAR OBSERVACIONES
	=============================================*/

	static public function ctrMostrarobservaciones($itemobs){

		$tabla = "observacionesOrdenes";

		$respuesta = ModeloObservaciones::mdlMostrarobservaciones($tabla, $itemobs);

		return $respuesta;
		
	
	}
	/*=============================================
	MOSTRAR OBSERVACIONES INFO DE USUARIO
	=============================================*/

	static public function ctrMostrarInfoUser($idadmin){

		$tabla = "administradores";

		$respuesta = ModeloObservaciones::mdlMostrarInfoUser($tabla, $idadmin);

		return $respuesta;
		
	
	}
	/*=============================================
	ÚLTIMAS OBSERVACIONES GLOBALES
	=============================================*/

	static public function ctrUltimasObservaciones($limite = 12){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlUltimasObservaciones($tabla, $limite);

	}

	/*=============================================
	OBSERVACIONES DE HOY
	=============================================*/

	static public function ctrObservacionesHoy(){

		$tabla = "observacionesOrdenes";

		return ModeloObservaciones::mdlObservacionesHoy($tabla);

	}

	/*=============================================
	INSERTAR OBSERVACIONES
	=============================================*/
	static public function ctrlCrearObservacion(){
		
		if (isset($_POST["observacion"])){
			
			$tabla = "observacionesOrdenes";

			$datos = array("id_creador" => $_POST["id_creador"],
						   "id_orden" => $_POST["id_orden"],
						   "observacion" => $_POST["observacion"]
						    );

			$respuesta = ModeloObservaciones::mdlCrearObservacion($tabla, $datos);

				if ($respuesta == "ok") {
					
					echo '<script>

					swal({

						type: "success",
						title: "!La observacion guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
						    window.history.back();
							

						}

					});
				

					</script>';
				}

			}
		
	}
		/*=============================================

	ELIMINAR Observacion

	=============================================*/



	static public function ctrEliminarObservacion(){



		if(isset($_GET["idobs"])){
		    
		    $tabla = "observacionesOrdenes";
			$datos = $_GET["idobs"];



			$respuesta = ModeloObservaciones::mdlEliminarObservacion($tabla,$datos);



			if($respuesta == "ok"){



				echo'<script>



				swal({

					  type: "success",

					  title: "La orden ha sido borrado correctamente",

					  showConfirmButton: true,

					  confirmButtonText: "Cerrar"

					  }).then(function(result){

								if (result.value) {



								window.history.back();



								}

							})



				</script>';



			}		







		}



	}
}