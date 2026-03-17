<?php



require_once "conexion.php";



class ModeloProductos{



	/*=============================================

	MOSTRAR EL TOTAL DE VENTAS

	=============================================*/	



	static public function mdlMostrarTotalProductos($tabla, $orden){

	

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");



		$stmt -> execute();



		return $stmt -> fetchAll();



		$stmt-> close();



		$stmt = null;



	}



	/*=============================================

	MOSTRAR SUMA VENTAS

	=============================================*/	



	static public function mdlMostrarSumaVentas($tabla){



		$stmt = Conexion::conectar()->prepare("SELECT SUM(ventas) as total FROM $tabla");



		$stmt -> execute();



		return $stmt -> fetch();



		$stmt -> close();



		$stmt = null;



	}



	/*=============================================

	ACTUALIZAR PRODUCTOS

	=============================================*/



	static public function mdlActualizarProductos($tabla, $item1, $valor1, $item2, $valor2){



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

	ACTUALIZAR OFERTA PRODUCTOS

	=============================================*/

	static public function mdlActualizarOfertaProductos($tabla, $datos, $ofertadoPor, $precioOfertaActualizado, $descuentoOfertaActualizado, $idOferta){



		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $ofertadoPor = :$ofertadoPor, oferta = :oferta, precioOferta = :precioOferta, descuentoOferta = :descuentoOferta, imgOferta = :imgOferta, finOferta = :finOferta WHERE id = :id");



		$stmt->bindParam(":".$ofertadoPor, $datos["oferta"], PDO::PARAM_STR);

		$stmt->bindParam(":oferta", $datos["oferta"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOferta", $precioOfertaActualizado, PDO::PARAM_STR);

		$stmt->bindParam(":descuentoOferta", $descuentoOfertaActualizado, PDO::PARAM_STR);

		$stmt->bindParam(":imgOferta", $datos["imgOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":finOferta", $datos["finOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":id", $idOferta, PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}



		$stmt->close();

		$stmt = null;



	}



	/*=============================================

	MOSTRAR PRODUCTOS

	=============================================*/



	static public function mdlMostrarProductos($tabla, $item, $valor){



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

	CREAR PRODUCTO

	=============================================*/



	static public function mdlIngresarProducto($tabla, $datos){



		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_categoria, id_subcategoria, empresa, tipo, ruta, estado, titulo, titular, descripcion, multimedia, detalles, precio, portada, oferta, precioOferta, descuentoOferta, imgOferta, finOferta, peso, entrega, disponibilidad, Proveedor, codigo, inversion, medida, disponibilidadPor, id_almacen, id_empresa) VALUES (:id_categoria, :id_subcategoria, :empresa, :tipo, :ruta, :estado, :titulo, :titular, :descripcion, :multimedia, :detalles, :precio, :portada, :oferta, :precioOferta, :descuentoOferta, :imgOferta, :finOferta,  :peso, :entrega, :disponibilidad, :Proveedor, :codigo, :inversion, :medida, :disponibilidadPor, :id_almacen, :id_empresa)");



		$stmt->bindParam(":id_categoria", $datos["idCategoria"], PDO::PARAM_STR);

		$stmt->bindParam(":disponibilidadPor", $datos["cantidadTipo"], PDO::PARAM_STR); 

		$stmt->bindParam(":inversion", $datos["EntradInversion"], PDO::PARAM_STR);

		$stmt->bindParam(":medida", $datos["medida"], PDO::PARAM_STR);

		$stmt->bindParam(":codigo", $datos["SubircodigoProducto"], PDO::PARAM_STR);

		$stmt->bindParam(":id_subcategoria", $datos["idSubCategoria"], PDO::PARAM_STR);

		$stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);

		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);

		$stmt->bindParam(":ruta", $datos["ruta"], PDO::PARAM_STR);

		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);

		$stmt->bindParam(":titular", $datos["titular"], PDO::PARAM_STR);

		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);

		$stmt->bindParam(":multimedia", $datos["multimedia"], PDO::PARAM_STR);

		$stmt->bindParam(":detalles", $datos["detalles"], PDO::PARAM_STR);

		$stmt->bindParam(":precio", $datos["precio"], PDO::PARAM_STR);

		$stmt->bindParam(":portada", $datos["imgFotoPrincipal"], PDO::PARAM_STR);

		$stmt->bindParam(":oferta", $datos["oferta"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOferta", $datos["precioOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":descuentoOferta", $datos["descuentoOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":imgOferta", $datos["imgOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":finOferta", $datos["finOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":peso", $datos["peso"], PDO::PARAM_STR);

		$stmt->bindParam(":entrega", $datos["entrega"], PDO::PARAM_STR);

		$stmt->bindParam(":disponibilidad", $datos["disponibilidad"], PDO::PARAM_STR);

		$stmt->bindParam(":Proveedor", $datos["Proveedor"], PDO::PARAM_STR);

		$stmt->bindParam(":id_almacen", $datos["id_almacen"], PDO::PARAM_INT);

		$stmt->bindParam(":id_empresa", $datos["id_empresa"], PDO::PARAM_INT);

		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}



		$stmt->close();

		$stmt = null;



	}



		/*=============================================

	EDITAR PRODUCTO

	=============================================*/



	static public function mdlEditarProducto($tabla, $datos){



		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET id_categoria = :id_categoria, id_subcategoria = :id_subcategoria, empresa = :empresa, tipo = :tipo, ruta = :ruta, estado = :estado, titulo = :titulo, titular = :titular, descripcion = :descripcion, multimedia = :multimedia, detalles = :detalles, precio = :precio, portada = :portada, oferta = :oferta, precioOferta = :precioOferta, descuentoOferta = :descuentoOferta, imgOferta = :imgOferta, finOferta = :finOferta, peso = :peso, entrega = :entrega, disponibilidad = :disponibilidad, Proveedor = :Proveedor, codigo = :codigo, inversion = :inversion, medida = :medida WHERE id = :id");



		$stmt->bindParam(":id_categoria", $datos["idCategoria"], PDO::PARAM_STR);

		$stmt->bindParam(":codigo", $datos["codigoEditado"], PDO::PARAM_STR);

		$stmt->bindParam(":medida", $datos["medida"], PDO::PARAM_STR);

		$stmt->bindParam(":id_subcategoria", $datos["idSubCategoria"], PDO::PARAM_STR);

		$stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);

		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);

		$stmt->bindParam(":ruta", $datos["ruta"], PDO::PARAM_STR);

		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);

		$stmt->bindParam(":titular", $datos["titular"], PDO::PARAM_STR);

		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);

