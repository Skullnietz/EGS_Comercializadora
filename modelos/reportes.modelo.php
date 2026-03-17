<?php

require_once "conexion.php";

class ModeloReportes{
		
	/*=============================================
	DESCARGAR REPORTE
	=============================================*/

	static public function mdlDescargarReporte($tabla,
				$item, $valorReporteVentasR){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = $valorReporteVentasR ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();
		
		$stmt = null;
	
	}

	public function mdlDescargarReporteComisiones($tabla,$idEmpleado,$fechaInicial,$fechaFinal){
		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_tecnico = $idEmpleado OR id_Asesor = $idEmpleado AND  fecha_Salida BETWEEN $fechaInicial AND  $fechaFinal");


		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();
		
		$stmt = null;

	}
	
	public function ctrlMostrarComicionTecnico($tabla,$tecnico,$fechaInicial,$fechaFinal){
		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_tecnico = $tecnico AND  fecha_Salida BETWEEN $fechaInicial AND  $fechaFinal");


		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();
		
		$stmt = null;

	}
}