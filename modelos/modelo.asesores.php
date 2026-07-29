<?php
require_once "conexion.php";
class ModeloAsesores{

	/*=============================================
	MOSTRAR ASESORES EN TABLA
	=============================================*/	
	
	static public function mdlMostrarAsesores($tabla){

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
	static public function mdlMostrarAsesoresEmpresas($tabla, $item, $valor, $soloActivos = true){
		
		if($item != null){

			$filtroEstado = $soloActivos ? " AND estado = 'Activo'" : "";
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item$filtroEstado ORDER BY id DESC");

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

	static public function mdlIngresarAsesores($tabla, $datos){

		$porcentajeComision = isset($datos["porcentajeComision"]) ? intval($datos["porcentajeComision"]) : 0;
			
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, correo, numerodeCelular, numeroTelefono, id_empresa, porcentajeComision, estado) VALUES (:nombre, :correo, :numerodeCelular, :numeroTelefono, :id_empresa, :porcentajeComision, :estado)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":numerodeCelular", $datos["numerodeCelular"], PDO::PARAM_STR);
		$stmt->bindParam(":numeroTelefono", $datos["numeroTelefono"], PDO::PARAM_STR);
		$stmt->bindParam(":porcentajeComision", $porcentajeComision, PDO::PARAM_INT);
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


	static public function mdlEditarAsesor($tabla,$datos){

		$porcentajeComision = isset($datos["porcentajeComision"]) ? intval($datos["porcentajeComision"]) : 0;
		$idEmpresa = isset($datos["id_empresa"]) && intval($datos["id_empresa"]) > 0
			? intval($datos["id_empresa"])
			: null;
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, correo = :correo, numerodeCelular = :numerodeCelular, numeroTelefono = :numeroTelefono, porcentajeComision = :porcentajeComision, estado = :estado, id_empresa = COALESCE(:id_empresa, id_empresa) WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":numerodeCelular", $datos["numerodeCelular"], PDO::PARAM_STR);
		$stmt -> bindParam(":numeroTelefono", $datos["numeroTelefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt -> bindParam(":porcentajeComision", $porcentajeComision, PDO::PARAM_INT);
		$stmt -> bindValue(":id_empresa", $idEmpresa, $idEmpresa === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
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
	
	static public function mdlMostrarTodosLosEmpleado($tablaUno,$tablaDos){

		$stmt = Conexion::conectar()->prepare("SELECT nombre, id FROM $tablaUno UNION ALL SELECT nombre, id FROM $tablaDos");

		$stmt -> execute();

		return $stmt -> fetchAll();


		$stmt-> close();

		$stmt = null;
		
	}
}
