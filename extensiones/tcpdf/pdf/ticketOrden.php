
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

        </table>';

      // ═══════════════════════════════════════
      // DATOS DEL EQUIPO (Marca, Modelo, Serie)
      // ═══════════════════════════════════════
      $marcaEquipo = isset($value["marcaDelEquipo"]) ? trim($value["marcaDelEquipo"]) : '';
      $modeloEquipo = isset($value["modeloDelEquipo"]) ? trim($value["modeloDelEquipo"]) : '';
      $serieEquipo = isset($value["numeroDeSerieDelEquipo"]) ? trim($value["numeroDeSerieDelEquipo"]) : '';

      if ($marcaEquipo !== '' || $modeloEquipo !== '' || $serieEquipo !== '') {
          echo '
        <table border="0" align="center" width="100%" style="margin:8px 0 4px">
          <tr>
            <td colspan="2" style="border-bottom:1px dashed #000;padding-bottom:4px">
              <div style="font-size:12px;font-weight:900;text-align:center;text-transform:uppercase;letter-spacing:1px">Datos del equipo</div>
            </td>
          </tr>';
          if ($marcaEquipo !== '') {
              echo '
          <tr>
            <td style="font-size:11px;font-weight:700;padding:3px 4px;width:30%">Marca:</td>
            <td style="font-size:12px;padding:3px 4px;text-transform:uppercase">'.$marcaEquipo.'</td>
          </tr>';
          }
          if ($modeloEquipo !== '') {
              echo '
          <tr>
            <td style="font-size:11px;font-weight:700;padding:3px 4px;width:30%">Modelo:</td>
            <td style="font-size:12px;padding:3px 4px;text-transform:uppercase">'.$modeloEquipo.'</td>
          </tr>';
          }
          if ($serieEquipo !== '') {
              echo '
          <tr>
            <td style="font-size:11px;font-weight:700;padding:3px 4px;width:30%">No. Serie:</td>
            <td style="font-size:12px;padding:3px 4px;text-transform:uppercase;font-weight:700;letter-spacing:0.5px">'.$serieEquipo.'</td>
          </tr>';
          }
          echo '
          <tr>
            <td colspan="2" style="border-bottom:1px dashed #000;padding-top:2px"></td>
          </tr>
        </table>';
      }

      // ═══════════════════════════════════════
      // DETALLE DE SERVICIOS / PARTIDAS
      // ═══════════════════════════════════════
      echo '
        <div style="margin-top:10px">
          <div style="font-size:12px;font-weight:900;text-align:center;text-transform:uppercase;letter-spacing:1px;padding-bottom:6px">Detalle de servicios</div>
          <div style="border-top:2px solid #000;margin-bottom:8px"></div>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr style="border-bottom:1px solid #000">
              <td style="font-size:10px;font-weight:700;text-transform:uppercase;padding:4px 0;border-bottom:1px solid #000" width="75%">Descripcion</td>
              <td style="font-size:10px;font-weight:700;text-transform:uppercase;padding:4px 0;border-bottom:1px solid #000;text-align:right" width="25%">Precio</td>
            </tr>';

      // Contador para numerar partidas visibles
      $numPartida = 1;

      // Array con las 10 partidas fijas
      $partidasFijas = array(
          array("desc" => $value["partidaUno"], "precio" => $precioUno),
          array("desc" => $value["partidaDos"], "precio" => $precioDos),
          array("desc" => $value["partidaTres"], "precio" => $precioTres),
          array("desc" => $value["partidaCuatro"], "precio" => $precioCuatro),
          array("desc" => $value["partidaCinco"], "precio" => $precioCinco),
          array("desc" => $value["partidaSeis"], "precio" => $precioSeis),
          array("desc" => $value["partidaSiete"], "precio" => $precioSiete),
          array("desc" => $value["partidaOcho"], "precio" => $precioOcho),
          array("desc" => $value["partidaNueve"], "precio" => $precioNueve),
          array("desc" => $value["partidaDiez"], "precio" => $precioDiez),
      );

      foreach ($partidasFijas as $pf) {
          if (trim($pf["desc"]) != "") {
              echo '<tr>
                <td style="text-transform:uppercase;padding:6px 0 6px 2px;font-size:11px;border-bottom:1px dotted #ccc">'.$numPartida.'. '.$pf["desc"].'</td>
                <td style="text-align:right;padding:6px 2px 6px 0;font-size:12px;font-weight:700;border-bottom:1px dotted #ccc;white-space:nowrap">$'.$pf["precio"].'</td>
              </tr>';
              $numPartida++;
          }
      }

      // Partidas dinamicas (JSON)
      if (is_array($partidas)) {
          foreach ($partidas as $valuePartidas) {
              if (isset($valuePartidas["descripcion"]) && trim($valuePartidas["descripcion"]) != "") {
                  echo '<tr>
                    <td style="text-transform:uppercase;padding:6px 0 6px 2px;font-size:11px;border-bottom:1px dotted #ccc">'.$numPartida.'. '.$valuePartidas["descripcion"].'</td>
                    <td style="text-align:right;padding:6px 2px 6px 0;font-size:12px;font-weight:700;border-bottom:1px dotted #ccc;white-space:nowrap">$'.$valuePartidas["precioPartida"].'</td>
                  </tr>';
                  $numPartida++;
              }
          }
      }

      // Fila de TOTAL
      echo '
            <tr>
              <td style="padding:10px 0 4px 2px;font-size:13px;font-weight:900;text-align:right;border-top:2px solid #000">TOTAL</td>
              <td style="padding:10px 2px 4px 0;font-size:16px;font-weight:900;text-align:right;border-top:2px solid #000;white-space:nowrap">$'.$value["total"].'</td>
            </tr>
          </table>';

      // Ahorro por dinero electronico
      if ($montoCanjeado > 0) {
          echo '<div style="border:2px dashed #000;padding:10px;text-align:center;margin-top:8px">
                  <span style="font-size:14px;font-weight:800;color:#000">AHORRASTE $'.number_format($montoCanjeado, 2).' CON TU MONEDERO EGS</span>
                </div>';
      }

      } // fin foreach ordenes

      // ═══════════════════════════════════════
      // ATENDIDO POR
      // ═══════════════════════════════════════
      echo '
          <div style="margin-top:14px;border-top:1px solid #000;padding-top:8px">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="font-size:11px;font-weight:700;padding:2px 0;width:28%">Asesor:</td>
                <td style="font-size:11px;padding:2px 0">'.$NombreAsesor.'</td>
              </tr>
              <tr>
                <td style="font-size:11px;font-weight:700;padding:2px 0">Tecnico:</td>
                <td style="font-size:11px;padding:2px 0">'.$NombreTecnico.'</td>
              </tr>
            </table>
          </div>';

      // ═══════════════════════════════════════
      // FIRMA DEL CLIENTE
      // ═══════════════════════════════════════
      echo '
          <div style="margin-top:24px;text-align:center">
            <div style="border-bottom:1px solid #000;width:70%;margin:0 auto"></div>
            <div style="font-size:11px;font-weight:700;margin-top:4px;text-transform:uppercase;letter-spacing:1px">Firma del cliente</div>
          </div>';

      // ═══════════════════════════════════════
      // TERMINOS Y CONDICIONES
      // ═══════════════════════════════════════
      echo '
          <div style="margin-top:16px;border-top:1px dashed #000;padding-top:8px">
            <div style="font-size:10px;font-weight:900;text-align:center;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">Terminos y condiciones</div>
            <div style="font-size:9px;color:#000;line-height:1.6">
              <div style="padding:2px 0">1. La garantia del servicio es por 30 dias a partir de la fecha de entrega.</div>
              <div style="padding:2px 0">2. La empresa no se responsabiliza por accesorios que el cliente reclame y no se encuentren detallados en esta orden.</div>
              <div style="padding:2px 0">3. El equipo sera entregado solamente al portador de esta orden, en su defecto debera retirar el propietario que funge como CLIENTE exhibiendo INE.</div>
              <div style="padding:2px 0">4. Toda reparacion debe ser retirada dentro de los 30 dias de comunicado su arreglo, caso contrario se perdera el reclamo.</div>
              <div style="padding:2px 0">5. En consumibles originales y compatibles no hay garantia.</div>
              <div style="padding:2px 0">6. No nos responsabilizamos de la informacion contenida en su equipo.</div>
              <div style="padding:2px 0">7. Al ingresar su orden acepta que esta sera publica en nuestro sitio web sin mostrar datos del cliente, solamente datos tecnicos y fotos del equipo.</div>
            </div>
          </div>';

      // ═══════════════════════════════════════
      // FACTURACION Y HORARIO
      // ═══════════════════════════════════════
      echo '
          <div style="margin-top:10px;border:1px solid #000;padding:8px;text-align:center">
            <div style="font-size:10px;font-weight:700;margin-bottom:4px">Facturacion: comercializadoraegs.com/facturacion</div>
            <div style="font-size:10px">Lunes a Viernes 10:00am - 6:30pm | Sabados 9:00am - 2:00pm</div>
          </div>

          <div style="margin-top:8px;border:2px solid #000;padding:8px;text-align:center">
            <div style="font-size:11px;font-weight:900;text-transform:uppercase">Importante</div>
            <div style="font-size:10px;margin-top:4px">Por seguridad, dentro del horario de comida <b>2:00pm a 4:00pm</b> no se realizan entregas de ningun tipo.</div>
          </div>
        </div>';

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
