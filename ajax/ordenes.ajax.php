<?php

require_once "../controladores/ordenes.controlador.php";
require_once "../modelos/ordenes.modelo.php";

class AjaxOrdenes{
	

	/*=============================================
	VALIDAR NO REPETIR ORDEN
	=============================================*/	

	public $validarOrden;

	public function ajaxValidarOrden(){

		$item = "titulo";
		$valor = $this->validarOrden;

		$respuesta = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

		echo json_encode($respuesta);

	}

	/*=============================================
	RECIBIR MULTIMEDIA
	=============================================*/
	public $imagenMultimedia;
	public $rutaMultimedia;	

	public function ajaxRecibirMultimedia(){
		

		$datos = $this->imagenMultimedia;
		$ruta = $this->rutaMultimedia;

		$respuesta = controladorOrdenes::ctrSubirMultimediaOrden($datos, $ruta);

		echo $respuesta;

	}

	/*=============================================
	GUARDAR ORDEN
	=============================================*/	

	public $creador;
	public $idOrdenObservacion;
	public $observacion1;
	public $observacion2;
	public $observacion3;
	public $observacion4;
	public $observacion5;
	public $observacion6;
	public $observacion7;
	public $observacion8;
	public $observacion9;
	public $observacion10;

	public function  ajaxCrearObservacion(){ 

		$datos = array(
			"creador"=>$this->creador,
			"idOrdenObservacion"=>$this->idOrdenObservacion,
			"observacion1"=>$this->observacion1,
			"observacion2"=>$this->observacion2,
			"observacion3"=>$this->observacion3,
			"observacion4"=>$this->observacion4,
			"observacion5"=>$this->observacion5,
			"observacion6"=>$this->observacion6,
			"observacion7"=>$this->observacion7,
			"observacion8"=>$this->observacion8,
			"observacion9"=>$this->observacion9,
			"observacion10"=>$this->observacion10
			
		);

		$respuesta = controladorOrdenes::ctrAgregarObservacion($datos);

		
		echo $respuesta;

	}

	/*=============================================
	GUARDAR OBSERVACION
	=============================================*/	

	public $tituloOrden;
	public $empresa;
	public $rutaOrden;
	public $tecnico;
	public $asesor;
	public $cliente;
	public $descripcionOrden;
	public $multimedia;
	public $fotoPortada;
	public $fotoPrincipal;
	public $partida1;
	public $partida2;
	public $partida3;
	public $partida4;
	public $partida5;
	public $partida6;
	public $partida7;
	public $partida8;
	public $partida9;
	public $partida10;
	public $precio1;
	public $precio2;
	public $precio3;
	public $precio4;
	public $precio5;
	public $precio6;
	public $precio7;
	public $precio8;
	public $precio9;
	public $precio10;
	public $totalOrden;
    public $seleccionarTecnico;
    public $seleccionarAsesor;
    public $seleccionarCliente;
    public $seleccionarEstatus;
    public $totalOrdenEditar;
    public $perfil;
    public $seleccionarPedido;
    public $EstadoDelPedido;
    public $idPedido;

    public $marcaDelEquipo;

    public $modeloDelEquipo;

    public $numeroDeSerieDelEquipo;


