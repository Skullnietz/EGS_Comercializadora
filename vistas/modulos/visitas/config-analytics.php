<?php
$cfg = class_exists('ControladorAnalytics') ? ControladorAnalytics::ctrObtenerConfig() : array();
$gaActivo = !empty($panorama['ga4']['activo']);
$ga = isset($panorama['ga4']['metricas']) ? $panorama['ga4']['metricas'] : array();
$trackUrl = isset($cfg['tracking_endpoint']) ? $cfg['tracking_endpoint'] : '';
?>
<div class="crm-card">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-sliders"></i> Configuración de analítica</h4>
  </div>
  <div class="crm-card-body">
    <p style="font-size:13px;color:var(--crm-text2);line-height:1.55">
      Sigue la guía en <code>docs/ANALITICA-WEB.md</code> del proyecto para instalar GA4, Microsoft Clarity y el tracking propio en el sitio público.
    </p>

    <div class="row" style="margin-top:16px">
      <div class="col-md-6">
        <h5 style="font-size:13px;font-weight:700">Estado GA4 (backend)</h5>
        <?php if ($gaActivo): ?>
          <p style="color:#16a34a"><i class="fa-solid fa-circle-check"></i> Conectado — sesiones 30d: <?php echo intval(isset($ga['sesiones']) ? $ga['sesiones'] : 0); ?></p>
        <?php else: ?>
          <p style="color:#d97706"><i class="fa-solid fa-triangle-exclamation"></i> No configurado. Copia <code>config/analytics.example.php</code> a <code>config/analytics.local.php</code> con property ID y JSON del service account.</p>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <h5 style="font-size:13px;font-weight:700">Tracking propio</h5>
        <p style="font-size:12px;color:var(--crm-muted)">Endpoint:</p>
        <pre class="visitas-config-pre"><?php echo htmlspecialchars($trackUrl); ?></pre>
      </div>
    </div>

    <h5 style="font-size:13px;font-weight:700;margin-top:20px">Snippet GA4 (sitio público)</h5>
    <pre class="visitas-config-pre">&lt;script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"&gt;&lt;/script&gt;
&lt;script&gt;
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
&lt;/script&gt;</pre>

    <h5 style="font-size:13px;font-weight:700;margin-top:16px">Snippet tracking EGS</h5>
    <pre class="visitas-config-pre">&lt;script&gt;
(function () {
  var u = '<?php echo htmlspecialchars($trackUrl); ?>';
  fetch(u + '?pais=' + encodeURIComponent(navigator.language||''), {mode:'cors'});
})();
&lt;/script&gt;</pre>
  </div>
</div>
