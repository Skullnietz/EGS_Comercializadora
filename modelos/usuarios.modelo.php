<?php

require_once "conexion.php";

class ModeloUsuarios{

	/*=============================================
	MOSTRAR EL TOTAL DE USUARIOS
	=============================================*/	

	static public function mdlMostrarTotalUsuarios($tabla, $orden){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}
		/*=============================================
	MOSTRAR EL TOTAL DE USUARIOS MES
	=============================================*/	

	static public function mdlMostrarTotalUsuariosMes($tabla, $orden){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE MONTH(`fecha`) = MONTH(NOW()) AND YEAR(`fecha`) = YEAR(NOW()) ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}
			/*=============================================
	MOSTRAR EL TOTAL DE USUARIOS MES ASESOR
	=============================================*/	

	static public function mdlMostrarTotalUsuariosMesAsesor($tabla, $orden, $idAsesor){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE MONTH(`fecha`) = MONTH(NOW()) AND YEAR(`fecha`) = YEAR(NOW()) AND `id_Asesor` = $idAsesor ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}
	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function mdlMostrarUsuarios($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;


	}

	/*=============================================
	ACTIVAR USUARIO
	=============================================*/

	static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
	
	
	/*=====================================
	Mostrar compras
	======================================*/

	static public function mdlMostrarCompras($tabla, $item, $valor){
		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

		$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	
	
	/*=============================================
	MOSTRAR USUARIOS PARA EDICION
	=============================================*/

	static public function mdlMostrarAdministradoresEdicion($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;


	}

	/*=============================================
	EDITAR ASESOR EN BD
	=============================================*/

	static public function mdlEditarAsesorU($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET asesor = :asesor WHERE id = :id");

		$stmt -> bindParam(":asesor", $datos["asesor"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
	
	
	
}