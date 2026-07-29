<?php
require_once __DIR__ . '/../modelos/tecnicos.modelo.php';
require_once __DIR__ . '/../modelos/modelo.asesores.php';

class ControladorAdministradores{

	private static function resolverEmpresaAsignada($empresaSolicitada){

		if (isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "Super-Administrador") {
			return intval($empresaSolicitada);
		}

		return isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
	}

	private static function puedeGestionarPerfil($perfil){

		if (!$perfil || !isset($_SESSION["perfil"])) {
			return false;
		}

		if ($_SESSION["perfil"] === "Super-Administrador") {
			return true;
		}

		return $_SESSION["perfil"] === "administrador"
			&& isset($perfil["id_empresa"], $_SESSION["empresa"])
			&& intval($perfil["id_empresa"]) === intval($_SESSION["empresa"]);
	}

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

						$idOrdenDestino = isset($_POST["redirigirOrden"]) ? intval($_POST["redirigirOrden"]) : 0;
						$destinoLogin = $idOrdenDestino > 0
							? "index.php?ruta=infoOrden&idOrden=" . $idOrdenDestino
							: "index.php?ruta=inicio";

						echo '<script>window.location = ' . json_encode($destinoLogin) . ';</script>';

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

		if($item == "id" && $respuesta){
			if($respuesta["perfil"] == "tecnico"){
				$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $respuesta["email"]);
				if($tec){
					$respuesta["telefono_tec"] = $tec["telefono"];
					$respuesta["telefonoDos_tec"] = $tec["telefonoDos"];
					$respuesta["HoraDeComida_tec"] = $tec["HoraDeComida"];
					$respuesta["areratecnico_tec"] = $tec["departamento"];
				}
			} elseif($respuesta["perfil"] == "vendedor"){
				$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $respuesta["email"]);
				if($ase){
					$respuesta["numeroTelefono_ase"] = $ase["numeroTelefono"];
					$respuesta["numerodeCelular_ase"] = $ase["numerodeCelular"];
				}
			}
		}

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
					$empresaAsignada = self::resolverEmpresaAsignada(isset($_POST["empresa"]) ? $_POST["empresa"] : 0);

					$datos = array("nombre" => $_POST["nuevoNombre"],
					           "Departamento" => $_POST["Departamento"],
					           "email" => $_POST["nuevoEmail"],
					           "password" => $encriptar,
					           "perfil" => $_POST["nuevoPerfil"],	
					           "empresa"=>$empresaAsignada,
					           "foto"=>$ruta,
					           "estado" => 1);


					$respuesta = ModeloAdministradores::mdlIngresarPerfil($tabla, $datos);

				}


				if(isset($respuesta) && $respuesta == "ok"){

					if(isset($_POST["nuevoPerfil"]) && $_POST["nuevoPerfil"] == "tecnico"){
						$datosTec = array(
							"nombre" => $_POST["nuevoNombre"], 
							"correo" => $_POST["nuevoEmail"], 
							"telefono" => isset($_POST["numeroTelTecnico"]) ? $_POST["numeroTelTecnico"] : "", 
							"telefonoDos" => isset($_POST["numeroTelDosTecnico"]) ? $_POST["numeroTelDosTecnico"] : "", 
							"HoraDeComida" => isset($_POST["HoraDeComida"]) ? $_POST["HoraDeComida"] : "",
							"areratecnico" => isset($_POST["areratecnico"]) ? $_POST["areratecnico"] : "",
							"empresa" => $empresaAsignada,
							"estado" => "Activo"
						);
						ModeloTecnicos::mdlCrearTecnico("tecnicos", $datosTec);
					} else if(isset($_POST["nuevoPerfil"]) && $_POST["nuevoPerfil"] == "vendedor"){
						$datosAse = array(
							"nombre" => $_POST["nuevoNombre"], 
							"correo" => $_POST["nuevoEmail"], 
							"numeroTelefono" => isset($_POST["nuevoNumeroUno"]) ? $_POST["nuevoNumeroUno"] : "", 
							"numerodeCelular" => isset($_POST["nuevoNumeroDos"]) ? $_POST["nuevoNumeroDos"] : "", 
							"empresa" => $empresaAsignada,
							"estado" => "Activo"
						);
						ModeloAsesores::mdlIngresarAsesores("asesores", $datosAse);
					}

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

			$tabla = "administradores";
			$oldProfile = ModeloAdministradores::mdlMostrarAdministradores($tabla, "id", $_POST["idPerfil"]);

			if (!self::puedeGestionarPerfil($oldProfile)) {
				echo '<script>
					swal({
						type: "error",
						title: "No tienes permisos para editar este perfil",
						confirmButtonText: "Cerrar"
					}).then(function(){ window.location = "index.php?ruta=perfiles"; });
				</script>';
				return;
			}

			$empresaAsignada = self::resolverEmpresaAsignada(isset($_POST["empresa"]) ? $_POST["empresa"] : 0);

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"])){

				/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["fotoActual"];

				$directorio = "vistas/img/perfiles/";

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

				if($_POST["editarPassword"] != ""){

					if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])){

						$item = "email";
						$valor = $_POST["editarEmail"];

						$respuestaAadmin = ModeloAdministradores::mdlMostrarAdministradores($tabla, $item, $valor);
						
		
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
							   "id_empresa" =>$empresaAsignada
				);

				$respuesta = ModeloAdministradores::mdlEditarPerfil($tabla, $datos);

				if($respuesta == "ok"){

					$newRole = $_POST["editarPerfil"];
					$oldEmail = $oldProfile["email"];
					$oldRole = $oldProfile["perfil"];
					$newEmail = $_POST["editarEmail"];
					
					if($oldRole == "tecnico" && $newRole != "tecnico"){
						$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $oldEmail);
						if($tec){
							ModeloTecnicos::mdlActualizarTecnico("tecnicos", "estado", "Inactivo", "id", $tec["id"]);
						}
					} else if($oldRole == "vendedor" && $newRole != "vendedor"){
						$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $oldEmail);
						if($ase){
							$datosAse = array("id" => $ase["id"], "nombre" => $ase["nombre"], "correo" => $ase["correo"], "numerodeCelular" => $ase["numerodeCelular"], "numeroTelefono" => $ase["numeroTelefono"], "porcentajeComision" => $ase["porcentajeComision"], "id_empresa" => $oldProfile["id_empresa"], "estado" => "Inactivo");
							ModeloAsesores::mdlEditarAsesor("asesores", $datosAse);
						}
					}

					if($newRole == "tecnico"){
						$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $oldEmail);
						$datosTec = array(
							"nombre" => $_POST["editarNombre"], 
							"correo" => $newEmail, 
							"telefono" => isset($_POST["editarNumeroUnoTecnico"]) ? $_POST["editarNumeroUnoTecnico"] : "", 
							"telefonoDos" => isset($_POST["editarTelefonoDosTecnico"]) ? $_POST["editarTelefonoDosTecnico"] : "", 
							"HoraDeComidaEditada" => isset($_POST["HoraDeComidaEditada"]) ? $_POST["HoraDeComidaEditada"] : "",
							"departamento" => isset($_POST["editarAreratecnico"]) ? $_POST["editarAreratecnico"] : "",
							"id_empresa" => $empresaAsignada,
							"estado" => "Activo"
						);
						if($tec){
							$datosTec["id"] = $tec["id"];
							ModeloTecnicos::mdlEditarTecnico("tecnicos", $datosTec);
						} else {
							$datosTec["HoraDeComida"] = $datosTec["HoraDeComidaEditada"];
							$datosTec["areratecnico"] = isset($_POST["editarAreratecnico"]) ? $_POST["editarAreratecnico"] : "";
							$datosTec["empresa"] = $empresaAsignada;
							ModeloTecnicos::mdlCrearTecnico("tecnicos", $datosTec);
						}
					} else if($newRole == "vendedor"){
						$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $oldEmail);
						if($ase){
							$datosAse = array(
								"id" => $ase["id"],
								"nombre" => $_POST["editarNombre"],
								"correo" => $newEmail,
								"numerodeCelular" => isset($_POST["editarTelefonoDosAsesor"]) ? $_POST["editarTelefonoDosAsesor"] : "",
								"numeroTelefono" => isset($_POST["editarNumeroUnoAsesor"]) ? $_POST["editarNumeroUnoAsesor"] : "",
								"porcentajeComision" => $ase["porcentajeComision"],
								"id_empresa" => $empresaAsignada,
								"estado" => "Activo"
							);
							ModeloAsesores::mdlEditarAsesor("asesores", $datosAse);
						} else {
							$datosAse = array(
								"nombre" => $_POST["editarNombre"], 
								"correo" => $newEmail, 
								"numeroTelefono" => isset($_POST["editarNumeroUnoAsesor"]) ? $_POST["editarNumeroUnoAsesor"] : "", 
								"numerodeCelular" => isset($_POST["editarTelefonoDosAsesor"]) ? $_POST["editarTelefonoDosAsesor"] : "", 
								"empresa" => $empresaAsignada,
								"estado" => "Activo"
							);
							ModeloAsesores::mdlIngresarAsesores("asesores", $datosAse);
						}
					}

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

			$profile = ModeloAdministradores::mdlMostrarAdministradores($tabla, "id", $datos);

			if (!self::puedeGestionarPerfil($profile)) {
				echo '<script>
					swal({
						type: "error",
						title: "No tienes permisos para eliminar este perfil",
						confirmButtonText: "Cerrar"
					}).then(function(){ window.location = "index.php?ruta=perfiles"; });
				</script>';
				return;
			}

			if($profile){
				$email = $profile["email"];
				if($profile["perfil"] == "tecnico"){
					$tec = ModeloTecnicos::mdlMostrarTecnicos("tecnicos", "correo", $email);
					if($tec){
						ModeloTecnicos::mdlEliminarTecnico("tecnicos", $tec["id"]);
					}
				} else if($profile["perfil"] == "vendedor"){
					$ase = ModeloAsesores::mdlMostrarAsesoresEleg("asesores", "correo", $email);
					if($ase){
						ModeloAsesores::mdlEliminarAsesor("asesores", $ase["id"]);
					}
				}
			}

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
