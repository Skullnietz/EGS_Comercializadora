<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";


class AjxClientes{
	
	/*=============================================
  	TRAER DATOS CLIENTE
 	=============================================*/ 
 	public $idCliente;
	public function ajaxEditarCliente(){
		
		$item = "id";
		$valor = $this->idCliente;

		$respuesta = ControladorClientes::ctrMostrarClientes($item, $valor);

		echo json_encode($respuesta);
	}

	/*=============================================
	VALIDAR NO REPETIR CLIENTE
	=============================================*/	
	public $nombreCliente;

	public function ajaxValidarNorepetirCliente(){
		
		$item = "nombre";
		$valor = $this->nombreCliente;

		$respuesta = ControladorClientes::ctrMostrarClientesTabla($item, $valor);

		echo json_encode($respuesta);
	}
}

/*=============================================
TRAER DATOS CLIENTE
=============================================*/
if(isset($_POST["idCliente"])){

	$editar = new AjxClientes();
	$editar -> idCliente = $_POST["idCliente"];
	$editar -> ajaxEditarCliente();

}

/*=============================================
	VALIDAR NO REPETIR CLIENTE
=============================================*/
if(isset($_POST["nombreCliente"])){

	$valProducto = new AjxClientes();
	$valProducto -> nombreCliente = $_POST["nombreCliente"];
	$valProducto -> ajaxValidarNorepetirCliente();

}

/*=============================================
CREAR CLIENTE RÁPIDO (desde formulario de venta)
=============================================*/
if(isset($_POST["crearClienteRapido"])){

	$nombre = trim($_POST["nombreClienteRapido"] ?? '');
	$whatsapp = trim($_POST["whatsappClienteRapido"] ?? '');
	$idEmpresa = intval($_POST["empresaClienteRapido"] ?? 0);

	if($nombre === '' || $whatsapp === ''){
		echo json_encode(["status" => "error", "mensaje" => "El nombre y WhatsApp son obligatorios"]);
		exit;
	}

	try {
		$pdo = Conexion::conectar();
		$stmt = $pdo->prepare("INSERT INTO clientesTienda (nombre, telefono, telefonoDos, correo, id_Asesor, etiqueta, id_empresa) VALUES (:nombre, :telefono, :telefonoDos, '', 0, 0, :id_empresa)");
		$stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $whatsapp, PDO::PARAM_STR);
		$stmt->bindParam(":telefonoDos", $whatsapp, PDO::PARAM_STR);
		$stmt->bindParam(":id_empresa", $idEmpresa, PDO::PARAM_INT);
		
		if($stmt->execute()){
			$nuevoId = $pdo->lastInsertId();
			echo json_encode(["status" => "ok", "id" => intval($nuevoId), "nombre" => $nombre]);
		} else {
			echo json_encode(["status" => "error", "mensaje" => "No se pudo guardar el cliente"]);
		}
	} catch(Exception $e) {
		echo json_encode(["status" => "error", "mensaje" => "Error al crear el cliente"]);
	}
	exit;
}