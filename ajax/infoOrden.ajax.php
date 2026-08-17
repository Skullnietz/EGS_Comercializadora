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

		// Fotos de las observaciones de la orden, agrupadas por id_observacion
		$fotosPorObs = array();
		$fotosOrden = controladorObservaciones::ctrMostrarFotosPorOrden($idOrden);
		if (is_array($fotosOrden)) {
			foreach ($fotosOrden as $f) {
				$fotosPorObs[$f["id_observacion"]][] = $f["ruta"];
			}
		}

		$obsEnriquecidas = array();
		if (is_array($observaciones)) {
			foreach ($observaciones as $obs) {
				$obsEnriquecidas[] = array(
					"id"          => $obs["id"],
					"observacion" => $obs["observacion"],
					"fecha"       => $obs["fecha"],
					"nombre"      => isset($obs["creador_nombre"]) ? $obs["creador_nombre"] : "Usuario",
					"foto"        => isset($obs["creador_foto"]) ? $obs["creador_foto"] : "",
					"perfil"      => isset($obs["creador_perfil"]) ? $obs["creador_perfil"] : "",
					"fotos"       => isset($fotosPorObs[$obs["id"]]) ? $fotosPorObs[$obs["id"]] : array()
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
