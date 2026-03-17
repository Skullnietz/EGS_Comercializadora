<?php


require_once "conexion.php";



class ModeloPeticionM{
    	/*=============================================

	MOSTRAR peticiones

	=============================================*/

static public function mdlMostrarPeticiones($tabla){


		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_peticionM DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;

	}
	
	

		/*================================
	MODELO PARA SUBIR DATOS DE peticion
		=================================*/
	public function mdlCrearPeticion($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (material_orden, tecnico_solicita, material_solicitado, entrega, devolucion, entregado, devuelto) VALUES (:material_orden, :tecnico_solicita, :material_solicitado, :entrega, :devolucion, :entregado, :devuelto)");

		$stmt->bindParam(":material_orden", $datos["material_orden"], PDO::PARAM_INT);
		$stmt->bindParam(":tecnico_solicita", $datos["tecnico_solicita"], PDO::PARAM_STR);
		$stmt->bindParam(":material_solicitado", $datos["material_solicitado"], PDO::PARAM_STR);
		$stmt->bindParam(":entrega", $datos["entrega"], PDO::PARAM_STR);
		$stmt->bindParam(":entregado", $datos["entregado"], PDO::PARAM_STR);
		$stmt->bindParam(":devolucion", $datos["devolucion"], PDO::PARAM_STR);
		$stmt->bindParam(":devuelto", $datos["devuelto"], PDO::PARAM_STR);


		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}
	
	/*=============================================
	SELECCIONAR CREADOR DE LA Peticion
	=============================================*/
	
	static public function mdlMostrarCreadorPeticion($tabla, $idperfil ){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id = '$idperfil'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
		/*=============================================
	SELECCIONAR ULTIMAS 5 CITAS
	=============================================*/
	
	static public function mdlMostrarUltimasPeticiones($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_peticionM DESC LIMIT 5");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
		/*=============================================
	fecha de entrega
	=============================================*/
	public function mdlFechaEntrega($entrega){
	$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (entrega) VALUES (:entrega)");

		$stmt->bindParam(":entrega", $datos["entrega"], PDO::PARAM_INT);
			
			if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}
		/*=============================================
	fecha devolucion
	=============================================*/
	public function mdlFechaDevolucion($tabla){
	$stmt = Conexion::conectar()->prepare("insert into $tabla devolucion VALUES:devolucion");


		$stmt->bindParam(":devolucion", $datos["devolucion"], PDO::PARAM_INT);
			
		$update_field='peticionesmaterial';
if(isset($input['entrega'])) {
    $update_field.= "entrega='".$input['entrega']."'";
} else if(isset($input['devolucion'])) {
    $update_field.= "devolucion='".$input['devolucion']."'";
}   

	}
		/*=================================
	MOSTRAR TECNICOS
	=================================*/
	
	static public function ctrMostrarTecnicos($item, $valor){
		
		$tabla = "tecnicos";

		$respuesta = ModeloTecnicos::mdlMostrarTecnicos($tabla, $item, $valor);

		return $respuesta;

	}

	/*=================================
	MOSTRAR TECNICOS PARA EMPRESAS
	=================================*/
	
	static public function ctrMostrarTecnicosDeEmpresas($item, $valor){
		
		$tabla = "tecnicos";

		$respuesta = ModeloTecnicos::mdlMostrarTecnicosDeEmpresa($tabla, $item, $valor);

		return $respuesta;

	}
	 /*=============================================
	MOSTRAR Tecnico
	=============================================*/
    static public function mdlMostrarTecnico($tabla, $id_perfil){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id = '$id_perfil'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
		/*=============================================
	ACTUALIZAR entrega de peticiones
	=============================================*/
	static public function mdlActualizarPeticion($tabla, $item1, $valor1, $item2, $valor2){

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
	ACTUALIZAR ESTADO DE Peticion
	=============================================*/
	static public function mdlActivarPeticion($tabla, $datos, $Peticionentregada){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET entregado = :entregado WHERE id_peticionM = :id_peticionM");

		$stmt -> bindParam(":entregado", $Peticionentregada, PDO::PARAM_STR);
		$stmt -> bindParam(":id_peticionM", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
}
	