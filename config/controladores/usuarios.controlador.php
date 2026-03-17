<?php

class ControladorUsuarios{

	/*=============================================
	MOSTRAR TOTAL USUARIOS
	=============================================*/

	static public function ctrMostrarTotalUsuarios($orden){

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::mdlMostrarTotalUsuarios($tabla, $orden);

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

									window.location = "usuarios";

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

							window.location = "usuarios";

							}
						})

			  	</script>';
				
			}
		}



	}

}