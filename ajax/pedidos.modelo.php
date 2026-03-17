<?php

require_once "conexion.php";

class ModeloPedidos{
	
		/*=============================================
	MOSTRAR VENTAS
	=============================================*/	

	static public function mdlMostrarPedidos($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;

	}


	/*=============================================
	CREAR PEDIDO
	=============================================*/

	static public function mdlIngresarPedido($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_empresa, id_cliente, id_Asesor, productoUno, precioProductoUno, cantidaProductoUno, totalPedidoUno, ProductoDos, precioProductoDos, cantidadProductoDos, totalPedidoDos, ProductoTres, precioProductoTres, cantidadProductoTres, totalPedidoTres, ProductoCuatro, precioProductoCuatro, cantidadProductoCuatro, totalPedidoCuatro, ProductoCinco, precioProductoCinco, cantidadProductoCinco, totalPedidoCinco, metodo, pagoPedido, total, adeudo, fechaEntrega, estado) VALUES (:id_empresa, :id_cliente, :id_Asesor, :productoUno, :precioProductoUno, :cantidaProductoUno, :totalPedidoUno, :ProductoDos, :precioProductoDos, :cantidadProductoDos, :totalPedidoDos, :ProductoTres, :precioProductoTres, :cantidadProductoTres, :totalPedidoTres, :ProductoCuatro, :precioProductoCuatro, :cantidadProductoCuatro, :totalPedidoCuatro, :ProductoCinco, :precioProductoCinco, :cantidadProductoCinco, :totalPedidoCinco, :metodo, :pagoPedido, :total, :adeudo, :fechaEntrega, :estado)");

		$stmt->bindParam(":id_empresa", $datos["empresaPedido"], PDO::PARAM_INT);
		$stmt->bindParam(":id_Asesor", $datos["AsesorPedido"], PDO::PARAM_INT);
		$stmt->bindParam(":id_cliente", $datos["clientePeido"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["IngresarEstadoDelPedido"], PDO::PARAM_STR);


		$stmt->bindParam(":productoUno", $datos["Producto1"], PDO::PARAM_STR);
		$stmt->bindParam(":precioProductoUno", $datos["precioProducto1"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidaProductoUno", $datos["cantidadProducto1"], PDO::PARAM_INT);
		$stmt->bindParam(":totalPedidoUno", $datos["totalPedidoUno"], PDO::PARAM_INT);

		$stmt->bindParam(":ProductoDos", $datos["Producto2"], PDO::PARAM_STR);
		$stmt->bindParam(":precioProductoDos", $datos["precioProducto2"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductoDos", $datos["cantidadProducto2"], PDO::PARAM_INT);
		$stmt->bindParam(":totalPedidoDos", $datos["totalPedidoDos"], PDO::PARAM_INT);

		$stmt->bindParam(":ProductoTres", $datos["Producto3"], PDO::PARAM_STR);
		$stmt->bindParam(":precioProductoTres", $datos["precioProducto3"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductoTres", $datos["cantidadProducto3"], PDO::PARAM_INT);
		$stmt->bindParam(":totalPedidoTres", $datos["totalPedidoTres"], PDO::PARAM_INT);

		$stmt->bindParam(":ProductoCuatro", $datos["Producto4"], PDO::PARAM_STR);
		$stmt->bindParam(":precioProductoCuatro", $datos["precioProducto4"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductoCuatro", $datos["cantidadProducto4"], PDO::PARAM_INT);
		$stmt->bindParam(":totalPedidoCuatro", $datos["totalPedidoCuatro"], PDO::PARAM_INT);

		$stmt->bindParam(":ProductoCinco", $datos["Producto5"], PDO::PARAM_STR);
		$stmt->bindParam(":precioProductoCinco", $datos["precioProducto5"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductoCinco", $datos["cantidadProducto5"], PDO::PARAM_INT);
		$stmt->bindParam(":totalPedidoCinco", $datos["totalPedidoCinco"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductoCinco", $datos["cantidadProducto5"], PDO::PARAM_INT);
		
		$stmt->bindParam(":metodo", $datos["metodo"], PDO::PARAM_STR);

		$stmt->bindParam(":pagoPedido", $datos["pagoClientePedido"], PDO::PARAM_INT);
		$stmt->bindParam(":total", $datos["pagoPedido"], PDO::PARAM_INT);
		$stmt->bindParam(":adeudo", $datos["adeudo"], PDO::PARAM_INT);
		$stmt->bindParam(":adeudo", $datos["adeudo"], PDO::PARAM_INT);
		$stmt->bindParam(":fechaEntrega", $datos["fechaEntrega"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	MOSTRAR PEDIDOS
	=============================================

	static public function mdlMostrarpedidosParaValidar($tabla, $item, $valor){

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


	}*/
	/*=============================================
	MOSTRAR PEDIDOS PENDIENTES
	=============================================*/

	static public function mdlMostrarpedidosParaValidar($tabla, $item, $valor){

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
	/*=============================================
	EDITAR PEDIDO
	=============================================*/

	static public function mdlEditarPedido($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET productoUno = :productoUno, abonoUno = :abonoUno, fechaAbonoUno = :fechaAbonoUno, ProductoDos = :ProductoDos, abonoDos = :abonoDos, fechaAbonoDos = :fechaAbonoDos, abonoTres = :abonoTres, fechaAbonoTres = :fechaAbonoTres, abonoCuatro = :abonoCuatro, fechaAbonoCuatro = :fechaAbonoCuatro, abonoCinco = :abonoCinco, fechaAbonoCinco = :fechaAbonoCinco, adeudo = :adeudo, estado = :estado WHERE id = :id");

		$stmt->bindParam(":productoUno", $datos["edicionProductoUnoPedido"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["EstadoDelPedido"], PDO::PARAM_STR);
		$stmt->bindParam(":abonoUno", $datos["abono1"], PDO::PARAM_STR);
		$stmt->bindParam(":fechaAbonoUno", $datos["fechaAbono1"], PDO::PARAM_STR);
		$stmt->bindParam(":ProductoDos", $datos["edicionProductoUnoPedidoDos"], PDO::PARAM_STR);
		$stmt->bindParam(":abonoDos", $datos["abono2"], PDO::PARAM_STR);
		$stmt->bindParam(":fechaAbonoDos", $datos["fechaAbono2"], PDO::PARAM_STR);

		$stmt->bindParam(":abonoTres", $datos["abono3"], PDO::PARAM_STR);
		$stmt->bindParam(":fechaAbonoTres", $datos["fechaAbono3"], PDO::PARAM_STR);

		$stmt->bindParam(":abonoCuatro", $datos["abono4"], PDO::PARAM_STR);
		$stmt->bindParam(":fechaAbonoCuatro", $datos["fechaAbono4"], PDO::PARAM_STR);

		$stmt->bindParam(":abonoCinco", $datos["abono5"], PDO::PARAM_STR);
		$stmt->bindParam(":fechaAbonoCinco", $datos["fechaAbono5"], PDO::PARAM_STR);
		
		$stmt->bindParam(":adeudo", $datos["adeudoPedidoEditado"], PDO::PARAM_STR);
		

		$stmt -> bindParam(":id", $datos["idPedido"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	public function mdlEliminarPedido($tabla, $datos)
	{
		
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