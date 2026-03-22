<?php

require_once "../modelos/ordenes.modelo.php";
require_once "../modelos/conexionWordpress.php";
require_once "../modelos/conexion.php";
require_once "../controladores/observacionOrdenes.controlador.php";
require_once "../modelos/observacionOrdenes.modelo.php";

/*=============================================
POLLING INFO ORDEN - Actualización en tiempo real
=============================================*/

if(isset($_POST["pollInfoOrden"]) && isset($_POST["idOrden"])){

	$idOrden = intval($_POST["idOrden"]);

	// Obtener datos actuales de la orden
	$orden = ModeloOrdenes::mdlMostrarordenesParaValidar("ordenes", "id", $idOrden);

	if(!empty($orden)){

		$row = $orden[0];

		// Obtener observaciones de la tabla observacionesOrdenes
		$observaciones = controladorObservaciones::ctrMostrarobservaciones($idOrden);
		$obsEnriquecidas = array();
		if (is_array($observaciones)) {
			foreach ($observaciones as $obs) {
				$infoUser = controladorObservaciones::ctrMostrarInfoUser($obs["id_creador"]);
				$u = is_array($infoUser) && !empty($infoUser) ? $infoUser[0] : array();
				$obsEnriquecidas[] = array(
					"id"          => $obs["id"],
					"observacion" => $obs["observacion"],
					"fecha"       => $obs["fecha"],
					"nombre"      => isset($u["nombre"]) ? $u["nombre"] : "Usuario",
					"foto"        => isset($u["foto"]) ? $u["foto"] : "",
					"perfil"      => isset($u["perfil"]) ? $u["perfil"] : ""
				);
			}
		}

		$response = array(
			"estado"        => $row["estado"],
			"fecha"         => $row["fecha"],
			"fecha_Salida"  => $row["fecha_Salida"],
			"observaciones" => $obsEnriquecidas
		);

		echo json_encode($response);

	} else {
		echo json_encode(array("error" => "Orden no encontrada"));
	}

}
