<?php
session_start();

if (!isset($_SESSION["perfil"]) || $_SESSION["perfil"] == "tecnico") {
	http_response_code(isset($_SESSION["perfil"]) ? 403 : 401);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['ok' => false, 'mensaje' => isset($_SESSION["perfil"])
		? 'Tu usuario no tiene permiso para modificar pedidos.'
		: 'Tu sesión expiró y el cambio no se guardó. Inicia sesión en otra pestaña y vuelve a guardar aquí; no cierres esta página para no perder lo capturado.']);
	exit;
}

require_once "../controladores/pedidos.controlador.php";
require_once "../modelos/pedidos.modelo.php";

/*=============================================
DETALLE DEL PEDIDO (AJAX)
Va antes del despachador: también se envía "idPedido".
=============================================*/
if (isset($_POST['accionPedido'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    try {
        $id = $_POST['idPedido'] ?? 0;
        $respuesta = [];
        switch ($_POST['accionPedido']) {
            case 'guardar':
                $entrada = ['estado' => $_POST['estado'] ?? '', 'total' => $_POST['total'] ?? '', 'productos' => $_POST['productos'] ?? '', 'pagos' => $_POST['pagos'] ?? ''];
                $respuesta['version'] = PedidosPersistencia::version(PedidosPersistencia::guardar($id, $entrada, $_POST['versionPedido'] ?? ''));
                break;
            case 'agregarObservacion':
                $respuesta['observacion'] = PedidosPersistencia::agregarObservacion($id, $_POST['observacion'] ?? '', $_POST['ref'] ?? null);
                break;
            case 'quitarObservacion':
                PedidosPersistencia::quitarObservacion($id, json_decode($_POST['observacion'] ?? '', true));
                break;
            default:
                throw new InvalidArgumentException('La acción no es válida. Recarga la página.');
        }
        echo json_encode(['ok' => true] + $respuesta);
    } catch (Throwable $e) {
        // El motivo queda en el error_log del servidor para soporte.
        error_log('[pedidos] ' . $_POST['accionPedido'] . ' pedido ' . ($_POST['idPedido'] ?? '') . ' usuario ' . ($_SESSION['nombre'] ?? '') . ': ' . get_class($e) . ': ' . $e->getMessage());
        if ($e instanceof PDOException) {
            http_response_code(500);
            $codigo = $e->errorInfo[1] ?? $e->getCode();
            $mensaje = $codigo == 1406
                ? 'El pedido llegó al límite de espacio para ese dato y el cambio no se guardó. Avisa a soporte (código 1406).'
                : 'La base de datos rechazó el cambio (código ' . $codigo . '). Avisa a soporte con este código.';
        } elseif ($e instanceof InvalidArgumentException || $e instanceof RuntimeException) {
            http_response_code(409);
            $mensaje = $e->getMessage();
        } else {
            http_response_code(500);
            $mensaje = 'Ocurrió un error interno y el cambio no se guardó. Avisa a soporte.';
        }
        echo json_encode(['ok' => false, 'mensaje' => $mensaje]) ?: '{"ok":false,"mensaje":"No se pudo guardar."}';
    }
    exit;
}

/* Pestañas abiertas antes de esta versión: sus datos ya no son compatibles. */
if (isset($_POST['guardarPedidoDetalle']) || isset($_POST['idPedidoDinamicoAjax'])) {
    http_response_code(409);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'mensaje' => 'Esta página es de una versión anterior y no puede guardar. Recarga la página (F5) y vuelve a capturar tus cambios.']);
    exit;
}

class AjaxPedidos{
	
		public $empresaPedido;
		public $AsesorPedido;
		public $clientePeido;
		public $Producto1;
		public $precioProducto1;
		public $cantidadProducto1;
		public $totalPedidoUno;
		public $Producto2;
		public $precioProducto2;
		public $cantidadProducto2;
		public $totalPedidoDos;
		public $Producto3;
		public $precioProducto3;
		public $cantidadProducto3;
		public $totalPedidoTres;
		public $Producto4;
		public $precioProducto4;
		public $cantidadProducto4;
		public $totalPedidoCuatro;
		public $Producto5;
		public $precioProducto5;
		public $cantidadProducto5;
		public $totalPedidoCinco;
		public $metodo;
		public $pagoClientePedido;
		public $pagoPedido;
		public $adeudo;
		public $IngresarEstadoDelPedido;

