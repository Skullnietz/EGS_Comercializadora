<?php
$cfg = array();
$trackUrl = 'https://backend.comercializadoraegs.com/extensiones/tracking/registrar-visita.php';
$gaActivo = !empty($panorama['ga4']['activo']);
$ga = isset($panorama['ga4']['metricas']) ? $panorama['ga4']['metricas'] : array();

if (!class_exists('ControladorAnalytics', false)) {
	$analyticsCtrl = __DIR__ . '/../../../controladores/analytics.controlador.php';
	if (is_file($analyticsCtrl)) {
		require_once $analyticsCtrl;
	}
}
if (class_exists('ControladorAnalytics')) {
	try {
		$cfg = ControladorAnalytics::ctrObtenerConfig();
		if (!empty($cfg['tracking_endpoint'])) {
			$trackUrl = $cfg['tracking_endpoint'];
		}
	} catch (Exception $e) {}
}

$docsPath = __DIR__ . '/../../../docs/ANALITICA-WEB.md';
$docsExiste = is_file($docsPath);
?>
<div class="crm-card">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-sliders"></i> Configuración de analítica</h4>
  </div>
  <div class="crm-card-body">
    <p style="font-size:13px;color:var(--crm-text2);line-height:1.55">
      Instala el tracking en <strong>comercializadoraegs.com</strong> para alimentar este panel.
      <?php if ($docsExiste): ?>
      Guía del proyecto: <code>docs/ANALITICA-WEB.md</code>
      <?php endif; ?>
    </p>

    <div class="row" style="margin-top:16px">
      <div class="col-md-6">
        <h5 style="font-size:13px;font-weight:700">Estado GA4 (backend)</h5>
        <?php if ($gaActivo): ?>
          <p style="color:#16a34a"><i class="fa-solid fa-circle-check"></i> Conectado — sesiones 30d: <?php echo intval(isset($ga['sesiones']) ? $ga['sesiones'] : 0); ?></p>
        <?php else: ?>
          <p style="color:#d97706"><i class="fa-solid fa-triangle-exclamation"></i> No configurado.</p>
          <ol style="font-size:12px;color:var(--crm-text2);padding-left:18px;margin:8px 0 0">
            <li>Copia <code>config/analytics.example.php</code> → <code>config/analytics.local.php</code></li>
            <li>Indica <code>ga4_property_id</code> y ruta al JSON del service account</li>
            <li>Recarga esta pestaña</li>
          </ol>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <h5 style="font-size:13px;font-weight:700">Tracking propio EGS</h5>
        <p style="font-size:12px;color:var(--crm-muted);margin-bottom:6px">Endpoint (pegar en el sitio público):</p>
        <pre class="visitas-config-pre"><?php echo htmlspecialchars($trackUrl); ?></pre>
        <p style="font-size:12px;color:var(--crm-muted);margin-top:8px">
          <i class="fa-solid fa-circle-info"></i>
          Las tablas <code>visitasPersonas</code> y <code>visitasPaises</code> se crean automáticamente al abrir este panel.
        </p>
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
  var u = '<?php echo htmlspecialchars($trackUrl, ENT_QUOTES, 'UTF-8'); ?>';
  try {
    fetch(u + '?pais=' + encodeURIComponent(navigator.language || ''), { mode: 'cors', credentials: 'omit' });
  } catch (e) {
    new Image().src = u + '?_=' + Date.now();
  }
})();
&lt;/script&gt;</pre>

    <h5 style="font-size:13px;font-weight:700;margin-top:16px">Microsoft Clarity (opcional)</h5>
    <p style="font-size:12px;color:var(--crm-text2)">
      Registra un proyecto en <a href="https://clarity.microsoft.com/" target="_blank" rel="noopener">clarity.microsoft.com</a>
      y pega el script en el <code>&lt;head&gt;</code> de la tienda para mapas de calor y grabaciones.
    </p>
  </div>
</div>
