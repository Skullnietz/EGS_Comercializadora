<?php
/*  ═══════════════════════════════════════════════════
    CRM — Pendientes de Autorización (seguimiento)
    ═══════════════════════════════════════════════════ */
require_once __DIR__ . "/../../../config/clienteBadges.helper.php";

$_pend_raw = isset($_crm_ordAUT) && is_array($_crm_ordAUT) ? $_crm_ordAUT : array();

if (!function_exists('_crmDiasDesde')) {
    function _crmDiasDesde($fecha) {
        if (empty($fecha)) return 0;
        try {
            $hoy = new DateTime();
            $ing = new DateTime($fecha);
            return max(0, $ing->diff($hoy)->days);
        } catch (Exception $e) { return 0; }
    }
}

// ── Filtrar: solo órdenes de los últimos 6 meses ──
$_pend_limite = strtotime("-6 months");
$_pend_ordenes = array_filter($_pend_raw, function($ord) use ($_pend_limite) {
    $fi = isset($ord["fecha_ingreso"]) ? strtotime($ord["fecha_ingreso"]) : 0;
    return $fi >= $_pend_limite;
});

// ── Ordenar: más recientes primero ──
usort($_pend_ordenes, function($a, $b) {
    return strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now")
         - strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now");
});

$_pend_numAUT = count($_pend_ordenes);

