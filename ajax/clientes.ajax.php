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

	/*=============================================
	VALIDAR DUPLICADOS (nombre y/o whatsapp)
	=============================================*/
	public $validarNombre;
	public $validarWhatsapp;
	public $validarExcluirId;

	public function ajaxValidarDuplicado(){
		$resultado = ModeloClientes::mdlValidarDuplicado(
			"clientesTienda",
			$this->validarNombre,
			$this->validarWhatsapp,
			$this->validarExcluirId
		);

		if($resultado){
			$tipo = "";
			$nombreLimpio = strtolower(trim($this->validarNombre));
			$whatsLimpio = trim($this->validarWhatsapp);
			$nombreDB = strtolower(trim($resultado["nombre"]));

			if(!empty($nombreLimpio) && $nombreDB === $nombreLimpio){
				$tipo = "nombre";
			}
			if(!empty($whatsLimpio) && $whatsLimpio !== "sin whatsapp" &&
			   ($resultado["telefonoDos"] === $whatsLimpio || $resultado["telefono"] === $whatsLimpio)){
				$tipo = ($tipo === "nombre") ? "ambos" : "whatsapp";
			}

			echo json_encode([
				"duplicado" => true,
				"tipo" => $tipo,
				"cliente" => $resultado["nombre"],
				"id" => $resultado["id"]
			]);
		} else {
			echo json_encode(["duplicado" => false]);
		}
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
VALIDAR DUPLICADOS (nombre y/o whatsapp)
=============================================*/
if(isset($_POST["validarDuplicadoCliente"])){

	$val = new AjxClientes();
	$val->validarNombre = trim($_POST["validarNombre"] ?? '');
	$val->validarWhatsapp = trim($_POST["validarWhatsapp"] ?? '');
	$val->validarExcluirId = !empty($_POST["validarExcluirId"]) ? intval($_POST["validarExcluirId"]) : null;
	$val->ajaxValidarDuplicado();

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

	// Validar duplicados antes de insertar
	$duplicado = ModeloClientes::mdlValidarDuplicado("clientesTienda", $nombre, $whatsapp);
	if($duplicado){
		$msg = "Ya existe un cliente registrado";
		$nombreDB = strtolower(trim($duplicado["nombre"]));
		$nombreInput = strtolower(trim($nombre));
		if($nombreDB === $nombreInput){
			$msg = "El cliente \"" . $duplicado["nombre"] . "\" ya está registrado";
		} elseif($duplicado["telefonoDos"] === $whatsapp || $duplicado["telefono"] === $whatsapp){
			$msg = "El WhatsApp " . $whatsapp . " ya está registrado con el cliente \"" . $duplicado["nombre"] . "\"";
		}
		echo json_encode(["status" => "error", "mensaje" => $msg, "duplicado" => true, "clienteExistente" => $duplicado["nombre"]]);
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