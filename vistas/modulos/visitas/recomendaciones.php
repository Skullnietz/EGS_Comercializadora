<?php
/** @var array $insights */
$recs = isset($insights['recomendaciones']) ? $insights['recomendaciones'] : array();
?>
<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-lightbulb"></i> Recomendaciones para tu negocio</h4>
    <span style="font-size:12px;color:var(--crm-muted)"><?php echo count($recs); ?> acciones sugeridas</span>
  </div>
  <div class="crm-card-body" style="padding:16px 18px">
    <?php if (empty($recs)): ?>
      <p style="color:var(--crm-muted);margin:0">No hay recomendaciones en este momento.</p>
    <?php else: ?>
      <?php foreach ($recs as $rec):
        $pri = isset($rec['prioridad']) ? $rec['prioridad'] : 'baja';
        $icon = isset($rec['icono']) ? $rec['icono'] : 'fa-circle-info';
        $url = isset($rec['accion_url']) ? $rec['accion_url'] : '#';
        $ext = (strpos($url, 'http') === 0);
      ?>
      <div class="visitas-rec visitas-rec-<?php echo htmlspecialchars($pri); ?>">
        <div class="visitas-rec-icon"><i class="fa-solid <?php echo htmlspecialchars($icon); ?>"></i></div>
        <div class="visitas-rec-body">
          <span class="visitas-rec-badge visitas-badge-<?php echo htmlspecialchars($pri); ?>"><?php echo htmlspecialchars($pri); ?></span>
          <h4 class="visitas-rec-title"><?php echo htmlspecialchars($rec['titulo']); ?></h4>
          <p class="visitas-rec-msg"><?php echo htmlspecialchars($rec['mensaje']); ?></p>
          <a href="<?php echo htmlspecialchars($url); ?>" class="crm-quick crm-quick-primary"<?php echo $ext ? ' target="_blank" rel="noopener"' : ''; ?>>
            <span class="crm-quick-icon" style="background:rgba(255,255,255,.25)"><i class="fa-solid fa-arrow-right"></i></span>
            <?php echo htmlspecialchars($rec['accion_label']); ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
