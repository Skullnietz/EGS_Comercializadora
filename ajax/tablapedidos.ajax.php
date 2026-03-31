<?php
session_start();

if (!isset($_SESSION["perfil"]) || $_SESSION["perfil"] == "tecnico") {
	echo '{"data":[]}';
	exit;
}

require_once "../controladores/pedidos.controlador.php";
require_once "../modelos/pedidos.modelo.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../config/clienteBadges.helper.php";
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

	if (!isset($_GET["perfil"]) || $_GET["perfil"] == "tecnico") {
		echo '{"data":[]}';
		return;
	}

	

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

		if ($_GET["perfil"] == "administrador" || $_GET["perfil"] == "editor" || $_GET["perfil"] == "vendedor" || $_GET["perfil"] == "Super-Administrador") {
		
			$detallesPedido = "<button class='btn btn-warning btnAgregarDetallePedido' idPedido='".$pedidos[$i]["id"]."' data-toggle='modal' data-target='#modalEditarPedido'><i class='fas fa-edit'></i></button>";

			// Determinar estado con badge
			$estado = $pedidos[$i]["estado"];
			$estadoClass = 'badge-otro';
			if (strpos(strtolower($estado), 'pendiente') !== false) $estadoClass = 'badge-pedido-pendiente';
			elseif (strpos(strtolower($estado), 'adquirido') !== false) $estadoClass = 'badge-adquirido';
			elseif (strpos(strtolower($estado), 'almacen') !== false) $estadoClass = 'badge-almacen';
			elseif (strpos(strtolower($estado), 'asesor') !== false) $estadoClass = 'badge-asesor';
			elseif (strpos(strtolower($estado), 'pagado') !== false) $estadoClass = 'badge-pagado';
			elseif (strpos(strtolower($estado), 'credito') !== false) $estadoClass = 'badge-credito';

			$estadoHtml = "<span class='badge " . $estadoClass . "'>" . htmlspecialchars($estado) . "</span>";

			$acciones = "<div class='btn-group' style='gap:4px;'><button class='btn btn-sm btn-danger btnEliminarPedido' idPedido='".$pedidos[$i]["id"]."' title='Eliminar' style='padding:6px 10px;'><i class='fa fa-trash'></i></button><button class='btn btn-sm btn-warning btnImprimirTicketPedido' idPedido='".$pedidos[$i]["id"]."' cliente='".$pedidos[$i]["id_cliente"]."'  asesor='".$pedidos[$i]["id_Asesor"]."' empresa='".$pedidos[$i]["id_empresa"]."' title='Imprimir' style='padding:6px 10px;'><i class='fas fa-print'></i></button></div>";

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
      $__bh = ClienteBadgesHelper::getInstance();
      $NombreUsuarioConBadge = $__bh->renderWithName($NombreUsuario, intval($pedidos[$i]["id_cliente"]));

	  // Usar campos solicitados por negocio: fechaDePedido y fechaEntrega
	  $fechaDePedidoRaw = isset($pedidos[$i]["fechaDePedido"]) ? trim((string)$pedidos[$i]["fechaDePedido"]) : '';
	  $fechaEntregaRaw = isset($pedidos[$i]["fechaEntrega"]) ? trim((string)$pedidos[$i]["fechaEntrega"]) : '';
	  $fechaRegistro = ($fechaDePedidoRaw !== '' && $fechaDePedidoRaw !== '0000-00-00' && $fechaDePedidoRaw !== '0000-00-00 00:00:00')
	    ? date('d/m/Y H:i', strtotime($fechaDePedidoRaw))
	    : '—';
	  $fechaModificacion = ($fechaEntregaRaw !== '' && $fechaEntregaRaw !== '0000-00-00' && $fechaEntregaRaw !== '0000-00-00 00:00:00')
	    ? date('d/m/Y H:i', strtotime($fechaEntregaRaw))
	    : '—';

	  $InfoPedido = "<button class='btn btn-sm btn-info btnVerInfoPedido' idPedido='".$pedidos[$i]["id"]."' cliente='".$pedidos[$i]["id_cliente"]."' asesor='".$pedidos[$i]["id_Asesor"]."' empresa='".$pedidos[$i]["id_empresa"]."' title='Ver detalles' style='padding:6px 10px;'><i class='fas fa-eye'></i></button>";
         		
		/*=============================================
		DEVOLVER DATOS JSON MEJORADO
		=============================================*/

		$__NomUsuBadgeEscaped = str_replace(["'", "\n", "\r"], ["\\'", "", ""], $NombreUsuarioConBadge);

		$datosJson	 .= '[

			      		"'.($i+1).'",

			      		"'.$NombreEmpresa.'",

			      		"<b>'.$pedidos[$i]["id"].'</b>",

			      		"'.$__NomUsuBadgeEscaped.'",

			      		"'.$estadoHtml.'",
			      		
			      		"$ '.number_format($pedidos[$i]["total"], 2).'",

			      		"'.(!empty($pedidos[$i]["id_orden"]) && $pedidos[$i]["id_orden"] != '0' ? '<span style=\"color:#6366f1;font-weight:600\">#'.$pedidos[$i]["id_orden"].'</span>' : '<span style=\"color:#94a3b8\">Sin orden</span>').'",

			      		"'.$fechaRegistro.'",

			      		"'.$fechaModificacion.'",

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



