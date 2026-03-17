<?php
class ControladorTecnicos{

	/*=================================
	MOSTRAR TECNICOS
	=================================*/
	
	static public function ctrMostrarTecnicos($item, $valor){
		
		$tabla = "tecnicos";

		$respuesta = ModeloTecnicos::mdlMostrarTecnicos($tabla, $item, $valor);

		return $respuesta;

	}

	/*=================================
	MOSTRAR TECNICOS PARA EMPRESAS
	=================================*/
	
	static public function ctrMostrarTecnicosDeEmpresas($item, $valor){
		
		$tabla = "tecnicos";

		$respuesta = ModeloTecnicos::mdlMostrarTecnicosDeEmpresa($tabla, $item, $valor);

		return $respuesta;

	}
	/*=================================
	CREAR TECNICO
	=================================*/
	public function ctrCrearTecnico(){
		
		if (isset($_POST["NombreDelTecnico"])) {
			

			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["NombreDelTecnico"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["Emailtecnico"])) {
				
				$tabla = "tecnicos";

				$datos = array("nombre" => $_POST["NombreDelTecnico"], 
							   "correo" => $_POST["Emailtecnico"], 
    						   "telefono" => $_POST["numeroTelTecnico"], 
    						   "telefonoDos" => $_POST["numeroTelDosTecnico"], 
    						   "HoraDeComida" => $_POST["HoraDeComida"],
    						   "areratecnico" =>$_POST["areratecnico"],
    						   "empresa" => $_POST["empresa"],
    						   "estado" => "Activo"
    						   );

				$respuesta = ModeloTecnicos::mdlCrearTecnico($tabla, $datos);

				if ($respuesta == "ok") {
					
					echo '<script>

					swal({

						type: "success",
						title: "¡El Técnico ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=tecnicos";

						}

					});
				

					</script>';
				}

			}else{


				echo '<script>

					swal({

						type: "error",
						title: "¡El Técnico no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=tecnicos";

						}

					});
				

				</script>';

			}
		}
	}


	public function ctrEditarTecnico(){
		
		if (isset($_POST["idTecnico"])) {
			
			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreTecnico"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["editarEmailTecnico"])){
				

				$tabla = "tecnicos";

				$datos = array("id" => $_POST["idTecnico"],
							   "nombre" => $_POST["editarNombreTecnico"], 
							   "correo" => $_POST["editarEmailTecnico"], 
    						   "telefono" => $_POST["editarNumeroUnoTecnico"], 
    						   "telefonoDos" => $_POST["editarTelefonoDosTecnico"],
    						   "HoraDeComidaEditada" => $_POST["HoraDeComidaEditada"],
    						   "estado" => $_POST["estado"]
    						   );

				$respuesta = ModeloTecnicos::mdlEditarTecnico($tabla, $datos);

				if ($respuesta == "ok") {
					
					echo '<script>

					swal({

						type: "success",
						title: "¡El Técnico ha sido editado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=tecnicos";

						}

					});
				

					</script>';
				}
			}
		}
	}

	/*=============================================
	ELIMINAR TECNICO
	=============================================*/

	static public function ctrEliminarTecnico(){

		if(isset($_GET["idtecnico"])){

			$tabla ="tecnicos";
			$datos = $_GET["idtecnico"];

			$respuesta = ModeloTecnicos::mdlEliminarTecnico($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El técnico ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=tecnicos";

								}
							})

				</script>';

			}		

		}

	}
}