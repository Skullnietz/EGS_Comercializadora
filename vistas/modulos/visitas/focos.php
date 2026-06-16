<?php
/** @var array $insights */
$focos = isset($insights['focos']) ? $insights['focos'] : array();
$labels = array(
  'conversion' => array('icon' => 'fa-filter', 'titulo' => 'Conversión'),
  'contenido'  => array('icon' => 'fa-pen', 'titulo' => 'Contenido y UX'),
  'geografia'  => array('icon' => 'fa-earth-americas', 'titulo' => 'Geografía y audiencia'),
  'catalogo'   => array('icon' => 'fa-box-open', 'titulo' => 'Catálogo y productos'),
);
?>
<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-list-check"></i> Focos de mejora en la web</h4>
  </div>
  <div class="crm-card-body">
    <div class="row">
      <?php foreach ($labels as $key => $meta):
        $items = isset($focos[$key]) ? $focos[$key] : array();
      ?>
      <div class="col-md-6" style="margin-bottom:12px">
        <div class="visitas-foco-group">
          <h5><i class="fa-solid <?php echo $meta['icon']; ?>" style="margin-right:6px;color:var(--crm-accent)"></i><?php echo $meta['titulo']; ?></h5>
          <?php if (empty($items)): ?>
            <p style="font-size:12px;color:var(--crm-muted);margin:0">Sin pendientes detectados.</p>
          <?php else: ?>
            <ul>
              <?php foreach ($items as $item): ?>
                <li><?php echo htmlspecialchars($item); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
