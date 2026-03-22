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