	public function  ajaxCrearOrden(){ 

		$datos = array(
			"empresa"=>$this->empresa,
			"tituloOrden"=>$this->tituloOrden,
			"rutaOrden"=>$this->rutaOrden,
			"tecnico"=>$this->tecnico,
			"asesor"=>$this->asesor,
			"cliente"=>$this->cliente,
			"status"=>$this->status,
			"descripcionOrden"=>$this->descripcionOrden,
			"multimedia"=>$this->multimedia,
			"fotoPortada"=>$this->fotoPortada,
			"fotoPrincipal"=>$this->fotoPrincipal,
			"partida1"=>$this->partida1,
			"partida2"=>$this->partida2,
			"partida3"=>$this->partida3,
			"partida4"=>$this->partida4,
			"partida5"=>$this->partida5,
			"partida6"=>$this->partida6,
			"partida7"=>$this->partida7,
			"partida8"=>$this->partida8,
			"partida9"=>$this->partida9,
			"partida10"=>$this->partida10,
			"precio1"=>$this->precio1,
			"precio2"=>$this->precio2,
			"precio3"=>$this->precio3,
			"precio4"=>$this->precio4,
			"precio5"=>$this->precio5,
			"precio6"=>$this->precio6,
			"precio7"=>$this->precio7,
			"precio8"=>$this->precio8,
			"precio9"=>$this->precio9,
			"precio10"=>$this->precio10,
			"totalOrden"=>$this->totalOrden,
			"perfil"=>$this->perfil,
			"marcaDelEquipo"=>$this->marcaDelEquipo,
			"modeloDelEquipo"=>$this->modeloDelEquipo,
			"numeroDeSerieDelEquipo"=>$this->numeroDeSerieDelEquipo
		);

		$respuesta = controladorOrdenes::ctrCrearOrden($datos);

		
		echo $respuesta;

	}
	/*=============================================
	TRAER ORDENES
	=============================================*/	

	public $idOrden;

	public function ajaxTraerOrdenes(){

		$item = "id";
		$valor = $this->idOrden;

		$respuesta = controladorOrdenes::ctrMostrarordenesParaValidar($item, $valor);

		echo json_encode($respuesta);

	}

	/*=============================================
	EDITAR ORDENES
	=============================================*/	

	public function  ajaxEditarOrden(){

		$datos = array(
			"idOrden"=>$this->id,
			"tituloOrden"=>$this->tituloOrden,
			"rutaOrden"=>$this->rutaOrden,
			"tecnico"=>$this->seleccionarTecnico,
			"asesor"=>$this->seleccionarAsesor,					
			"cliente"=>$this->seleccionarCliente,
			"estado"=>$this->seleccionarEstatus,
			"descripcionOrden"=>$this->descripcionOrden,
			"multimedia"=>$this->multimedia,
			"partida1"=>$this->partida1,
			"partida2"=>$this->partida2,
			"partida3"=>$this->partida3,
			"partida4"=>$this->partida4,
			"partida5"=>$this->partida5,
			"partida6"=>$this->partida6,
			"partida7"=>$this->partida7,
			"partida8"=>$this->partida8,
			"partida9"=>$this->partida9,
			"partida10"=>$this->partida10,
			"precio1"=>$this->precio1,
			"precio2"=>$this->precio2,
			"precio3"=>$this->precio3,
			"precio4"=>$this->precio4,
			"precio5"=>$this->precio5,
			"precio6"=>$this->precio6,
			"precio7"=>$this->precio7,
			"precio8"=>$this->precio8,
			"precio9"=>$this->precio9,
			"precio10"=>$this->precio10,
			"totalOrdenEditar"=>$this->totalOrdenEditar,
			"fotoPortada"=>$this->fotoPortada,
			"fotoPrincipal"=>$this->fotoPrincipal,
			"antiguaFotoPortada"=>$this->antiguaFotoPortada,
			"antiguaFotoPrincipal"=>$this->antiguaFotoPrincipal,
			"seleccionarPedido"=>$this->seleccionarPedido,
			"EstadoDelPedido"=>$this->EstadoDelPedido,
			"idPedido"=>$this->idPedido
			);

		$respuesta = controladorOrdenes::ctrEditarOrden($datos);
		
		echo $respuesta;

	}
	/*=============================================
	BUSCADOR DE ORDENES
	=============================================*/		
	public $datos;

	public function ajaxBuscarOrdenes(){

		$datos = $this->consulta;
		
		$respuesta = controladorOrdenes::ctrBuscadorDeOrdenes($datos);

		echo json_encode($respuesta);


	}
}
/*=============================================
VALIDAR NO REPETIR ORDEN
=============================================*/

