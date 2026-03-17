<?php

class Controladorasesores{

	/*=============================================
  	MOSTRAR ASESORES
  	=============================================*/	
	function ctrMostrarAsesores(){
		
		$tabla = "asesores";

		$respuesta = ModeloAsesores::mdlMostrarAsesores($tabla);

		return $respuesta;
	}

	public function ctrMostrarAsesoresEleg($item, $valor){
		
		$tabla = "asesores";

		$respuesta = ModeloAsesores::mdlMostrarAsesoresEleg($tabla, $item, $valor);

		return $respuesta;

	}

	/*=============================================
    AGREGAR ASESOR
    =============================================*/
     public function ctrCrearPerfil(){

		if(isset($_POST["nuevoNombreAsesor"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombreAsesor"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["nuevoEmailAsesor"])){

			   	/*=============================================
				VALIDAR IMAGEN
				=============================================*/


				$tabla = "asesores";

				$datos = array("nombre" => $_POST["nuevoNombreAsesor"], 
    						   "correo" => $_POST["nuevoEmailAsesor"], 
    						   "numerodeCelular" => $_POST["nuevoNumeroUno"], 
    						   "numeroTelefono" => $_POST["nuevoNumeroDos"]
    						   );

				$respuesta = ModeloAsesores::mdlIngresarAsesores($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El Asesor ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "asesores";

						}

					});
				

					</script>';


				}	


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El Asesor no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "asesores";

						}

					});
				

				</script>';

			}


		}

	}

static public function ctrEditarAsesor(){

		if(isset($_POST["idAsesor"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreAsesor"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["editarEmailAsesor"])){

				$tabla = "asesores";

				$datos = array("id" => $_POST["idAsesor"],
							   "nombre" => $_POST["editarNombreAsesor"],
							   "correo" => $_POST["editarEmailAsesor"],
							   "numerodeCelular" => $_POST["editarNumeroUno"],
							   "numeroTelefono" => $_POST["editarTelefonoDos"],
							   );

				$respuesta = ModeloAsesores::mdlEditarAsesor($tabla,$datos);

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El asesor ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
									if (result.value) {

									window.location = "asesores";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {

							window.location = "asesores";

							}
						})

			  	</script>';

			}

		}

	}


	/*=============================================
	ELIMINAR PERFIL
	=============================================*/

	static public function ctrEliminarAsesor(){

		if(isset($_GET["idAsesor"])){

			$tabla ="asesores";
			$datos = $_GET["idAsesor"];

			$respuesta = ModeloAsesores::mdlEliminarAsesor($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El perfil ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "asesores";

								}
							})

				</script>';

			}		

		}

	}

}