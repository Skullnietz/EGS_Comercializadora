<?php

class ControladorClientes{
	/*======================================
	METODO PARA MOSTRAR ASESOR
	=======================================*/
		public function ctrMostrarAsesor($tabla, $id_perfil){
		
		$tabla = "asesores";

		$respuesta = ModeloClientes::mdlMostrarAsesor($tabla, $id_perfil);

		return $respuesta;
	}
	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function ctrMostrarClientes($item, $valor){

		$tabla = "clientesTienda";

		$respuesta = ModeloClientes::mdlMostrarClientesAjax($tabla, $item, $valor);

		return $respuesta;
	
	}


	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function ctrMostrarClientesTabla($item, $valor){

		$tabla = "clientesTienda";

		$respuesta = ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);

		return $respuesta;
	
	}
	/*=============================================
	CONTAR ORDENES DE UN CLIENTE
	=============================================*/

	static public function ctrContarOrdenesCliente($id_cliente){

		return ModeloClientes::mdlContarOrdenesCliente($id_cliente);

	}

	static public function ctrContarOrdenesClientesBulk(){

		return ModeloClientes::mdlContarOrdenesClientesBulk();

	}

	static public function ctrContarOrdenesEstadoBulk(){

		return ModeloClientes::mdlContarOrdenesEstadoBulk();

	}

	/*=============================================
	MOSTRAR CLIENTES ORDENES
	=============================================*/

	static public function ctrMostrarClientesOrdenes($item, $valor){

		$tabla = "clientesTienda";

		$respuesta = ModeloClientes::mdlMostrarClientesOrdenes($tabla, $item, $valor);

		return $respuesta;
	
	}
	/*=============================================
  	AGREGAR CLIENTE
  	=============================================*/	
	function ctrMostrarAgregarCliente(){
		
		if (isset($_POST["AgregarNombreCliente"])){
			
			$tabla = "clientesTienda";

			if ($_POST["telefonoDosCliente"] != "") {
				
				if (preg_match('/^[0-9]+$/', $_POST["telefonoDosCliente"])) {
					
					$whatsapp = $_POST["telefonoDosCliente"];

				}else{

					echo '<script>
						swal({
		      				title: "ERROR",
		      				text: "¡Los números telefónicos no deben llevar letras!",
		      				type: "error",
		      				confirmButtonText: "¡Cerrar!"
		    			});

					</script>';

					return;

				}

			}else{

				$whatsapp = "sin whatsapp";
			}

			if ($_POST["telefonoUnoCliente"] != "") {
				
				if (preg_match('/^[0-9]+$/', $_POST["telefonoUnoCliente"])) {
					
					$telefonoCasa = $_POST["telefonoUnoCliente"];

				}else{

					echo '<script>
						swal({
		      				title: "ERROR",
		      				text: "¡Los números telefónicos no deben llevar letras!",
		      				type: "error",
		      				confirmButtonText: "¡Cerrar!"
		    			});

					</script>';

					return;

				}
				
			}else{

				$telefonoCasa = "sin Telefono";
			}


			$datos = array("AgregarNombreCliente" =>$_POST["AgregarNombreCliente"],
					  	   "AgregarCorreoCliente" =>$_POST["AgregarCorreoCliente"],
					       "telefonoUnoCliente" => $telefonoCasa,
					       "telefonoDosCliente" => $whatsapp,
					       "AgreagrAsesorAlCliente" => $_POST["AgreagrAsesorAlCliente"],
					       "etiqueta" => $_POST["EtiquetaCliente"],
					       "empresa" => $_POST["id_empresa"]
					);

			$respuesta = ModeloClientes::mdlAgregarCliente($tabla,$datos);

			if ($respuesta == "ok") {
				echo '<script>

					swal({

						type: "success",
						title: "¡El cliente ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=clientes";

						}

					});

				</script>';

			}else{

				echo '<script>

				swal({

						type: "error",
						title: "¡El cliente no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=clientes";

						}

					});

				</script>';

			}
		}
	}
/*=============================================
  	AGREGAR CLIENTE
  	=============================================*/	
	function ctrMostrarAgregarClienteDnetroDeVenta(){
		
		if (isset($_POST["AgregarNombreCliente"])){
			
			$tabla = "clientesTienda";

			$datos = array("AgregarNombreCliente" =>$_POST["AgregarNombreCliente"],
					  	   "AgregarCorreoCliente" =>$_POST["AgregarCorreoCliente"],
					       "telefonoUnoCliente" => $_POST["telefonoUnoCliente"],
					       "telefonoDosCliente" => $_POST["telefonoDosCliente"],
					       "AgreagrAsesorAlCliente" => $_POST["AgreagrAsesorAlCliente"]
					);

			$respuesta = ModeloClientes::mdlAgregarCliente($tabla,$datos);

			if ($respuesta == "ok") {
				echo '<script>

					swal({

						type: "success",
						title: "¡El cliente ha sido guardado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=creararventa";

						}

					});

				</script>';

			}else{

				echo '<script>

				swal({

						type: "error",
						title: "¡El cliente no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=creararventa";

						}

					});

				</script>';

			}
		}
	}


