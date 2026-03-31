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

class tablaOrdenes
{

	/*=============================================
	   MOSTRAR LA TABLA DE ORDENES
	  =============================================*/

	public function mostrarTablaOrdenes()
	{

		$item = null;
		$valor = null;
		$ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

		// Bulk: calificación y recogida de clientes
		$_bo_ordenesMap = []; $_bo_estadoMap = []; $_bo_recogidaMap = [];
		try { $_bo_ordenesMap = ControladorClientes::ctrContarOrdenesClientesBulk(); } catch(Exception $e) {}
		try { $_bo_estadoMap = ControladorClientes::ctrContarOrdenesEstadoBulk(); } catch(Exception $e) {}
		try { $_bo_recogidaMap = ControladorClientes::ctrPromedioRecogidaBulk(); } catch(Exception $e) {}


		$datosJson = '{

		 

	 "data": [ ';



		for ($i = 0; $i < count($ordenes); $i++) {


			//if ($_GET["perfil"] == "administrador") {

			//TRAER EMPRESA

			$item = "id";
			$valor = $ordenes[$i]["id_empresa"];

			$respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item, $valor);

			$NombreEmpresa = $respuesta["empresa"];

			//TRAER TECNICO
			$item = "id";
			$valor = $ordenes[$i]["id_tecnico"];

			$tecnico = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

			$NombreTecnico = $tecnico["nombre"];

			//TRAER ASESOR

			$item = "id";
			$valor = $ordenes[$i]["id_Asesor"];

			$asesor = Controladorasesores::ctrMostrarAsesoresEleg($item, $valor);

			$NombreAsesor = $asesor["nombre"];

			//TRAER CLIENTE (USUARIO)

			$item = "id";
			$valor = $ordenes[$i]["id_usuario"];

			$usuario = ControladorClientes::ctrMostrarClientes($item, $valor);

			$NombreUsuario = $usuario["nombre"];

			// ── Badges de calificación y recogida (solo iconos) ──
			$_cliId = intval($ordenes[$i]["id_usuario"]);
			$_cliOrd = isset($_bo_ordenesMap[$_cliId]) ? $_bo_ordenesMap[$_cliId] : 0;
			$_cliEnt = isset($_bo_estadoMap[$_cliId]) ? $_bo_estadoMap[$_cliId]["entregadas"] : 0;
			$_cliCan = isset($_bo_estadoMap[$_cliId]) ? $_bo_estadoMap[$_cliId]["canceladas"] : 0;
			$_cliBadges = "";
			if ($_cliOrd >= 3 && ($_cliEnt + $_cliCan) > 0) {
				$_r = $_cliEnt / ($_cliEnt + $_cliCan) * 100;
				if ($_r >= 90)      { $_cIco = "fa-star";         $_cCol = "#16a34a"; $_cBg = "#f0fdf4"; }
				elseif ($_r >= 70)  { $_cIco = "fa-thumbs-up";    $_cCol = "#2563eb"; $_cBg = "#eff6ff"; }
				elseif ($_r >= 50)  { $_cIco = "fa-minus-circle"; $_cCol = "#d97706"; $_cBg = "#fffbeb"; }
				else                { $_cIco = "fa-thumbs-down";  $_cCol = "#dc2626"; $_cBg = "#fef2f2"; }
				$_cliBadges .= "<span style='display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:{$_cBg};margin-left:4px;' title='Calif: " . round($_r) . "%'><i class='fas {$_cIco}' style='font-size:10px;color:{$_cCol}'></i></span>";
			}
			if (isset($_bo_recogidaMap[$_cliId])) {
				$_d = $_bo_recogidaMap[$_cliId];
				if ($_d <= 7)       { $_rIco = "fa-bolt";           $_rCol = "#16a34a"; $_rBg = "#f0fdf4"; }
				elseif ($_d <= 14)  { $_rIco = "fa-clock";          $_rCol = "#2563eb"; $_rBg = "#eff6ff"; }
				elseif ($_d <= 30)  { $_rIco = "fa-hourglass-half"; $_rCol = "#d97706"; $_rBg = "#fffbeb"; }
				else                { $_rIco = "fa-hourglass-end";  $_rCol = "#dc2626"; $_rBg = "#fef2f2"; }
				$_cliBadges .= "<span style='display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:{$_rBg};margin-left:3px;' title='Recoge: ~{$_d} días'><i class='fas {$_rIco}' style='font-size:10px;color:{$_rCol}'></i></span>";
			}
			if ($_cliBadges) {
				$NombreUsuario .= $_cliBadges;
			}


			$InfoOrdenes = "<button class='btn btn-warning btnVerInfoOrden' idOrden='" . $ordenes[$i]["id"] . "' cliente='" . $ordenes[$i]["id_usuario"] . "'  tecnico='" . $ordenes[$i]["id_tecnico"] . "' asesor='" . $ordenes[$i]["id_Asesor"] . "' empresa='" . $ordenes[$i]["id_empresa"] . "' pedido='" . $ordenes[$i]["id_pedido"] . "' data-toggle='modal'><i class='fa fa-pencil'></button>";

			$eliminarOrden = "<button class='btn btn-danger btnEliminarorden' idOrden='" . $ordenes[$i]["id"] . "'><i class='fa fa-times'></i></button>";

			$ticket = "<button class='btn btn-warning btnImprimirorden' idOrden='" . $ordenes[$i]["id"] . "' cliente='" . $ordenes[$i]["id_usuario"] . "'  tecnico='" . $ordenes[$i]["id_tecnico"] . "' asesor='" . $ordenes[$i]["id_Asesor"] . "' empresa='" . $ordenes[$i]["id_empresa"] . "' data-toggle='modal'><i class='fa fa-ticket'></i></button>";

			$pedido = "<button class='btn btn-info' data-toggle='modal' data-target='#modalAsignarPedido'><i class='fa fa-sort'></i></button>";

			$fechaDeIngreso = $ordenes[$i]["fecha_ingreso"];

			//}




			/*=============================================
			DEVOLVER DATOS JSON
			AQUÍ HICE UNA CORRECCIÓN BIEN PERRONA DE TABLAS
			=============================================*/

			$datosJson .= '[

			      		"' . ($i + 1) . '",

			      		"' . $NombreEmpresa . '",

			      		"<h5><b>ORDEN: ' . $ordenes[$i]["id"] . '<b></h5>",

			      		"' . $NombreTecnico . '",

			      		"' . $NombreAsesor . '",

			      		"' . $NombreUsuario . '",

			      	    "$ ' . number_format($ordenes[$i]["total"], 2) . '",

			      	    "' . trim($ordenes[$i]["estado"]) . '",

			      	    "' . $InfoOrdenes . '",
			      	    
			      	    "' . $eliminarOrden . '",

			      		"' . $ticket . '"

			      		],';



		}



		$datosJson = substr($datosJson, 0, -1);



		$datosJson .= ']

		  

	}';



		echo $datosJson;



	}


}

/*=============================================

ACTIVAR TABLA DE VENTAS

=============================================*/

$activar = new tablaOrdenes();

$activar->mostrarTablaOrdenes();