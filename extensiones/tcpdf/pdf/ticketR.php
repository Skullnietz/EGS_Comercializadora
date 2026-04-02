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
require_once "../../../config/Database.php";
require_once "../../../modelos/recompensas.modelo.php";
require_once "../../../controladores/recompensas.controlador.php";


/**MANDAR INFORMACION DE LA VENTA**/
class ImprimirTickets{
  
  function TraerImpresionTicket(){
   

      //TRAER LA INFORMACION DE LA VENTA
      
      $item = "id";
      $valor = $_GET["idventa"];

      $ventas = ControladorVentas::ctrMostrarVentasParaTiketimp($item,$valor);

      $id =  $ventas["id"];
      $producto =  $ventas["producto"];
      $productoUno =  $ventas["productoUno"];
      $PrecioUno =  $ventas["precioUno"];
      $CantidadUno =  $ventas["cantidadUno"];
      $productoDos =  $ventas["productoDos"];
      $PrecioDos =  $ventas["precioDos"];
      $CantidadDos =  $ventas["cantidadDos"];
      $productoTres =  $ventas["productoTres"];
      $PrecioTres =  $ventas["precioTres"];
      $CantidadTres =  $ventas["cantidadTres"];
      $productoCuatro =  $ventas["productoCuatro"];
      $PrecioCuatro =  $ventas["precioCuatro"];
      $CantidadCuatro =  $ventas["cantidadCuatro"];
      $productoCinco =  $ventas["productoCinco"];
      $PrecioCinco =  $ventas["precioCinco"];
      $CantidadCinco =  $ventas["cantidadCinco"];
      $productoSeis =  $ventas["productoSeis"];
      $PrecioSeis =  $ventas["precioSeis"];
      $CantidadSeis =  $ventas["cantidadSeis"];
      $productoSiete =  $ventas["productoSiete"];
      $PrecioSiete =  $ventas["precioSiete"];
      $CantidadSiete =  $ventas["cantidadSiete"];
      $productoOcho =  $ventas["productoOcho"];
      $PrecioOcho =  $ventas["precioOcho"];
      $CantidadOcho =  $ventas["cantidadOcho"];
      $productoNueve =  $ventas["productoNueve"];
      $PrecioNueve =  $ventas["precioNueve"];
      $CantidadNueve =  $ventas["cantidadNueve"];
      $productoDiez =  $ventas["productoDiez"];
      $PrecioDiez =  $ventas["precioDiez"];
      $CantidadDiez =  $ventas["cantidadDiez"];
      $descripcion =  $ventas["desccripcion"];
      $nombreDelCliente =  $ventas["nombreCliente"];
      $NumeroDelCleiente =  $ventas["numeroClienteUno"];
      $NumeroDosDelCliente =  $ventas["numeroClienteDos"];
      $correoCliente =  $ventas["correo"];
      $DireccionDelCliente =  $ventas["direccion"];
      $asesor =  $ventas["asesor"];
      $CantidadDeProductos =  $ventas["cantidadProductos"];
      $PagoTotal =  $ventas["pago"];
      $metodoDePago =  $ventas["metodo"];

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
      $Telefono3 = $respuesta["telefonoTres"];
      $Telefono4 = $respuesta["telefonoCuatro"];
      $Facebook = $respuesta["Facebook"];
      $Sitio = $respuesta["Sitio"];

      // ═══════════════════════════════════════
      // RECOMPENSAS - DINERO ELECTRÓNICO
      // ═══════════════════════════════════════
      $idClienteVenta = intval($ventas["id_cliente"] ?? 0);
      $totalVenta = floatval($PagoTotal);
      $saldoElectronico = 0;
      $porcentajeCliente = 1;
      $entregadasCliente = 0;
      $ordenesEnPrograma = 0;
      $tokenMonedero = '';
      $montoGenerado = 0;

      if ($idClienteVenta > 0) {
          try {
              $infoRecompensas = ControladorRecompensas::ctrObtenerInfoRecompensas($idClienteVenta);
              $saldoElectronico = $infoRecompensas["saldo"];
              $porcentajeCliente = $infoRecompensas["porcentaje"];
              $entregadasCliente = $infoRecompensas["entregadas"];
              $ordenesEnPrograma = intval($infoRecompensas["ordenes_en_programa"]);
              $tokenMonedero = $infoRecompensas["token"];
              $montoGenerado = round($totalVenta * ($porcentajeCliente / 100), 2);
          } catch (Exception $e) {
              // defaults already set
          }
      }


      echo '<div class="zona_impresion">
         <br>


              
                

                  <!-- Mostramos los datos de la empresa en el documento HTML -->
                        <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="float: left"></div>
                        <center>
                        <div style="margin-top:20px;font-size: 200%"><strong>'.$NombreEmpresa.'</strong></div>
                        <hr><div style="font-size: 100%"><b>'.$Sitio.'</b></div><br>
                        <div><h3><b>'.$Direccion.' <h3><b> TELEFONOS<br> '.$Telefono.' <br> '.$Telefono3.' <br> '.$Telefono4.'</b></h3></div><div>Sólo Whatsapp (No llamar): '.$Telefono2.'</div>
                
                
<b><hr></b>
<center>
<div>'.$nuevaFecha.'</div>
                        <div><b> Venta No.'.$id.'</b></div>
                        <div> Cliente: '.$nombreDelCliente.'</div>
                        </center>
      
        <!-- Mostramos los detalles de la venta en el documento HTML -->

        <table border="0" align="center" width="10px">
            
            <tr>
                <td>Pzas.</td>
                <td align="center">Productos<td>
                <td align="left">P.U</td>
                <td>TOTAL</td>

            </tr>

          <tr>

            <td colspan="5">==============================================</td>
             
          </tr>';
              
              //$precioTotal = number_format($item["pago"], 2);
              if ($producto != "") {
              
               echo '<tr>
                      '.$producto.'
                    </tr>';
              }

              if ($productoUno != "") {

                  $precioTotal = $PrecioUno * $CantidadUno;

                  echo'<!-- Mostramos los totales de la venta en el documento HTML -->
                    <tr>
                      <td>'.$CantidadUno.'</td>
                      <td style="text-transform:uppercase;">'.$productoUno.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioUno.'</b></td>
                      <center><td align="right"><b>$'.$precioTotal.'</b></td></center>
                    </tr>
                    <tr></tr>
                    <tr>';
              }

              if ($productoDos != "") {

                $precioTotal = $PrecioDos * $CantidadDos;

                  echo'<td>'.$CantidadDos.'</td>
                      <td style="text-transform:uppercase;">'.$productoDos.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioDos.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>
                    <tr>';
              }
              
              if ($productoTres != ""){
                 
                 $precioTotal = $PrecioTres * $CantidadTres;

                 echo'<td>'.$CantidadTres.'</td>
                      <td style="text-transform:uppercase;">'.$productoTres.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioTres.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoCuatro != ""){
                 
                 $precioTotal = $PrecioCuatro * $CantidadCuatro;

                 echo'<td>'.$CantidadCuatro.'</td>
                      <td style="text-transform:uppercase;">'.$productoCuatro.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioCuatro.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoCinco != ""){
                 
                 $precioTotal = $PrecioCinco * $CantidadCinco;

                 echo'<td>'.$CantidadCinco.'</td>
                      <td style="text-transform:uppercase;">'.$productoCinco.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioCinco.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoSeis != ""){
                 
                 $precioTotal = $PrecioSeis * $CantidadSeis;

                 echo'<td>'.$CantidadSeis.'</td>
                      <td style="text-transform:uppercase;">'.$productoSeis.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioSeis.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoSiete != ""){
                 
                 $precioTotal = $PrecioSiete * $CantidadSiete;

                 echo'<td>'.$CantidadSiete.'</td>
                      <td style="text-transform:uppercase;">'.$productoSiete.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioSiete.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoOcho != ""){
                 
                 $precioTotal = $PrecioOcho * $CantidadOcho;

                 echo'<td>'.$CantidadOcho.'</td>
                      <td style="text-transform:uppercase;">'.$productoOcho.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioOcho.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoNueve != ""){
                 
                 $precioTotal = $PrecioNueve * $CantidadNueve;

                 echo'<td>'.$CantidadNueve.'</td>
                      <td style="text-transform:uppercase;">'.$productoNueve.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioNueve.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                if ($productoDiez != ""){
                 
                 $precioTotal = $PrecioDiez * $CantidadDiez;

                 echo'<td>'.$CantidadDiez.'</td>
                      <td style="text-transform:uppercase;">'.$productoDiez.'</br></br>
                      <td></td>
                      <td align="right"><b>$'.$PrecioDiez.'</b></td>
                      <td align="right"><b>$'.$precioTotal.'</b></td>
                    </tr>';
                }
                    

                echo'<tr>                            
                  <td>&nbsp;</td>
                  <td align="right"><b>TOTAL:</b></td>
                  <td align="right"><b>$'.$PagoTotal.'</b></td>
                </tr>

                 <tr>
                  <td colspan="4"> Artículos: '.$CantidadDeProductos.'</td>
                </tr>';

                echo'<tr>
                    <td colspan="4">&nbsp;</td>
                </tr> 
                
                <tr>      
                    <td colspan="4" align="left"><b>Asesor:'.$asesor.'</b></td>
 
                </tr>
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
                      
                    <!-- Mostramos los datos del cliente en el documento HTML -->
                    <br><br>
                    <td colspan="4" align="center">'.$nombreDelCliente.'</td>

                </tr>
                   
              </tr>

             

            </table>
                  

        
              <center>¡Gracias por su compra!<div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/facebook-logo.png" alt="LOGO" width="30px"><b>'.$Facebook.'</b></div></center>';

      // ═══════════════════════════════════════════════════════════════
      // SECCIÓN DE RECOMPENSAS - MONEDERO ELECTRÓNICO EGS (VENTA)
      // ═══════════════════════════════════════════════════════════════
      if ($idClienteVenta > 0) {
      echo '<hr size="5" style="margin: 20px 0 10px;">
            <table border="0" align="center" width="100%">
              <tr>
                <td align="center" colspan="3">
                  <div style="border:3px solid #000;padding:15px;margin:10px 0;text-align:center">';

      echo '    <div style="font-size:18px;font-weight:900;color:#000;margin-bottom:8px">
                      *** MONEDERO EGS ***
                    </div>';

      if ($totalVenta > 0) {
          // Mostrar lo que generó la venta
          if ($ordenesEnPrograma > 0) {
              // Cliente existente: mostrar saldo + generación
              echo '    <div style="border:1px solid #000;padding:12px;margin:6px 0;text-align:center">
                          <div style="font-size:11px;color:#000;font-weight:700;text-transform:uppercase;letter-spacing:1px">Tu saldo disponible</div>
                          <div style="font-size:28px;font-weight:900;color:#000;margin:4px 0">$'.number_format($saldoElectronico, 2).'</div>
                          <div style="font-size:10px;color:#000">Nivel: '.$porcentajeCliente.'% | '.$entregadasCliente.' transacciones</div>
                        </div>';
          }

          echo '    <div style="font-size:14px;color:#000;font-weight:900;margin-top:8px;border:2px solid #000;padding:10px;text-align:center">
                      Esta compra te generó<br>
                      <span style="font-size:22px;display:block;margin:4px 0">$'.number_format($montoGenerado, 2).'</span>
                      <span style="font-size:11px;color:#000">en dinero electrónico ('.$porcentajeCliente.'% de $'.number_format($totalVenta, 2).')</span>
                    </div>
                    <div style="font-size:10px;color:#000;margin-top:6px">Tu dinero electrónico vence cada 6 meses. ¡Úsalo antes!</div>';
      } else {
          // Sin monto: bienvenida al programa
          echo '    <div style="font-size:13px;color:#000;font-weight:700;margin-bottom:6px">
                      ¡Bienvenido al programa de recompensas!
                    </div>
                    <div style="font-size:12px;color:#000;line-height:1.5;margin-bottom:8px">
                      Por cada compra acumulas <b>dinero electrónico</b> que puedes usar como descuento en tu próximo servicio.
                    </div>
                    <div style="border:1px solid #000;padding:8px;margin:6px 0">
                      <div style="font-size:11px;color:#000;font-weight:700">
                        * Clientes nuevos: <b>1%</b> de recompensa<br>
                        * +3 transacciones: <b>2%</b> de recompensa<br>
                        * +5 transacciones: <b>3%</b> de recompensa
                      </div>
                    </div>';
      }

      if (!empty($tokenMonedero)) {
          $urlMonedero = 'https://backend.comercializadoraegs.com/monedero.php?token=' . $tokenMonedero;
          $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($urlMonedero);
          echo '    <div style="margin-top:12px;padding-top:10px;border-top:1px dashed #000">
                      <div style="font-size:11px;color:#000;font-weight:700;margin-bottom:6px">Escanea para ver tu monedero:</div>
                      <img src="'.$qrUrl.'" alt="QR Monedero" style="width:150px;height:150px">
                    </div>';
      }

      echo '      </div>
                </td>
              </tr>
            </table>';
      } // fin if ($idClienteVenta > 0)

      echo '


                
            
        </div>
        
        <p>&nbsp;</p>';

    
  }
} 

//datos de venta
$ticket = new ImprimirTickets();
$ticket -> TraerImpresionTicket();


//$empresas = new ctrMostrarEmpresasParaEditar();
//$empresas ->ControladorEmpresas();


?>

</body>
</html>