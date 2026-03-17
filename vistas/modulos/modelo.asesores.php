<?php
require_once "conexion.php";
class ModeloAsesores{

	/*=============================================
	MOSTRAR ASESORES EN TABLA
	=============================================*/	
	
	function mdlMostrarAsesores($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetchAll();


		$stmt-> close();

		$stmt = null;
		
	}

	/*=============================================
	MOSTRAR ASESORES EN SELECT DE EEDICION
	=============================================*/	
	static public function mdlMostrarAsesoresEleg($tabla, $item, $valor){
		
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
	MOSTRAR ASESORES EMPRESAS
	=============================================*/	
	static public function mdlMostrarAsesoresEmpresas($tabla, $item, $valor){
		
		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

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

	public function mdlIngresarAsesores($tabla, $datos){
			
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, correo, numerodeCelular, numeroTelefono, id_empresa, estado) VALUES (:nombre, :correo, :numerodeCelular, :numeroTelefono, :id_empresa, :estado)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":numerodeCelular", $datos["numerodeCelular"], PDO::PARAM_STR);
		$stmt->bindParam(":numeroTelefono", $datos["numeroTelefono"], PDO::PARAM_STR);
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


	public function mdlEditarAsesor($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, correo = :correo, numerodeCelular = :numerodeCelular, numeroTelefono = :numeroTelefono, porcentajeComision = :porcentajeComision, estado WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":numerodeCelular", $datos["numerodeCelular"], PDO::PARAM_STR);
		$stmt -> bindParam(":numeroTelefono", $datos["numeroTelefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt -> bindParam(":porcentajeComision", $datos["porcentajeComision"], PDO::PARAM_INT);
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
	ELIMINAR PERFIL
	=============================================*/

	static public function mdlEliminarAsesor($tabla, $datos){

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
	MOSTRAR ASESORES Y TECNICOS
	=============================================*/	
	
	function mdlMostrarTodosLosEmpleado($tablaUno,$tablaDos){

		$stmt = Conexion::conectar()->prepare("SELECT nombre, id FROM $tablaUno UNION ALL SELECT nombre, id FROM $tablaDos");

		$stmt -> execute();

		return $stmt -> fetchAll();


		$stmt-> close();

		$stmt = null;
		
	}
}