if(isset($_POST["validarOrden"])){

	$valProducto = new AjaxOrdenes();
	$valProducto -> validarOrden = $_POST["validarOrden"];
	$valProducto -> ajaxValidarOrden();

}

/*============================================
RECIBIR ARCHIVOS MULTIMEDIA
=============================================*/
if(isset($_FILES["file"])){

	$multimedia = new AjaxOrdenes();
	$multimedia -> imagenMultimedia = $_FILES["file"];
	$multimedia -> rutaMultimedia = $_POST["ruta"];
	$multimedia -> ajaxRecibirMultimedia();

}
#CREAR OBSERVACIO
#-----------------------------------------------------------
if(isset($_POST["observacion1"])){

	$orden = new AjaxOrdenes();
	$orden -> creador = $_POST["creador"];
	$orden -> idOrdenObservacion = $_POST["idOrdenObservacion"];
	$orden -> observacion1 = $_POST["observacion1"];
	$orden -> observacion2 = $_POST["observacion2"];
	$orden -> observacion3 = $_POST["observacion3"];
	$orden -> observacion4 = $_POST["observacion4"];
	$orden -> observacion5 = $_POST["observacion5"];
	$orden -> observacion6 = $_POST["observacion6"];
	$orden -> observacion7 = $_POST["observacion7"];
	$orden -> observacion8 = $_POST["observacion8"];
	$orden -> observacion9 = $_POST["observacion9"];
	$orden -> observacion10 = $_POST["observacion10"];
	
	$orden -> ajaxCrearObservacion();

}
#CREAR ORDEN
#-----------------------------------------------------------
if(isset($_POST["tituloOrden"])){

	$orden = new AjaxOrdenes();
	$orden -> tituloOrden = $_POST["tituloOrden"];
	$orden -> empresa = $_POST["empresa"];
	$orden -> rutaOrden = $_POST["rutaOrden"];
	$orden -> tecnico = $_POST["tecnico"];
	$orden -> asesor = $_POST["asesor"];
	$orden -> cliente = $_POST["cliente"];
	$orden -> status = $_POST["status"];
	$orden -> descripcionOrden = $_POST["descripcionOrden"];
	$orden -> multimedia = $_POST["multimedia"];
	$orden -> totalOrden = $_POST["totalOrden"];

	$orden -> partida1 = $_POST["partida1"];
	$orden -> partida2 = $_POST["partida2"];
	$orden -> partida3 = $_POST["partida3"];
	$orden -> partida4 = $_POST["partida4"];
	$orden -> partida5 = $_POST["partida5"];
	$orden -> partida6 = $_POST["partida6"];
	$orden -> partida7 = $_POST["partida7"];
	$orden -> partida8 = $_POST["partida8"];
	$orden -> partida9 = $_POST["partida9"];
	$orden -> partida10 = $_POST["partida10"];
	$orden -> precio1 = $_POST["precio1"];
	$orden -> precio2 = $_POST["precio2"];
	$orden -> precio3 = $_POST["precio3"];
	$orden -> precio4 = $_POST["precio4"];
	$orden -> precio5 = $_POST["precio5"];
	$orden -> precio6 = $_POST["precio6"];
	$orden -> precio7 = $_POST["precio7"];
	$orden -> precio8 = $_POST["precio8"];
	$orden -> precio9 = $_POST["precio9"];
	$orden -> precio10 = $_POST["precio10"];
	$orden -> perfil = $_POST["perfil"];
	
	$orden -> marcaDelEquipo = $_POST["marcaDelEquipo"];
	$orden -> modeloDelEquipo = $_POST["modeloDelEquipo"];
	$orden -> numeroDeSerieDelEquipo = $_POST["numeroDeSerieDelEquipo"];	

	if(isset($_FILES["fotoPortada"])){

		$orden -> fotoPortada = $_FILES["fotoPortada"];

	}else{

		$orden -> fotoPortada = null;

	}	

	if(isset($_FILES["fotoPrincipal"])){

		$orden -> fotoPrincipal = $_FILES["fotoPrincipal"];

	}else{

		$orden -> fotoPrincipal = null;

	}	


	$orden -> ajaxCrearOrden();

}
/*=============================================
TRAER ORDENES
=============================================*/
if(isset($_POST["idOrden"])){

	$traerOrden = new AjaxOrdenes();
	$traerOrden -> idOrden = $_POST["idOrden"];
	$traerOrden -> ajaxTraerOrdenes();

}