		$stmt->bindParam(":multimedia", $datos["multimedia"], PDO::PARAM_STR);

		$stmt->bindParam(":detalles", $datos["detalles"], PDO::PARAM_STR);

		$stmt->bindParam(":precio", $datos["precio"], PDO::PARAM_STR);

		$stmt->bindParam(":portada", $datos["imgFotoPrincipal"], PDO::PARAM_STR);

		$stmt->bindParam(":oferta", $datos["oferta"], PDO::PARAM_STR);

		$stmt->bindParam(":precioOferta", $datos["precioOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":descuentoOferta", $datos["descuentoOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":imgOferta", $datos["imgOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":finOferta", $datos["finOferta"], PDO::PARAM_STR);

		$stmt->bindParam(":peso", $datos["peso"], PDO::PARAM_STR);

		$stmt->bindParam(":entrega", $datos["entrega"], PDO::PARAM_STR);

		$stmt->bindParam(":disponibilidad", $datos["disponibilidad"], PDO::PARAM_STR);

		$stmt->bindParam(":Proveedor", $datos["Proveedor"], PDO::PARAM_STR);

		$stmt->bindParam(":inversion", $datos["inversionEditada"], PDO::PARAM_STR); 

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}



		$stmt->close();

		$stmt = null;



	}



	/*=============================================

	ELIMINAR PRODUCTO

	=============================================*/



	static public function mdlEliminarProducto($tabla, $datos){



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





	static public function mdlMostrarProdutosConAletaDeStock($tabla){



		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE disponibilidad > 5  AND disponibilidad <= 15  AND estado != 0");



		$stmt -> execute();



		return $stmt -> fetchAll();



		$stmt -> close();



		$stmt = null;

	}



	static public function mdlMostrarProductosApuntoDeAgotarse($tabla){



		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE disponibilidad < 5 AND estado != 0");



		$stmt -> execute();



		return $stmt -> fetchAll();



		$stmt -> close();



		$stmt = null;

	}



	/*=============================================

	ACTUALIZAR PRODUCTO

	=============================================*/

	static public function mdlActualizarProductoVentasDinamicas($tabla, $item1, $valor1, $valor2){



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

	ACTUALIZAR STOCK

	=============================================*/



	static public function mdlEditarStock($tabla, $datos){



	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET disponibilidad = :disponibilidad, inversion = :inversion WHERE id = :id");



		$stmt->bindParam(":disponibilidad", $datos["stock"], PDO::PARAM_STR);

		$stmt->bindParam(":inversion", $datos["inversion"], PDO::PARAM_STR);

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);



		if($stmt->execute()){



			return "ok";



		}else{



			return "error";

		

		}



		$stmt->close();

		$stmt = null;


	}


}