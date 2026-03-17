<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

require_once "../controladores/subcategorias.controlador.php";
require_once "../modelos/subcategorias.modelo.php";

require_once "../controladores/cabeceras.controlador.php";
require_once "../modelos/cabeceras.modelo.php";

class TablaProductosDinamicos{

  /*=============================================
  MOSTRAR LA TABLA DE PRODUCTOS
  =============================================*/ 

  public function mostrarTablaProductosDinamicos(){	

  	$empresa = $_GET["empresa"];
    $item = "id_empresa";
    $valor = $empresa;

  	$productos = ControladorProductos::ctrMostrarProductos($item, $valor);

  	$datosJson = '

  		{	
  			"data":[';

	 	for($i = 0; $i < count($productos); $i++){

            /*=============================================
            TRAEMOS LA IMAGEN
            =============================================*/ 

            $imagen = "<img loading='lazy' src='".$productos[$i]["portada"]."' width='40px'>";

            /*=============================================
            STOCK
            =============================================*/ 

            if($productos[$i]["disponibilidad"] <= 10){

              $stock = "<button class='btn btn-danger'>".$productos[$i]["disponibilidad"]."</button>";

            }else if($productos[$i]["disponibilidad"] > 11 && $productos[$i]["disponibilidad"] <= 15){

              $stock = "<button class='btn btn-warning'>".$productos[$i]["disponibilidad"]."</button>";

            }else{

              $stock = "<button class='btn btn-success'>".$productos[$i]["disponibilidad"]."</button>";

            }


        /*=============================================
        TRAEMOS LAS ACCIONES
        =============================================*/ 

        $botones =  "<div class='btn-group'><button class='btn btn-primary agregarProducto recuperarBoton' idProducto='".$productos[$i]["id"]."'>Agregar</button></div>"; 



  			/*=============================================
  			CONSTRUIR LOS DATOS JSON
  			=============================================*/


			$datosJson .='[
					
		        "'.($i+1).'",
            "'.$imagen.'",
            "'.$productos[$i]["codigo"].'",
            "'.$productos[$i]["titulo"].'",
            "'.$stock.'",
            "'.$botones.'"

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
$activarProductos = new TablaProductosDinamicos();
$activarProductos -> mostrarTablaProductosDinamicos();