<?php
require_once "conexion.php";
class ModeloTecnicos{

	/*=================================
	MOSTRAR TECNICOS
	=================================*/
	static public function mdlMostrarTecnicos($tabla, $item, $valor){
		
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
	/*=================================
	MOSTRAR TECNICOS DE CADA EMPRESA
	=================================*/
	static public function mdlMostrarTecnicosDeEmpresa($tabla, $item, $valor){
		
		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND estado = 'Activo'");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();
		
		$stmt = null;
	}
	/*=================================
	CREAR TECNICO
	=================================*/
	public function mdlCrearTecnico($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, correo, telefono, telefonoDos, HoraDeComida, departamento, id_empresa, estado) VALUES (:nombre, :correo, :telefono, :telefonoDos, :HoraDeComida, :departamento, :id_empresa, :estado)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_INT);
		$stmt->bindParam(":telefonoDos", $datos["telefonoDos"], PDO::PARAM_INT);
		$stmt->bindParam(":HoraDeComida", $datos["HoraDeComida"], PDO::PARAM_STR);
		$stmt->bindParam(":departamento", $datos["areratecnico"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt->bindParam(":id_empresa", $datos["empresa"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}
	/*=================================
	EDITAR TECNICO
	=================================*/
	public function mdlEditarTecnico($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, correo = :correo, telefono = :telefono, telefonoDos = :telefonoDos, HoraDeComida = :HoraDeComida, estado = :estado WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefonoDos", $datos["telefonoDos"], PDO::PARAM_STR);
		$stmt -> bindParam(":HoraDeComida", $datos["HoraDeComidaEditada"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;
	}
	/*=============================================
	ELIMINAR TECNICO
	=============================================*/

	static public function mdlEliminarTecnico($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

		$stmt -> bindParam(":id", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;


	}

	/*=============================================
	ACTIVAR TECNICO
	=============================================*/

	static public function mdlActualizarTecnico($tabla, $item1, $valor1, $item2, $valor2){

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
}