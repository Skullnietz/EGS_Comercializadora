<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Ultimas ordenes (estilo CRM)
    ═══════════════════════════════════════════════════ */
$_admOrd = controladorOrdenes::ctrlTraerOrdenesConTope(0, 10);

function _admEstadoBadge($estado) {
    $e = strtolower(trim($estado));
    // Autorización / Pendiente — antes de entregado
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

function _admGetImgOrd($ord) {
    if (!empty($ord["multimedia"])) {
        $album = json_decode($ord["multimedia"], true);
        if (is_array($album)) {
            foreach ($album as $img) {
                if (isset($img["foto"]) && !empty($img["foto"])) return $img["foto"];
            }
        }
    }
    return "";
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-rectangle-list"></i> Ultimas Ordenes</h4>
    <a href="index.php?ruta=ordenes" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
      Ver todas <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
    </a>
  </div>
  <div class="crm-card-body-flush">
    <?php if (empty($_admOrd)): ?>
      <div class="crm-empty">
        <i class="fa-solid fa-inbox"></i>
        <strong>Sin ordenes registradas</strong>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="crm-table">
          <thead><tr>
            <th></th>
            <th>Orden</th>
            <th>Equipo / Titulo</th>
            <th>Estado</th>
            <th style="text-align:right">Total</th>
            <th>Ingreso</th>
          </tr></thead>
          <tbody>
            <?php foreach ($_admOrd as $o):
              $badge = _admEstadoBadge($o['estado']);
              $img = _admGetImgOrd($o);
              $titulo = isset($o['titulo']) ? $o['titulo'] : '';
              $marca = isset($o['marcaDelEquipo']) ? $o['marcaDelEquipo'] : '';
              $modelo = isset($o['modeloDelEquipo']) ? $o['modeloDelEquipo'] : '';
              $equipo = trim($marca . ' ' . $modelo);
              $display = !empty($equipo) ? $equipo : (!empty($titulo) ? $titulo : '—');
            ?>
              <tr>
                <td style="padding:6px 4px;width:40px">
                  <img src="<?php echo !empty($img) ? htmlspecialchars($img) : 'vistas/img/default/default.jpg'; ?>"
                       onerror="this.onerror=null;this.src='vistas/img/default/default.jpg'"
                       style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0" loading="lazy">
                </td>
                <td><span style="font-weight:700;color:#6366f1">#<?php echo htmlspecialchars($o['id']); ?></span></td>
                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  <?php echo htmlspecialchars($display); ?>
                </td>
                <td>
                  <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:<?php echo $badge[1]; ?>;background:<?php echo $badge[0]; ?>;padding:3px 10px;border-radius:12px">
                    <i class="fa-solid <?php echo $badge[2]; ?>" style="font-size:9px"></i>
                    <?php echo htmlspecialchars($o['estado']); ?>
                  </span>
                </td>
                <td style="text-align:right;font-weight:700;font-size:13px">$<?php echo number_format((float)($o['total']), 0); ?></td>
                <td style="font-size:12px;color:#64748b"><?php echo htmlspecialchars(substr($o['fecha_ingreso'], 0, 10)); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
