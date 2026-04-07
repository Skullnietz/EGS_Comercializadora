
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<link href="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/ticket.css" rel="stylesheet" type="text/css">
</head>
<body onload="window.print();">

<?php


require_once "../../../controladores/ordenes.controlador.php";
require_once "../../../modelos/ordenes.modelo.php";
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
class ImprimirTicketsOrden{

  function TraerImpresionTicketOrden(){


      //TRAER LA INFORMACION DE LA VENTA

      $item = "id";
      $valor = $_GET["idOrden"];
      $ordenes = controladorOrdenes::ctrMostrarordenesParaValidar($item,$valor);
      foreach ($ordenes as $key => $value) {

          $id =  $value["id"];


          $precioUno =  $value["precioUno"];
          $precioDos =  $value["precioDos"];
          $precioTres =  $value["precioTres"];
          $partidaCuatro =  $value["partidaCuatro"];
          $precioCuatro =  $value["precioCuatro"];
          $precioCinco =  $value["precioCinco"];
          $precioSeis =  $value["precioSeis"];
          $partidaSiete =  $value["partidaSiete"];
          $precioSiete =  $value["precioSiete"];
          $precioOcho =  $value["precioOcho"];
          $partidaNueve =  $value["partidaNueve"];
          $precioNueve =  $value["precioNueve"];
          $precioDiez =  $value["precioDiez"];
          $total =  $value["total"];

          $partidas = json_decode($value["partidas"], true);
          $uno = 1;

      $fecha = date_create($value["fecha"]);
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
      $HorarioEmpresa = $respuesta["Horario"];
      $Facebook = $respuesta["Facebook"];
      $Sitio = $respuesta["Sitio"];


     //TRAER ASESOR

      $item = "id";
      $valor = $_GET["asesor"];

      $asesor = Controladorasesores::ctrMostrarAsesoresEleg($item,$valor);

      $NombreAsesor = $asesor["nombre"];

      //TRAER CLIENTE (USUARIO)
      $itemc = "id";
      $valorc = $_GET["cliente"];

      $usuario = ControladorClientes::ctrMostrarClientesTabla($itemc,$valorc);


      foreach ($usuario as $key => $valueUsuario) {
        $NombreUsuario = $valueUsuario["nombre"];
      }

      //TRAER TECNICO
      $item = "id";
      $valor = $_GET["tecnico"];

      $tecnico = ControladorTecnicos::ctrMostrarTecnicos($item,$valor);

      $NombreTecnico = $tecnico["nombre"];
      $hrsComida = $tecnico["HoraDeComida"];

      // ═══════════════════════════════════════
      // RECOMPENSAS - DINERO ELECTRÓNICO
      // ═══════════════════════════════════════
      $idCliente = intval($_GET["cliente"]);
      $idOrden = intval($_GET["idOrden"]);
      $totalOrden = floatval($value["total"]);

      try {
          $infoRecompensas = ControladorRecompensas::ctrObtenerInfoRecompensas($idCliente);
          $saldoElectronico = $infoRecompensas["saldo"];
          $porcentajeCliente = $infoRecompensas["porcentaje"];
          $entregadasCliente = $infoRecompensas["entregadas"];
          $esNuevo = $infoRecompensas["es_nuevo"];
          $ordenesEnPrograma = intval($infoRecompensas["ordenes_en_programa"]);
          $tokenMonedero = $infoRecompensas["token"];

          $montoGenerado = round($totalOrden * ($porcentajeCliente / 100), 2);

          $canjeOrden = ControladorRecompensas::ctrObtenerCanjeOrden($idOrden);
          $montoCanjeado = $canjeOrden ? abs(floatval($canjeOrden["monto"])) : 0;
      } catch (Exception $e) {
          $saldoElectronico = 0;
          $porcentajeCliente = 1;
          $entregadasCliente = 0;
          $esNuevo = true;
          $ordenesEnPrograma = 0;
          $tokenMonedero = '';
          $montoGenerado = 0;
          $montoCanjeado = 0;
      }

      echo '<div class="zona_impresion">
         <br>

<!-- Mostramos los datos de la empresa en el documento HTML -->
                        <div><img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="float: left"></div>
                        <center>
                        <div style="margin-top:20px;font-size: 200%"><strong>Comercializadora EGS</strong></div>
                        <hr><div style="font-size: 100%"><b>'.$Sitio.'</b></div><br>
                        <div><h3><b>'.$Direccion.' <h3><b> TELEFONOS<br> '.$Telefono.' <br> '.$Telefono3.' <br> '.$Telefono4.'</b></h3></div><div>Sólo Whatsapp (No llamar): '.$Telefono2.'</div>
                        <div><h3>Horario:
                        <br>Lunes a Viernes de 10:00-14:00 y de 16:00-18:30
                        <br>Sabado de 9:00 a 14:30</h3></div>
                        <div><b><h4>Conoce el estado de tu orden en: comercializadoraegs.com/ordenes</h4> </b></div></center>


<b><hr></b>

        <table border="0" align="center" width="200px">

            <tr>
                <td align="center">'.$nuevaFecha.'</td>
            </tr>

            <tr>
                <td align="center"><b><h1>ORDEN No.'.$id.'</h1></b></td>
            </tr>';

            if ($value["estado"] == "Entregado (Ent)"){

              $fechaEntrega = date_create($value["fecha_Salida"]);

              $nuevaFechaEntrega = date_format($fecha, 'd/m/Y H:i:s');

                echo'<tr>

                  <td align="center"><b>'.$value["estado"].' EL '.$nuevaFechaEntrega.'</b></td>

                </tr>';

            }else{

               echo'<tr>
                <td align="center"><b>Orden:'.$value["estado"].'</b></td>
            </tr>';

            }


            echo'<tr>
                <td align="center"></td>
            </tr>

            <!-- Mostramos los datos del cliente en el documento HTML -->

            <tr><center>
             <b>Cliente:</b> '.$NombreUsuario.'
            </center></tr>

        </table>

        <br>

        <!-- Mostramos los detalles de la venta en el documento HTML -->

        <table border="0" align="center" width="10px">

            <tr>
               <td>Part.</td>
               <td align="left">Total</td>

            </tr>

          <tr>

            <td colspan="4">====================================================</td>

          </tr>';

              //$precioTotal = number_format($item["pago"], 2);

               echo '<tr></tr>
                      <!-- Mostramos los totales de la venta en el documento HTML -->';

                      if ($value["partidaUno"] != "") {

                         echo'<tr>
                         <!--PARTIDA UNO -->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaUno"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioUno.'</b></td>';
                      }else{

                        echo'<tr  style="display:none">
                        <!-- PARTIDA UNO OCULTA -->
                            <td  style="display:none">'.$uno.' '.$value["partidaUno"].'</td>
                            <td align="right"  style="display:none"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioUno.'</b></td>
                        </tr>';
                      }

                    echo'<tr></tr>';
                    if ($value["partidaDos"] != "") {

                      echo'<tr>
                      <!-- PARTIDA DOS -->
                      <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaDos"].'</td>
                      <td></br></br>
                      <td align="right"><b>$'.$precioDos.'</b></td>';
                    }else{

                      echo'<tr style="display:none">
                       <!-- PARTIDA DOS OCULTA -->
                      <td style="display:none">'.$uno.' '.$value["partidaDos"].'</td>
                      <td style="display:none"></br></br>
                      <td align="right" style="display:none"><b>$'.$precioDos.'</b></td>';
                    }


                    echo'</tr><tr>';
                    if ($value["partidaTres"] != "") {

                      echo'<tr>
                       <!--  //PARTIDA TRES -->
                      <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaTres"].'</td>
                      <td></br></br>
                      <td align="right"><b>$'.$precioTres.'</b></td>';

                    }else{

                      echo'<tr style="display:none">
                       <!--PARTIDA TRES OCULTA-->
                      <td style="display:none">'.$uno.' '.$value["partidaTres"].'</td>
                      <td style="display:none"></br></br>
                      <td align="right" style="display:none"><b>$'.$value["precioTres"].'</b></td>';

                    }

                    echo'</tr><tr>';
                    if ($value["partidaCuatro"] != "") {

                      echo'<tr>
                      <!--PARTIDA CUATRO -->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaCuatro"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioCuatro.'</b></td>';
                    }else{

                      echo'<tr style="display:none">
                      <!--PARTIDA CUATRO OCULTA -->
                      <td style="display:none">'.$uno.' '.$value["partidaCuatro"].'</td>
                      <td style="display:none"></br></br>
                      <td align="right" style="display:none"><b>$'.$precioCuatro.'</b></td>';

                    }

                     echo'</tr><tr>';
                    if ($value["partidaCinco"] != ""){

                      echo'<tr>
                      <!--PARTIDA CINCO-->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaCinco"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioCinco.'</b></td>';
                    }else{

                       echo'<tr  style="display:none">
                            <!--PARTIDA CINCO OCULTA-->
                            <td  style="display:none">'.$uno.' '.$$value["partidaCinco"].'</td>
                            <td align="right"  style="display:none"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioCinco.'</b></td>
                        </tr>';

                    }
                     echo'</tr><tr>';
                    if($value["partidaSeis"] != ""){

                      echo'<tr>
                      <!--PARTIDA SEIS -->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaSeis"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioSeis.'</b></td>';
                    }else{

                       echo'<tr  style="display:none">
                       <!--PARTIDA SEIS OCULTA -->
                            <td  style="display:none">'.$uno.' '.$value["partidaSeis"].'</td>
                            <td align="right"  style="display:none"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioSeis.'</b></td>
                        </tr>';

                    }
                  echo'</tr><tr>';
                    if($value["partidaSiete"] != ""){

                      echo'<tr>
                      <!--PARTIDA SIETE-->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaSiete"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioSiete.'</b></td>';
                    }else{

                       echo'<tr  style="display:none">
                       <!--PARTIDA SIETE OCULTA -->
                            <td  style="display:none">'.$uno.' '.$$value["partidaSiete"].'</td>
                            <td align="right"  style="display:none"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioSiete.'</b></td>
                        </tr>';

                    }

                  echo'</tr><tr>';
                    if ($value["partidaOcho"] != "") {

                       echo'<tr>
                       <!--PARTIDA OCHO-->
                      <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaOcho"].'</td>
                      <td></br></br>
                      <td align="right"><b>$'.$precioOcho.'</b></td>';

                    }else{

                       echo'<tr  style="display:none">
                            <!--PARTIDA OCHO OCULTA -->
                            <td  style="display:none">'.$uno.' '.$value["partidaOcho"].'</td>
                            <td align="right"  style="display:none"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioSiete.'</b></td>
                        </tr>';

                    }

                  echo'</tr><tr>';
                    if($value["partidaNueve"] != ""){

                       echo'<tr>
                       <!--PARTIDA NUEVA -->
                      <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaNueve"].'</td>
                      <td></br></br>
                      <td align="right"><b>$'.$precioNueve.'</b></td>';
                    }else{

                      echo'<tr  style="display:none">
                            <!--PARTIDA NUEVE OCULTA -->
                            <td  style="display:none">'.$uno.' '.$value["partidaNueve"].'</td>
                            <td align="right"><b></b></td>
                            <td align="right"  style="display:none"><b>$'.$precioSiete.'</b></td>
                        </tr>';
                    }


                  echo'</tr><tr>';
                    if($value["partidaDiez"] != ""){

                      echo'<tr>
                      <!--PARTIDA DIEZ -->
                        <td style="text-transform:uppercase;">'.$uno.' '.$value["partidaDiez"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$precioDiez.'</b></td>';
                    }else{

                        echo'<tr style="display:none">
                         <!--PARTIDA DIEZ OCULTA -->
                        <td  style="display:none">'.$uno.' '.$value["partidaDiez"].'</td>
                        <td></br></br>
                        <td align="right"  style="display:none"><b>$'.$precioDiez.'</b></td>';

                    }

                    echo'</tr>';

                    foreach ($partidas as $key => $valuePartidas) {

                      if ($valuePartidas["descripcion"] != "") {
                         echo'<tr>
                      <!--PARTIDAS -->
                        <td>'.$uno.' '.$valuePartidas["descripcion"].'</td>
                        <td></br></br>
                        <td align="right"><b>$'.$valuePartidas["precioPartida"].'</b></td>';

                      }else{

                        echo'<tr style="display:none">
                         <!--PARTIDAS OCULTAS -->
                        <td style="display:none">'.$uno.' '.$valuePartidas["descripcion"].'</td>
                        <td></br></br>
                        <td align="right"  style="display:none"><b>$'.$valuePartidas["precioPartida"].'</b></td>';

                      }
                    }

                echo' </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td align="right"><b>TOTAL:</b></td>
                  <td align="right"><b>$'.$value["total"].'</b></td>
                </tr>';

                // ═══════════════════════════════════════
                // MOSTRAR AHORRO POR DINERO ELECTRÓNICO
                // ═══════════════════════════════════════
                if ($montoCanjeado > 0) {
                    echo '<tr>
                      <td colspan="3" style="padding-top:8px">
                        <div style="border:2px dashed #000;padding:10px;text-align:center">
                          <span style="font-size:14px;font-weight:800;color:#000">AHORRASTE $'.number_format($montoCanjeado, 2).' CON TU MONEDERO EGS</span>
                        </div>
                      </td>
                    </tr>';
                }

                }


                echo'<tr>
                    <td colspan="4">&nbsp;</td>
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
                    <td colspan="4" align="left"><b>Lunes a Viernes de 10:00 am a 6:30 pm <br> Sabados de 9:00 am a 2:00 pm
</b></td>
                </tr>
                <tr>
                    <td colspan="4" align="left"><b>Asesor:'.$NombreAsesor.'</b></td>

                </tr>

                 <tr>
                    <td colspan="4" align="left"><b>Tecnico:'.$NombreTecnico.'</b></td>
                </tr>

                 <!--<tr>
                    <td colspan="4" align="left"><b>Horario De Comida:'.$hrsComida.'</b></td>
                </tr>-->
                <tr>
                    <td colspan="4" align="left"><h4><b><center>IMPORTANTE</center></b><h4></td>
                </tr>
                 <tr>
                <center>
                <b><hr></b>
                FIRMA
                </center>
                </tr>
                <tr>
                    <td colspan="4" align="left"><h3><b><center>Por seguridad y atención a sus servicios dentro del horario de comida 2:00pm a 4:00pm no se realizan entregas de ningún tipo. Gracias.</center></b><h3></td>
                </tr>

                <tr>

                    <!-- Mostramos los datos del cliente en el documento HTML -->
                    <td colspan="4" align="center">'.$nombreDelCliente.'</td>

                </tr>

              </tr>

            </table>';

      // ═══════════════════════════════════════════════════════════════
      // SECCIÓN DE RECOMPENSAS - MONEDERO ELECTRÓNICO EGS
      // Solo mostrar si la orden NO está cancelada ni sin reparación
      // ═══════════════════════════════════════════════════════════════
      $estadoOrden = $value["estado"];
      // No mostrar recompensas en órdenes canceladas o sin reparación
      // ya que no se están entregando al cliente
      $mostrarRecompensas = (stripos($estadoOrden, 'cancel') === false
                          && stripos($estadoOrden, 'can)') === false
                          && stripos($estadoOrden, 'SR)') === false);

      if ($mostrarRecompensas) {
      echo '<hr size="5" style="margin: 20px 0 10px;">
            <table border="0" align="center" width="100%">
              <tr>
                <td align="center" colspan="3">
                  <div style="border:3px solid #000;padding:15px;margin:10px 0;text-align:center">';

      $esEstadoREV = (stripos($estadoOrden, 'REV') !== false);

      if ($ordenesEnPrograma == 0) {
          // PRIMERA ORDEN EN EL PROGRAMA - Mensaje de bienvenida
          echo '    <div style="font-size:18px;font-weight:900;color:#000;margin-bottom:8px">
                      *** MONEDERO EGS ***
                    </div>
                    <div style="font-size:13px;color:#000;font-weight:700;margin-bottom:6px">
                      ¡Bienvenido al programa de recompensas!
                    </div>
                    <div style="font-size:12px;color:#000;line-height:1.5;margin-bottom:8px">
                      Por cada orden entregada acumulas <b>dinero electrónico</b> que puedes usar como descuento en tu próximo servicio.
                    </div>
                    <div style="border:1px solid #000;padding:8px;margin:6px 0">
                      <div style="font-size:11px;color:#000;font-weight:700">
                        Por cada orden entregada acumulas el <b>1%</b> del total como recompensa.
                      </div>
                    </div>';
          if ($esEstadoREV) {
              echo '  <div style="font-size:13px;color:#000;font-weight:700;margin-top:8px;border:2px solid #000;padding:8px">
                        Tu equipo se encuentra en revisión por nuestro equipo técnico.<br>
                        <span style="font-size:11px;font-weight:500;display:block;margin-top:4px">Una vez definido el servicio, podrás conocer el monto de dinero electrónico que esta orden generará para ti.</span>
                      </div>';
          } else {
              echo '  <div style="font-size:13px;color:#000;font-weight:900;margin-top:8px;border:2px solid #000;padding:8px">
                        Esta orden te generará $'.number_format($montoGenerado, 2).' en dinero electrónico al ser entregada
                      </div>';
          }
      } else {
          // CLIENTE CON HISTORIAL - Mostrar saldo y recompensa generada
          echo '    <div style="font-size:18px;font-weight:900;color:#000;margin-bottom:8px">
                      *** MONEDERO EGS ***
                    </div>
                    <div style="border:1px solid #000;padding:12px;margin:6px 0;text-align:center">
                      <div style="font-size:11px;color:#000;font-weight:700;text-transform:uppercase;letter-spacing:1px">Tu saldo disponible</div>
                      <div style="font-size:28px;font-weight:900;color:#000;margin:4px 0">$'.number_format($saldoElectronico, 2).'</div>
                      <div style="font-size:10px;color:#000">Recompensa: 1% | '.$entregadasCliente.' órdenes entregadas</div>
                    </div>';

          if ($esEstadoREV) {
              echo '  <div style="font-size:14px;color:#000;font-weight:700;margin-top:8px;border:2px solid #000;padding:10px;text-align:center">
                        Tu equipo se encuentra en revisión.<br>
                        <span style="font-size:12px;font-weight:500;display:block;margin:4px 0">El monto de dinero electrónico se calculará una vez definido el servicio. ¡Pronto tendrás novedades!</span>
                      </div>';
          } else {
              $textoGenerado = ($value["estado"] == "Entregado (Ent)") ? 'generó' : 'generará al ser entregada';
              echo '  <div style="font-size:14px;color:#000;font-weight:900;margin-top:8px;border:2px solid #000;padding:10px;text-align:center">
                        Esta orden te '.$textoGenerado.'<br>
                        <span style="font-size:22px;display:block;margin:4px 0">$'.number_format($montoGenerado, 2).'</span>
                        <span style="font-size:11px;color:#000">en dinero electrónico ('.$porcentajeCliente.'% de $'.number_format($totalOrden, 2).')</span>
                      </div>';
          }

          echo '    <div style="font-size:10px;color:#000;margin-top:6px">Tu dinero electrónico vence cada 6 meses. ¡Úsalo antes!</div>';
      }

      // QR para consultar monedero
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
      } // fin if ($mostrarRecompensas)

      echo '<hr size="5" style="margin: 20px 0;">
            <table>
            <center>

              <h3>¡Gracias por su visita!</h3>


              </center>




              <tr>

          </table>
        </div>

        <p>&nbsp;</p>';


  }
}

//datos de venta
$ticket = new ImprimirTicketsOrden();
$ticket -> TraerImpresionTicketOrden();





?>

</body>
</html>