	function ajaxCrearPedido(){
		
		/*=============================================
		GUARDAR PEDIDO
		=============================================*/

		$datos = array("empresaPedido"=>$this->empresaPedido,
					   "clientePeido"=>$this->clientePeido,
					   "AsesorPedido"=>$this->AsesorPedido,
					   "Producto1"=>$this->Producto1,
					   "precioProducto1"=>$this->precioProducto1,
					   "cantidadProducto1"=>$this->cantidadProducto1,
					   "totalPedidoUno"=>$this->totalPedidoUno,
					   "Producto2"=>$this->Producto2,
					   "precioProducto2"=>$this->precioProducto2,
					   "cantidadProducto2"=>$this->cantidadProducto2,
					   "totalPedidoDos"=>$this->totalPedidoDos,
					   "Producto3"=>$this->Producto3,
					   "precioProducto3"=>$this->precioProducto3,
					   "cantidadProducto3"=>$this->cantidadProducto3,
					   "totalPedidoTres"=>$this->totalPedidoTres,
					   "Producto4"=>$this->Producto4,
					   "precioProducto4"=>$this->precioProducto4,
					   "cantidadProducto4"=>$this->cantidadProducto4,
					   "totalPedidoCuatro"=>$this->totalPedidoCuatro,
					   "Producto5"=>$this->Producto5,
					   "precioProducto5"=>$this->precioProducto5,
					   "cantidadProducto5"=>$this->cantidadProducto5,
					   "totalPedidoCinco"=>$this->totalPedidoCinco,
					   "pagoClientePedido"=>$this->pagoClientePedido,
					   "metodo"=>$this->metodo,
					   "pagoPedido"=>$this->pagoPedido,
					   "adeudo"=>$this->adeudo,
					   "IngresarEstadoDelPedido"=>$this->IngresarEstadoDelPedido
	 	);

		$respuesta = ControladorPedidos::ctrCrearPedido($datos);

		
		echo $respuesta;
	}

	/*=============================================
	TRAER PEDIDOS
	=============================================*/	

	public $idPedido;

	public function ajaxTraerPedidos(){

		$item = "id";
		$valor = $this->idPedido;

		$respuesta = ControladorPedidos::ctrMostrarorpedidosParaValidar($item, $valor);

		echo json_encode($respuesta);

	}

	/*=============================================
	EDITAR PEDIDOS
	=============================================*/
	
	public $abono1;
	public $fechaAbono1;
	public $abono2;
	public $fechaAbono2;
	public $abono3;
	public $fechaAbono3;
	public $abono4;
	public $fechaAbono4;
	public $abono5;
	public $fechaAbono5;
	public $adeudoPedidoEditado;
	public $edicionProductoUnoPedido;
	public $edicionProductoUnoPedidoDos;
	public $EstadoDelPedido;

