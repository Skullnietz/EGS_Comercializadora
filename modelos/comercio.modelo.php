<?php

require_once "conexion.php";

class ModeloComercio{

	/*=============================================
	SELECCIONAR PLANTILLA
	=============================================*/

	static public function mdlSeleccionarPlantilla($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR LOGO O ICONO
	=============================================*/

	static public function mdlActualizarLogoIcono($tabla, $id, $item, $valor){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item = :$item WHERE id = :id");

		$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR COLORES
	=============================================*/

	static public function mdlActualizarColores($tabla, $id, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET barraSuperior = :barraSuperior, textoSuperior = :textoSuperior, colorFondo = :colorFondo, colorTexto = :colorTexto  WHERE id = :id");

		$stmt->bindParam(":barraSuperior", $datos["barraSuperior"], PDO::PARAM_STR);
		$stmt->bindParam(":textoSuperior", $datos["textoSuperior"], PDO::PARAM_STR);
		$stmt->bindParam(":colorFondo", $datos["colorFondo"], PDO::PARAM_STR);
		$stmt->bindParam(":colorTexto", $datos["colorTexto"], PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR SCRIPT
	=============================================*/

	static public function mdlActualizarScript($tabla, $id, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET apiFacebook = :apiFacebook, pixelFacebook = :pixelFacebook, googleAnalytics = :googleAnalytics WHERE id = :id");

		$stmt->bindParam(":apiFacebook", $datos["apiFacebook"], PDO::PARAM_STR);
		$stmt->bindParam(":pixelFacebook", $datos["pixelFacebook"], PDO::PARAM_STR);
		$stmt->bindParam(":googleAnalytics", $datos["googleAnalytics"], PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}
	/*=============================================
	SELECCIONAR COMERCIO
	=============================================*/

	static public function mdlSeleccionarComercio($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR INFORMACION
	=============================================*/

	static public function mdlActualizarInformacion($tabla, $id, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET impuesto = :impuesto,
		envioNacional = :envioNacional, envioInternacional = :envioInternacional, tasaMininaNa = :tasaMininaNa, tasaMininaInter = :tasaMininaInter, pais = :pais, modoPaypal = :modoPaypal, clienteidPaypal = :clienteidPaypal, llaveSecretPaypal = :llaveSecretPaypal WHERE id = :id");

		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":envioNacional", $datos["envioNacional"], PDO::PARAM_STR); 
		$stmt->bindParam(":envioInternacional", $datos["envioInternacional"], PDO::PARAM_STR); 
		$stmt->bindParam(":tasaMininaNa", $datos["tasaMininaNa"], PDO::PARAM_STR); 
		$stmt->bindParam(":tasaMininaInter", $datos["tasaMininaInter"], PDO::PARAM_STR); 
		$stmt->bindParam(":pais", $datos["seleccionarPais"], PDO::PARAM_STR);
		$stmt->bindParam(":modoPaypal", $datos["modoPaypal"], PDO::PARAM_STR); 
		$stmt->bindParam(":clienteidPaypal", $datos["clienteidPaypal"], PDO::PARAM_STR); 
		$stmt->bindParam(":llaveSecretPaypal", $datos["llaveSecretPaypal"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

}