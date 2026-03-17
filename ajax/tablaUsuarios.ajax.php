<?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";
class TablaUsuarios{

 	/*=============================================
  	MOSTRAR LA TABLA DE USUARIOS
  	=============================================*/ 

	public function mostrarTabla(){	

		$empresaDelPerfil = $_GET["empresa"];
		
		$item = "id_empresa";
 		$valor = $empresaDelPerfil;
 		var_dump($_GET["empresa"]);
 		$usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

 		$item1 = "id";
        $valor1 = $usuarios[$i]["id_Asesor"];
        $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item1,$valor1);

      	$nombre_asesor = $asesor["nombre"];


 		$datosJson = '{
		 
	 	"data": [ ';

	 	for($i = 0; $i < count($usuarios); $i++){

	 		/*=============================================
			TRAER FOTO USUARIO
			=============================================*/

			if($usuarios[$i]["foto"] != "" ){

				$foto = "<img loading='lazy' class='img-circle' src='".$usuarios[$i]["foto"]."' width='60px'>";

			}else{

				$foto = "<img loading='lazy' class='img-circle' src='vistas/img/usuarios/default/anonymous.png' width='60px'>";
			}

			/*=============================================
  			REVISAR ESTADO
  			=============================================*/

  			if($usuarios[$i]["modo"] == "directo"){

	  			if($usuarios[$i]["verificacion"] == 1){

	  				$colorEstado = "btn-danger";
	  				$textoEstado = "Desactivado";
	  				$estadoUsuario = 0;

	  			}else{

	  				$colorEstado = "btn-success";
	  				$textoEstado = "Activado";
	  				$estadoUsuario = 1;

	  			}

	  			$estado = "<button class='btn btn-xs btnActivar ".$colorEstado."' idUsuario='". $usuarios[$i]["id"]."' estadoUsuario='".$estadoUsuario."'>".$textoEstado."</button>";

	  		}else{


	  		}

			$acciones = "<div class='btn-group'><button class='btn btn-warning btnEditarUsuario' idUsuario='".$usuarios[$i]["id"]."' data-toggle='modal' data-target='#modalEditarUsuario'><i class='fa fa-pencil'></i></button></div>";

	 		/*=============================================
			DEVOLVER DATOS JSON
			=============================================*/

			$datosJson	 .= '[
				      "'.($i+1).'",
				      "'.$usuarios[$i]["nombre"].'",
				      "'.$nombre_asesor.'",
				      "'.$usuarios[$i]["correo"].'",
				      "'.$usuarios[$i]["modo"].'",
				      "'.$foto.'",
				      "'.$estado.'",
				      "'.$acciones.'",
				      "'.$usuarios[$i]["fecha"].'"    
				    ],';

	 	}

	 	$datosJson = substr($datosJson, 0, -1);

		$datosJson.=  ']
			  
		}'; 

		echo $datosJson;

 	}

}

/*=============================================
ACTIVAR TABLA DE VENTAS
=============================================*/ 
$activar = new TablaUsuarios();
$activar -> mostrarTabla();