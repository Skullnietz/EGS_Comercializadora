<!--=====================================
PÁGINA DE INICIO
======================================-->
<style>
/* ── Separadores de sección del Dashboard ── */
.dash-section {
  display: flex;
  align-items: center;
  margin: 24px 0 12px;
  padding: 0 15px;
}
.dash-section-bar {
  width: 4px;
  height: 34px;
  border-radius: 3px;
  margin-right: 12px;
  flex-shrink: 0;
}
.dash-section-text h4 {
  margin: 0 0 1px;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: #444;
}
.dash-section-text small {
  color: #aaa;
  font-size: 11px;
  font-weight: 400;
}
.dash-divider {
  border: none;
  border-top: 1px solid #ecf0f1;
  margin: 0 15px 16px;
}
/* ── Iguala alturas en la misma fila ── */
.dash-row-equal [class*="col-"] {
  display: flex;
  flex-direction: column;
}
.dash-row-equal .box {
  flex: 1;
}
</style>

<!-- content-wrapper -->
<div class="content-wrapper">

  <!-- content-header -->
  <section class="content-header">
    <h1>Tablero <small>Panel de Control</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
      <li class="active">Tablero</li>
    </ol>
  </section>

  <!-- content -->
  <section class="content">

    <?php if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "tecnico"): ?>
      <!-- ══════════════════════════════════════════════════════
           CRM DESIGN SYSTEM — Compartido entre Admin, Vendedor y Técnico
      ══════════════════════════════════════════════════════ -->
      <style>
      /* ─── Tokens ─── */
      :root {
        --crm-bg:       #f8fafc;
        --crm-surface:  #ffffff;
        --crm-border:   #e2e8f0;
        --crm-text:     #0f172a;
        --crm-text2:    #475569;
        --crm-muted:    #94a3b8;
        --crm-accent:   #6366f1;
        --crm-accent2:  #818cf8;
        --crm-radius:   14px;
        --crm-radius-sm:10px;
        --crm-shadow:   0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
        --crm-shadow-lg:0 4px 24px rgba(15,23,42,.10);
        --crm-ease:     cubic-bezier(.4,0,.2,1);
      }

      /* ─── Card base ─── */
      .crm-card {
        background: var(--crm-surface);
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius);
        box-shadow: var(--crm-shadow);
        overflow: hidden;
        transition: box-shadow .2s var(--crm-ease), transform .2s var(--crm-ease);
      }
      .crm-card:hover {
        box-shadow: var(--crm-shadow-lg);
      }
      .crm-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px 14px;
        border-bottom: 1px solid #f1f5f9;
      }
      .crm-card-title {
        display: flex; align-items: center; gap: 10px;
        font-size: 14px; font-weight: 700; color: var(--crm-text);
        margin: 0; line-height: 1.3;
      }
      .crm-card-title i {
        font-size: 15px; color: var(--crm-accent); opacity: .85;
      }
      .crm-card-body { padding: 20px 22px; }
      .crm-card-body-flush { padding: 0; }

      /* ─── Badge ─── */
      .crm-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; line-height: 1.4;
      }

      /* ─── Section header ─── */
      .crm-section {
        display: flex; align-items: center; gap: 14px;
        margin: 28px 0 16px; padding: 0 4px;
      }
      .crm-section-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: #fff; flex-shrink: 0;
      }
      .crm-section h3 {
        margin: 0; font-size: 15px; font-weight: 800;
        color: var(--crm-text); letter-spacing: -.01em;
      }
      .crm-section p {
        margin: 2px 0 0; font-size: 12px; color: var(--crm-muted); font-weight: 400;
      }

      /* ─── KPI Cards ─── */
      .crm-kpi {
        border-radius: var(--crm-radius);
        padding: 22px 20px 18px;
        position: relative; overflow: hidden;
        color: #fff; transition: transform .2s var(--crm-ease), box-shadow .2s var(--crm-ease);
      }
      .crm-kpi:hover { transform: translateY(-3px); box-shadow: var(--crm-shadow-lg); }
      .crm-kpi-icon {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        font-size: 48px; opacity: .12;
      }
      .crm-kpi-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .5px; opacity: .85; margin-bottom: 6px;
      }
      .crm-kpi-value {
        font-size: 28px; font-weight: 800; line-height: 1.1; margin-bottom: 6px;
        letter-spacing: -.02em;
      }
      .crm-kpi-sub {
        font-size: 12px; opacity: .75; font-weight: 500;
        display: flex; align-items: center; gap: 4px;
      }
      .crm-kpi-bar {
        height: 4px; background: rgba(255,255,255,.2);
        border-radius: 2px; margin-top: 12px; overflow: hidden;
      }
      .crm-kpi-bar span {
        display: block; height: 100%; border-radius: 2px;
        background: rgba(255,255,255,.65); transition: width .6s var(--crm-ease);
      }

      /* ─── Pipeline ─── */
      .crm-pipe-track {
        display: flex; gap: 4px; border-radius: 10px;
        overflow: hidden; height: 10px; background: #f1f5f9;
        margin-bottom: 20px;
      }
      .crm-pipe-track div {
        height: 100%; transition: width .5s var(--crm-ease);
        min-width: 4px;
      }
      .crm-pipe-stages {
        display: grid; grid-template-columns: repeat(5,1fr); gap: 10px;
      }
      .crm-pipe-stage {
        text-align: center; padding: 16px 8px; border-radius: var(--crm-radius-sm);
        background: var(--crm-bg); border: 1px solid var(--crm-border);
        transition: transform .18s var(--crm-ease), box-shadow .18s var(--crm-ease);
        cursor: default;
      }
      .crm-pipe-stage:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
      }
      .crm-pipe-stage-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 16px; color: #fff; margin-bottom: 8px;
      }
      .crm-pipe-stage-num {
        font-size: 24px; font-weight: 800; color: var(--crm-text);
        line-height: 1.2; margin-bottom: 2px;
      }
      .crm-pipe-stage-lbl {
        font-size: 11px; font-weight: 600; color: var(--crm-muted);
        text-transform: uppercase; letter-spacing: .3px;
      }

      /* ─── Table ─── */
      .crm-table { width: 100%; border-collapse: separate; border-spacing: 0; }
      .crm-table thead th {
        padding: 10px 16px; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .6px;
        color: var(--crm-muted); background: #f8fafc;
        border-bottom: 1px solid var(--crm-border);
      }
      .crm-table tbody tr {
        transition: background .12s;
      }
      .crm-table tbody tr:hover { background: #f8fafc; }
      .crm-table tbody td {
        padding: 12px 16px; font-size: 13px; color: var(--crm-text);
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
      }
      .crm-table tbody tr:last-child td { border-bottom: none; }

      /* ─── Urgency dot ─── */
      .crm-urg {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 700;
      }
      .crm-urg::before {
        content: ''; width: 8px; height: 8px; border-radius: 50%;
        flex-shrink: 0; animation: crm-pulse 2s infinite;
      }
      @keyframes crm-pulse {
        0%,100% { opacity: 1; }
        50%     { opacity: .4; }
      }

      /* ─── Quick actions ─── */
      .crm-quick {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px; border-radius: var(--crm-radius-sm);
        border: 1px solid var(--crm-border); text-decoration: none;
        transition: all .18s var(--crm-ease); background: var(--crm-surface);
      }
      .crm-quick:hover {
        border-color: var(--crm-accent); background: #eef2ff;
        transform: translateY(-1px); box-shadow: 0 2px 8px rgba(99,102,241,.1);
        text-decoration: none;
      }
      .crm-quick-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: #fff; flex-shrink: 0;
      }
      .crm-quick-text { font-size: 13px; font-weight: 600; color: var(--crm-text); }
      .crm-quick-text small { display: block; font-size: 11px; font-weight: 400; color: var(--crm-muted); }

      /* ─── Client row ─── */
      .crm-client {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 16px; border-bottom: 1px solid #f8fafc;
        transition: background .12s; text-decoration: none;
      }
      .crm-client:hover { background: #f8fafc; text-decoration: none; }
      .crm-client-av {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,.12);
      }
      .crm-client-name {
        font-size: 13px; font-weight: 600; color: var(--crm-text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      }
      .crm-client-info {
        font-size: 11px; color: var(--crm-muted);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      }

      /* ─── Cotizacion row ─── */
      .crm-cot-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 18px; border-bottom: 1px solid #f8fafc;
        transition: background .12s;
      }
      .crm-cot-row:hover { background: #f8fafc; }

      /* ─── Progress ring ─── */
      .crm-ring {
        width: 54px; height: 54px; position: relative;
        flex-shrink: 0;
      }
      .crm-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
      .crm-ring-track { fill: none; stroke: #e2e8f0; stroke-width: 4; }
      .crm-ring-fill  { fill: none; stroke-width: 4; stroke-linecap: round;
                         transition: stroke-dashoffset .8s var(--crm-ease); }
      .crm-ring-label {
        position: absolute; inset: 0; display: flex;
        align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: var(--crm-text);
      }

      /* ─── Empty state ─── */
      .crm-empty {
        text-align: center; padding: 36px 20px; color: var(--crm-muted);
      }
      .crm-empty i { font-size: 36px; margin-bottom: 12px; display: block; opacity: .4; }
      .crm-empty strong { display: block; color: var(--crm-text); font-size: 14px; margin-bottom: 4px; }

      /* ─── Responsive pipeline ─── */
      @media (max-width: 991px) {
        .crm-pipe-stages { grid-template-columns: repeat(3,1fr); }
      }
      @media (max-width: 576px) {
        .crm-pipe-stages { grid-template-columns: repeat(2,1fr); }
        .crm-kpi-value { font-size: 22px; }
      }
      </style>
    <?php endif; ?>

    <?php if ($_SESSION["perfil"] == "administrador"): ?>

      <!-- ══ WELCOME BANNER ADMIN ══ -->
      <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%);border-radius:var(--crm-radius,14px);padding:28px 30px;margin-bottom:24px;position:relative;overflow:hidden">
        <div style="position:absolute;right:-20px;top:-20px;width:180px;height:180px;border-radius:50%;background:rgba(99,102,241,.12)"></div>
        <div style="position:absolute;right:60px;bottom:-40px;width:120px;height:120px;border-radius:50%;background:rgba(99,102,241,.08)"></div>
        <div style="position:relative;z-index:1">
          <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;letter-spacing:-.02em">
            <i class="fa-solid fa-shield-halved" style="margin-right:8px;opacity:.7"></i>
            Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
          </h2>
          <?php
            $diasEs  = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
            $mesesEs = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
            $_fechaAdmin = $diasEs[date('w')].' '.date('j').' de '.$mesesEs[intval(date('n'))].', '.date('Y');
          ?>
          <p style="margin:0;color:rgba(255,255,255,.55);font-size:13px;font-weight:400">
            <?php echo $_fechaAdmin; ?> &mdash; Panel de administración general
          </p>
        </div>
      </div>

      <!-- ══ SECCIÓN 1: KPIs ══ -->
      <?php include "inicio/admin-kpis.php"; ?>

      <!-- ══ SECCIÓN 2: Pipeline de Negocio ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
          <i class="fa-solid fa-diagram-project"></i>
        </div>
        <div>
          <h3>Pipeline de Negocio</h3>
          <p>Flujo del servicio: Diagnóstico → Autorización → Reparación → Entrega</p>
        </div>
      </div>
      <?php include "inicio/admin-pipeline.php"; ?>

      <!-- ══ SECCIÓN 3: Últimos Movimientos + Acciones Rápidas ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
          <h3>Últimos Movimientos</h3>
          <p>Observaciones recientes en órdenes de servicio</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-7 col-xs-12">
          <?php include "inicio/admin-ultimos-movimientos.php"; ?>
        </div>
        <div class="col-lg-4 col-md-5 col-xs-12">
          <?php include "inicio/admin-acciones-rapidas.php"; ?>
        </div>
      </div>

      <!-- ══ SECCIÓN 4: Análisis y Rendimiento ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#8b5cf6,#06b6d4)">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
          <h3>Análisis y Rendimiento</h3>
          <p>Ventas por periodo y eficiencia real de técnicos del mes</p>
        </div>
      </div>
      <div class="row dash-row-equal">
        <div class="col-lg-8 col-md-8 col-xs-12">
          <?php include "inicio/admin-grafico-ventas.php"; ?>
        </div>
        <div class="col-lg-4 col-md-4 col-xs-12">
          <?php include "inicio/admin-top-tecnicos.php"; ?>
        </div>
      </div>

      <!-- ══ SECCIÓN 5: Equipo ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#22c55e,#06b6d4)">
          <i class="fa-solid fa-users"></i>
        </div>
        <div>
          <h3>Equipo</h3>
          <p>Asesores y técnicos registrados en la empresa</p>
        </div>
      </div>
      <?php include "inicio/admin-equipo.php"; ?>

    <?php elseif ($_SESSION["perfil"] == "vendedor"): ?>

      <!-- ══ WELCOME BANNER ══ -->
      <div style="background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 50%,#a78bfa 100%);border-radius:var(--crm-radius);padding:28px 30px;margin-bottom:24px;position:relative;overflow:hidden">
        <div style="position:absolute;right:-20px;top:-20px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06)"></div>
        <div style="position:absolute;right:60px;bottom:-40px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.04)"></div>
        <div style="position:relative;z-index:1">
          <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;letter-spacing:-.02em">
            Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
          </h2>
          <?php
            $diasEs  = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
            $mesesEs = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
            $_fechaHoy = $diasEs[date('w')].' '.date('j').' de '.$mesesEs[intval(date('n'))].', '.date('Y');
          ?>
          <p style="margin:0;color:rgba(255,255,255,.7);font-size:13px;font-weight:400">
            <?php echo $_fechaHoy; ?> &mdash; Aquí está tu resumen comercial del día
          </p>
        </div>
      </div>

      <!-- ══ SECCIÓN 1: KPIs ══ -->
      <?php include "inicio/crm-kpis-vendedor.php"; ?>

      <!-- ══ SECCIÓN 2: Pipeline ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
          <i class="fa-solid fa-diagram-project"></i>
        </div>
        <div>
          <h3>Pipeline de Ventas</h3>
          <p>Flujo completo de tus órdenes por estado</p>
        </div>
      </div>
      <?php include "inicio/crm-pipeline-vendedor.php"; ?>

      <!-- ══ SECCIÓN 3: Seguimiento + Acciones ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
          <i class="fa-solid fa-phone-volume"></i>
        </div>
        <div>
          <h3>Seguimiento Pendiente</h3>
          <p>Órdenes que requieren contacto con el cliente</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-7 col-xs-12">
          <?php include "inicio/crm-pendientes-vendedor.php"; ?>
        </div>
        <div class="col-lg-4 col-md-5 col-xs-12">
          <?php include "inicio/crm-acciones-rapidas.php"; ?>
        </div>
      </div>

      <!-- ══ SECCIÓN 4: Cotizaciones + Clientes ══ -->
      <div class="crm-section">
        <div class="crm-section-icon" style="background:linear-gradient(135deg,#8b5cf6,#06b6d4)">
          <i class="fa-solid fa-handshake"></i>
        </div>
        <div>
          <h3>Oportunidades y Cartera</h3>
          <p>Cotizaciones activas y tus clientes asignados</p>
        </div>
      </div>
      <div class="row dash-row-equal">
        <div class="col-lg-7 col-md-7 col-xs-12">
          <?php include "inicio/crm-cotizaciones-vendedor.php"; ?>
        </div>
        <div class="col-lg-5 col-md-5 col-xs-12">
          <?php include "inicio/crm-clientes-vendedor.php"; ?>
        </div>
      </div>

    <?php elseif ($_SESSION["perfil"] == "tecnico"): ?>

      <!-- ══ WELCOME BANNER TÉCNICO ══ -->
      <div style="background:linear-gradient(135deg,#0ea5e9 0%,#6366f1 50%,#8b5cf6 100%);border-radius:var(--crm-radius,14px);padding:28px 30px;margin-bottom:24px;position:relative;overflow:hidden">
        <div style="position:absolute;right:-20px;top:-20px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06)"></div>
        <div style="position:absolute;right:60px;bottom:-40px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.04)"></div>
        <div style="position:relative;z-index:1">
          <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;letter-spacing:-.02em">
            <i class="fa-solid fa-wrench" style="margin-right:8px;opacity:.7"></i>
            Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
          </h2>
          <?php
            $diasEs  = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
            $mesesEs = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
            $_fechaTec = $diasEs[date('w')].' '.date('j').' de '.$mesesEs[intval(date('n'))].', '.date('Y');
          ?>
          <p style="margin:0;color:rgba(255,255,255,.7);font-size:13px;font-weight:400">
            <?php echo $_fechaTec; ?> &mdash; Tu panel de trabajo del día
          </p>
        </div>
      </div>

      <!-- ══ KPIs + Pipeline + Órdenes ══ -->
      <?php include "inicio/tec-dashboard.php"; ?>

    <?php endif; ?>

  </section>
  <!-- content -->

</div>
