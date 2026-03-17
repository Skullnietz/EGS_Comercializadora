<?php
require_once "../controladores/pedidos.controlador.php";
require_once "../modelos/pedidos.modelo.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../controladores/empresas.controlador.php";
require_once "../modelos/empresas.modelo.php";
require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";

class TablaOrdenes{
  /*=============================================
  MOSTRAR LA TABLA DE VENTAS
  =============================================*/
  public function mostrarTabla(){	

	

  	if ($_GET["perfil"] == "Super-Administrador") {
  		
		$item = null;
 		$valor = null;

  		$pedidos = ControladorPedidos::ctrMostrarPedido($item,$valor);
  	}else{

  		$empresaDelPerfil = $_GET["empresa"];
		$item = "id_empresa";
 		$valor = $empresaDelPerfil;

  		$pedidos = ControladorPedidos::ctrMostrarPedidoEmpresas($item,$valor);
  	}

  	$datosJson = '{

	 "data": [ ';

	for($i = 0; $i < count($pedidos); $i++){

		if ($_GET["perfil"] == "administrador" || $_GET["perfil"] == "editor" || $_GET["perfil"] == "vendedor") {
		
				$detallesPedido = "<button class='btn btn-warning btnAgregarDetallePedido' idPedido='".$pedidos[$i]["id"]."' data-toggle='modal' data-target='#modalEditarPedido'><i class='fas fa-edit'></i></button>";

		//if($_GET["perfil"] == "administrador"){

		//}

		$acciones = "<div class='btn-group'><button class='btn btn-danger btnEliminarPedido' idPedido='".$pedidos[$i]["id"]."'><i class='fa fa-times'></i></button><button class='btn btn-warning btnImprimirTicketPedido' idPedido='".$pedidos[$i]["id"]."' cliente='".$pedidos[$i]["id_cliente"]."'  asesor='".$pedidos[$i]["id_Asesor"]."' empresa='".$pedidos[$i]["id_empresa"]."' data-toggle='modal'><i class='fas fa-ticket-alt'></i></button></div>";

		}

      $item = "id";
      $valor = $pedidos[$i]["id_empresa"];

      $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

      $NombreEmpresa = $respuesta["empresa"];

       //TRAER CLIENTE (USUARIO)

      $item = "id";
      $valor = $pedidos[$i]["id_cliente"];

      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

      $NombreUsuario = $usuario["nombre"];

	  $InfoPedido = "<button class='btn btn-warning btnVerInfoPedido' idPedido='".$pedidos[$i]["id"]."' cliente='".$pedidos[$i]["id_cliente"]."' asesor='".$pedidos[$i]["id_Asesor"]."' empresa='".$pedidos[$i]["id_empresa"]."' data-toggle='modal'><i class='fas fa-edit'></i></button>";
         		
		/*=============================================
		DEVOLVER DATOS JSON
		=============================================*/

		$datosJson	 .= '[

			      		"'.($i+1).'",

			      		"'.$NombreEmpresa.'",

			      		"<b> PEDIDO: '.$pedidos[$i]["id"].'</b>",

			      		"'.$NombreUsuario.'",

			      		"<b>'.$pedidos[$i]["estado"].'</b>",
			      		
			      		"'.$pedidos[$i]["total"].'",

			      		"'.$pedidos[$i]["metodo"].'",

			      	   	"'.$acciones.'",

			      	   	"'.$InfoPedido.'"

			      		],';

	} 



	$datosJson = substr($datosJson, 0, -1);



	$datosJson.=  ']

		  

	}'; 

  	

  	echo $datosJson;	



  }



}



/*=============================================

ACTIVAR TABLA DE pedidos

=============================================*/ 

$activar = new TablaOrdenes();

$activar -> mostrarTabla(); 



