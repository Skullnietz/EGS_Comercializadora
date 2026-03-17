<?php

class ControladorUsuarios{

	/*=============================================
	MOSTRAR TOTAL USUARIOS
	=============================================*/

	static public function ctrMostrarTotalUsuarios($orden){

		$tabla = "clientesTienda";

		$respuesta = ModeloUsuarios::mdlMostrarTotalUsuarios($tabla, $orden);

		return $respuesta;

	}
	
	/*=============================================
	MOSTRAR TOTAL USUARIOS
	=============================================*/

	static public function ctrMostrarTotalUsuariosMes($orden){

		$tabla = "clientesTienda";

		$respuesta = ModeloUsuarios::mdlMostrarTotalUsuariosMes($tabla, $orden);

		return $respuesta;

	}
	
		/*=============================================
	MOSTRAR TOTAL USUARIOS POR ASESOR
	=============================================*/

	static public function ctrMostrarTotalUsuariosMesAsesor($orden, $idAsesor){

		$tabla = "clientesTienda";

		$respuesta = ModeloUsuarios::mdlMostrarTotalUsuariosMesAsesor($tabla, $orden, $idAsesor);

		return $respuesta;

	}


	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function ctrMostrarUsuarios($item, $valor){

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

		return $respuesta;
	
	}
	
	/*=====================================
	Mostrar compras 
	======================================*/

	static public function ctrMostrarCompras($item, $valor){
	
		$tabla = "compras";

		$respuesta = ModeloUsuarios::mdlMostrarCompras($tabla, $item, $valor);

		return $respuesta;
	}
	
	/*=============================================
	MOSTRAR USUARIOS PARA EDICION
	=============================================*/

	static public function ctrMostrarUsuariosEdicion($item, $valor){

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::mdlMostrarAdministradoresEdicion($tabla, $item, $valor);

		return $respuesta;
	
	}
	
	/*=============================================
	EDITAR ASESOR
	=============================================*/

	static public function ctrEditarAsesorU(){
	
		if(isset($_POST["editarAsesor"])){
			

			$tabla = "usuarios";

			$datos = array('id' => $_POST["idUsuario"],
							'asesor' => $_POST["editarAsesor"] );


			$respuesta = ModeloUsuarios::mdlEditarAsesorU($tabla, $datos);


			if ($respuesta == "ok"){
				

				echo '<script>

					swal({
						  type: "success",
						  title: "Asesor editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
									if (result.value) {

									window.location = "index.php?ruta=usuarios";

									}
								})

					</script>';
			}else{

				echo'<script>

					swal({
						  type: "error",
						  title: "¡El Asesor no se pudo editar correctamente!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {

							window.location = "index.php?ruta=usuarios";

							}
						})

			  	</script>';
				
			}
		}

	}

	/*=============================================
  	AGREGAR EMPRESA
  	=============================================*/	
	function ctrAgregarUsuarioDirecto(){
		
		if (isset($_POST["AgregarNombre"])){
			
			$tabla = "usuarios";

			$datos = array("nombre" =>$_POST["AgregarCorreo"],
					  	   "correo" =>$_POST["AgregarCorreo"],
					       "telefono" => $_POST["telefono"],
					       "modo" => $_POST["directo"],
					       "asesor" => $_POST["AgreagrAsesor"]
					);

			$respuesta = ModeloUsuarios::mdlAgregarUsuarioDirecto($tabla,$datos);

			if ($respuesta == "ok") {
				echo '<script>

					swal({

						type: "success",
						title: "¡El usuario ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "usuarios";

						}

					});

				</script>';

			}else{

				echo '<script>

				swal({

						type: "error",
						title: "¡El usuario no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=usuarios";

						}

					});

				</script>';

			}
		}
	}

}