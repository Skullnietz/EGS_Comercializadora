<?php
$tokenEntrada = isset($_GET["g"]) ? trim((string) $_GET["g"]) : (isset($_GET["token"]) ? trim((string) $_GET["token"]) : "");
$tokenGarantia = strtolower($tokenEntrada);
if (preg_match('/^[A-Za-z0-9_-]{43}$/', $tokenEntrada)) {
    $base64 = strtr($tokenEntrada, '-_', '+/') . '=';
    $binario = base64_decode($base64, true);
    if ($binario !== false && strlen($binario) === 32) {
        $tokenGarantia = bin2hex($binario);
    }
}
$garantia = null;
$ordenIdentificacion = null;
if (preg_match('/^[a-f0-9]{64}$/', $tokenGarantia)) {
    try {
        $garantia = ModeloEtiquetas::mdlGarantiaPorToken($tokenGarantia);
    } catch (Exception $e) {
        $garantia = null;
    }
} elseif (preg_match('/^[a-f0-9]{32}$/', $tokenGarantia)) {
    try {
        $ordenIdentificacion = controladorOrdenes::ctrMostrarOrdenPorToken($tokenGarantia);
    } catch (Exception $e) {
        $ordenIdentificacion = null;
    }
}

$sesionBackendGarantia = isset($_SESSION["validarSesionBackend"]) && $_SESSION["validarSesionBackend"] === "ok";
$perfilGarantia = isset($_SESSION["perfil"]) ? (string) $_SESSION["perfil"] : "";
$perfilesInfoOrden = array("administrador", "vendedor", "tecnico", "secretaria");
$idOrdenQr = is_array($garantia) ? intval($garantia["id_orden"]) : (is_array($ordenIdentificacion) ? intval($ordenIdentificacion["id"]) : 0);
if ($idOrdenQr > 0 && $sesionBackendGarantia && in_array($perfilGarantia, $perfilesInfoOrden, true)) {
    $destinoOrden = "index.php?ruta=infoOrden&idOrden=" . $idOrdenQr;
    echo '<script>window.location.replace(' . json_encode($destinoOrden) . ');</script>';
    return;
}

$empresaGarantia = is_array($garantia) && !empty($garantia["id_empresa"]) ? intval($garantia["id_empresa"]) : (is_array($ordenIdentificacion) && !empty($ordenIdentificacion["id_empresa"]) ? intval($ordenIdentificacion["id_empresa"]) : 1);
$configGarantia = ModeloEtiquetas::mdlObtenerConfiguracion($empresaGarantia);
$sitioGarantiaUrl = (preg_match('#^https?://#i', $configGarantia["sitio_web"]) && filter_var($configGarantia["sitio_web"], FILTER_VALIDATE_URL) !== false)
    ? $configGarantia["sitio_web"] : "https://comercializadoraegs.com";
$estadoGarantia = "invalida";
$tituloGarantia = "Garantía no encontrada";
$detalleGarantia = "El código no existe o el enlace está incompleto.";
$iconoGarantia = "fa-circle-xmark";
$diasGarantia = null;

if (is_array($garantia)) {
    $hoyGarantia = new DateTime("today");
    $inicioGarantia = new DateTime($garantia["fecha_entrega"]);
    $finGarantia = new DateTime($garantia["fecha_vencimiento"]);
    if ($hoyGarantia < $inicioGarantia) {
        $estadoGarantia = "programada";
        $tituloGarantia = "La garantía aún no inicia";
        $detalleGarantia = "La cobertura comenzará en la fecha de entrega indicada.";
        $iconoGarantia = "fa-clock";
    } elseif ($hoyGarantia <= $finGarantia) {
        $estadoGarantia = "vigente";
        $tituloGarantia = "Garantía vigente";
        $diasGarantia = intval($hoyGarantia->diff($finGarantia)->format("%a"));
        $detalleGarantia = $diasGarantia === 0 ? "La cobertura vence hoy." : "Quedan " . $diasGarantia . " días de cobertura.";
        $iconoGarantia = "fa-circle-check";
    } else {
        $estadoGarantia = "vencida";
        $tituloGarantia = "Garantía vencida";
        $diasGarantia = intval($finGarantia->diff($hoyGarantia)->format("%a"));
        $detalleGarantia = "La cobertura terminó hace " . $diasGarantia . " días.";
        $iconoGarantia = "fa-calendar-xmark";
    }
} elseif (is_array($ordenIdentificacion)) {
    $estadoGarantia = "identificada";
    $tituloGarantia = "Equipo identificado";
    $detalleGarantia = "La orden está registrada en EGS. Su garantía se activará al momento de la entrega.";
    $iconoGarantia = "fa-qrcode";
}