static public function ctrEditarCliente(){

		if(isset($_POST["idCliente"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreDelCliente"]) &&
			   preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["EditarCorreoCliente"])){

				$tabla = "clientesTienda";

				$datos = array("id" => $_POST["idCliente"],
							   "editarNombreDelCliente" => $_POST["editarNombreDelCliente"],
							   "EditarCorreoCliente" => $_POST["EditarCorreoCliente"],
							   "EditarNumeroDelCliente" => $_POST["EditarNumeroDelCliente"],
							   "EditarSegundoNumeroDeTel" => $_POST["EditarSegundoNumeroDeTel"],
							   "EditarAsesorDelCliente" => $_POST["EditarAsesorDelCliente"]
							   );

				$respuesta = ModeloClientes::mdlEditarCliente($tabla,$datos);

				if($respuesta == "ok"){

					echo'<script>

					swal({
						  type: "success",
						  title: "El cliente ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
									if (result.value) {

									window.location = "index.php?ruta=clientes";

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

							window.location = "index.php?ruta=clientes";

							}
						})

			  	</script>';

			}

		}

	}

	/*=============================================
  	REPORTE CLIENTES 
  	=============================================*/	
  	static public function ctrDescargarReporteClientesTienda($valorEmpresa){
  		
  		if (isset($_GET["reporte"])){

  			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/
			$tabla = "clientesTienda";
			$item = "id_empresa";

			$clientesTienda = ModeloClientes::mdlMostrarClientes($tabla, $item, $valorEmpresa);
			
			$Name = $_GET["reporte"].'.xls';


			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");
			

			echo utf8_decode("<table border='0'> 

				<tr> 
					<td style='font-weight:bold; border:1px solid #eee;'>No.</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Nombre</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Email</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Telefono</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Telefono</td>
					<td style='font-weight:bold; border:1px solid #eee;'>Fecha</td>
				</tr>");

			foreach ($clientesTienda as $key => $valueClientesTienda) {

				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $valueClientesTienda["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];
						
				/*=============================================
				TRAER EMAIL DATOS DE CLIENTE DE TIENDA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$valueClientesTienda["id"]."</td>
									 <td style='border:1px solid #eee;'>".$valueClientesTienda["nombre"]."</td>
			 					  	 <td style='border:1px solid #eee;'>". $NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueClientesTienda["correo"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueClientesTienda["telefono"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$valueClientesTienda["telefonoDos"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$value["fecha"]."</td>
			 					  	 </tr>"); 		

			}



  		}
  	}

	/*=============================================
  	REPORTE CLIENTES CON ORDENES ENTREGADAS
	=============================================*/

	public function ctrDescargarReporteClientesTiendaENT($valorEmpresa){
	
		if (isset($_GET["reportes"])) {

			$tabla = "ordenes";
			
			/*=============================================
			TRAEMOS ORDENES ENTREGADAS
			=============================================*/
			$estado = "Entregado (Ent)";
			$tabalaOrdenes = "ordenes";

			$OrdenesFecha = ModeloOrdenes::mdlMostrarOrdenesPorEstadoyEmpresa($tabalaOrdenes, $estado, $valorEmpresa);


			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/

			$Name = $_GET["reportes"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");

			echo utf8_decode("<table border='0'> 

					<tr> 
						<td style='font-weight:bold; border:1px solid #eee;'>Orden</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Descripcion</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Asesor</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Cliente</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Telefono #1</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Telefono #2</td>
						<td style='font-weight:bold; border:1px solid #eee;'>Correo</td>

					</tr>");


			foreach ($OrdenesFecha as $key => $value) {
				
			
				//TRAER ASESOR
                    
	              $item = "id";
	              $valor = $value["id_Asesor"];

	              $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);
					
				  $NombreAsesor = $asesor["nombre"];

             	//TRAER CLIENTE (USUARIO)

                $item = "id";
                $valor = $value["id_usuario"];

                $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

                $NombreUsuario = $usuario["nombre"];
                $telefono = $usuario["telefono"];
                $telefonoDos = $usuario["telefonoDos"];
                $correo = $usuario["correo"];

				/*=============================================
				TRAER EMAIL DATOS DE COMPRA
				=============================================*/

					echo utf8_decode("</td>
									 <td style='border:1px solid #eee;'>".$value["id"]."</td>
									 <td style='border:1px solid #eee;'>".$value["partidaUno"]."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreAsesor."</td>
			 					  	 <td style='border:1px solid #eee;'>".$NombreUsuario."</td>
			 					  	 <td style='border:1px solid #eee;'>".$telefono."</td>
			 					  	 <td style='border:1px solid #eee;'>".$telefonoDos."</td>
			 					  	 <td style='border:1px solid #eee;'>".$correo."</td>
			 					  	 </tr>"); 		

			}
			   



				echo utf8_decode("</table>

					");
		}

	}
}

