<?php
 
require_once "../../../controladores/ventas.controlador.php";
require_once "../../../modelos/ventas.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

/**MANDAR INFORMACION DE LA VENTA**/
class ImprimirTicket{
	
	function TraerImpresionTicket(){
		
		//TRAER LA INFORMACION DE LA VENTA

		$ventas = ControladorVentas::ctrMostrarVentas();

			for($i = 0; $i < count($ventas); $i++){

			/*=============================================
			DATOS DE LA VENTA
			=============================================*/

			$idDeVenta = $ventas[$i]["id"];

			$fecha = substr($ventas[$i]["fecha"],0,-8);

			$detalles = $ventas[$i]["detalles"];

			$cantidad = $ventas[$i]["cantidad"];

			$pago = number_format($ventas[$i]["pago"],2);

			/*=============================================
			TRAER PRODUCTO
			=============================================*/

			$item = "id";
			$valor = $ventas[$i]["id_producto"];

			$traerProducto = ControladorProductos::ctrMostrarProductos($item, $valor);

			$producto = $traerProducto[0]["titulo"];

			$imgProducto = "<img class='img-thumbnail' src='".$traerProducto[0]["portada"]."' width='100px'>";

			$tipo = $traerProducto[0]["tipo"];


			/*=============================================
			TRAER CLIENTE
			=============================================*/

			$item2 = "id";
			$valor2 = $ventas[$i]["id_usuario"];

			$traerCliente = ControladorUsuarios::ctrMostrarUsuarios($item2, $valor2);

			$cliente = $traerCliente["nombre"];


 // Incluimos la libreria
require_once('tcpdf_include.php');

// Instanciamos la clase para crear documentos pdf
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->startPageGroup();

// Creamos las paginas necesarias
$pdf->AddPage();

//agregamos el texto del primer bloque
$PriemrBloque = <<<EOF
	
	<table>
		
		<tr>

			<td  border="0" cellspacing="0" cellpadding="4" style="width:150px"><img src="images/logoEGS.png"></td>

			<td style="background-color:white; width:140px">
				
				<div style="font-size:8.5px; text-align:right; line-height:15px;">
					
					<br>
					Dirección: Pino Suàrez 308 Colonia Santa Clara 50090

				</div>

			</td>

				<td style="background-color:white; width:140px">

				<div style="font-size:8.5px; text-align:right; line-height:15px;">
					
					<br>
					TEL: (722)167 16 84

					<br>
					egs.compras@comercializadoraegs.com

					<br>
					ORDEN : $idDeVenta 
				</div>
				
			</td>

		</tr>

	</table>
EOF;

//Ejecutamos el metodo para escribir html
$pdf->writeHTML($PriemrBloque, false, false, false, false, '');

// ---------------------------------------------------------
//agregamos el texto del segundo bloque
$SegundoBloque = <<<EOF
	
	
	<table>
		
		<tr>
			
			<td style="width:540px"><img src="images/back.jpg"></td>
		
		</tr>

	</table>

	<table  border="1" cellspacing="1" cellpadding="4" style="font-size:10px; padding:5px 10px;">
	
		<tr>
		
			<td style="border: 1px solid #666; background-color:white; width:390px">

				Cliente: $cliente

			</td>

			<td style="border: 1px solid #666; background-color:white; width:150px; text-align:right">
			
				Fecha: $fecha

			</td>

		</tr>

		<tr>
		
			<td style="border: 1px solid #666; background-color:white; width:540px">Vendedor: TRARER DATO</td>

		</tr>

	</table>
<br><br>
EOF;


//Ejecutamos el metodo para escribir html
$pdf->writeHTML($SegundoBloque, false, false, false, false, '');

// ---------------------------------------------------------

$TercerBloque = <<<EOF

	<table  border="1" cellspacing="1" cellpadding="4" style="font-size:10px; padding:5px 10px;">

		<tr>
		
		<td style="border: 1px solid #666; background-color:white; width:260px; text-align:center">Producto</td>
		<td style="border: 1px solid #666; background-color:white; width:80px; text-align:center">Cantidad</td>
		<td style="border: 1px solid #666; background-color:white; width:100px; text-align:center">Valor Total</td>

		</tr>

	</table>

EOF;

$pdf->writeHTML($TercerBloque, false, false, false, false, '');

// ---------------------------------------------------------

$CuartoBloque = <<<EOF

	<table  border="1" cellspacing="1" cellpadding="4" style="font-size:10px; padding:5px 10px;">

		<tr>
			
			<td style="border: 1px solid #666; color:#333; background-color:white; width:260px; text-align:center">
				$detalles
			</td>

			<td style="border: 1px solid #666; color:#333; background-color:white; width:80px; text-align:center">
				$cantidad
			</td>

			<td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">$ 
				$pago
			</td>


		</tr>

	</table>


EOF;

$pdf->writeHTML($CuartoBloque, false, false, false, false, '');
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('comprobanteDeVenta.php');
		}
	}	
}

$ticket = new ImprimirTicket();

$ticket -> TraerImpresionTicket();

?>