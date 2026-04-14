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
  

require_once "../../../controladores/pedidos.controlador.php";
require_once "../../../modelos/pedidos.modelo.php";
require_once "../../../controladores/ventas.controlador.php";
require_once "../../../modelos/ventas.modelo.php";
require_once "../../../controladores/controlador.asesore.php";
require_once "../../../modelos/modelo.asesores.php";
require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";
require_once "../../../controladores/tecnicos.controlador.php";
require_once "../../../modelos/tecnicos.modelo.php";
require_once "../../../config/Database.php";
require_once "../../../modelos/recompensas.modelo.php";
require_once "../../../controladores/recompensas.controlador.php";
/**MANDAR INFORMACION DE LA VENTA**/
class ImprimirTicketsPedido{
  
  function TraerImpresionTicketPedido(){
   
   //TRAER LA INFORMACION DE LA VENTA
      
      $item = "id";
      $valor = $_GET["idPedido"];

      $pedidos = ControladorPedidos::ctrMostrarorpedidosParaValidar($item,$valor);
      foreach ($pedidos as $key => $value) {
        # code...
      
    


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

      //TRAER CLIENTE (USUARIO)
      $item = "id";
      $valor = $_GET["cliente"];

      $usuario = ControladorClientes::ctrMostrarClientes($item,$valor);

      $NombreUsuario = $usuario["nombre"];

      // ═══════════════════════════════════════
      // RECOMPENSAS - DINERO ELECTRÓNICO
      // ═══════════════════════════════════════
      $idClientePedido = intval($_GET["cliente"]);
      $totalPedido = floatval($value["total"]);

      try {
          $infoRecompensas = ControladorRecompensas::ctrObtenerInfoRecompensas($idClientePedido);
          $saldoElectronico = $infoRecompensas["saldo"];
          $porcentajeCliente = $infoRecompensas["porcentaje"];
          $entregadasCliente = $infoRecompensas["entregadas"];
          $ordenesEnPrograma = intval($infoRecompensas["ordenes_en_programa"]);
          $tokenMonedero = $infoRecompensas["token"];
          $montoGenerado = round($totalPedido * ($porcentajeCliente / 100), 2);
      } catch (Exception $e) {
          $saldoElectronico = 0;
          $porcentajeCliente = 1;
          $entregadasCliente = 0;
          $ordenesEnPrograma = 0;
          $tokenMonedero = '';
          $montoGenerado = 0;
      }

       echo '<div class="zona_impresion">
         <br>

<!-- Mostramos los datos de la empresa en el documento HTML -->
                        <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="float: left"></div>
                        <center>
                        <div style="margin-top:20px;font-size: 200%"><strong>'.$NombreEmpresa.'</strong></div>
                        <hr><div style="font-size: 100%"><b>'.$Sitio.'</b></div><br>
                        <div><h3><b>'.$Direccion.' <h3><b> TELEFONOS<br> '.$Telefono.' <br> '.$Telefono3.' <br> '.$Telefono4.'</b></h3></div><div>Sólo Whatsapp (No llamar): '.$Telefono2.'</div>
                        <div><b><h4>Conoce el estado de tu orden en: comercializadoraegs.com/ordenes</h4> </b></div></center>
                
                
<b><hr></b>

        <table border="0" align="center" width="200px">';
            
            
            $fecha = date_create($vvalue["fechaDePedido"]);
            $nuevaFecha = date_format($fecha, 'd/m/Y H:i:s');

            echo'<tr>
                <td align="center">'.$nuevaFecha.'</td>
            </tr>

            <tr>
                <td align="center"><b>Pedido No.'.$value["id"].'</b></td>
            </tr>

            <tr>
                <td align="center"><b>'.$value["estado"].'</b></td>
            </tr>

            <!-- Mostramos los datos del cliente en el documento HTML -->
       
            <tr>
              <td align="center">Cliente: '.$NombreUsuario.'</td>
            </tr>
   
        </table>

        <br>

        <!-- Mostramos los detalles de la venta en el documento HTML -->

        <table border="0" align="center" width="10px">
            
            <tr>
                <td>Pzas.</td>
                <td>Productos<td>

                <!---<td align="left">Precio</td>--->
                <td align="left">TOTAL</td>

            </tr>

          <tr>

            <td colspan="4">==========================================</td>
             
          </tr>';
              
              //$precioTotal = number_format($item["pago"], 2);
              if ($value["productoUno"] != undefined and $value["productoUno"] != "") {
              
               echo '<tr>
                      
                    </tr>';
          

                  echo'<!-- Mostramos los totales de la venta en el documento HTML -->
                    <tr>
                      <td>'.$value["cantidaProductoUno"].'</td>
                      <td>'.$value["productoUno"].'</br></br>
                      <td>&nbsp;</td>                      
                      <center><td align="right"><b>$'.$value["totalPedidoUno"].'</b></td></center>
                    </tr>
                    <tr></tr>
                    <tr>';
                
                }

              if ($value["ProductoDos"] != undefined and $value["ProductoDos"] != "") {
                  echo'<!-- producto dos -->
                    <td>'.$value["cantidadProductoDos"].'</td>
                      <td>'.$value["ProductoDos"].'</br></br>
                      <td>&nbsp;</td>                     
                      <td align="right"><b>$'.$value["totalPedidoDos"].'</b></td>
                    </tr>
                    <tr>';
              }
              
              if ($value["ProductoTres"] != undefined and $value["ProductoTres"] != ""){
                 echo'<!-- producto tres-->
                    <td>'.$value["cantidadProductoTres"].'</td>
                      <td>'.$value["ProductoTres"].'</br></br>
                      <td>&nbsp;</td>                    
                      <td align="right"><b>$'.$value["totalPedidoTres"].'</b></td>
                    </tr>';
                }  
                
                if ($value["ProductoCuatro"] != undefined and $value["ProductoCuatro"] != ""){
                 echo'<!-- producto cuatro -->
                    <td>'.$value["cantidadProductoCuatro"].'</td>
                      <td>'.$value["ProductoCuatro"].'</br></br>
                      <td>&nbsp;</td>                
                      <td align="right"><b>$'.$value["totalPedidoCuatro"].'</b></td>
                    </tr>';
                }  

                if ($value["ProductoCinco"] != undefined and $value["ProductoCinco"] != ""){
                 echo'<!-- Producto Cinco -->
                    <td>'.$value["cantidadProductoCinco"].'</td>
                      <td>'.$value["ProductoCinco"].'</br></br>
                      <td>&nbsp;</td>
                      <td align="right"><b>$'.$value["totalPedidoCinco"].'</b></td>
                    </tr>';
                }  



                $productos = json_decode($value["productos"], true); 

                foreach ($productos as $key => $valueProductos) {
                        

                          echo'<!-- Producto Cinco -->
                          <td>'.$valueProductos["cantidad"].'</td>
                            <td style="text-transform:uppercase;">'.$valueProductos["Descripcion"].'</br></br>
                            <td>&nbsp;</td>
                            <td align="right"><b>$'.$valueProductos["precio"].'</b></td>
                          </tr>';   
                      }

                echo'<tr>                            
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td align="right"><b>TOTAL: $'.$value["total"].'</b></td>
                </tr>
            <tr>
              <td colspan="4">==========================================</td>
            </tr>';

                // ═══════════════════════════════════════
                // RESUMEN DE PAGOS Y ADEUDO
                // ═══════════════════════════════════════
                $totalAbonado = 0;

                echo '<tr>
                  <td colspan="4" style="padding-top:8px;">
                    <div style="border:2px solid #000;padding:10px;margin:4px 0;">
                      <div style="font-size:14px;font-weight:900;text-align:center;margin-bottom:8px;text-decoration:underline;">RESUMEN DE PAGOS</div>';

                // Pago inicial
                if (!empty($value["pagoPedido"]) && floatval($value["pagoPedido"]) > 0) {
                    $montoPago = floatval($value["pagoPedido"]);
                    $totalAbonado += $montoPago;
                    echo '<div style="display:flex;justify-content:space-between;padding:3px 0;font-size:12px;">
                            <span>Pago inicial</span>
                            <span><b>$'.number_format($montoPago, 2).'</b></span>
                          </div>';
                }

                // Abonos registrados en JSON
                $pagos = json_decode($value["pagos"], true);
                if (is_array($pagos)) {
                    $numAbono = 1;
                    foreach ($pagos as $valuePagos) {
                        $montoAbono = floatval($valuePagos["pago"]);
                        if ($montoAbono > 0) {
                            $totalAbonado += $montoAbono;
                            $fechaAbono = !empty($valuePagos["fecha"]) ? $valuePagos["fecha"] : "";
                            $labelFecha = $fechaAbono ? " (".$fechaAbono.")" : "";
                            echo '<div style="display:flex;justify-content:space-between;padding:3px 0;font-size:12px;">
                                    <span>Abono '.$numAbono.$labelFecha.'</span>
                                    <span><b>$'.number_format($montoAbono, 2).'</b></span>
                                  </div>';
                            $numAbono++;
                        }
                    }
                }

                // Abonos directos (abonoUno, etc.)
                $camposAbono = array(
                    array("monto" => "abonoUno", "fecha" => "fechaAbonoUno"),
                    array("monto" => "abonoDos", "fecha" => "fechaAbonoDos"),
                    array("monto" => "abonoTres", "fecha" => "fechaAbonoTres"),
                    array("monto" => "abonoCuatro", "fecha" => "fechaAbonoCuatro"),
                    array("monto" => "abonoCinco", "fecha" => "fechaAbonoCinco"),
                );
                $numAbonoDir = 1;
                foreach ($camposAbono as $campo) {
                    if (!empty($value[$campo["monto"]]) && floatval($value[$campo["monto"]]) > 0) {
                        $montoAb = floatval($value[$campo["monto"]]);
                        $totalAbonado += $montoAb;
                        $fechaAb = !empty($value[$campo["fecha"]]) ? $value[$campo["fecha"]] : "";
                        $labelFechaAb = $fechaAb ? " (".$fechaAb.")" : "";
                        echo '<div style="display:flex;justify-content:space-between;padding:3px 0;font-size:12px;">
                                <span>Abono'.$labelFechaAb.'</span>
                                <span><b>$'.number_format($montoAb, 2).'</b></span>
                              </div>';
                        $numAbonoDir++;
                    }
                }

                // Línea separadora
                echo '  <div style="border-top:1px dashed #000;margin:6px 0;"></div>';

                // Total abonado
                echo '  <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;font-weight:900;">
                          <span>TOTAL ABONADO</span>
                          <span>$'.number_format($totalAbonado, 2).'</span>
                        </div>';

                // Adeudo
                $adeudo = floatval($value["adeudo"]);
                $colorAdeudo = ($adeudo > 0) ? "#d00" : "#060";
                $textoAdeudo = ($adeudo > 0) ? "ADEUDO PENDIENTE" : "PAGADO";
                echo '  <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:15px;font-weight:900;color:'.$colorAdeudo.';">
                          <span>'.$textoAdeudo.'</span>
                          <span>$'.number_format($adeudo, 2).'</span>
                        </div>';

                echo '  </div>
                  </td>
                </tr>';

                echo'<tr>
                    <td colspan="4">&nbsp;</td>
                </tr> 

                <ul>
                    <td colspan="4" align="left">

                      <li>Dentro de las primeras 24 a 48 horas puedes cancelar tu pedido</li>

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

                      <!--<li>Puedes transferir el monto total de tu pedido al numero de cuenta </li>-->

                    </td>

                </ul>

                <tr>  
                <tr>
                  <td colspan="4" align="left"><b><center><h4>Recuerda que para facturación debes solicitar tu factura en: https://comercializadoraegs.com/facturacion</h4></center></b></td>
                </tr>
                <tr>
                <td colspan="4" align="left"><b><center><h4>Contestando nuestra encuenta, obtén un cupón de regalo en: https://comercializadoraegs.com/encuesta/</h4></center></b></td>
                </tr>
                    <!-- Mostramos los datos del cliente en el documento HTML -->
                    <br><br>
                    <td colspan="4" align="center">'.$nombreDelCliente.'</td>

                </tr>
                   
              </tr>

            </table>
                  
                  <hr size="5" /style="position: line; margin: 20px;">
            <table>
        
              <center>¡Gracias por su compra!</center>

                
            <tr>
            
          </table>';

      // ═══════════════════════════════════════════════════════════════
      // SECCIÓN DE RECOMPENSAS - MONEDERO ELECTRÓNICO EGS (PEDIDO)
      // ═══════════════════════════════════════════════════════════════
      $estadoPedido = $value["estado"];
      $mostrarRecompensasPedido = (stripos($estadoPedido, 'cancel') === false);

      if ($mostrarRecompensasPedido && $idClientePedido > 0) {
      echo '<hr size="5" style="margin: 20px 0 10px;">
            <table border="0" align="center" width="100%">
              <tr>
                <td align="center" colspan="3">
                  <div style="border:3px solid #000;padding:15px;margin:10px 0;text-align:center">';

      echo '    <div style="font-size:18px;font-weight:900;color:#000;margin-bottom:8px">
                      *** MONEDERO EGS ***
                    </div>';

      if ($totalPedido > 0) {
          // Mostrar lo que genera el pedido
          $textoGeneradoPed = (stripos($estadoPedido, 'Entregado') !== false) ? 'generó' : 'generará al ser entregado';

          if ($ordenesEnPrograma > 0) {
              // Cliente existente: mostrar saldo + generación
              echo '    <div style="border:1px solid #000;padding:12px;margin:6px 0;text-align:center">
                          <div style="font-size:11px;color:#000;font-weight:700;text-transform:uppercase;letter-spacing:1px">Tu saldo disponible</div>
                          <div style="font-size:28px;font-weight:900;color:#000;margin:4px 0">$'.number_format($saldoElectronico, 2).'</div>
                          <div style="font-size:10px;color:#000">Recompensa vigente: '.$porcentajeCliente.'% fijo</div>
                        </div>';
          }

          echo '    <div style="font-size:14px;color:#000;font-weight:900;margin-top:8px;border:2px solid #000;padding:10px;text-align:center">
                      Este pedido te '.$textoGeneradoPed.'<br>
                      <span style="font-size:22px;display:block;margin:4px 0">$'.number_format($montoGenerado, 2).'</span>
                      <span style="font-size:11px;color:#000">en dinero electrónico ('.$porcentajeCliente.'% de $'.number_format($totalPedido, 2).')</span>
                    </div>
                    <div style="font-size:10px;color:#000;margin-top:6px">Tu dinero electrónico vence cada 6 meses. ¡Úsalo antes!</div>';
      } else {
          // Sin monto: bienvenida al programa
          echo '    <div style="font-size:13px;color:#000;font-weight:700;margin-bottom:6px">
                      ¡Bienvenido al programa de recompensas!
                    </div>
                    <div style="font-size:12px;color:#000;line-height:1.5;margin-bottom:8px">
                      Por cada pedido o servicio entregado acumulas <b>dinero electrónico</b> que puedes usar como descuento.
                    </div>
                    <div style="border:1px solid #000;padding:8px;margin:6px 0">
                      <div style="font-size:11px;color:#000;font-weight:700">
                        Actualmente acumulas <b>'.$porcentajeCliente.'%</b> fijo en dinero electrónico por cada servicio elegible.
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
      } // fin if ($mostrarRecompensasPedido)

      echo '<br>


                
        </div>
        
        <p>&nbsp;</p>';
}
    
  }
} 

//datos de venta
$ticket = new ImprimirTicketsPedido();
$ticket -> TraerImpresionTicketPedido();


//$empresas = new ctrMostrarEmpresasParaEditar();
//$empresas ->ControladorEmpresas();


?>

</body>
</html>