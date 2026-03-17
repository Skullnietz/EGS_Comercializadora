<?php

require_once "conexion.php";

class ModeloAdministradores{

	/*=============================================
	MOSTRAR ADMINISTRADORES
	=============================================*/

	static public function mdlMostrarAdministradores($tabla, $item, $valor){

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
	MOSTRAR ADMINISTRADORES POR EMPRESA
	=============================================*/

	static public function MdlMostrarAdministradoresPorEmpresa($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;


	}
	/*=============================================
	MOSTRAR ADMINISTRADORES POR EMPRESA
	=============================================*/

	static public function MdlMostrarAdministradoresPorEmpresaRol($tabla, $item, $valor,$itemDos, $valorDos){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND $itemDos = :$itemDos AND estado != 0");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> bindParam(":".$itemDos, $valorDos, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;


	}
	/*=============================================
	ACTUALIZAR PERFIL
	=============================================*/

	static public function mdlActualizarPerfil($tabla, $item1, $valor1, $item2, $valor2){

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

	/*=============================================
	REGISTRO DE PERFIL
	=============================================*/

	static public function mdlIngresarPerfil($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre,Departamento, email, password, perfil, foto, estado, id_empresa) VALUES (:nombre,:Departamento, :email, :password, :perfil, :foto, :estado, :id_empresa)");
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt->bindParam(":Departamento", $datos["Departamento"], PDO::PARAM_STR);
		$stmt->bindParam(":perfil", $datos["perfil"], PDO::PARAM_STR);
		$stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
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

	/*=============================================
	EDITAR PERFIL
	=============================================*/

	static public function mdlEditarPerfil($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, Departamento = :Departamento, email = :email, password = :password, perfil = :perfil, foto = :foto, id_empresa = :id_empresa WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":Departamento", $datos["Departamento"], PDO::PARAM_STR);
		$stmt -> bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt -> bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt -> bindParam(":perfil", $datos["perfil"], PDO::PARAM_STR);
		$stmt -> bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt -> bindParam(":id_empresa", $datos["id_empresa"], PDO::PARAM_INT);

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

	static public function mdlEliminarPerfil($tabla, $datos){

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
	MOSTRAR ADMINISTRADORES ACTIVOS EN VENTAS
	=============================================*/

	static public function MdlMostrarAdministradoresActivosEnVentas($tabla){

		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = 1 and perfil = 'vendedor'");
		

			$stmt -> execute();

			return $stmt -> fetchAll();


			$stmt -> close();

			$stmt = null;

	}

	/*=============================================
	MOSTRAR ADMINISTRADORES COMO TECNICOS
	=============================================*/

	static public function MdlMostrarTecnicosActivos($tabla){

		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = 1 and perfil = 'tecnico'");
		

			$stmt -> execute();

			return $stmt -> fetchAll();
			

			$stmt -> close();

			$stmt = null;

	}


	/*=============================================
	ACTUALIZAR ESTADO DE SESION 
	=============================================*/
	static public function mdlActivarSesion($tabla, $datos,$sesionActiva){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET sesion = :sesion WHERE id = :id");

		$stmt -> bindParam(":sesion", $sesionActiva, PDO::PARAM_STR);
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
	REGISTRO DE INICO DE SESION
	=============================================*/
 
	static public function mdlRegistrarInicio($tabaDos,$datosInicioDeSesion){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabaDos(id_usuario, navegador, ip) VALUES (:id_usuario, :navegador, :ip)");

		$stmt->bindParam(":id_usuario", $datosInicioDeSesion["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":navegador", $datosInicioDeSesion["navegador"], PDO::PARAM_STR);
		$stmt->bindParam(":ip", $datosInicioDeSesion["ip"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}

	
	
	

}
