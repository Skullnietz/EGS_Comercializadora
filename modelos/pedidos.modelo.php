<?php

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/pedidos.persistencia.php";

class ModeloPedidos{
   /*=============================================
	MOSTRAR HISTORIAL PEDIDOS
	=============================================*/	
	static public function mdlMostrarHistorial($tabla, $valoru){
        
    $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_cliente = $valoru ");
    
   $stmt -> execute();



		return $stmt -> fetchAll();



		$stmt -> close();



		$stmt = null;

	}
	
	/*=============================================
	MOSTRAR PEDIDOS
	=============================================*/	

	static public function mdlMostrarPedidos($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;

	}
	
	/*=============================================
	MOSTRAR PEDIDO
	=============================================*/

	static public function mdlMostrarPedido($tabla, $item, $valor){

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
	MOSTRAR PEDIDO Empresas
	=============================================*/

	static public function mdlMostrarPedidoEmpresas($tabla, $item, $valor){

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
	CREAR PEDIDO
	=============================================*/

	static public function mdlIngresarPedido($tabla, $datos){
        throw new RuntimeException('Usa Nuevo Pedido en la pantalla de pedidos para guardar con confirmación y protección contra duplicados.');
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
	MOSTRAR PEDIDOS CON ESTADO Y EMPRESA
	=============================================*/

	static public function mdlMostrarpedidosParaEmpresaCOnestado($tabla, $item, $valor, $itemDos, $valorDos){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND $itemDos = :$itemDos");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			
			$stmt -> bindParam(":".$itemDos, $valorDos, PDO::PARAM_STR);

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
        throw new RuntimeException('Usa el detalle del pedido para editar con control de versión y confirmación de todos los campos.');
	}

	static public function mdlEliminarPedido($tabla, $datos)
	{
        return PedidosPersistencia::eliminar($datos);
	}

	/*=============================================
	EDITAR PEDIDOS DINAMICOS
	=============================================*/

	static public function mdlEditarPedidoDinamico($tabla, $datos){
        throw new RuntimeException('Esta edición no incluye control de versión. Recarga el detalle del pedido y vuelve a guardar.');
	}

	/*=============================================
	ASIGANR PEDIDO
	=============================================*/

	static public function mdlAsignarPedidoDinamico($tabla, $datos){
        return PedidosPersistencia::asignar($datos['id_pedido'], $datos['id']);
    }
	/*=============================================
	ASIGANR PEDIDO
	=============================================*/
	static public function mdlAsignarNuevoEstadoPedido($tabla, $datosEstadoPeido){
        return PedidosPersistencia::cambiarEstado($datosEstadoPeido['id'], $datosEstadoPeido['estado']);
    }
	
	/*=============================================
	MOSTRAR TOTAL PEDIDOS
	=============================================*/
	static public function mdlMostrarTotalPedido($tabla, $orden){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}
	
		/*=============================================
	MOSTRAR TOTAL PEDIDOS MES
	=============================================*/
	static public function mdlMostrarTotalPedidoMes($tabla, $orden){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE `estado` = 'Entregado/Pagado' AND MONTH(`fechaEntrega`) = MONTH(NOW()) AND YEAR(`fechaEntrega`) = YEAR(NOW()) ORDER BY $orden DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR TOTAL PEDIDOS SIN ENLACE
	=============================================*/
	static public function mdlMostrarpedidossinEnlace($tabla, $item, $valor, $itemDos, $valorDos){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND $itemDos != :$itemDos");

		$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
		$stmt -> bindParam(":".$itemDos, $valorDos, PDO::PARAM_INT);

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}

}
