<?php
$cfg = array();
$site = 'https://comercializadoraegs.com/';
if (class_exists('ControladorAnalytics')) {
	try {
		$cfg = ControladorAnalytics::ctrObtenerConfig();
		if (isset($cfg['site_url']) && $cfg['site_url'] !== '') {
			$site = $cfg['site_url'];
		}
	} catch (Exception $e) {}
}
?>
<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-bolt"></i> Acciones rápidas</h4>
  </div>
  <div class="crm-card-body">
    <div class="visitas-quick-grid">
      <a href="<?php echo htmlspecialchars($site); ?>" target="_blank" rel="noopener" class="visitas-quick">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#6366f1,#818cf8)"><i class="fa-solid fa-store"></i></span>
        <span class="visitas-quick-text"><strong>Abrir tienda pública</strong><small>comercializadoraegs.com</small></span>
      </a>
      <a href="index.php?ruta=productos" class="visitas-quick">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#22c55e,#4ade80)"><i class="fa-solid fa-box"></i></span>
        <span class="visitas-quick-text"><strong>Gestionar productos</strong><small>Catálogo y precios</small></span>
      </a>
      <a href="index.php?ruta=pedidos" class="visitas-quick">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)"><i class="fa-solid fa-truck"></i></span>
        <span class="visitas-quick-text"><strong>Pedidos proveedor</strong><small>Reposición e inventario</small></span>
      </a>
      <a href="index.php?ruta=ordenes" class="visitas-quick">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)"><i class="fa-solid fa-screwdriver-wrench"></i></span>
        <span class="visitas-quick-text"><strong>Órdenes de servicio</strong><small>Reparación y seguimiento</small></span>
      </a>
      <a href="https://comercializadoraegs.com/facturacion" target="_blank" rel="noopener" class="visitas-quick">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)"><i class="fa-solid fa-file-invoice"></i></span>
        <span class="visitas-quick-text"><strong>Facturación web</strong><small>Portal de clientes</small></span>
      </a>
      <a href="#visitas-config" class="visitas-quick visitas-tab-link" data-visitas-tab="config">
        <span class="visitas-quick-icon" style="background:linear-gradient(135deg,#64748b,#94a3b8)"><i class="fa-solid fa-chart-pie"></i></span>
        <span class="visitas-quick-text"><strong>Configurar analítica</strong><small>GA4, Clarity y tracking</small></span>
      </a>
    </div>
  </div>
</div>
