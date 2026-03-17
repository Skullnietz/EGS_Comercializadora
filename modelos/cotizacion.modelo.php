<?php
require_once "conexion.php";

class CotizacionModelo
{

	/*=============================================
	INGRESAR COTIZACION
	=============================================*/
	static public function mdlIngresarCotizacion($tabla, $datos)
	{
		// Log input data for debugging
		$logMsg = "DEBUG MODELO (mdlIngresarCotizacion):\n";
		$logMsg .= "Tabla: " . $tabla . "\n";
		$logMsg .= "Datos: " . print_r($datos, true) . "\n";
		$logMsg .= "-----------------------------------\n";
		file_put_contents("debug_log.txt", $logMsg, FILE_APPEND);

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_cliente, nombre_cliente, id_vendedor, empresa, productos, impuesto, neto, total, asunto, vigencia, observaciones, descuento_porcentaje, codigo_qr, fecha) VALUES (:id_cliente, :nombre_cliente, :id_vendedor, :empresa, :productos, :impuesto, :neto, :total, :asunto, :vigencia, :observaciones, :descuento_porcentaje, :codigo_qr, NOW())");

		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre_cliente", $datos["nombre_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_INT);
		$stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
		$stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR);
		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":neto", $datos["neto"], PDO::PARAM_STR);
		$stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":asunto", $datos["asunto"], PDO::PARAM_STR);
		$stmt->bindParam(":vigencia", $datos["vigencia"], PDO::PARAM_STR);
		$stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
		$stmt->bindParam(":descuento_porcentaje", $datos["descuento_porcentaje"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo_qr", $datos["codigo_qr"], PDO::PARAM_STR);

		if ($stmt->execute()) {

			$stmt->closeCursor();
			$stmt = null;
			return "ok";

		} else {

			$stmt->closeCursor();
			$stmt = null;
			return "error";

		}

	}

	/*=============================================
	MOSTRAR COTIZACION
	=============================================*/
	static public function mdlMostrarCotizacion($tabla, $item, $valor)
	{
		$logMsg = "DEBUG MODELO (mdlMostrarCotizacion):\n";
		$logMsg .= "Tabla: " . $tabla . "\n";
		$logMsg .= "Item: " . $item . "\n";
		$logMsg .= "Valor: " . $valor . "\n";
		$logMsg .= "-----------------------------------\n";
		file_put_contents("debug_log.txt", $logMsg, FILE_APPEND);

		if ($item == "codigo_qr") {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE codigo_qr = :codigo_qr");
			$stmt->bindParam(":codigo_qr", $valor, PDO::PARAM_STR);
			$stmt->execute();

			$respuesta = $stmt->fetchAll();

		} else {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt->execute();

			$respuesta = $stmt->fetchAll();

		}

		$stmt->closeCursor();
		$stmt = null;

		return $respuesta;
	}

	static public function mdlcotizacion($tabla)
	{

		$stmt = Conexion::conectar()->prepare("SELECT nombre FROM $tabla");

		$stmt->execute();

		$resultado = $stmt->fetch();

		$stmt->closeCursor();
		$stmt = null;

		return $resultado;

	}
	
	/*=============================================
    MOSTRAR COTIZACIONES
    =============================================*/
    static public function mdlMostrarCotizaciones($tabla, $item, $valor)
    {

        if ($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

            $stmt->execute();

            return $stmt->fetchAll();

        }

        $stmt->close();

        $stmt = null;

    }



}