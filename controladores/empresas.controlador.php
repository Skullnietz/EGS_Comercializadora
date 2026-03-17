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
  	MOSTRAR DATOS DE EMPRESA PARA REPORTE
  	=============================================*/	

  	static public function ctrMostrarEmpresasParaReportes($item3, $valor3){
  		
  		$tabla = "empresa";

  		$respuesta = ModeloEmpresas::mdlMostrarEmpresasParaReportes($tabla, $item3, $valor3);

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
					       "telefonoDeEmpresa" => $_POST["telefonoDeEmpresa"],
					       "telefonoDosDeEmpresa" => $_POST["telefonoDosDeEmpresa"],
					       "direccion" => $_POST["direccion"],
					       "Horario" => $_POST["Horario"], 
					       "Facebook" => $_POST["Facebook"],
					       "Sitio" => $_POST["Sitio"]
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
						
							window.location = "index.php?ruta=empresas";

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
						
							window.location = "index.php?ruta=empresa";

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
							   "editarNumeroUnoDeEmpresa" => $_POST["editarNumeroUnoDeEmpresa"],
							   "telefonoDosDeEmpresaEditado" => $_POST["telefonoDosDeEmpresaEditado"],
							   "direccion" => $_POST["EditarDireccion"],
							   "HoraEditada" => $_POST["HoraEditada"],
							   "FacebookEditado" => $_POST["FacebookEditado"],
							   "SitioEditado" => $_POST["SitioEditado"]
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

									window.location = "index.php?ruta=empresas";

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

							window.location = "index.php?ruta=empresas";

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

								window.location = "index.php?ruta=empresas";

								}
							})

				</script>';

			}		

		}

	}

}