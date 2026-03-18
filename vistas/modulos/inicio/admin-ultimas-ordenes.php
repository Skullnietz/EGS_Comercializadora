<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Ultimas ordenes (estilo CRM)
    ═══════════════════════════════════════════════════ */
$_admOrd = controladorOrdenes::ctrlTraerOrdenesConTope(0, 10);

function _admEstadoBadge($estado) {
    $e = strtolower(trim($estado));
    if (strpos($e, 'entregado') !== false) return array('#f0fdf4','#16a34a','fa-circle-check');
    if (strpos($e, 'terminada') !== false) return array('#fef3c7','#92400e','fa-flag-checkered');
    if (strpos($e, 'aceptado') !== false)  return array('#dbeafe','#1d4ed8','fa-circle-check');
    if (strpos($e, 'revision') !== false || strpos($e, 'revisión') !== false) return array('#fef2f2','#dc2626','fa-magnifying-glass');
    if (strpos($e, 'aut') !== false)       return array('#f5f3ff','#7c3aed','fa-hourglass-half');
    return array('#f1f5f9','#475569','fa-circle-question');
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
                  <?php if (!empty($img)): ?>
                    <img src="<?php echo htmlspecialchars($img); ?>"
                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
                         style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0">
                    <div style="display:none;width:36px;height:36px;border-radius:6px;background:#f1f5f9;align-items:center;justify-content:center;color:#cbd5e1;font-size:13px">
                      <i class="fa-solid fa-laptop"></i>
                    </div>
                  <?php else: ?>
                    <div style="width:36px;height:36px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:13px">
                      <i class="fa-solid fa-laptop"></i>
                    </div>
                  <?php endif; ?>
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
