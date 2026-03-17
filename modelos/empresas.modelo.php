<?php
require_once "conexion.php";
class ModeloEmpresas{
	/*=============================================
  	MOSTRAR EMPRESAS
  	=============================================*/
  	static public function mdlMostrarEmpresas($tabla, $item, $valor){
  		
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
  	MOSTRAR EMPRESAS  PARA EDITAR
  	=============================================*/
  	static public function mdlMostrarEmpresasParaEditar($tabla, $item, $valor){
  		
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
  	MOSTRAR EMPRESAS  PARA REPORTES
  	=============================================*/
  	static public function mdlMostrarEmpresasParaReportes($tabla, $item3, $valor3){
  		
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
  	AGREGAR EMPRESA
  	=============================================*/	
	function mdlcrearEmpresas($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(empresa, correo, telefono, telefonoDos, direccion, Horario, Facebook, Sitio) VALUES (:empresa, :correo, :telefono, :telefonoDos, :direccion, :Horario, :Facebook, :Sitio)");

		$stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefonoDeEmpresa"], PDO::PARAM_INT);
		$stmt->bindParam(":telefonoDos", $datos["telefonoDosDeEmpresa"], PDO::PARAM_INT);
		$stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt->bindParam(":Horario", $datos["Horario"], PDO::PARAM_STR);
		$stmt->bindParam(":Facebook", $datos["Facebook"], PDO::PARAM_STR);
        $stmt->bindParam(":Sitio", $datos["Sitio"], PDO::PARAM_STR);
		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}

	/*=============================================
	EDITAR EMPRESA
	=============================================*/	

	public function mdlEditarEmpresa($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET empresa = :empresa, correo = :correo, telefono = :telefono, telefonoDos = :telefonoDos, direccion = :direccion, Horario = :Horario, Facebook = :Facebook, Sitio = :Sitio WHERE id = :id");

		$stmt -> bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["editarNumeroUnoDeEmpresa"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefonoDos", $datos["telefonoDosDeEmpresaEditado"], PDO::PARAM_STR);
		$stmt -> bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt -> bindParam(":Horario", $datos["HoraEditada"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt -> bindParam(":Facebook", $datos["FacebookEditado"], PDO::PARAM_STR);
		$stmt -> bindParam(":Sitio", $datos["SitioEditado"], PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;
	}

	/*=============================================
	ELIMINAR EMPRESA
	=============================================*/

	static public function mdlEliminarEmpresa($tabla, $datos){

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