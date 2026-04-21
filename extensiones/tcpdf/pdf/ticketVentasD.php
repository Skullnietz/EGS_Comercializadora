<?php
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link href="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticket.css" rel="stylesheet" type="text/css">
</head>
<body onload="window.print();">

<?php
  

require_once "../../../controladores/ventas.controlador.php";
require_once "../../../modelos/ventas.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";


require_once "../../../controladores/controlador.asesore.php";
require_once "../../../modelos/modelo.asesores.php";


/**MANDAR INFORMACION DE LA VENTA**/
class ImprimirTicketsVentasD{
  
  function TraerImpresionTicketVentasD(){
   

      //TRAER LA INFORMACION DE LA VENTA
      
      $item = "id";
      $valor = $_GET["idventa"];

      $ventas = ControladorVentas::ctrMostrarVentasParaTiketimp($item,$valor);

      $codigo =  $ventas["id"];
      $PagoTotal = $ventas["pago"];

      // ── Canje de dinero electrónico aplicado a esta venta (si existe) ──
      $montoCanjeVenta = 0;
      if (isset($ventas["id"]) && intval($ventas["id"]) > 0) {
          if (!class_exists('ModeloRecompensas')) {
              @require_once __DIR__ . "/../../../modelos/recompensas.modelo.php";
          }
          if (class_exists('ModeloRecompensas') && method_exists('ModeloRecompensas', 'mdlObtenerCanjeVenta')) {
              try {
                  $canjeVenta = ModeloRecompensas::mdlObtenerCanjeVenta(intval($ventas["id"]));
                  if ($canjeVenta && isset($canjeVenta["monto"])) {
                      $montoCanjeVenta = abs(floatval($canjeVenta["monto"]));
                  }
              } catch (Exception $e) {
                  $montoCanjeVenta = 0;
              }
          }
      }
      $subtotalAntesCanje = floatval($PagoTotal) + floatval($montoCanjeVenta);
      
      $fecha = date_create($ventas["fecha"]);
      $nuevaFecha = date_format($fecha, 'd/m/Y H:i:s');

      //TRAER LA INFORMACION DE LA EMPRESA
      
      $item = "id";
      $valor = $_GET["empresa"];

      $respuesta = ControladorVentas::ctrMostrarEmpresasParaTiketimp($item,$valor);

      $NombreEmpresa = $respuesta["empresa"];
      $Direccion = $respuesta["direccion"];
      $Telefono = $respuesta["telefono"];
      $Telefono2 = $respuesta["telefonoDos"];
      $Facebook = $respuesta["Facebook"];
      $Sitio = $respuesta["Sitio"];

      //TRAER ASESOR
            
      $item = "id";
      $valor = $_GET["asesor"];

      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

      $NombreAsesor = $asesor["nombre"];

        /*=============================================
        TARER EL CLIENTE
        =============================================*/

        $item = "id";
        $valor = $_GET["cliente"];

        $respuestaCliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);

        $nombreDelCliente = $respuestaCliente["nombre"];
        


      echo '<div class="zona_impresion">
         <br>



        <table border="0" align="center" width="200px">

           <tr>
              
                <td align="center">

                  <!-- Mostramos los datos de la empresa en el documento HTML -->
                        .:::<strong>"'.$NombreEmpresa.'"</strong>:::.<br>
                              "
                </td>
                

            </tr>
                 <tr>
                <td align="center"><b>Visita nuestro sitio web: '.$Sitio.'</b></td>
            </tr>            
            <tr>
                <td align="center"><b>Facebook: '.$Facebook.'</b></td>
            </tr>
            <tr>
            <td align="center">'.$Direccion.' - Tel:'.$Telefono.'Tel: '.$Telefono2.'</td>
            </tr>
            <tr>
                <td align="center">'.$nuevaFecha.'</td>
            </tr>

            <tr>
                <td align="center"><b> No.'.$codigo.'</b></td>
            </tr>

            <tr>
                <td align="center"></td>
            </tr>

            <!-- Mostramos los datos del cliente en el documento HTML -->
       
            <tr>
              <td> Cliente: '.$nombreDelCliente.'</td>
            </tr>
   
        </table>

        <br>

        <!-- Mostramos los detalles de la venta en el documento HTML -->

        <table border="0" align="center" width="10px">
            
            <tr>
                <td>Pzas.</td>
                <td align="center">Productos<td>
                <!---<td align="left">Precio</td>--->
                <td align="left">TOTAL</td>

            </tr>

          <tr>

            <td colspan="4">==========================================</td>
             
