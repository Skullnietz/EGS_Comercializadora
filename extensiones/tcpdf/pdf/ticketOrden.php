
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

      // RECOMPENSAS - DINERO ELECTRONICO
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

      // =============================================
      // ENCABEZADO: LOGO + DATOS DE EMPRESA
      // =============================================
      echo '<div class="zona_impresion" style="font-family:\'Arial Narrow\',Arial,Helvetica,sans-serif">
        <div style="text-align:center">
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="width:60px;vertical-align:middle;text-align:center">
                <img src="https://backend.comercializadoraegs.com/extensiones/tcpdf/pdf/images/logoEGS (1).png" alt="LOGO" style="width:55px;height:auto">
              </td>
              <td style="vertical-align:middle;text-align:center">
                <div style="font-size:15px;font-weight:900">Comercializadora EGS</div>
              </td>
            </tr>
          </table>
          <hr style="margin:2px 0">
          <div style="font-size:12px;font-weight:700">'.$Sitio.'</div>
          <div style="font-size:12px;font-weight:700;margin:2px 0">'.$Direccion.'</div>
          <div style="font-size:12px">'.$Telefono.' | '.$Telefono3.' | '.$Telefono4.'</div>
          <div style="font-size:12px">Solo WhatsApp (No llamar): '.$Telefono2.'</div>
          <div style="font-size:12px;margin:2px 0">L-V 10:00-14:00 y 16:00-18:30 | Sab 9:00-14:30</div>
          <div style="font-size:12px;font-weight:700">Estado de tu orden: comercializadoraegs.com/ordenes</div>
        </div>';

      // =============================================
      // DATOS DE LA ORDEN
      // =============================================
      echo '
        <hr style="margin:3px 0">
        <div style="text-align:center">
          <div style="font-size:12px">'.$nuevaFecha.'</div>
          <div style="font-size:20px;font-weight:900;margin:2px 0">ORDEN No.'.$id.'</div>';

      if ($value["estado"] == "Entregado (Ent)") {
          $fechaEntrega = date_create($value["fecha_Salida"]);
          $nuevaFechaEntrega = date_format($fecha, 'd/m/Y H:i:s');
          echo '<div style="font-size:12px;font-weight:700">'.$value["estado"].' EL '.$nuevaFechaEntrega.'</div>';
      } else {
          echo '<div style="font-size:12px;font-weight:700">Orden: '.$value["estado"].'</div>';
      }

      echo '  <div style="font-size:12px;margin:2px 0"><b>Cliente:</b> '.$NombreUsuario.'</div>
        </div>';

      // =============================================
      // DATOS DEL EQUIPO (Marca, Modelo, Serie)
      // =============================================
      $marcaEquipo = isset($value["marcaDelEquipo"]) ? trim($value["marcaDelEquipo"]) : '';
      $modeloEquipo = isset($value["modeloDelEquipo"]) ? trim($value["modeloDelEquipo"]) : '';
      $serieEquipo = isset($value["numeroDeSerieDelEquipo"]) ? trim($value["numeroDeSerieDelEquipo"]) : '';

      if ($marcaEquipo !== '' || $modeloEquipo !== '' || $serieEquipo !== '') {
          echo '
        <div style="border-top:1px dashed #000;border-bottom:1px dashed #000;margin:3px 0;padding:2px 0">
          <div style="font-size:12px;font-weight:900;text-align:center;text-transform:uppercase">Datos del equipo</div>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">';
          if ($marcaEquipo !== '') {
              echo '<tr>
              <td style="font-size:12px;font-weight:700;padding:1px 4px;width:28%">Marca:</td>
              <td style="font-size:12px;padding:1px 4px;text-transform:uppercase">'.$marcaEquipo.'</td>
            </tr>';
          }
          if ($modeloEquipo !== '') {
              echo '<tr>
              <td style="font-size:12px;font-weight:700;padding:1px 4px;width:28%">Modelo:</td>
              <td style="font-size:12px;padding:1px 4px;text-transform:uppercase">'.$modeloEquipo.'</td>
            </tr>';
          }
          if ($serieEquipo !== '') {
              echo '<tr>
              <td style="font-size:12px;font-weight:700;padding:1px 4px;width:28%">No. Serie:</td>
              <td style="font-size:12px;padding:1px 4px;text-transform:uppercase;font-weight:700">'.$serieEquipo.'</td>
            </tr>';
          }
          echo '</table>
        </div>';
      }

      // =============================================
      // DETALLE DE SERVICIOS / PARTIDAS
      // =============================================
      echo '
        <div style="margin-top:4px">
          <div style="font-size:12px;font-weight:900;text-align:center;text-transform:uppercase;padding-bottom:2px">Detalle de servicios</div>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="font-size:12px;font-weight:700;text-transform:uppercase;padding:2px 0;border-bottom:1px solid #000" width="78%">Descripcion</td>
              <td style="font-size:12px;font-weight:700;text-transform:uppercase;padding:2px 0;border-bottom:1px solid #000;text-align:right" width="22%">Precio</td>
            </tr>';

      $numPartida = 1;

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
                <td style="text-transform:uppercase;padding:3px 0 3px 2px;font-size:12px;border-bottom:1px dotted #ccc">'.$numPartida.'. '.$pf["desc"].'</td>
                <td style="text-align:right;padding:3px 2px;font-size:12px;font-weight:700;border-bottom:1px dotted #ccc;white-space:nowrap">$'.$pf["precio"].'</td>
              </tr>';
              $numPartida++;
          }
      }

      // Partidas dinamicas (JSON)
      if (is_array($partidas)) {
          foreach ($partidas as $valuePartidas) {
              if (isset($valuePartidas["descripcion"]) && trim($valuePartidas["descripcion"]) != "") {
                  echo '<tr>
                    <td style="text-transform:uppercase;padding:3px 0 3px 2px;font-size:12px;border-bottom:1px dotted #ccc">'.$numPartida.'. '.$valuePartidas["descripcion"].'</td>
                    <td style="text-align:right;padding:3px 2px;font-size:12px;font-weight:700;border-bottom:1px dotted #ccc;white-space:nowrap">$'.$valuePartidas["precioPartida"].'</td>
                  </tr>';
                  $numPartida++;
              }
          }
      }

      // Fila de TOTAL
      echo '
            <tr>
              <td style="padding:4px 0 2px 2px;font-size:12px;font-weight:900;text-align:right;border-top:2px solid #000">TOTAL</td>
              <td style="padding:4px 2px 2px 0;font-size:14px;font-weight:900;text-align:right;border-top:2px solid #000;white-space:nowrap">$'.$value["total"].'</td>
            </tr>
          </table>';

      // Ahorro por dinero electronico
      if ($montoCanjeado > 0) {
          echo '<div style="border:1px dashed #000;padding:4px;text-align:center;margin-top:3px">
                  <span style="font-size:12px;font-weight:800">AHORRASTE $'.number_format($montoCanjeado, 2).' CON TU MONEDERO EGS</span>
                </div>';
      }

      echo '</div>';

      } // fin foreach ordenes

      // =============================================
      // ATENDIDO POR (una sola linea)
      // =============================================
      echo '
        <div style="margin-top:4px;border-top:1px solid #000;padding-top:3px;font-size:12px;text-align:center">
          <b>Asesor:</b> '.$NombreAsesor.' &nbsp;|&nbsp; <b>Tecnico:</b> '.$NombreTecnico.'
        </div>';

      // =============================================
      // FIRMA DEL CLIENTE
      // =============================================
      echo '
        <div style="margin-top:16px;text-align:center">
          <div style="border-bottom:1px solid #000;width:65%;margin:0 auto"></div>
          <div style="font-size:12px;font-weight:700;margin-top:2px;text-transform:uppercase">Firma del cliente</div>
        </div>';

      // =============================================
      // TERMINOS Y CONDICIONES
      // =============================================
      echo '
        <div style="margin-top:6px;border-top:1px dashed #000;padding-top:3px">
          <div style="font-size:12px;font-weight:900;text-align:center;text-transform:uppercase;margin-bottom:2px">Terminos y condiciones</div>
          <div style="font-size:12px;color:#000;line-height:1.4">
            <div style="padding:1px 0">1. La garantia del servicio es por 30 dias a partir de la fecha de entrega.</div>
            <div style="padding:1px 0">2. La empresa no se responsabiliza por accesorios que el cliente reclame y no se encuentren detallados en esta orden.</div>
            <div style="padding:1px 0">3. El equipo sera entregado solamente al portador de esta orden, en su defecto debera retirar el propietario que funge como CLIENTE exhibiendo INE.</div>
            <div style="padding:1px 0">4. Toda reparacion debe ser retirada dentro de los 30 dias de comunicado su arreglo, caso contrario se perdera el reclamo.</div>
            <div style="padding:1px 0">5. En consumibles originales y compatibles no hay garantia.</div>
            <div style="padding:1px 0">6. No nos responsabilizamos de la informacion contenida en su equipo.</div>
            <div style="padding:1px 0">7. Al ingresar su orden acepta que esta sera publica en nuestro sitio web sin mostrar datos del cliente, solamente datos tecnicos y fotos del equipo.</div>
          </div>
        </div>';

      // =============================================
      // FACTURACION + IMPORTANTE (combinados)
      // =============================================
      echo '
        <div style="margin-top:4px;border:1px solid #000;padding:3px 4px;text-align:center;font-size:12px">
          <div><b>Facturacion:</b> comercializadoraegs.com/facturacion</div>
          <div style="margin-top:2px"><b>Importante:</b> En horario de comida <b>2:00pm a 4:00pm</b> no se realizan entregas.</div>
        </div>';

      // =============================================
      // SECCION DE RECOMPENSAS - MONEDERO ELECTRONICO EGS
      // =============================================
      $estadoOrden = $value["estado"];
      $mostrarRecompensas = (stripos($estadoOrden, 'cancel') === false
                          && stripos($estadoOrden, 'can)') === false
                          && stripos($estadoOrden, 'SR)') === false);

      if ($mostrarRecompensas) {
      echo '
        <hr style="margin:6px 0 3px">
        <div style="border:2px solid #000;padding:6px;text-align:center">';

      $esEstadoREV = (stripos($estadoOrden, 'REV') !== false);

      if ($ordenesEnPrograma == 0) {
          // PRIMERA ORDEN - Mensaje de bienvenida
          echo '<div style="font-size:13px;font-weight:900;margin-bottom:3px">*** MONEDERO EGS ***</div>
                <div style="font-size:12px;font-weight:700;margin-bottom:3px">Bienvenido al programa de recompensas</div>
                <div style="font-size:12px;line-height:1.3;margin-bottom:4px">Por cada orden entregada acumulas <b>dinero electronico</b> que puedes usar como descuento en tu proximo servicio. Acumulas el <b>1%</b> del total como recompensa.</div>';
          if ($esEstadoREV) {
              echo '<div style="font-size:12px;font-weight:700;border:1px solid #000;padding:4px">
                      Tu equipo se encuentra en revision por nuestro equipo tecnico.
                      <span style="font-size:12px;font-weight:400;display:block;margin-top:2px">Una vez definido el servicio, podras conocer el monto de dinero electronico que esta orden generara para ti.</span>
                    </div>';
          } else {
              echo '<div style="font-size:12px;font-weight:900;border:1px solid #000;padding:4px">
                      Esta orden te generara $'.number_format($montoGenerado, 2).' en dinero electronico al ser entregada
                    </div>';
          }
      } else {
          // CLIENTE CON HISTORIAL
          echo '<div style="font-size:13px;font-weight:900;margin-bottom:3px">*** MONEDERO EGS ***</div>
                <div style="border:1px solid #000;padding:4px;margin-bottom:4px">
                  <div style="font-size:12px;font-weight:700;text-transform:uppercase">Tu saldo disponible</div>
                  <div style="font-size:20px;font-weight:900;margin:2px 0">$'.number_format($saldoElectronico, 2).'</div>
                  <div style="font-size:12px">Recompensa: 1% | '.$entregadasCliente.' ordenes entregadas</div>
                </div>';

          if ($esEstadoREV) {
              echo '<div style="font-size:12px;font-weight:700;border:1px solid #000;padding:4px">
                      Tu equipo se encuentra en revision.
                      <span style="font-size:12px;font-weight:400;display:block;margin-top:2px">El monto de dinero electronico se calculara una vez definido el servicio.</span>
                    </div>';
          } else {
              $textoGenerado = ($value["estado"] == "Entregado (Ent)") ? 'genero' : 'generara al ser entregada';
              echo '<div style="font-size:12px;font-weight:900;border:1px solid #000;padding:4px">
                      Esta orden te '.$textoGenerado.'
                      <span style="font-size:16px;display:block;margin:2px 0">$'.number_format($montoGenerado, 2).'</span>
                      <span style="font-size:12px">en dinero electronico ('.$porcentajeCliente.'% de $'.number_format($totalOrden, 2).')</span>
                    </div>';
          }

          echo '<div style="font-size:12px;margin-top:3px">Tu dinero electronico vence cada 6 meses. Usalo antes!</div>';
      }

      // QR para consultar monedero
      if (!empty($tokenMonedero)) {
          $urlMonedero = 'https://backend.comercializadoraegs.com/monedero.php?token=' . $tokenMonedero;
          $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($urlMonedero);

          echo '<div style="margin-top:4px;padding-top:4px;border-top:1px dashed #000">
                  <div style="font-size:12px;font-weight:700;margin-bottom:3px">Escanea para ver tu monedero:</div>
                  <img src="'.$qrUrl.'" alt="QR Monedero" style="width:100px;height:100px">
                </div>';
      }

      echo '</div>';
      } // fin if ($mostrarRecompensas)

      // =============================================
      // CIERRE
      // =============================================
      echo '
        <div style="text-align:center;font-size:12px;font-weight:700;margin:6px 0 4px">Gracias por su visita!</div>
      </div>';

      // =============================================
      // AVISO DE PRIVACIDAD PARA REV
      // =============================================
      if (stripos($estadoOrden, 'REV') !== false) {
          echo '
          <div class="zona_impresion" style="font-family:\'Arial Narrow\',Arial,Helvetica,sans-serif; page-break-before: always; margin-top:10px;">
            <div style="border-top:1px dashed #000; margin-bottom:5px;"></div>
            <div style="text-align:center;font-size:10px;font-weight:700;margin-bottom:10px;">- CORTE DE ETIQUETADORA -</div>
            <div style="font-size:12px; text-align:justify; line-height:1.3;">
              <div style="text-align:center;font-weight:900;margin-bottom:8px;font-size:13px;">AVISO Y POLÍTICA DE PRIVACIDAD PARA EL MANEJO DE DATOS PERSONALES</div>
              <div style="text-align:center;font-weight:700;margin-bottom:8px;">COMERCIALIZADORA EGS (EQUIPO DE CÓMPUTO Y SOFTWARE)</div>
              <p style="margin:4px 0;"><b>Asunto:</b> Confidencialidad y Autorización de Mensajes Promocionales</p>
              <p style="margin:4px 0;">ESTIMADO/A CLIENTE: <b>'.$NombreUsuario.'</b></p>
              <p style="margin:4px 0;">En COMERCIALIZADORA EGS valoramos la confianza que depositas en nosotros. Para proteger tu información, nos comprometemos a mantener la confidencialidad de los datos que compartas con nosotros.</p>
              <p style="margin:4px 0;">Además, nos gustaría mantenerte al tanto de nuestras ofertas y novedades. Si deseas recibir mensajes promocionales de COMERCIALIZADORA EGS a través de WhatsApp, por favor, responde a este aviso seleccionando "ACEPTO".</p>
              
              <div style="text-align:center; font-weight:900; margin:14px 0;">[ &nbsp;&nbsp; ] ACEPTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ &nbsp;&nbsp; ] NO ACEPTO</div>
              
              <p style="margin:4px 0;">Esta carta o acuerdo de confidencialidad de la empresa COMERCIALIZADORA EGS se fundamenta principalmente en la protección de datos personales. Se apega a los siguientes artículos y leyes fundamentales:</p>
              
              <div style="margin:4px 0;">
                <b>1. Constitución Política de los Estados Unidos Mexicanos</b><br>
                Artículo 16 (Segundo párrafo): Protege el derecho a la protección de datos personales, el acceso, rectificación, cancelación y oposición (derechos ARCO), así como la privacidad de las comunicaciones.
              </div>
              <div style="margin:4px 0;">
                <b>2. Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP)</b><br>
                Esta es la ley principal para el manejo de información de clientes.<br>
                - <b>Artículo 6:</b> Establece que los responsables del tratamiento de datos (la empresa) deben garantizar la confidencialidad.<br>
                - <b>Artículos 14 y 15:</b> Obligan a que el tratamiento de datos se limite a las finalidades acordadas y se proteja contra el uso indebido.<br>
                - <b>Artículo 21:</b> Obliga a los terceros que reciban datos a mantener la confidencialidad.
              </div>
              
              <p style="margin:6px 0;">Tu privacidad es importante. Puedes revocar este permiso por escrito en cualquier momento.</p>
              <p style="text-align:center;margin:12px 0 25px 0;">Atentamente,<br><b>COMERCIALIZADORA EGS</b></p>
              
              <div style="margin-top:30px;text-align:center">
                  <div style="border-bottom:1px solid #000;width:70%;margin:0 auto"></div>
                  <div style="font-size:12px;font-weight:700;margin-top:4px;text-transform:uppercase">FIRMA DE CONFORMIDAD</div>
                  <div style="font-size:11px;font-weight:700;margin-top:15px;text-align:left;">FECHA: _____/_____/_________</div>
              </div>
            </div>
          </div>';
      }

  }
}


//datos de venta
$ticket = new ImprimirTicketsOrden();
$ticket -> TraerImpresionTicketOrden();


?>

</body>
</html>
