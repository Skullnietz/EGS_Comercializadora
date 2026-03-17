<?php
class ControladorEmpresas{
	

	/*=============================================
  	MOSTRAR EMPRESAS
  	=============================================*/	

  	function ctrMostrarEmpresas($item, $valor){
  		
  		$tabla = "empresa";

  		$respuesta = ModeloEmpresas::mdlMostrarEmpresas($tabla, $item, $valor);

  		return $respuesta;
  	}

  	/*=============================================
  	MOSTRAR DATOS DE EMPRESA PARA EDITAR
  	=============================================*/	

  	static public function ctrMostrarEmpresasParaEditar($item, $valor){
  		
  		$tabla = "empresa";

  		$respuesta = ModeloEmpresas::mdlMostrarEmpresasParaEditar($tabla, $item, $valor);

  		return $respuesta;
  	}


	/*=============================================
  	AGREGAR EMPRESA
  	=============================================*/	
	function ctrCrearEmpresa(){
		
		if (isset($_POST["empresa"])){
			
			$tabla = "empresa";

			$datos = array("empresa" =>$_POST["empresa"],
					  	   "correo" =>$_POST["correo"],
					       "telefono" => $_POST["telefono"],
					       "telefonoDos" => $_POST["telefonoDos"],
					       "direccion" => $_POST["direccion"]
					);

			$respuesta = ModeloEmpresas::mdlcrearEmpresas($tabla,$datos);

			if ($respuesta == "ok") {
				echo '<script>

					swal({

						type: "success",
						title: "¡La empresa ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "empresas";

						}

					});

				</script>';

			}else{

				echo '<script>

				swal({

						type: "error",
						title: "¡La empresa no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "empresa";

						}

					});

				</script>';

			}
		}
	}

	static public function ctrEditarEmpresa(){

		if(isset($_POST["idEmpresa"])){


				$tabla = "empresa";

				$datos = array("id" => $_POST["idEmpresa"],
							   "empresa" => $_POST["editarNombreEmpresa"],
							   "correo" => $_POST["editarCorreoEmpresa"],
							   "telefono" => $_POST["editarNumeroUno"],
							   "telefonoDos" => $_POST["editarTelefonoDos"],
							   "direccion" => $_POST["EditarDireccion"],
							   );

				$respuesta = ModeloEmpresas::mdlEditarEmpresa($tabla,$datos);

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El asesor ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
									if (result.value) {

									window.location = "empresas";

									}
								})

					</script>';



			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {

							window.location = "empresas";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
	ELIMINAR EMPRESA
	=============================================*/

	static public function ctrEliminarEmpresa(){

		if(isset($_GET["idEmpresa"])){

			$tabla ="empresa";
			$datos = $_GET["idEmpresa"];

			$respuesta = ModeloEmpresas::mdlEliminarEmpresa($tabla, $datos);

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

								window.location = "empresas";

								}
							})

				</script>';

			}		

		}

	}

}