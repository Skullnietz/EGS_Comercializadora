<?php

Class ControladorNotificaciones{

	/*=============================================
	MOSTRAR NOTIFICACIONES (legacy)
	=============================================*/

	static public function ctrMostrarNotificaciones(){

		$tabla = "notificaciones";

		$respuesta = ModeloNotificaciones::mdlMostrarNotificaciones($tabla);

		return $respuesta;

	}

	/*=============================================
	CREAR TABLA DE NOTIFICACIONES DE ESTADO
	(se ejecuta 1 vez, idempotente)
	=============================================*/

	static public function ctrCrearTablaEstado(){

		return ModeloNotificaciones::mdlCrearTablaEstado();

	}

	/*=============================================
	REGISTRAR CAMBIO DE ESTADO
	=============================================*/

	static public function ctrRegistrarCambioEstado($datos){

		return ModeloNotificaciones::mdlInsertarNotifEstado($datos);

	}

	/*=============================================
	OBTENER NOTIFICACIONES DE ESTADO NO LEÍDAS
	=============================================*/

	static public function ctrNotifEstadoNoLeidas($perfil, $idEmpresa, $idAsesor = null){

		return ModeloNotificaciones::mdlNotifEstadoNoLeidas($perfil, $idEmpresa, $idAsesor);

	}

	/*=============================================
	MARCAR NOTIFICACIONES DE ESTADO COMO LEÍDAS
	=============================================*/

	static public function ctrMarcarLeidasEstado($perfil, $idEmpresa, $idAsesor = null){

		return ModeloNotificaciones::mdlMarcarLeidasEstado($perfil, $idEmpresa, $idAsesor);

	}

	/*=============================================
	OBTENER TODAS LAS NOTIFICACIONES (vista completa)
	=============================================*/

	static public function ctrTodasNotificaciones($perfil, $idEmpresa, $idRol = null, $limite = 50, $offset = 0){

		return ModeloNotificaciones::mdlTodasNotificaciones($perfil, $idEmpresa, $idRol, $limite, $offset);

	}

	/*=============================================
	HISTORIAL COMPLETO DE UNA ORDEN (seguimiento)
	=============================================*/

	static public function ctrHistorialOrden($idOrden){

		return ModeloNotificaciones::mdlHistorialOrden($idOrden);

	}

}
