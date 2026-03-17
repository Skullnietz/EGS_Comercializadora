<?php

require_once "conexion.php";

class ModeloTickets{
	
	public function mdlIngresarTicket($tablaTickets,$datosTicket){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tablaTickets(id_empresa, codigo, productos, total) VALUES (:id_empresa, :codigo, :productos, :total)");

		$stmt->bindParam(":id_empresa", $datosTicket["empresa"], PDO::PARAM_INT);
		$stmt->bindParam(":codigo", $datosTicket["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":productos", $datosTicket["productos"], PDO::PARAM_STR);
		$stmt->bindParam(":total", $datosTicket["total"], PDO::PARAM_STR);
		
		if ($stmt->execute()){
			
			return "ok";

		}else{

			return "error";
		}
		
		$stmt->close();
		
		$stmt = null;
	}


	static public function mdlMostrarTickets($tabla, $item, $valor){

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
}