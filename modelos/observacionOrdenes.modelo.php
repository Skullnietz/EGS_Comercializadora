<?php

require_once "conexion.php";

class ModeloObservaciones{
	
	/*=============================================
	MOSTRAR OBSERVACIONES
	=============================================*/

	static public function mdlMostrarobservaciones($tabla, $itemobs){

		

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_orden = $itemobs ORDER BY `fecha` ASC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		

		$stmt -> close();

		$stmt = null;


	}
	
	/*=============================================
	MOSTRAR OBSERVACIONES INFO USUARIO
	=============================================*/

	static public function mdlMostrarInfoUser($tabla, $idadmin){

		

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id = $idadmin");

			$stmt -> execute();

			return $stmt -> fetchAll();

		

		$stmt -> close();

		$stmt = null;


	}

	/*=============================================
	MOSTRAR ÚLTIMAS OBSERVACIONES (GLOBAL)
	=============================================*/

	static public function mdlUltimasObservaciones($tabla, $limite){

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
			        a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 ORDER BY o.fecha DESC
			 LIMIT :limite"
		);

		$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	OBSERVACIONES DE HOY (GLOBAL)
	=============================================*/

	static public function mdlObservacionesHoy($tabla){

		$stmt = Conexion::conectar()->prepare(
			"SELECT o.id, o.id_creador, o.id_orden, o.observacion, o.fecha,
			        a.nombre AS creador_nombre, a.foto AS creador_foto, a.perfil AS creador_perfil
			 FROM $tabla o
			 LEFT JOIN administradores a ON a.id = o.id_creador
			 WHERE DATE(o.fecha) = CURDATE()
			 ORDER BY o.fecha DESC"
		);

		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	CREAR OBSERVACIONES
	=============================================*/
	static public function mdlCrearObservacion($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_creador, id_orden, observacion, fecha) VALUES (:id_creador, :id_orden, :observacion, :fecha)");

		$stmt->bindParam(":id_creador", $datos["id_creador"], PDO::PARAM_INT);
		$stmt->bindParam(":id_orden", $datos["id_orden"], PDO::PARAM_INT);
		$stmt->bindParam(":observacion", $datos["observacion"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha", (date ("Y-m-d H:i:s")), PDO::PARAM_STR);
		

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}
	/*=============================================
	ELIMINAR OBSERVACIONES
	=============================================*/
		static public function mdlEliminarObservacion($tabla, $datos){



		$stmt = ConexionWP::conectarWP()->prepare("DELETE FROM $tabla WHERE id = :id");



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