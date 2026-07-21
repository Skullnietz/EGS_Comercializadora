<?php

class controladorComentarioCliente
{

	/*=============================================
	MOSTRAR COMENTARIOS DE UNA ORDEN
	=============================================*/
	static public function ctrMostrar($idOrden)
	{
		$tabla = "comentariosClienteOrden";
		return ModeloComentarioCliente::mdlMostrarPorOrden($tabla, intval($idOrden));
	}

	/*=============================================
	COMENTARIOS RECIENTES PARA NOTIFICACION
	=============================================*/
	static public function ctrComentariosRecientesNotif($limite = 15, $ordenIds = null)
	{
		return ModeloComentarioCliente::mdlComentariosRecientesNotif(
			"comentariosClienteOrden",
			$limite,
			$ordenIds
		);
	}

	/*=============================================
	GUARDAR COMENTARIO DEL CLIENTE (acceso público sin sesión)

	El $idOrden lo provee la vista (ya validado contra el token del QR),
	no se confía en un id enviado por POST. Devuelve un estado:
	"ok" | "duplicate" | "vacio" | "error".
	=============================================*/
	static public function ctrlGuardarComentario($idOrden)
	{
		if (!isset($_POST["comentarioCliente"])) {
			return null; // no es un envío de comentario
		}

		$comentario = trim((string) $_POST["comentarioCliente"]);

		if ($comentario === "") {
			return "vacio";
		}

		// Límite de longitud (coincide con la columna VARCHAR(1000)).
		if (mb_strlen($comentario) > 1000) {
			$comentario = mb_substr($comentario, 0, 1000);
		}

		$datos = array(
			"id_orden"   => intval($idOrden),
			"comentario" => $comentario
		);

		$respuesta = ModeloComentarioCliente::mdlCrear("comentariosClienteOrden", $datos);

		if ($respuesta === "duplicate") {
			return "duplicate";
		}
		return is_numeric($respuesta) ? "ok" : "error";
	}
}
