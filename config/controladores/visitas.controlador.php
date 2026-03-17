<?php

class ControladorVisitas{

	/*=============================================
	MOSTRAR TOTAL VISITAS
	=============================================*/

	public function ctrMostrarTotalVisitas(){

		$tabla = "visitasPaises";

		$respuesta = ModeloVisitas::mdlMostrarTotalVisitas($tabla);

		return $respuesta;

	}

	/*=============================================
	MOSTRAR PAISES DE VISITAS
	=============================================*/
	
	static public function ctrMostrarPaises($orden){

		$tabla = "visitasPaises";
	
		$respuesta = ModeloVisitas::mdlMostrarPaises($tabla, $orden);
		
		return $respuesta;
	}

	/*=============================================
	MOSTRAR VISITAS
	=============================================*/
	
	static public function ctrMostrarVisitas(){

		$tabla = "visitasPersonas";
	
		$respuesta = ModeloVisitas::mdlMostrarVisitas($tabla);
		
		return $respuesta;
	}


}