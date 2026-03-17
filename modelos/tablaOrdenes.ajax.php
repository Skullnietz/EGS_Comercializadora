<?php
require_once "../controladores/ordenes.controlador.php";
require_once "../modelos/ordenes.modelo.php";

require_once "../controladores/tecnicos.controlador.php";
require_once "../modelos/tecnicos.modelo.php";

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";

require_once "../controladores/ventas.controlador.php";

require_once "../modelos/ventas.modelo.php";

class tablaOrdenes{
	
	  /*=============================================
  	   MOSTRAR LA TABLA DE ORDENES
  	  =============================================*/

  public function mostrarTablaOrdenes(){

  	  $item = "id";
      $valor = 22;


  	  $ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);


  	$datosJson = '{

		 

	 "data": [ ';



	foreach ($ordenes as $key => $value) {


  		/*=============================================
		TRAER MULTIMEDIA
  		=============================================*/

  		if($value["multimedia"] != null){

  			$multimedia = json_decode($value["multimedia"],true);

  			if($multimedia[0]["foto"] != ""){

  				$vistaMultimedia = "<img src='".$multimedia[0]["foto"]."' class='img-thumbnail imgTablaMultimedia' width='100px'>";

  				}else{

  					$vistaMultimedia = "<img src='http://i3.ytimg.com/vi/".$value["multimedia"]."/hqdefault.jpg' class='img-thumbnail imgTablaMultimedia' width='100px'>";

  				}


  			}else{

  				$vistaMultimedia = "<img src='vistas/img/multimedia/default/default.jpg' class='img-thumbnail imgTablaMultimedia' width='100px'>";

  		}


		$ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='".$value["id"]."' cliente='".$value["id_usuario"]."'  tecnico='".$value["id_tecnico"]."' asesor='".$value["id_Asesor"]."' empresa='".$value["id_empresa"]."' data-toggle='modal'><i class='fa fa-ticket'></i></button>";
		if ($_GET["tipoidperfil"] == "administrador") {
		
		$eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='".$value["id"]."'><i class='fa fa-times'></i></button>";
		}
		

		$observacion = "<button class='btn btn-warning btnEditarOrden' idOrden='".$value["id"]."' data-toggle='modal' data-target='#modalEditarOrden'><i class='fa fa-pencil'></i></button>";
		
		$detallesInternos = "<button class='btn btn-warning btnAgregarDetalleInterno' idOrden='".$value["id"]."' data-toggle='modal' data-target='#modalAgregarDetalle'><i class='fa fa-info-circle'></i></button>";


	  //TRAER TECNICO
      $item = "id";
      $valor = $value["id_tecnico"];

      $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

      $NombreTecnico = $tecnico["nombre"];

      //TRAER CLIENTE (USUARIO)

      $item = "id";
      $valor = $value["id_usuario"];

      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

      $NombreUsuario = $usuario["nombre"];

      //TRAER ASESOR
            
      $item = "id";
      $valor = $value["id_Asesor"];

      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

      $NombreAsesor = $asesor["nombre"];
   	
   	  //TRAER EMPRESA

      $item = "id";
      $valor = $value["id_empresa"];

      $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

      $NombreEmpresa = $respuesta["empresa"];
      		
		/*=============================================

		DEVOLVER DATOS JSON

		=============================================*/

		$datosJson	 .= '[

			      		"'.($i+1).'",

			      		"'.$NombreEmpresa.'",

			      		"ORDEN: '.$value["id"].'",

			      		"'.$NombreTecnico.'",

			      		"'.$NombreAsesor.'",

			      		"'.$NombreUsuario.'",

			      		"'.$value["descripcion"].'",

			      		"'.$value["partidaUno"].'",

			      		"'.$value["precioUno"].'",

			      		"'.$value["precioTres"].'",

			      		"'.$value["partidaDos"].'",

			      		"'.$value["partidaTres"].'",

			      		"'.$value["precioTres"].'",

			      	    "$ '.number_format($value["total"],2).'",

			      	    "'.$value["estado"].'",

			      	    "'.$vistaMultimedia.'",

			      	    "'.$value["portada"].'",

			      	    "'.$value["fecha"].'",

			      	    "'.$observacion.'",

			      	    "'.$detallesInternos.'",

			      	    "'.$eliminarOrden.'",

			      		"'.$ticket.'",

			      		"'.$eliminarVenta.'"	

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

$activar = new tablaOrdenes();

$activar -> mostrarTablaOrdenes(); 