<?php

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";
class TablaClientes{

 	/*=============================================
  	MOSTRAR LA TABLA DE clientes
  	=============================================*/ 

	public function mostrarTablaClientes(){	
		
		$empresaDelPerfil = $_GET["empresa"];
		$item = "id_empresa";
 		$valor = $empresaDelPerfil;
 		$clientes = ControladorClientes::ctrMostrarClientesTabla($item, $valor);

 		$datosJson = '{
		 
	 	"data": [ ';

	 	for($i = 0; $i < count($clientes); $i++){
		
		$item1 = "id";
        $valor1 = $clientes[$i]["id_Asesor"];
        $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item1,$valor1);

      	$nombre_asesor = $asesor["nombre"];

	 		
			$acciones = "<div class='form-group text-center'><button class='btn btn-warning btn-sm btnEditarCliente' idCliente='".$clientes[$i]["id"]."' data-toggle='modal' data-target='#btnEditarCliente'><i class='fas fa-user-edit'></i>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Editar<a href='index.php?ruta=Historialdecliente&idCliente=".$clientes[$i]["id"]."&nombreCliente=".$clientes[$i]["nombre"]."'target='_blank'><button class='btn btn-primary btn-sm'><i class='fas fa-clipboard-list'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Historial</button></a></button><a href='https://api.whatsapp.com/send/?phone=521".$clientes[$i]["telefonoDos"]."&text=BIENVENIDO+*".$clientes[$i]["nombre"]."*.%0A%0A+Somos+*COMERCIALIZADORA%20EGS*,+Gracias+por+venir+y+permitirnos+apoyarte+en+tu+proyecto+de+reparación,+recuerda+que+es+importante+seguir+en+comunicación+por+este+medio.+%0A%0A+*HORARIO*:+%0A+*LUNES+A+VIERNES+DE+10:00+AM+A+2:00+PM+Y+DE+4:00+PM+A+6:30+PM*+%0A+*SABADOS+DE+9:00+AM+A+2:00+PM*+%0A%0A*Teléfonos:*+%207222144416%20/%207221671684%20/%207222831159%20+solo+para+dudas+y+aclaraciones+%0A%0A+Mi+nombre+es+*".$nombre_asesor."*+y+estoy+a+tus+ordenes.'target='_blank'><button class='btn btn-success btn-sm'><i class='fab fa-whatsapp'></i> Whatsapp</button></a></div>";

	 		/*=============================================
			DEVOLVER DATOS JSON
			=============================================*/

			$datosJson	 .= '[
				      "'.($i+1).'",
				      "'.$clientes[$i]["nombre"].'",
				      "'.$nombre_asesor.'",
				      "'.$clientes[$i]["correo"].'",
				      "'.$clientes[$i]["telefono"].'",
				      "'.$clientes[$i]["telefonoDos"].'",
				      "'.$acciones.'",
				      "'.$clientes[$i]["fecha"].'"
				      
				    ],';

	 	}

	 	$datosJson = substr($datosJson, 0, -1);

		$datosJson.=  ']
			  
		}'; 

		echo $datosJson;

 	}

}

/*=============================================
ACTIVAR TABLA DE VENTAS
=============================================*/ 
$activar = new TablaClientes();
$activar -> mostrarTablaClientes();