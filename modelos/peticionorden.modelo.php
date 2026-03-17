
<?php


require_once "conexion.php";



class ModeloPeticionO{
	/*================================
	MODELO PARA SUBIR DATOS DE peticion
		=================================*/
	public function mdlCrearPeticionO($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (orden_mod, tecnico_solicita, dep_desarrollo, acciones, motivos,fecha_hora) VALUES (:orden_mod, :tecnico_solicita, :dep_desarrollo, :acciones, :motivos, :fecha_hora)");

		$stmt->bindParam(":orden_mod", $datos["orden_mod"], PDO::PARAM_INT);
		$stmt->bindParam(":tecnico_solicita", $datos["tecnico_solicita"], PDO::PARAM_STR);
		$stmt->bindParam(":dep_desarrollo", $datos["dep_desarrollo"], PDO::PARAM_STR);
		$stmt->bindParam(":acciones", $datos["acciones"], PDO::PARAM_STR);
		$stmt->bindParam(":motivos", $datos["motivos"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_hora", $datos["fecha_hora"], PDO::PARAM_STR);


		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}
	/*=============================================
	SELECCIONAR ULTIMAS 5 PETICIONES
	=============================================*/
	
	static public function mdlMostrarUltimasPeticionesO($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_peticionO DESC LIMIT 5");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
}
	