<?php
/* =====================================================
   ÚLTIMAS ÓRDENES — Dashboard Admin
   ===================================================== */
$_ultimasOrd = controladorOrdenes::ctrlTraerOrdenesConTope(0, 8);

// Mapa estandarizado de estado → [bg, color, icon]
function _estadoBadge($estado) {
    $e = strtolower(trim($estado));
    // Autorización / Pendiente
    if (strpos($e, 'autorización') !== false || strpos($e, 'autorizacion') !== false || $e === 'aut') return array('#fffbeb','#f59e0b','fa-hourglass-half');
    if (strpos($e, 'pendiente') !== false) return array('#fffbeb','#f59e0b','fa-hourglass-half');
    // Supervisión
    if (strpos($e, 'supervisión') !== false || strpos($e, 'supervision') !== false || $e === 'sup') return array('#f5f3ff','#8b5cf6','fa-eye');
    // Garantías
    if (strpos($e, 'garantía aceptada') !== false || strpos($e, 'garantia aceptada') !== false || $e === 'ga') return array('#fef2f2','#dc2626','fa-rotate-left');
    if (strpos($e, 'probable garantía') !== false || strpos($e, 'probable garantia') !== false) return array('#fef2f2','#dc2626','fa-triangle-exclamation');
    if (strpos($e, 'garantía') !== false || strpos($e, 'garantia') !== false) return array('#fef2f2','#dc2626','fa-rotate-left');
    // Revisión
    if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || $e === 'rev') return array('#fef2f2','#ef4444','fa-magnifying-glass');
    // Terminada
    if (strpos($e, 'terminada') !== false || $e === 'ter') return array('#ecfeff','#06b6d4','fa-flag-checkered');
    // Entregados
    if (strpos($e, 'entregado al asesor') !== false) return array('#ecfdf5','#10b981','fa-user-check');
    if (strpos($e, 'entregado/pagado') !== false) return array('#f0fdf4','#22c55e','fa-money-check-dollar');
    if (strpos($e, 'entregado/credito') !== false || strpos($e, 'entregado/crédito') !== false) return array('#f0fdf4','#22c55e','fa-credit-card');
    if (strpos($e, 'entregado') !== false || strpos($e, 'entregada') !== false) return array('#f0fdf4','#22c55e','fa-handshake');
    // Aceptado
    if (strpos($e, 'aceptado') !== false || strpos($e, 'aceptada') !== false || $e === 'ok') return array('#eff6ff','#3b82f6','fa-circle-check');
    // Cancelada
    if (strpos($e, 'cancel') !== false) return array('#f1f5f9','#64748b','fa-ban');
    // Sin reparación
    if (strpos($e, 'sin reparación') !== false || strpos($e, 'sin reparacion') !== false || $e === 'sr') return array('#f8fafc','#94a3b8','fa-xmark');
    // Producto para venta
    if (strpos($e, 'producto para venta') !== false || $e === 'pv') return array('#fff7ed','#f97316','fa-tags');
    // Producto en almacén
    if (strpos($e, 'producto en almac') !== false) return array('#fafaf9','#78716c','fa-warehouse');
    // Seguimiento
    if (strpos($e, 'seguimiento') !== false) return array('#f0f9ff','#0ea5e9','fa-chart-line');
    return array('#f1f5f9','#64748b','fa-circle-info');
}
?>

<!--=====================================
ÚLTIMAS ÓRDENES
======================================-->
<div class="box box-default">

  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa-solid fa-rectangle-list" style="margin-right:6px"></i>Últimas Órdenes</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse">
        <i class="fa-solid fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body no-padding">
    <div class="table-responsive">
      <table class="table table-condensed table-hover" style="margin:0">
        <thead>
          <tr>
            <th>#</th>
            <th>Título / Equipo</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Ingreso</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($_ultimasOrd)): ?>
            <tr><td colspan="6" class="text-center text-muted">Sin órdenes registradas.</td></tr>
          <?php else: ?>
            <?php foreach ($_ultimasOrd as $o): ?>
              <?php $badge = _estadoBadge($o['estado'] ?? ''); ?>
              <tr>
                <td><strong><?= htmlspecialchars($o['id']) ?></strong></td>
                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  <?= htmlspecialchars($o['titulo'] ?? '—') ?>
                </td>
                <td>
                  <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:<?= $badge[1] ?>;background:<?= $badge[0] ?>;padding:3px 10px;border-radius:12px">
                    <i class="fa-solid <?= $badge[2] ?>" style="font-size:9px"></i>
                    <?= htmlspecialchars($o['estado'] ?? '—') ?>
                  </span>
                </td>
                <td>$<?= number_format((float)($o['total'] ?? 0), 0) ?></td>
                <td><?= htmlspecialchars(substr($o['fecha_ingreso'] ?? '', 0, 10)) ?></td>
                <td>
                  <a href="index.php?ruta=ordenes" class="btn btn-xs btn-default" title="Ver órdenes">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="box-footer text-right">
    <a href="index.php?ruta=ordenes" class="btn btn-sm btn-primary">
      Ver todas las órdenes <i class="fa-solid fa-circle-arrow-right"></i>
    </a>
  </div>

</div>
