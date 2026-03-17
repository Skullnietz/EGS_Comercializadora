<?php
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

class tablaStockApuntoDeAgotarse{
	
	public function tablaMostrarTablaStcokApuntoDeAgotarse()
	{
		
	  	//$item = null;
	  	//$valor = null;

	  	//$productos = ControladorProductos::ctrMostrarProductos($item, $valor);

		$productos = ControladorProductos::ctrMostrarProductosApuntoDeAgotarse();

	  	$datosJson = '{

		 

	 		"data": [ ';

	 			for ($i=0; $i < count($productos); $i++) { 

	 				$disponibles = "<button class='btn btn-danger'>".$productos[$i]["disponibilidad"]."</button>";

	 				$acciones = "<button class='btn btn-warning btnEditarStockApuntoDeAgotarse' data-toggle='modal' data-target='#ModalAgregarStockAgotandose' idProducto=".$productos[$i]["id"]."><i class='fa fa-pencil'><i></button>";
	 			
					$datosJson	 .= '[

			      		"'.($i+1).'",

			      		"'.$productos[$i]["titulo"].'",

			      		"'.$productos[$i]["descripcion"].'",	

			      		"'.$disponibles.'",

			      		"'.$acciones.'"
			      		
			      	],';



	 			}

				$datosJson = substr($datosJson, 0, -1);



			$datosJson.=  ']

		  

		}'; 
	//mostrar mensaje o contenido guardado 
  	echo $datosJson;
	}
}

$objeto = new tablaStockApuntoDeAgotarse();
$objeto -> tablaMostrarTablaStcokApuntoDeAgotarse();