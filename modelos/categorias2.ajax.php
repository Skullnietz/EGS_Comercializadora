<?php

require_once "../controladores/categorias2.controlador.php";
require_once "../modelos/categorias2.modelo.php";

//require_once "../controladores/subcategorias.controlador.php";
require_once "../modelos/subcategorias2.modelo.php";

//require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

class AjaxCategorias{

	public $activarCategoria;
  	public $activarId;

  	public function ajaxActivarCategoria(){


	    ModeloSubCategorias::mdlActualizarSubCategorias2("subcategorias", "estado", $this->activarCategoria, "id_categoria", $this->activarId);

	   ModeloProductos::mdlActualizarProductos("productos", "estado", $this->activarCategoria, "id_categoria", $this->activarId);

	  	$respuesta = ModeloCategorias::mdlActualizarCategoria2("categorias", "estado", $this->activarCategoria, "id", $this->activarId);

	  	echo $respuesta;
  	}

  	/*=============================================
  	VALIDAR NO REPETIR CATEGORÍA
  	=============================================*/ 

  	public $validarCategoria;

  	public function ajaxValidarCategoria2(){

    $item = "categoria";
    $valor = $this->validarCategoria2;

    	$respuesta = ControladorCategorias::ctrMostrarCategorias2($item, $valor);

    	echo json_encode($respuesta);

 	 }

  /*=============================================
  EDITAR CATEGORIA
  =============================================*/ 

  public $idCategoria;

  public function ajaxEditarCategoria2(){

    $item = "id";
    $valor = $this->idCategoria2;

    $respuesta = ControladorCategorias::ctrMostrarCategorias2($item, $valor);

    echo json_encode($respuesta);

  }

}

/*=============================================
ACTIVAR CATEGORIA
=============================================*/

if(isset($_POST["activarCategoria2"])){

	$activarCategoria = new AjaxCategorias2();
	$activarCategoria -> activarCategoria = $_POST["activarCategoria2"];
	$activarCategoria -> activarId = $_POST["activarId"];
	$activarCategoria -> ajaxActivarCategoria2();

}


/*=============================================
VALIDAR NO REPETIR CATEGORÍA
=============================================*/

if(isset( $_POST["validarCategoria2"])){

  $valCategoria = new AjaxCategorias2();
  $valCategoria -> validarCategoria = $_POST["validarCategoria2"];
  $valCategoria -> ajaxValidarCategoria2();

}

/*=============================================
EDITAR CATEGORIA
=============================================*/
if(isset($_POST["idCategoria2"])){

  $editar = new AjaxCategorias();
  $editar -> idCategoria = $_POST["idCategoria2"];
  $editar -> ajaxEditarCategoria2();

}