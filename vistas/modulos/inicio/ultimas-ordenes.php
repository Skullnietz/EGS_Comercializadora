<?php
/* =====================================================
   ÚLTIMAS ÓRDENES — Dashboard Admin
   ===================================================== */
$_ultimasOrd = controladorOrdenes::ctrlTraerOrdenesConTope(0, 8);

// Mapa de estado → clase de etiqueta Bootstrap
function _estadoBadge($estado) {
    $e = strtolower(trim($estado));
    if (strpos($e, 'entregado') !== false) return 'success';
    if (strpos($e, 'proceso')   !== false) return 'primary';
    if (strpos($e, 'revision')  !== false || strpos($e, 'revisión') !== false) return 'info';
    if (strpos($e, 'espera')    !== false) return 'warning';
    if (strpos($e, 'cancel')    !== false) return 'danger';
    return 'default';
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
                  <span class="label label-<?= $badge ?>">
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
