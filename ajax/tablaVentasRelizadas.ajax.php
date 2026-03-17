<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

require_once "../controladores/controlador.asesore.php";
require_once "../modelos/modelo.asesores.php";

class TablaVentasDinamicasRealizadas{

  /*=============================================
  MOSTRAR LA TABLA DE PRODUCTOS
  =============================================*/ 

  public function mostrarTablaVentasDinamicasRelizadas(){	

  	$ventas = ControladorVentas::ctrMostrarVentas();

  	$datosJson = '

  		{	
  			"data":[';

	 	for($i = 0; $i < count($ventas); $i++){


        /*=============================================
        TARER EL CLIENTE
        =============================================*/

          $itemCliente = "id";
          $valorCliente = $ventas[$i]["id_usuario"];

          $respuestaCliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);

          $cliente = $respuestaCliente["nombre"];
        

        /*=============================================
        TARER EL ASESOR DE VENTA
        =============================================*/
        $itemUsuario = "id";
        $valorUsuario = $ventas[$i]["id_Asesor"];

        $respuestaAsesor = Controladorasesores::ctrMostrarAsesoresEleg($itemUsuario, $valorUsuario);
        $asesor = $respuestaAsesor["nombre"];

        /*=============================================
        TARER PRODUCTOS
        =============================================*/
        $productos = json_decode($ventas[$i]["productos"], true);

        if (is_array($productos) || is_object($productos)) {

         foreach ($productos as $key => $item) {

           $productosComprados = $item["titulo"];

          }
          
        }
                  
          

        /*=============================================
        ACCIONES (BOTONES)
        =============================================*/
        $acciones = "<div class='btn-group'><button class='btn btn-info imprimirTicketVentaDinamica'  idventa='".$ventas[$i]["id"]."' cliente='".$ventas[$i]["id_usuario"]."'  tecnico='".$ventas[$i]["id_tecnico"]."' asesor='".$ventas[$i]["id_Asesor"]."' empresa='".$ventas[$i]["id_empresa"]."' data-toggle='modal'><i class='fa fa-print'></i></button><button class='btn btn-warning btnEditarVenta' idVenta='".$ventas[$i]["id"]."'><i class='fa fa-pencil'></i></button><button class='btn btn-danger btnEliminarVenta' idVenta='".$ventas[$i]["id"]."'><i class='fa fa-times'></i></button></div>";
        /*=============================================
  			CONSTRUIR LOS DATOS JSON
  			=============================================*/


			$datosJson .='[
					
		        "'.($i+1).'",
            "'.$ventas[$i]["codigo"].'",
            "'.$cliente.'",
            "'.$respuestaAsesor["nombre"].'",
            "'.$ventas[$i]["metodo"].'",
            "'.number_format($ventas[$i]["neto"],2).'",
            "'.number_format($ventas[$i]["total"],2).'",
            "'.$ventas[$i]["fecha"].'",
            "'.$acciones.'"

			],';

		}

		$datosJson = substr($datosJson, 0, -1);

		$datosJson .= ']

		}';

		echo $datosJson;

  }


}

/*=============================================
ACTIVAR TABLA DE PRODUCTOS
=============================================*/ 
$activarProductos = new TablaVentasDinamicasRealizadas();
$activarProductos -> mostrarTablaVentasDinamicasRelizadas();