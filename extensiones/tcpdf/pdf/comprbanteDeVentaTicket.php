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


			/*=============================================
			DATOS DE LA VENTA
			=============================================*/

			$idDeVenta = $ventas["id"];

			$fecha = substr($ventas["fecha"],0,-8);

			$detalles = $ventas["detalles"];

			$cantidad = $ventas["cantidad"];

			$pago = number_format($ventas[$i]["pago"],2);

			/*=============================================
			TRAER PRODUCTO
			=============================================*/

			$item = "id";
			$valor = $ventas["id_producto"];

			$traerProducto = ControladorProductos::ctrMostrarProductos($item, $valor);

			$producto = $traerProducto["titulo"];

			$imgProducto = "<img class='img-thumbnail' src='".$traerProducto["portada"]."' width='100px'>";

			$tipo = $traerProducto["tipo"];


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

// Quitar lineas orizontales de cabecera y de foooter
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Creamos las paginas necesarias
$pdf->AddPage('p', 'A8');

//agregamos el texto del primer bloque
$PriemrBloque = <<<EOF
	<h5 style="color:black; font-size:2px;"><img src="images/logoEGS.png" width="15" height="10">&nbsp;<span style="color:black; font-size:2px;">COMERCIALIZADORA EGS</span></h5>

	<table style="font-size:9px; text-align:center">
		
		<tr>

			<td></td>

			<td style="width:160px;">

				<font size="7">

					<div>
						
						<br><br>
						Dirección: Pino Suàrez 308 Colonia Santa Clara 50090

						
						<br>
						TEL: (722)167 16 84

						<br>
						egs.compras@comercializadoraegs.com

						<br>
						ORDEN : $idDeVenta 

					</div>

				</font>

			</td>

		</tr>

	</table>
EOF;

//Ejecutamos el metodo para escribir html
$pdf->writeHTML($PriemrBloque, false, false, false, false, '');

// ---------------------------------------------------------
//agregamos el texto del segundo bloque
$SegundoBloque = <<<EOF
	<font size="8">

		<table>

			<tr>
			
				<td style="width:160px; text-align:left">
				$detalles
				</td>

			</tr>

			<tr>
			
				<td style="width:160px; text-align:right">
				$cantidad
				<br>
				</td>

			</tr>

		</table>

	</font>

EOF;


//Ejecutamos el metodo para escribir html
$pdf->writeHTML($SegundoBloque, false, false, false, false, '');

// ---------------------------------------------------------

$TercerBloque = <<<EOF
<font size="8">
	<table style="text-align:right">



		<tr>
		
			<td style="width:160px;">
				 --------------------------
			</td>

		</tr>

		<tr>
		
			<td style="width:80px;">
				 TOTAL: 
			</td>

			<td style="width:80px;">
				$cantidad
			</td>

		</tr>

		<tr>
		
			<td style="width:160px;">
				<br>
				<br>
				Muchas gracias por su compra
			</td>

		</tr>

	</table>

</table>
EOF;

$pdf->writeHTML($TercerBloque, false, false, false, false, '');


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('comprobanteDeVenta.php');
	}
		
}

$ticket = new ImprimirTicket();

$ticket -> TraerImpresionTicket();

?>