function egsGarantiaH($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8"); }
function egsGarantiaFecha($valor) {
    if (!$valor) return "—";
    $fecha = DateTime::createFromFormat("Y-m-d", substr((string) $valor, 0, 10));
    return $fecha ? $fecha->format("d/m/Y") : $valor;
}
?>

<main class="egs-warranty-public">
  <section class="egs-warranty-card">
    <header class="egs-warranty-brand">
      <img src="vistas/img/plantilla/logo-etiquetas.svg" alt="EGS">
      <div><b><?= egsGarantiaH($configGarantia["nombre_comercial"]) ?></b><small>Validación oficial EGS</small></div>
    </header>

    <div class="egs-warranty-status <?= egsGarantiaH($estadoGarantia) ?>">
      <span class="egs-status-icon"><i class="fa-solid <?= egsGarantiaH($iconoGarantia) ?>"></i></span>
      <div><span><?= is_array($ordenIdentificacion) ? "IDENTIFICACIÓN DEL EQUIPO" : "ESTADO DE COBERTURA" ?></span><h1><?= egsGarantiaH($tituloGarantia) ?></h1><p><?= egsGarantiaH($detalleGarantia) ?></p></div>
    </div>

    <?php if (is_array($garantia)): ?>
      <div class="egs-warranty-order-head"><span>ORDEN DE SERVICIO</span><strong>#<?= intval($garantia["id_orden"]) ?></strong></div>
      <div class="egs-warranty-grid">
        <div><small>Cliente</small><b><?= egsGarantiaH($garantia["nombre_cliente"] !== "" ? $garantia["nombre_cliente"] : "No especificado") ?></b></div>
        <div><small>Clave del cliente</small><b><?= egsGarantiaH($garantia["clave_cliente"] !== "" ? $garantia["clave_cliente"] : "—") ?></b></div>
        <div class="wide"><small>Equipo</small><b><?= egsGarantiaH($garantia["equipo"] !== "" ? $garantia["equipo"] : trim($garantia["marca_actual"] . " " . $garantia["modelo_actual"])) ?></b></div>
        <div class="wide"><small>Número de serie</small><b><?= egsGarantiaH($garantia["numero_serie"] !== "" ? $garantia["numero_serie"] : $garantia["serie_actual"]) ?></b></div>
        <div><small>Fecha de entrega</small><b><?= egsGarantiaFecha($garantia["fecha_entrega"]) ?></b></div>
        <div><small>Vencimiento</small><b><?= egsGarantiaFecha($garantia["fecha_vencimiento"]) ?></b></div>
        <div><small>Técnico</small><b><?= egsGarantiaH($garantia["tecnico"] !== "" ? $garantia["tecnico"] : "—") ?></b></div>
        <div class="wide"><small>Estado actual de la orden</small><b><?= egsGarantiaH($garantia["estado_orden"] !== "" ? $garantia["estado_orden"] : "—") ?></b></div>
        <?php if (!empty($garantia["proximo_servicio"])): ?><div class="wide service"><small>Próximo servicio recomendado</small><b><?= egsGarantiaFecha($garantia["proximo_servicio"]) ?></b></div><?php endif; ?>
      </div>
      <p class="egs-warranty-updated"><i class="fa-solid fa-shield-halved"></i> Información validada directamente en el sistema EGS.</p>
      <?php if (!$sesionBackendGarantia): ?>
        <div class="egs-warranty-login">
          <a href="index.php?ruta=ingreso&amp;orden=<?= intval($garantia["id_orden"]) ?>"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión y abrir orden</a>
          <small>Acceso para técnicos y personal autorizado.</small>
        </div>
      <?php endif; ?>
    <?php elseif (is_array($ordenIdentificacion)): ?>
      <div class="egs-warranty-order-head"><span>ORDEN DE SERVICIO</span><strong>#<?= intval($ordenIdentificacion["id"]) ?></strong></div>
      <div class="egs-warranty-grid">
        <div class="wide"><small>Equipo</small><b><?= egsGarantiaH(trim((string) $ordenIdentificacion["marcaDelEquipo"] . " " . (string) $ordenIdentificacion["modeloDelEquipo"]) !== "" ? trim((string) $ordenIdentificacion["marcaDelEquipo"] . " " . (string) $ordenIdentificacion["modeloDelEquipo"]) : "No especificado") ?></b></div>
        <div class="wide"><small>Número de serie</small><b><?= egsGarantiaH(trim((string) $ordenIdentificacion["numeroDeSerieDelEquipo"]) !== "" ? $ordenIdentificacion["numeroDeSerieDelEquipo"] : "—") ?></b></div>
        <div><small>Fecha de ingreso</small><b><?= egsGarantiaH(egsGarantiaFecha($ordenIdentificacion["fecha_ingreso"])) ?></b></div>
        <div><small>Estado actual</small><b><?= egsGarantiaH(trim((string) $ordenIdentificacion["estado"]) !== "" ? $ordenIdentificacion["estado"] : "En revisión") ?></b></div>
      </div>
      <p class="egs-warranty-updated"><i class="fa-solid fa-tag"></i> Esta etiqueta identifica el equipo dentro del proceso de servicio.</p>
      <?php if (!$sesionBackendGarantia): ?>
        <div class="egs-warranty-login">
          <a href="index.php?ruta=ingreso&amp;orden=<?= intval($ordenIdentificacion["id"]) ?>"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión y abrir orden</a>
          <small>Acceso para técnicos y personal autorizado.</small>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="egs-invalid-help"><p>Comprueba que el enlace del QR esté completo. Si el problema continúa, comunícate con nosotros.</p></div>
    <?php endif; ?>

    <footer>
      <b>¿Necesitas ayuda?</b>
      <p><?= egsGarantiaH($configGarantia["whatsapp"]) ?> · <?= egsGarantiaH($configGarantia["telefono_1"]) ?></p>
      <a href="<?= egsGarantiaH($sitioGarantiaUrl) ?>" rel="noopener">Visitar sitio web</a>
    </footer>
  </section>
</main>

<style>
html,body{min-height:100%;margin:0;background:#f1f5f9}.egs-warranty-public{min-height:100vh;padding:22px 14px;box-sizing:border-box;font-family:'Source Sans Pro',Arial,sans-serif;color:#0f172a}.egs-warranty-card{max-width:520px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,.10)}.egs-warranty-brand{display:flex;align-items:center;gap:14px;padding:20px 22px;border-bottom:1px solid #edf2f7}.egs-warranty-brand img{width:94px;height:34px;object-fit:contain}.egs-warranty-brand b,.egs-warranty-brand small{display:block}.egs-warranty-brand b{font-size:14px}.egs-warranty-brand small{font-size:11px;color:#64748b;margin-top:2px}.egs-warranty-status{display:flex;align-items:center;gap:16px;padding:22px;color:#fff}.egs-warranty-status.vigente{background:linear-gradient(125deg,#15803d,#22c55e)}.egs-warranty-status.vencida,.egs-warranty-status.invalida{background:linear-gradient(125deg,#991b1b,#dc2626)}.egs-warranty-status.programada{background:linear-gradient(125deg,#1d4ed8,#3b82f6)}.egs-status-icon{width:54px;height:54px;border-radius:50%;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:27px;flex:none}.egs-warranty-status span{font-size:10px;font-weight:700;letter-spacing:.1em;opacity:.85}.egs-warranty-status h1{font-size:23px;line-height:1.1;margin:3px 0 4px;font-weight:800}.egs-warranty-status p{font-size:13px;margin:0;opacity:.9}.egs-warranty-order-head{display:flex;justify-content:space-between;align-items:end;padding:20px 22px 10px}.egs-warranty-order-head span{font-size:10px;color:#64748b;font-weight:700;letter-spacing:.08em}.egs-warranty-order-head strong{font-size:22px;color:#166534}.egs-warranty-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#e2e8f0;margin:0 22px;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}.egs-warranty-grid>div{background:#fff;padding:11px 12px;min-width:0}.egs-warranty-grid .wide{grid-column:1/-1}.egs-warranty-grid .service{background:#f0fdf4}.egs-warranty-grid small,.egs-warranty-grid b{display:block}.egs-warranty-grid small{font-size:10px;color:#64748b;margin-bottom:3px}.egs-warranty-grid b{font-size:13px;overflow-wrap:anywhere}.egs-warranty-updated{margin:14px 22px 10px;font-size:11px;color:#64748b;text-align:center}.egs-warranty-updated i{color:#16a34a;margin-right:4px}.egs-warranty-login{margin:0 22px 20px;text-align:center}.egs-warranty-login a{display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 14px;border-radius:10px;background:#166534;color:#fff;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(22,101,52,.18)}.egs-warranty-login a:hover{background:#14532d;color:#fff}.egs-warranty-login small{display:block;margin-top:6px;font-size:10px;color:#64748b}.egs-invalid-help{padding:24px 28px;text-align:center;color:#64748b}.egs-warranty-card footer{text-align:center;background:#f8fafc;border-top:1px solid #e2e8f0;padding:17px 20px}.egs-warranty-card footer b{font-size:12px}.egs-warranty-card footer p{font-size:12px;margin:4px 0;color:#475569}.egs-warranty-card footer a{font-size:11px;color:#15803d;font-weight:700;text-decoration:none}@media(max-width:420px){.egs-warranty-public{padding:0}.egs-warranty-card{border-radius:0;min-height:100vh;border:0}.egs-warranty-grid{margin:0 14px}.egs-warranty-order-head{padding-left:16px;padding-right:16px}.egs-warranty-status{padding:20px 16px}.egs-warranty-status h1{font-size:21px}.egs-warranty-brand{padding:17px 16px}}
.egs-warranty-status.identificada{background:linear-gradient(125deg,#15803d,#22c55e)}
</style>
