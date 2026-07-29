<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "../controladores/tecnicos.controlador.php";
require_once "../modelos/tecnicos.modelo.php";

header("Content-Type: application/json; charset=utf-8");

if (
  !isset($_SESSION["validarSesionBackend"]) ||
  $_SESSION["validarSesionBackend"] !== "ok" ||
  !isset($_SESSION["perfil"]) ||
  !in_array($_SESSION["perfil"], array("administrador", "Super-Administrador"), true)
) {
  http_response_code(401);
  echo json_encode(array("error" => "Sesión no autorizada"), JSON_UNESCAPED_UNICODE);
  exit;
}

class AjaxTecnicos{
	
  /*=============================================
  EDITAR TECNICO
  =============================================*/ 

  public $idTecnico;

  public function ajaxEditarTecnico(){

    $item = "id";
    $valor = intval($this->idTecnico);

    $respuesta = ControladorTecnicos::ctrMostrarTecnicos($item, $valor);

    if (!$respuesta) {
      http_response_code(404);
      echo json_encode(array("error" => "Técnico no encontrado"), JSON_UNESCAPED_UNICODE);
      return;
    }

    $esSuperAdministrador = $_SESSION["perfil"] === "Super-Administrador";
    $empresaSesion = isset($_SESSION["empresa"]) ? intval($_SESSION["empresa"]) : 0;
    $empresaTecnico = isset($respuesta["id_empresa"]) ? intval($respuesta["id_empresa"]) : 0;

    if (!$esSuperAdministrador && ($empresaSesion <= 0 || $empresaSesion !== $empresaTecnico)) {
      http_response_code(403);
      echo json_encode(array("error" => "No tienes permiso para editar este técnico"), JSON_UNESCAPED_UNICODE);
      return;
    }

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

  }
}
/*=============================================
EDITAR TECNICO
=============================================*/
if(isset($_POST["idTecnico"])){

  $editar = new AjaxTecnicos();
  $editar -> idTecnico = $_POST["idTecnico"];
  $editar -> ajaxEditarTecnico();

}
