<?php

class controladorResenaOrden
{

	/*=============================================
	MOSTRAR LA RESEÑA DE UNA ORDEN (única o null)
	=============================================*/
	static public function ctrMostrar($idOrden)
	{
		$tabla = "resenaOrden";
		return ModeloResenaOrden::mdlMostrarPorOrden($tabla, intval($idOrden));
	}

	/*=============================================
	GUARDAR RESEÑA + CALIFICACIÓN (acceso público, envío único)

	El $idOrden lo provee la vista (ya validado contra el token del QR).
	Valida 1 <= calificacion <= 5. Devuelve un estado:
	"ok" | "duplicate" | "invalida" | "error".
	=============================================*/
	static public function ctrlGuardarResena($idOrden)
	{
		if (!isset($_POST["calificacionResena"])) {
			return null; // no es un envío de reseña
		}

		$calificacion = intval($_POST["calificacionResena"]);
		if ($calificacion < 1 || $calificacion > 5) {
			return "invalida";
		}

		// Si ya existe reseña para la orden, no se permite otra (envío único).
		$existente = ModeloResenaOrden::mdlMostrarPorOrden("resenaOrden", intval($idOrden));
		if ($existente) {
			return "duplicate";
		}

		$comentario = isset($_POST["comentarioResena"]) ? trim((string) $_POST["comentarioResena"]) : "";
		if (mb_strlen($comentario) > 1000) {
			$comentario = mb_substr($comentario, 0, 1000);
		}

		$datos = array(
			"id_orden"     => intval($idOrden),
			"calificacion" => $calificacion,
			"comentario"   => $comentario
		);

		$respuesta = ModeloResenaOrden::mdlCrear("resenaOrden", $datos);
		return $respuesta; // "ok" | "duplicate" | "error"
	}
}
