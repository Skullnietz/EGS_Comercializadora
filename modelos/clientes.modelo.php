<?php

require_once "conexion.php";
require_once "conexionWordpress.php";

class ModeloClientes{
    /*=============================================
	MOSTRAR ASESOR
	=============================================*/
    static public function mdlMostrarAsesor($tabla, $id_perfil){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id = '$id_perfil'");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	}
	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function mdlMostrarClientes($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

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


	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function mdlMostrarClientesAjax($tabla, $item, $valor){

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
  	AGREGAR USUARIO
  	=============================================*/	
	function mdlAgregarCliente($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, id_Asesor, correo, telefono, telefonoDos, etiqueta, id_empresa) VALUES (:nombre, :id_Asesor, :correo, :telefono, :telefonoDos, :etiqueta, :id_empresa)");

		$stmt->bindParam(":nombre", $datos["AgregarNombreCliente"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["AgregarCorreoCliente"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefonoUnoCliente"], PDO::PARAM_INT);
		$stmt->bindParam(":telefonoDos", $datos["telefonoDosCliente"], PDO::PARAM_INT);
		$stmt->bindParam(":etiqueta", $datos["etiqueta"], PDO::PARAM_INT);
		$stmt->bindParam(":id_Asesor", $datos["AgreagrAsesorAlCliente"], PDO::PARAM_INT);
		$stmt->bindParam(":id_empresa", $datos["empresa"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}

	public function mdlEditarCliente($tabla,$datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, correo = :correo, telefono = :telefono, telefonoDos = :telefonoDos, id_Asesor = :id_Asesor  WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["editarNombreDelCliente"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["EditarCorreoCliente"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["EditarNumeroDelCliente"], PDO::PARAM_INT);
		$stmt -> bindParam(":telefonoDos", $datos["EditarSegundoNumeroDeTel"], PDO::PARAM_INT);
		$stmt -> bindParam(":id_Asesor", $datos["EditarAsesorDelCliente"], PDO::PARAM_INT);
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
	ACTUALIZAR COMPRAS DE CLIENTE
	=============================================*/
	static public function mdlActualizarCantidadDeComprasCliente($tabla, $item1, $valor1, $valor2){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE id = :id");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":id", $valor2, PDO::PARAM_STR);


		if($stmt -> execute()){
			
			return "ok";

		}else{


			return "error";
		}

		$stmt -> close();

		$stmt = null;

	}	
	/*=============================================
	CONTAR ORDENES DE UN CLIENTE
	=============================================*/

	static public function mdlContarOrdenesCliente($id_cliente){

		$stmt = ConexionWP::conectarWP()->prepare("SELECT COUNT(*) as total FROM ordenes WHERE id_usuario = :id_cliente");

		$stmt->bindParam(":id_cliente", $id_cliente, PDO::PARAM_INT);

		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result ? intval($result["total"]) : 0;

	}

	/*=============================================
	CONTAR ORDENES DE TODOS LOS CLIENTES (BULK)
	=============================================*/

	static public function mdlContarOrdenesClientesBulk(){

		$stmt = ConexionWP::conectarWP()->prepare("SELECT id_usuario, COUNT(*) as total FROM ordenes GROUP BY id_usuario");

		$stmt->execute();

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$counts = [];

		foreach($results as $row){
			$counts[intval($row["id_usuario"])] = intval($row["total"]);
		}

		return $counts;

	}

	/*=============================================
	CONTAR ORDENES ENTREGADAS/CANCELADAS BULK
	=============================================*/

	static public function mdlContarOrdenesEstadoBulk(){

		$stmt = ConexionWP::conectarWP()->prepare(
			"SELECT id_usuario,
				SUM(CASE WHEN estado LIKE '%Ent%' THEN 1 ELSE 0 END) as entregadas,
				SUM(CASE WHEN estado LIKE '%can%' THEN 1 ELSE 0 END) as canceladas
			FROM ordenes GROUP BY id_usuario"
		);

		$stmt->execute();

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$map = [];

		foreach($results as $row){
			$map[intval($row["id_usuario"])] = [
				"entregadas" => intval($row["entregadas"]),
				"canceladas" => intval($row["canceladas"])
			];
		}

		return $map;

	}

	/*=============================================
	PROMEDIO DÍAS DE RECOGIDA BULK
	=============================================*/

	static public function mdlPromedioRecogidaBulk(){

		$stmt = ConexionWP::conectarWP()->prepare(
			"SELECT id_usuario,
				COUNT(*) as total_entregadas,
				AVG(DATEDIFF(fecha_Salida, fecha_ingreso)) as promedio_dias
			FROM ordenes
			WHERE estado LIKE '%Ent%'
			  AND fecha_ingreso IS NOT NULL AND fecha_ingreso != ''
			  AND fecha_Salida IS NOT NULL AND fecha_Salida != ''
			GROUP BY id_usuario
			HAVING total_entregadas >= 3"
		);

		$stmt->execute();

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$map = [];

		foreach($results as $row){
			$map[intval($row["id_usuario"])] = round(floatval($row["promedio_dias"]), 1);
		}

		return $map;

	}

	/*=============================================
	FECHA DE REGISTRO DE CLIENTES (BULK)
	=============================================*/

	static public function mdlFechaRegistroClientesBulk(){

		$stmt = Conexion::conectar()->prepare("SELECT id, fecha FROM clientesTienda WHERE fecha IS NOT NULL AND fecha != ''");

		$stmt->execute();

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$map = [];

		foreach($results as $row){
			$map[intval($row["id"])] = $row["fecha"];
		}

		return $map;

	}

	/*=============================================
	MOSTRAR USUARIOS ORDENES
	=============================================*/

	static public function mdlMostrarClientesOrdenes($tabla, $item, $valor){

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
}