/*=============================================
EDITAR ORDEN
=============================================*/
if(isset($_POST["id"])){

	$editarOrden = new AjaxOrdenes();
	$editarOrden -> id = $_POST["id"];
	$editarOrden -> tituloOrden = $_POST["editarOrden"];
	$editarOrden -> rutaOrden = $_POST["rutaOrden"];
	$editarOrden -> seleccionarTecnico = $_POST["seleccionarTecnico"];
	$editarOrden -> seleccionarAsesor = $_POST["seleccionarAsesor"];		
	$editarOrden -> seleccionarCliente = $_POST["seleccionarCliente"];
	$editarOrden -> seleccionarEstatus = $_POST["seleccionarEstatus"];
	$editarOrden -> descripcionOrden = $_POST["descripcionOrden"];
	$editarOrden -> totalOrdenEditar = $_POST["totalOrdenEditar"];
	$editarOrden -> multimedia = $_POST["multimedia"];
	$editarOrden -> partida1 = $_POST["partida1"];
	$editarOrden -> partida2 = $_POST["partida2"];
	$editarOrden -> partida3 = $_POST["partida3"];
	$editarOrden -> partida4 = $_POST["partida4"];
	$editarOrden -> partida5 = $_POST["partida5"];
	$editarOrden -> partida6 = $_POST["partida6"];
	$editarOrden -> partida7 = $_POST["partida7"];
	$editarOrden -> partida8 = $_POST["partida8"];
	$editarOrden -> partida9 = $_POST["partida9"];
	$editarOrden -> partida10 = $_POST["partida10"];
	$editarOrden -> precio1 = $_POST["precio1"];
	$editarOrden -> precio2 = $_POST["precio2"];
	$editarOrden -> precio3 = $_POST["precio3"];
	$editarOrden -> precio4 = $_POST["precio4"];
	$editarOrden -> precio5 = $_POST["precio5"];
	$editarOrden -> precio6 = $_POST["precio6"];
	$editarOrden -> precio7 = $_POST["precio7"];
	$editarOrden -> precio8 = $_POST["precio8"];
	$editarOrden -> precio9 = $_POST["precio9"];
	$editarOrden -> precio10 = $_POST["precio10"];
	$editarOrden -> seleccionarPedido = $_POST["seleccionarPedido"];
	$editarOrden -> idPedido = $_POST["idPedido"];
	$editarOrden -> EstadoDelPedido = $_POST["EstadoDelPedido"];

	if(isset($_FILES["fotoPortada"])){

		$editarOrden -> fotoPortada = $_FILES["fotoPortada"];

	}else{

		$editarOrden -> fotoPortada = null;

	}	

	if(isset($_FILES["fotoPrincipal"])){

		$editarOrden -> fotoPrincipal = $_FILES["fotoPrincipal"];

	}else{

		$editarOrden -> fotoPrincipal = null;

	}	

	$editarOrden -> antiguaFotoPortada = $_POST["antiguaFotoPortada"];
	$editarOrden -> antiguaFotoPrincipal = $_POST["antiguaFotoPrincipal"];

	$editarOrden -> ajaxEditarOrden();

}
/*=============================================
BUSCADOR DE ORDENES
=============================================*/
if(isset($_POST["caja_Busqueda"])){

	$realizarBusqeuda = new AjaxOrdenes();
	$realizarBusqeuda -> consulta = $_POST["caja_Busqueda"];
	$realizarBusqeuda -> ajaxBuscarOrdenes();

}