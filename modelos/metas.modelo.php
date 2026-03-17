<?php
require_once "conexion.php";

class ModeloMetas{

	/*================================
	MODELO PARA SUBIR DATOS DE META
	=================================*/
	public function mdlSubirMeta($datos,$tabla){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_perfil, area, tipo, observacion, estado, descripcion, actividades, progreso) VALUES (:idperfil, :area, :tipo, :observacion, :estado, :descripcion, :actividades, :progreso)");

		$stmt->bindParam(":idperfil", $datos["usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":area", $datos["area"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":observacion", $datos["observaciones"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":actividades", $datos["actividades"], PDO::PARAM_STR);
		$stmt->bindParam(":progreso", $datos["progreso"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}
	/*=============================================
	MOSTRAR METAS
	=============================================*/

    static public function mdlMostrarMetas($tabla, $item, $valor){
		
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
	SELECCIONAR METAS POR ID DE PERFIL
	=============================================*/
	
	static public function mdlMostrarMetasPorIdPerfil($tabla, $id_perfil){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_perfil = '$id_perfil'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
		/*=============================================
	SELECCIONAR METAS POR NOMBRE DE PERFIL
	=============================================*/
	
	static public function mdlMostrarNombrePorIdPerfil($tabla, $idperfil){

		$stmt = Conexion::conectar()->prepare("SELECT nombre FROM $tabla WHERE id = '$idperfil' ");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
			/*=============================================
	SELECCIONAR PERSONAL POR DEPARTAMENTO 
	=============================================*/
	
	static public function mdlMostrarPersonalDepartamento($tabla, $area ){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM `$tabla` WHERE `Departamento` = '$area' ");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	
	/*=============================================
	SELECCIONAR METAS POR ID DE META
	=============================================*/
	
	static public function mdlMostrarMetasPorIdMeta($tabla, $id_perfil,$id_meta ){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_perfil = '$id_perfil' AND id = '$id_meta'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	/*============================================
	 METODO PARA CONTAR LAS METAS COMPLETADAS POR ID
	==============================================*/
		static public function mdlMostrarMetasCompletadasPorId($tabla, $id_perfil, $estado){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as metasporid FROM $tabla WHERE id_perfil = '$id_perfil' AND estado = '$estado'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	/*============================================
	 METODO PARA CONTAR LAS METAS COMPLETADAS POR DEPARTAMENTO
	==============================================*/
		static public function mdlMostrarMetasCompletadasPorDepartamento($tabla, $area, $estado){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as metaspordepartamento FROM $tabla WHERE area = '$area' AND estado = '$estado'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	
	/*=============================================
	ELIMINAR META
	=============================================*/

	static public function mdlEliminarMeta($tabla, $datos){

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
    
}
