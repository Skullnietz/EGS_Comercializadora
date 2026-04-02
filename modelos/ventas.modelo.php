<?php

require_once "conexion.php";

class ModeloVentas{

	/*=============================================
	MOSTRAR EL TOTAL DE VENTAS
	=============================================*/	

	static public function mdlMostrarTotalVentas($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}
	
		/*=============================================
	MOSTRAR EL TOTAL DE VENTAS MES
	=============================================*/	

	static public function mdlMostrarTotalVentasMes($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla WHERE MONTH(`fecha`) = MONTH(NOW()) AND YEAR(`fecha`) = YEAR(NOW())");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}


	/*=============================================
	MOSTRAR VENTAS
	=============================================*/	

	static public function mdlMostrarVentas($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR VENTAS
	=============================================*/	

	static public function mdlMostrarVentasR($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> close();

		$stmt = null;
	
	}

	/*=============================================
	ACTUALIZAR ENVIO VENTA
	=============================================*/

	static public function mdlActualizarVenta($tabla, $item1, $valor1, $item2, $valor2){

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
	MOSTRAR VENTAS PARA TIKET
	=============================================*/

	static public function mdlMostrarVentasParaTiket($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;


	}
	/*=============================================
	MOSTRAR VENTAS PARA EMPREAS
	=============================================*/

	static public function mdlMostrarVentasParaEmpresas($tabla, $item, $valor){

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

		$stmt-> close();

		$stmt = null;


	}
	/*=============================================
	MOSTRAR VENTAS PARA TIKET
	=============================================*/

	static public function mdlMostrarVentasParaTiketimp($tabla, $item, $valor){

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
	MOSTRAR EMPRESAS PARA TIKET
	=============================================*/

	static public function mdlMostrarEmpresasParaTiketimp($tabla, $item, $valor){

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
		MOSTRAR ventas por empresa y asesor
	=============================================*/

	static public function mdlMostrarventaPorAsesoryEmpresa($tabla, $itemVentas, $valorVentas, $itemVentasDos, $valorventasDos){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $itemVentas = :$itemVentas AND $itemVentasDos = :$itemVentasDos");

			$stmt -> bindParam(":".$itemVentas, $valorVentas, PDO::PARAM_STR);
			$stmt -> bindParam(":".$itemVentasDos, $valorventasDos, PDO::PARAM_STR);

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

	static public function mdlIngresarVenta($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(productoUno, precioUno, cantidadUno, productoDos, precioDos, cantidadDos, productoTres, precioTres, cantidadTres, productoCuatro, precioCuatro, cantidadCuatro, productoCinco, precioCinco, cantidadCinco, productoSeis, precioSeis, cantidadSeis, productoSiete, precioSiete, cantidadSiete, productoOcho, precioOcho, cantidadOcho, productoNueve, precioNueve, cantidadNueve, productoDiez, precioDiez, cantidadDiez, cantidadProductos, asesor, empresa, pago, metodo, nombreCliente, correo, id_empresa, id_cliente) VALUES (:productoUno, :precioUno, :cantidadUno, :productoDos, :precioDos, :cantidadDos, :productoTres, :precioTres, :cantidadTres,:productoCuatro, :precioCuatro, :cantidadCuatro,:productoCinco, :precioCinco, :cantidadCinco,:productoSeis, :precioSeis, :cantidadSeis,:productoSiete, :precioSiete, :cantidadSiete,:productoOcho, :precioOcho, :cantidadOcho,:productoNueve, :precioNueve, :cantidadNueve,:productoDiez, :precioDiez, :cantidadDiez,  :cantidadProductos, :asesor, :empresa, :pago, :metodo, :nombreCliente, :correo, :id_empresa, :id_cliente)");

		$stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_INT);
		$stmt->bindParam(":productoUno", $datos["productoUno"], PDO::PARAM_STR);
		$stmt->bindParam(":precioUno", $datos["precioUno"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadUno", $datos["cantidadUno"], PDO::PARAM_INT);
		$stmt->bindParam(":productoDos", $datos["productoDos"], PDO::PARAM_STR);
		$stmt->bindParam(":precioDos", $datos["precioDos"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadDos", $datos["cantidadDos"], PDO::PARAM_INT);
		$stmt->bindParam(":productoTres", $datos["productoTres"], PDO::PARAM_STR);
		$stmt->bindParam(":precioTres", $datos["precioTres"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadTres", $datos["cantidadTres"], PDO::PARAM_INT);
		$stmt->bindParam(":productoCuatro", $datos["productoCuatro"], PDO::PARAM_STR);
		$stmt->bindParam(":precioCuatro", $datos["precioCuatro"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadCuatro", $datos["cantidadCuatro"], PDO::PARAM_INT);
		$stmt->bindParam(":productoCinco", $datos["productoCinco"], PDO::PARAM_STR);
		$stmt->bindParam(":precioCinco", $datos["precioCinco"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadCinco", $datos["cantidadCinco"], PDO::PARAM_INT);
		$stmt->bindParam(":productoSeis", $datos["productoSeis"], PDO::PARAM_STR);
		$stmt->bindParam(":precioSeis", $datos["precioSeis"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadSeis", $datos["cantidadSeis"], PDO::PARAM_INT);
		$stmt->bindParam(":productoSiete", $datos["productoSiete"], PDO::PARAM_STR);
		$stmt->bindParam(":precioSiete", $datos["precioSiete"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadSiete", $datos["cantidadSiete"], PDO::PARAM_INT);
		$stmt->bindParam(":productoOcho", $datos["productoOcho"], PDO::PARAM_STR);
		$stmt->bindParam(":precioOcho", $datos["precioOcho"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadOcho", $datos["cantidadOcho"], PDO::PARAM_INT);
		$stmt->bindParam(":productoNueve", $datos["productoNueve"], PDO::PARAM_STR);
		$stmt->bindParam(":precioNueve", $datos["precioNueve"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadNueve", $datos["cantidadNueve"], PDO::PARAM_INT);
		$stmt->bindParam(":productoDiez", $datos["productoDiez"], PDO::PARAM_STR);
		$stmt->bindParam(":precioDiez", $datos["precioDiez"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidadDiez", $datos["cantidadDiez"], PDO::PARAM_INT);
		$stmt->bindParam(":cantidadProductos", $datos["cantidadProductos"], PDO::PARAM_INT);
		$stmt->bindParam(":asesor", $datos["asesor"], PDO::PARAM_STR);  
		$stmt->bindParam(":pago", $datos["pago"], PDO::PARAM_STR);
		$stmt->bindParam(":metodo", $datos["metodo"], PDO::PARAM_STR);
		$stmt->bindParam(":nombreCliente", $datos["nombreCliente"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);

		$stmt->bindParam(":id_empresa", $datos["empresa"], PDO::PARAM_STR);

		$idCliente = isset($datos["id_cliente"]) && $datos["id_cliente"] > 0 ? $datos["id_cliente"] : null;
		$stmt->bindParam(":id_cliente", $idCliente, PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}

	/*=============================================
	OBTENER ÚLTIMA VENTA INSERTADA (para vincular canje)
	=============================================*/
	static public function mdlObtenerUltimaVenta($idEmpresa){
		$pdo = Conexion::conectar();
		$stmt = $pdo->prepare("SELECT id FROM compras WHERE id_empresa = :id_empresa ORDER BY id DESC LIMIT 1");
		$stmt->bindParam(":id_empresa", $idEmpresa, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? intval($result["id"]) : 0;
	}


	public function mdlEliminarVenta($tabla, $datos){
		
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
	RANGO FECHAS
	=============================================*/	

	static public function mdlRangoFechasVentas($tabla, $fechaInicial, $fechaFinal, $itemEmpresa, $valorEmpresa){

		if($fechaInicial == null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $itemEmpresa = $valorEmpresa ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();	


		}else if($fechaInicial == $fechaFinal){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE fecha like '%$fechaFinal%' AND $itemEmpresa = :$itemEmpresa");

			$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);
			$stmt -> bindParam(":".$itemEmpresa, $valorEmpresa, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$fechaActual = new DateTime();
			$fechaActual ->add(new DateInterval("P1D"));
			$fechaActualMasUno = $fechaActual->format("Y-m-d");

			$fechaFinal2 = new DateTime($fechaFinal);
			$fechaFinal2 ->add(new DateInterval("P1D"));
			$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

			if($fechaFinalMasUno == $fechaActualMasUno){

				$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinalMasUno' AND $itemEmpresa = $valorEmpresa");

			}else{


				$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND $itemEmpresa = $valorEmpresa");

			}
		
			$stmt -> execute();

			return $stmt -> fetchAll();

		}

	}

	/*=============================================
	MOSTRAR EMPRESAS PARA REPORTE POR FECHA
	=============================================*/

	static public function mdlMostrarEmpresasParaReporte($tablaDos, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDos WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDos");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;


	}


	/*=============================================
	SUMAR EL TOTAL DE LAS VENTAS
	=============================================*/
	static public function mdlSumarTotalVentas($tabla, $fechaInicial, $fechaFinal){
		
	if($fechaInicial == null){

				$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla");

				$stmt -> execute();

				return $stmt -> fetchAll();	


			}else if($fechaInicial == $fechaFinal){

				$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla WHERE fecha like '%$fechaFinal%'");

				$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

				$stmt -> execute();

				return $stmt -> fetchAll();

			}else{

				$fechaActual = new DateTime();
				$fechaActual ->add(new DateInterval("P1D"));
				$fechaActualMasUno = $fechaActual->format("Y-m-d");

				$fechaFinal2 = new DateTime($fechaFinal);
				$fechaFinal2 ->add(new DateInterval("P1D"));
				$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

				if($fechaFinalMasUno == $fechaActualMasUno){

					$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla  BETWEEN '$fechaInicial' AND '$fechaFinalMasUno'");

				}else{


					$stmt = Conexion::conectar()->prepare("SELECT SUM(pago) as total FROM $tabla  WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal'");

				}
			
				$stmt -> execute();

				return $stmt -> fetchAll();

			}

		}

	/*=============================================
	SUMAR EL TOTAL DE LAS VENTAS
	=============================================*/
	static public function mdlSumarTotalOrdenes($tabla, $fechaInicial, $fechaFinal){
		
	if($fechaInicial == null){

				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla");

				$stmt -> execute();

				return $stmt -> fetchAll();	


			}else if($fechaInicial == $fechaFinal){

				$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla WHERE fecha like '%$fechaFinal%'");

				$stmt -> bindParam(":fecha", $fechaFinal, PDO::PARAM_STR);

				$stmt -> execute();

				return $stmt -> fetchAll();

			}else{

				$fechaActual = new DateTime();
				$fechaActual ->add(new DateInterval("P1D"));
				$fechaActualMasUno = $fechaActual->format("Y-m-d");

				$fechaFinal2 = new DateTime($fechaFinal);
				$fechaFinal2 ->add(new DateInterval("P1D"));
				$fechaFinalMasUno = $fechaFinal2->format("Y-m-d");

				if($fechaFinalMasUno == $fechaActualMasUno){

					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla  BETWEEN '$fechaInicial' AND '$fechaFinalMasUno'");

				}else{


					$stmt = ConexionWP::conectarWP()->prepare("SELECT SUM(total) as total FROM $tabla  WHERE fecha BETWEEN '$fechaInicial' AND '$fechaFinal'");

				}
			
				$stmt -> execute();

				return $stmt -> fetchAll();

			}

		}

	public function mdlIngresarVentaDinamica($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_usuario, id_Asesor, productos, impuesto, neto, pago, id_empresa, inversion, metodo) VALUES (:id_usuario, :id_Asesor, :productos, :impuesto, :neto, :pago, :id_empresa, :inversion, :metodo)");

		$stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
		$stmt->bindParam(":id_Asesor", $datos["id_Asesor"], PDO::PARAM_INT);
		$stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR);
		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":neto", $datos["neto"], PDO::PARAM_STR);
		$stmt->bindParam(":pago", $datos["pago"], PDO::PARAM_STR);
		$stmt->bindParam(":inversion", $datos["inversion"], PDO::PARAM_STR);
		$stmt->bindParam(":id_empresa", $datos["id_empresa"], PDO::PARAM_STR);
		$stmt->bindParam(":metodo", $datos["metodo"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}


}