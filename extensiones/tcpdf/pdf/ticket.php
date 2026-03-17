<?php
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link href="ticket.css" rel="stylesheet" type="text/css">
</head>
<body onload="window.print();">

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

		 //foreach ($ventas as $key => $value) {
        for($i = 0; $i < count($ventas); $i++){

      /*=============================================
      DATOS DE LA VENTA
      =============================================*/

      $idDeVenta = $ventas[$i]["id"];

      $fecha = substr($ventas[$i]["fecha"],0,-8);

      $detalles = $ventas[$i]["detalles"];

      $cantidad = $ventas[$i]["cantidad"];
      $titulo = $ventas[$i]["titulo"];

      $pago = number_format($ventas[$i]["pago"],2);
			/*=============================================
			TRAER PRODUCTO
			=============================================*/

			$item = "id";
			$valor = $ventas[$i]["id_producto"];

			$traerProducto = ControladorProductos::ctrMostrarProductos($item, $valor);

			$producto = $traerProducto[0]["titulo"];

			$imgProducto = "<img class='img-thumbnail' src='".$traerProducto["portada"]."' width='100px'>";

			$tipo = $traerProducto["tipo"];


			/*=============================================
			TRAER CLIENTE
			=============================================*/

			$item2 = "id";
			$valor2 = $ventas[$i]["id_usuario"];

			$traerCliente = ControladorUsuarios::ctrMostrarUsuarios($item2, $valor2);

			$cliente = $traerCliente["nombre"];

     

    }
			echo '<div class="zona_impresion">
				 <br>



				<table border="0" align="center" width="200px">

				   <tr>
        			
	        			<td align="center">

	        			  <!-- Mostramos los datos de la empresa en el documento HTML -->
	                      .:::<strong>"EGS Equipo de Computo y Software"</strong>:::.<br>
	                            "<br>
	      					    Pino Suarez Nte 308, Col. Santa Clara Toluca Mexico,
                               50090 Toluca de Lerdo, Méx - 7222831159<br>


                               <input type="text" class="form-control input-lg" name="cantidadPagada" id="cantidadPagada" placeholder="Ingresar nombre del Asesor" required>
	        			</td>

    				</tr>
    				
    				<tr>
        				<td align="center">'.$fecha.'</td>
    				</tr>

    				<tr>
      					<td align="center"></td>
    				</tr>

    				<!-- Mostramos los datos del cliente en el documento HTML -->
       
    				<tr>
    					<td>cliente: '.$cliente.'</td>
    				</tr>
   
				</table>

				<br>

				<!-- Mostramos los detalles de la venta en el documento HTML -->

				<table border="0" align="center" width="100px">
	    			
	    			<tr>

		        		<td>CANT.</td>

		        		<td>DESCRIPCIÓN</td>

		       			<td align="right">IMPORTE</td>

	    			</tr>
    			
    			<tr>

    			 	<td colspan="3">==========================================</td>
   					 
   				</tr>';
         			
              //$precioTotal = number_format($item["pago"], 2);

               echo '<tr>
                      <td>'.$cantidad.'</td>
                      <td>'.$producto.'
                      </tr>
                      <!-- Mostramos los totales de la venta en el documento HTML -->
          			<tr>
      	    			<td>&nbsp;</td>
      	    			<td align="right"><b>TOTAL:</b></td>
      	    			<td align="right"><b>$ '.$pago.'</b></td>
      	    		</tr>

      	    		 <tr>
           				<td colspan="4"> artículos: '.$cantidad.'</td>
          			</tr>';

          			echo'<tr>
            				<td colspan="4">&nbsp;</td>
         				</tr> 

         				<tr>
            				
            				<td colspan="4" align="left">. La garantia del servicio es por 30 dias a partir de la fechade entrega.</td>
          			</tr>
          			
          			<tr>
            			
            				<td colspan="4" align="left"> .La empresa no se responsabiliza por accesorios que el cliente reclame y no se encuentren detallados en esta orden.</td>
          			</tr>
           			
           			<tr>
            			
            				<td colspan="4" align="left"> .El equipo sera entregado solamente al portdaor de esta orden,en su defecto debera retirar el propietario que en esta orden funge como CLIENTE exhibiendo copia de documento de identidad IFE.</td>
           			</tr>

           			<tr>
            				<td colspan="4" align="left"> .Toda reparacion debe ser retirada dentro de los 30 dias de comunicado su arreglo,caso contrario se perdera el reclamo.</td>
          			</tr>
          			
          			<tr>
            				
            				<td colspan="4" align="left"> .En consumibles originales y compatibles no hay garantia.</td>
          			</tr>
            			<tr>
            				<td colspan="4" align="left"> .No nos responsabilizamos de la informacion contenida en su equipo.</td>
          			</tr>

          			<tr>  
              				
              			<!-- Mostramos los datos del cliente en el documento HTML -->
             				 
             				<td colspan="4" align="center"> '.$ventas["cliente"].'</td>

          			</tr>
          			   
          		</tr>

      			</table>
            			
            			<hr size="5" /style="position: line; margin: 20px;">
      			<table>
        
        			<td colspan="4" align="center">¡Gracias por su compra!</td>
            		
            		<tr>
            			<td colspan="4" align="center">TODA REVISION GENERA UN CARGO DE 150.°MXN</td>
          		</tr>

          		<tr>
      			
      		</table><br>


                <tr>
                  <center><img src="images/visitaegs.jpeg" width="75" height="75"></center>
                  <center><h2>Escanea el siguiente código QR para recibir grandes promociones y descuentos</h2><center>
              </tr>
      	</div>
      	
      	<p>&nbsp;</p>';

		
	}
}	


$ticket = new ImprimirTicket();

$ticket -> TraerImpresionTicket();
?>

</body>
</html>