	public function  ajaxEditarPedido(){

		
		$datos = array(
			"idPedido"=>$this->id,
			"edicionProductoUnoPedido"=>$this->edicionProductoUnoPedido,
			"abono1"=>$this->abono1,
			"fechaAbono1"=>$this->fechaAbono1,
			"edicionProductoUnoPedidoDos"=>$this->edicionProductoUnoPedidoDos,
			"abono2"=>$this->abono2,
			"fechaAbono2"=>$this->fechaAbono2,					
			"abono3"=>$this->abono3,
			"fechaAbono3"=>$this->fechaAbono3,
			"abono4"=>$this->abono4,
			"fechaAbono4"=>$this->fechaAbono4,
			"abono5"=>$this->abono5,
			"fechaAbono5"=>$this->fechaAbono5,
			"adeudoPedidoEditado"=>$this->adeudoPedidoEditado,
			"EstadoDelPedido"=>$this->EstadoDelPedido
			);

		$respuesta = ControladorPedidos::ctrEditarPedido($datos);
		
		echo $respuesta;

	}	

}
#CREAR PEDIDO
#-----------------------------------------------------------
if(isset($_POST["empresaPedido"])){

	$pedido = new AjaxPedidos();
	$pedido -> empresaPedido = $_POST["empresaPedido"];
	$pedido -> clientePeido = $_POST["clientePeido"];
	$pedido -> AsesorPedido = $_POST["AsesorPedido"];
	$pedido -> Producto1 = $_POST["Producto1"];
	$pedido -> cantidadProducto1 = $_POST["cantidadProducto1"];
	$pedido -> precioProducto1 = $_POST["precioProducto1"];
	$pedido -> totalPedidoUno = $_POST["totalPedidoUno"];
	$pedido -> Producto2 = $_POST["Producto2"];
	$pedido -> precioProducto2 = $_POST["precioProducto2"];
	$pedido -> cantidadProducto2 = $_POST["cantidadProducto2"];
	$pedido -> totalPedidoDos = $_POST["totalPedidoDos"];
	$pedido -> Producto3 = $_POST["Producto3"];
	$pedido -> IngresarEstadoDelPedido = $_POST["IngresarEstadoDelPedido"];

	$pedido -> precioProducto3 = $_POST["precioProducto3"];
	$pedido -> cantidadProducto3 = $_POST["cantidadProducto3"];
	$pedido -> totalPedidoTres = $_POST["totalPedidoTres"];
	$pedido -> Producto4 = $_POST["Producto4"];
	$pedido -> precioProducto4 = $_POST["precioProducto4"];
	$pedido -> cantidadProducto4 = $_POST["cantidadProducto4"];
	$pedido -> totalPedidoCuatro = $_POST["totalPedidoCuatro"];
	$pedido -> Producto5 = $_POST["Producto5"];
	$pedido -> precioProducto5 = $_POST["precioProducto5"];
	$pedido -> cantidadProducto5 = $_POST["cantidadProducto5"];
	$pedido -> totalPedidoCinco = $_POST["totalPedidoCinco"];
	$pedido -> metodo = $_POST["metodo"];
	$pedido -> pagoClientePedido = $_POST["pagoClientePedido"];
	$pedido -> pagoPedido = $_POST["pagoPedido"];
	$pedido -> adeudo = $_POST["adeudo"];

	$pedido -> ajaxCrearPedido();

}

/*=============================================
TRAER ORDENES
=============================================*/
if(isset($_POST["idPedido"])){

	$traerPedido = new AjaxPedidos();
	$traerPedido -> idPedido = $_POST["idPedido"];
	$traerPedido -> ajaxTraerPedidos();

}
/*=============================================
EDITAR ORDEN
=============================================*/
if(isset($_POST["id"])){

	$editarPedido = new AjaxPedidos();
	$editarPedido -> id = $_POST["id"];
	$editarPedido -> edicionProductoUnoPedido = $_POST["edicionProductoUnoPedido"];
	$editarPedido -> abono1 = $_POST["abono1"];
	$editarPedido -> fechaAbono1 = $_POST["fechaAbono1"];
	$editarPedido -> edicionProductoUnoPedidoDos = $_POST["edicionProductoUnoPedidoDos"];
	$editarPedido -> abono2 = $_POST["abono2"];
	$editarPedido -> fechaAbono2 = $_POST["fechaAbono2"];
	$editarPedido -> abono3 = $_POST["abono3"];		
	$editarPedido -> fechaAbono3 = $_POST["fechaAbono3"];
	$editarPedido -> abono4 = $_POST["abono4"];
	$editarPedido -> fechaAbono4 = $_POST["fechaAbono4"];
	$editarPedido -> abono5 = $_POST["abono5"];
	$editarPedido -> fechaAbono5 = $_POST["fechaAbono5"];
	$editarPedido -> adeudoPedidoEditado = $_POST["adeudoPedidoEditado"];
	$editarPedido -> EstadoDelPedido = $_POST["EstadoDelPedido"];

	$editarPedido -> ajaxEditarPedido();

}