// ── Pre-cargar TODOS los clientes de las órdenes visibles en UNA sola query ──
$_pend_clienteIds = array();
foreach (array_slice($_pend_ordenes, 0, 12) as $ord) {
    if (!empty($ord["id_usuario"])) {
        $_pend_clienteIds[] = intval($ord["id_usuario"]);
    }
}
$_pend_clientesMap = array();
if (!empty($_pend_clienteIds)) {
    try {
        $_pend_clientesMap = ControladorClientes::ctrMostrarClientesPorIds($_pend_clienteIds);
    } catch (Exception $e) { $_pend_clientesMap = array(); }
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-phone-volume"></i> Contactar Clientes</h4>
    <?php if ($_pend_numAUT > 0): ?>
      <span class="crm-badge" style="background:#fef3c7;color:#92400e">
        <i class="fa-solid fa-bell" style="font-size:10px"></i>
        <?php echo $_pend_numAUT; ?> pendiente<?php echo $_pend_numAUT > 1 ? 's' : ''; ?>
      </span>
    <?php endif; ?>
  </div>
  <div class="crm-card-body-flush">

    <?php if (empty($_pend_ordenes)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-circle-check" style="color:#22c55e;opacity:.6"></i>
        <strong>Todo al día</strong>
        <span style="font-size:12px">No hay órdenes pendientes de autorización</span>
      </div>
    <?php else: ?>

      <div class="table-responsive">
        <table class="crm-table">
          <thead>
            <tr>
              <th>Orden</th>
              <th>Cliente</th>
              <th style="text-align:right">Total</th>
              <th style="text-align:center">Días</th>
              <th style="text-align:center">Urgencia</th>
              <th style="text-align:center"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($_pend_ordenes, 0, 12) as $ord):
              $dias = _crmDiasDesde(isset($ord["fecha_ingreso"]) ? $ord["fecha_ingreso"] : "");
              $total = isset($ord["total"]) ? floatval($ord["total"]) : 0;

              if ($dias >= 15)     { $uc='#ef4444'; $ul='Crítico'; }
              elseif ($dias >= 7)  { $uc='#f59e0b'; $ul='Alto'; }
              elseif ($dias >= 3)  { $uc='#3b82f6'; $ul='Medio'; }
              else                 { $uc='#22c55e'; $ul='Nuevo'; }

              $cliNom = "—";
              $cliTel1 = "";
              $cliTel2 = "";
              $_pendCliIdKey = intval(isset($ord["id_usuario"]) ? $ord["id_usuario"] : 0);
              if ($_pendCliIdKey > 0 && isset($_pend_clientesMap[$_pendCliIdKey])) {
                  $cd = $_pend_clientesMap[$_pendCliIdKey];
                  if (isset($cd["nombre"])) $cliNom = $cd["nombre"];
                  if (isset($cd["telefono"])) $cliTel1 = trim($cd["telefono"]);
                  if (isset($cd["telefonoDos"])) $cliTel2 = trim($cd["telefonoDos"]);
              }

              // Validar 10 dígitos puros
              $cliTel1Clean = preg_replace('/\D/', '', $cliTel1);
              $cliTel2Clean = preg_replace('/\D/', '', $cliTel2);
              $waTel1 = (strlen($cliTel1Clean) === 10) ? '52' . $cliTel1Clean : '';
              $waTel2 = (strlen($cliTel2Clean) === 10) ? '52' . $cliTel2Clean : '';
              // Evitar duplicados
              if ($waTel2 === $waTel1) $waTel2 = '';

              $waMsg = urlencode("Hola " . $cliNom . ", le contactamos de EGS respecto a su orden #" . $ord["id"] . ". ¿Podría indicarnos cómo desea proceder?");

              $link = 'index.php?ruta=infoOrden&idOrden='.$ord["id"]
                  .'&empresa='.(isset($ord["id_empresa"]) ? $ord["id_empresa"] : '')
                  .'&asesor='.(isset($ord["id_Asesor"]) ? $ord["id_Asesor"] : '')
                  .'&cliente='.(isset($ord["id_usuario"]) ? $ord["id_usuario"] : '')
                  .'&tecnico='.(isset($ord["id_tecnico"]) ? $ord["id_tecnico"] : '')
                  .'&tecnicodos='.(isset($ord["id_tecnicoDos"]) ? $ord["id_tecnicoDos"] : '')
                  .'&pedido='.(isset($ord["id_pedido"]) ? $ord["id_pedido"] : '');
            ?>
            <tr>
              <td>
                <span style="font-weight:700;color:var(--crm-accent)">#<?php echo $ord["id"]; ?></span>
              </td>
              <td>
                <div style="font-weight:600;color:var(--crm-text)">
                  <?php
                    $_pendCliId = isset($ord["id_usuario"]) ? intval($ord["id_usuario"]) : 0;
                    echo ClienteBadgesHelper::getInstance()->renderWithName($cliNom, $_pendCliId);
                  ?>
                </div>
                <div style="font-size:11px;color:var(--crm-muted)">
                  <?php echo isset($ord["fecha_ingreso"]) ? date("d M Y", strtotime($ord["fecha_ingreso"])) : "—"; ?>
                </div>
              </td>
              <td style="text-align:right;font-weight:700">$<?php echo number_format($total, 0); ?></td>
              <td style="text-align:center">
                <span style="font-weight:700;font-size:13px;color:<?php echo $uc; ?>"><?php echo $dias; ?>d</span>
              </td>
              <td style="text-align:center">
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:<?php echo $uc; ?>">
                  <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?php echo $uc; ?>"></span>
                  <?php echo $ul; ?>
                </span>
              </td>
              <td style="text-align:center">
                <div style="display:inline-flex;align-items:center;gap:4px;flex-wrap:nowrap">
                  <?php if (!empty($waTel1)): ?>
                    <a href="https://wa.me/<?php echo $waTel1; ?>?text=<?php echo $waMsg; ?>" target="_blank" title="WhatsApp <?php echo $cliTel1; ?>"
                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#25d366;color:#fff;font-size:13px;text-decoration:none;transition:background .15s,transform .15s"
                       onmouseover="this.style.background='#1da851';this.style.transform='translateY(-1px)'"
                       onmouseout="this.style.background='#25d366';this.style.transform='none'">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  <?php endif; ?>
                  <?php if (!empty($waTel2)): ?>
                    <a href="https://wa.me/<?php echo $waTel2; ?>?text=<?php echo $waMsg; ?>" target="_blank" title="WhatsApp 2: <?php echo $cliTel2; ?>"
                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#128c7e;color:#fff;font-size:13px;text-decoration:none;transition:background .15s,transform .15s"
                       onmouseover="this.style.background='#0d7a6e';this.style.transform='translateY(-1px)'"
                       onmouseout="this.style.background='#128c7e';this.style.transform='none'">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  <?php endif; ?>
                  <a href="<?php echo $link; ?>" target="_blank" title="Ver orden"
                     style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#6366f1;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"
                     onmouseover="this.style.background='#4f46e5';this.style.transform='translateY(-1px)'"
                     onmouseout="this.style.background='#6366f1';this.style.transform='none'">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if (count($_pend_ordenes) > 12): ?>
        <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
          <a href="index.php?ruta=ordenes" style="color:var(--crm-accent);font-size:12px;font-weight:600;text-decoration:none">
            Ver las <?php echo count($_pend_ordenes); ?> pendientes <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
          </a>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>
