<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Acciones Rapidas + Resumen del dia
    ═══════════════════════════════════════════════════ */

// Reutilizar datos de admin-kpis si existen
$_aradm_ingresadasHoy = isset($_adm_ingresadasHoy) ? $_adm_ingresadasHoy : 0;
$_aradm_entregadasHoy = isset($_adm_entregadasHoy) ? $_adm_entregadasHoy : 0;
$_aradm_pendientes    = isset($_adm_pendientes) ? $_adm_pendientes : 0;
$_aradm_eficiencia    = isset($_adm_eficiencia) ? $_adm_eficiencia : 0;
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-rocket"></i> Acciones Rapidas</h4>
  </div>
  <div class="crm-card-body" style="padding:16px 18px">

    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px">
      <a href="index.php?ruta=ordenes" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
          <i class="fa-solid fa-plus"></i>
        </div>
        <div class="crm-quick-text">Nueva Orden<small>Crear orden de servicio</small></div>
      </a>
      <a href="index.php?ruta=reportes" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
          <i class="fa-solid fa-chart-bar"></i>
        </div>
        <div class="crm-quick-text">Reportes<small>Generar reportes y cortes</small></div>
      </a>
      <a href="index.php?ruta=clientes" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div class="crm-quick-text">Clientes<small>Gestionar cartera de clientes</small></div>
      </a>
      <a href="index.php?ruta=AgregarPedido" class="crm-quick">
        <div class="crm-quick-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
          <i class="fa-solid fa-box"></i>
        </div>
        <div class="crm-quick-text">Nuevo Pedido<small>Registrar pedido a proveedor</small></div>
      </a>
    </div>

    <!-- Resumen del dia -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px">
      <div style="font-size:12px;font-weight:700;color:#0f172a;margin-bottom:10px">
        <i class="fa-solid fa-bolt" style="color:#f59e0b;margin-right:4px"></i> Estado del Dia
      </div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block"></span> Ingresadas hoy
          </span>
          <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo $_aradm_ingresadasHoy; ?></span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block"></span> Entregadas hoy
          </span>
          <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo $_aradm_entregadasHoy; ?></span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block"></span> Pendientes del mes
          </span>
          <span style="font-size:13px;font-weight:700;color:#0f172a"><?php echo $_aradm_pendientes; ?></span>
        </div>
        <hr style="border:none;border-top:1px solid #e2e8f0;margin:4px 0">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:12px;font-weight:700;color:#0f172a">Eficiencia</span>
          <span style="font-size:15px;font-weight:800;color:<?php echo $_aradm_eficiencia >= 70 ? '#22c55e' : ($_aradm_eficiencia >= 40 ? '#f59e0b' : '#ef4444'); ?>"><?php echo $_aradm_eficiencia; ?>%</span>
        </div>
      </div>
    </div>

  </div>
</div>
