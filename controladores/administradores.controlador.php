<?php

class ControladorAdministradores{

	/*=============================================
	INGRESO DE ADMINISTRADOR
	=============================================*/

	public function ctrIngresoAdministrador(){

		if(isset($_POST["ingEmail"])){

			if(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["ingEmail"]) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])){

			   
				
				$encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
				
			   
				$tabla = "administradores";
				$item = "email";
				$valor = $_POST["ingEmail"];

				$respuesta = ModeloAdministradores::mdlMostrarAdministradores($tabla, $item, $valor);

				if($respuesta["email"] == $_POST["ingEmail"] && $respuesta["password"] == $encriptar){
					//COLOCAR EN EL IF AND $respuesta["sesion"] == 0
					if($respuesta["estado"] == 1){

						$_SESSION["validarSesionBackend"] = "ok";
						$_SESSION["id"] = $respuesta["id"];
						$_SESSION["nombre"] = $respuesta["nombre"];
						$_SESSION["foto"] = $respuesta["foto"];
						$_SESSION["email"] = $respuesta["email"];
						$_SESSION["password"] = $respuesta["password"];
						$_SESSION["perfil"] = $respuesta["perfil"];
						$_SESSION["empresa"] = $respuesta["id_empresa"];

						echo '<script>

							window.location = "index.php?ruta=inicio";

						</script>';

						$tabla="administradores";
						$sesionActiva=1;
						$activarsesion = ModeloAdministradores::mdlActivarSesion($tabla,$_SESSION["id"],$sesionActiva);

						$tabaDos="INICIOS_DE_SESION";
					
						$navegador =$_SERVER["HTTP_USER_AGENT"];
						$ip = $_SERVER["REMOTE_ADDR"];
						$datosInicioDeSesion = array("navegador"=>$navegador,
								 "ip" => $ip,
								 "id_usuario" =>$_SESSION["id"]
						);
						$registraInico = ModeloAdministradores::mdlRegistrarInicio($tabaDos, $datosInicioDeSesion);

					}else{

						echo '<br>
						<div class="alert alert-warning">Este usuario aún no está activado o ya has iniciado sesión en otro dispositivo</div>';	

					}

				}else{

					echo '<br>
					<div class="alert alert-danger">Error al ingresar vuelva a intentarlo</div>';

				}


			}

		}

	}

	/*=============================================
	MOSTRAR ADMINISTRADORES
	=============================================*/

	static public function ctrMostrarAdministradores($item, $valor){

		$tabla = "administradores";

		$respuesta = ModeloAdministradores::MdlMostrarAdministradores($tabla, $item, $valor);

		return $respuesta;
	}
	/*=============================================
	MOSTRAR ADMINISTRADORES POR EMPRESA
	=============================================*/

	static public function ctrlMostrarAdministradoresPorEmpresa($item, $valor){

		$tabla = "administradores";

		$respuesta = ModeloAdministradores::MdlMostrarAdministradoresPorEmpresa($tabla, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR ADMINISTRADORES POR EMPRESA Y ROL
	=============================================*/

	static public function ctrlMostrarAdministradoresPorEmpresaRol($item, $valor,$itemDos, $valorDos){

		$tabla = "administradores";

		$respuesta = ModeloAdministradores::MdlMostrarAdministradoresPorEmpresaRol($tabla, $item, $valor,$itemDos, $valorDos);

		return $respuesta;
	}
	/*=============================================
	REGISTRO DE PERFIL
	=============================================*/

	static public function ctrCrearPerfil(){

		if(isset($_POST["nuevoPerfil"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) && preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoPassword"])){

			   	/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = "";

				if(isset($_FILES["nuevaFoto"]["tmp_name"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;


					/*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

					if($_FILES["nuevaFoto"]["type"] == "image/jpeg"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/perfiles/".$aleatorio.".jpg";

						$origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["nuevaFoto"]["type"] == "image/png"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/perfiles/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);

					}

				}

				$tabla = "administradores";

				$lista_denegar = array("admin","Admin","password", "Password",1234,"root","superuser");

				if (in_array($_POST["nuevoPassword"],$lista_denegar)) {
					
					echo '<br><div class="alert alert-warning">Contraseña no permitida</div>';	
				}else{

					$encriptar = crypt($_POST["nuevoPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

					$datos = array("nombre" => $_POST["nuevoNombre"],
					           "Departamento" => $_POST["Departamento"],
					           "email" => $_POST["nuevoEmail"],
					           "password" => $encriptar,
					           "perfil" => $_POST["nuevoPerfil"],	
					           "empresa"=>$_POST["empresa"],		       
					           "foto"=>$ruta,
					           "estado" => 1);

				}

				
				$respuesta = ModeloAdministradores::mdlIngresarPerfil($tabla, $datos);
				
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El perfil ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=perfiles";

						}

					});
				

					</script>';


				}	


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El perfil no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=perfiles";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	EDITAR PERFIL
	=============================================*/

	static public function ctrEditarPerfil(){

		if(isset($_POST["idPerfil"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"])){

				/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["fotoActual"];

				if(isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;

					/*=============================================
					PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
					=============================================*/

					if(!empty($_POST["fotoActual"])){

						unlink($_POST["fotoActual"]);

					}else{

						mkdir($directorio, 0755);

					}	

					/*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

					if($_FILES["editarFoto"]["type"] == "image/jpeg"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/perfiles/".$aleatorio.".jpg";

						$origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["editarFoto"]["type"] == "image/png"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/perfiles/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);

					}

				}

				$tabla = "administradores";

				if($_POST["editarPassword"] != ""){

					if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])){

						$item = "email";
						$valor = $_POST["editarEmail"];

						$respuestaAadmin = ModeloAdministradores::mdlMostrarAdministradores($tabla, $item, $valor);
						foreach ($respuestaAadmin as $key => $value1) {
							
							 
							
						}
						
						var_dump($value1[5]);
						
						//if (password_verify($_POST["editarPassword"], $value1["password"])) {

							$encriptar = crypt($_POST["editarPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

						//}else{

							//echo '<br><div class="alert alert-warning">Contraseña ya usada anteriormente</div>';

						//}

					}else{

						echo'<script>

								swal({
									  type: "error",
									  title: "¡La contraseña no puede ir vacía o llevar caracteres especiales!",
									  showConfirmButton: true,
									  confirmButtonText: "Cerrar"
									  }).then(function(result) {
										if (result.value) {

										window.location = "index.php?ruta=perfiles";

										}
									})

						  	</script>';

					}

				}else{

					$encriptar = $_POST["passwordActual"];

				}

				$datos = array("id" => $_POST["idPerfil"],
							   "nombre" => $_POST["editarNombre"],
							   "Departamento" => $_POST["Departamento"],
							   "email" => $_POST["editarEmail"],
							   "password" => $encriptar,
							   "perfil" => $_POST["editarPerfil"],
							   "foto" => $ruta,
							   "id_empresa" =>$_POST["empresa"]
				);

				$respuesta = ModeloAdministradores::mdlEditarPerfil($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El perfil ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
									if (result.value) {

									window.location = "index.php?ruta=perfiles";

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

							window.location = "index.php?ruta=perfiles";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
	ELIMINAR PERFIL
	=============================================*/

	static public function ctrEliminarPerfil(){

		if(isset($_GET["idPerfil"])){

			$tabla ="administradores";
			$datos = $_GET["idPerfil"];

			if($_GET["fotoPerfil"] != ""){

				unlink($_GET["fotoPerfil"]);
			
			}

			$respuesta = ModeloAdministradores::mdlEliminarPerfil($tabla, $datos);

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

								window.location = "index.php?ruta=perfiles";

								}
							})

				</script>';

			}		

		}

	}

	/*=============================================
	MOSTRAR ADMINISTRADORES ACTIVOS ENVENTAS
	=============================================*/

	static public function ctrMostrarAdministradoresActvisoEnVentas(){

		$tabla = "administradores";

		$respuesta = ModeloAdministradores::MdlMostrarAdministradoresActivosEnVentas($tabla);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR ADMINISTRADORES ACTIVOS COMO TECNICO
	=============================================*/

	static public function ctrMostrarTecnicosActivos(){

		$tabla = "administradores";

		$respuesta = ModeloAdministradores::MdlMostrarTecnicosActivos($tabla);

		return $respuesta;
	}


	
	
	
}