          </tr>';
              

      /*=============================================
      TARER PRODUCTOS
      =============================================*/
      $productos = json_decode($ventas["productos"], true);
                  
        foreach ($productos as $key => $itemDetallesProductos) {

          $itemProducto = "titulo";
          $valorProducto = $itemDetallesProductos["titulo"];


          $precioTotal = number_format($itemDetallesProductos["total"], 2);

            echo'<td>'.$itemDetallesProductos["cantidad"].'</td>
                      <td>'.$itemDetallesProductos["titulo"].'</br></br>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';

        }              
              
                if ($montoCanjeVenta > 0) {
                    echo '<tr>
                      <td>&nbsp;</td>
                      <td align="right">Subtotal:</td>
                      <td align="right">$'.number_format($subtotalAntesCanje, 2).'</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td align="right">Dinero electrónico:</td>
                      <td align="right">-$'.number_format($montoCanjeVenta, 2).'</td>
                    </tr>';
                }

                echo'<tr>
                  <td>&nbsp;</td>
                  <td align="right"><b>TOTAL:</b></td>
                  <td align="right"><b>$'.number_format(floatval($PagoTotal), 2).'</b></td>
                </tr>';
        foreach ($productos as $key => $itemDos) {

                 echo'<tr>
                  <!---<td colspan="4"> Artículos: '.$itemDos["cantidad"].'</td>-->
                </tr>';
        }
                echo'<tr>
                    <td colspan="4">&nbsp;</td>
                </tr> 
                <ul>
                
                </ul>
                <ul>
                <tr>
                    
                    <td colspan="4" align="left"> <li>La garantía del servicio es por 30 días a partir de la fecha de entrega. </li></td>
                </tr>
                <tr>
                  
                    <td colspan="4" align="left"> <li>La empresa no se responsabiliza por accesorios que el cliente reclame y no se encuentren detallados en esta orden. </li></td>
                </tr>
                
                <tr>
                  
                    <td colspan="4" align="left"> <li>El equipo será entregado solamente al portador de esta orden, en su defecto debera retirar el propietario que en esta orden funge como CLIENTE exhibiendo copia de documento de identidad INE. </li></td>
                </tr>

                <tr>
                    <td colspan="4" align="left"> <li>Toda reparación debe ser retirada dentro de los 30 días de comunicado su arreglo, caso contrario se perderá el reclamo. </li></td>
                </tr>
                
                <tr>
                    
                    <td colspan="4" align="left"> <li>En consumibles originales y compatibles no hay garantía. </li></td>
                </tr>
                <tr>
                    <td colspan="4" align="left"> <li>No nos responsabilizamos de la información contenida en su equipo. </li></td>
                </tr>
                 <tr>
                    <td colspan="4" align="left"><li>Al ingresar su orden acepta que esta sera publica en nuestro stio web www.comercializadoraegs.com sin mostrar datos del cliente solamente datos tecnicos y fotos del equipo recibido</li></td>
                </tr>
                </ul>
                <tr>
                  <td colspan="4" align="left"><b><center><h4>Recuerda que para facturación debes solicitar tu factura en: https://comercializadoraegs.com/facturacion</h4></center></b></td>
                </tr>
                <tr>
                <td colspan="4" align="left"><b><center><h4>Contestando nuestra encuenta, obtén un cupón de regalo en: https://comercializadoraegs.com/encuesta/</h4></center></b></td>
                </tr>
                <tr>  
                      
                    <!-- Mostramos los datos del cliente en el documento HTML -->
                    <br><br>
                    <td colspan="4" align="center">'.$nombreDelCliente.'</td>

                </tr>
                   
              </tr>

            </table>
                  
                  <hr size="5" /style="position: line; margin: 20px;">
            <table>
        
              <td colspan="4" align="center">¡Gracias por su compra!</td>
                
                <tr>
                  <td colspan="4" align="center">TODA REVISIÓN GENERA UN CARGO DE 150.°MXN</td>
              </tr>

              <tr>
            
          </table><br>


                <tr>
                  <center><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/visitaegs.jpeg" width="75" height="75"></center>
                  <center><h2>Escanea el siguiente código QR para recibir grandes promociones y descuentos</h2><center>
              </tr>
             
        </div>
        
        <p>&nbsp;</p>';

    
  }
} 

//datos de venta
$ticket = new ImprimirTicketsVentasD();
$ticket -> TraerImpresionTicketVentasD();


//$empresas = new ctrMostrarEmpresasParaEditar();
//$empresas ->ControladorEmpresas();


?>

</body>
</html>