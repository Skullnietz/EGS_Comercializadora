<?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../modelos/modelo.asesores.php";
require_once "../controladores/controlador.asesore.php";

class AjaxUsuarios{

  /*=============================================
  ACTIVAR USUARIOS
  =============================================*/	

  public $activarUsuario;
  public $activarId;

  public function ajaxActivarUsuario(){

  	$respuesta = ModeloUsuarios::mdlActualizarUsuario("usuarios", "verificacion", $this->activarUsuario, "id", $this->activarId);

  	echo $respuesta;

  }


  /*=============================================
  EDITAR ASESOR
  =============================================*/ 

    public $idUsuario;

    public function ajaxEditarAsesor(){

      $item = "id";
      $valor = $this->idUsuario;

      $respuesta = ControladorUsuarios::ctrMostrarUsuariosEdicion($item, $valor);

      echo json_encode($respuesta);

    }

}

/*=============================================
ACTIVAR CATEGORIA
=============================================*/

if(isset($_POST["activarUsuario"])){

	$activarUsuario = new AjaxUsuarios();
	$activarUsuario -> activarUsuario = $_POST["activarUsuario"];
	$activarUsuario -> activarId = $_POST["activarId"];
	$activarUsuario -> ajaxActivarUsuario();

}

/*=============================================
EDITAR ASESOR
=============================================*/
if(isset($_POST["idUsuario"])){

  $editar = new AjaxUsuarios();
  $editar -> idUsuario = $_POST["idUsuario"];
  $editar -> ajaxEditarAsesor();

}