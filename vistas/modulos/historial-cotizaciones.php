<?php
/*  ═══════════════════════════════════════════════════
    HISTORIAL DE COTIZACIONES — CRM Design System
    ═══════════════════════════════════════════════════ */

if ($_SESSION["perfil"] != "administrador" && $_SESSION["perfil"] != "vendedor"
    && $_SESSION["perfil"] != "secretaria" && $_SESSION["perfil"] != "Super-Administrador") {
  echo '<script>window.location = "index.php?ruta=inicio";</script>';
  return;
}

// ── Datos ──
$_hc_perfil = $_SESSION["perfil"];
$_hc_cotizaciones = array();

try {
  if ($_hc_perfil === "vendedor") {
    // Vendedor solo ve sus cotizaciones
    $asesorData = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
    if (is_array($asesorData) && isset($asesorData["id"])) {
      $_hc_cotizaciones = CotizacionesControlador::ctrMostrarCotizaciones("id_vendedor", $asesorData["id"]);
      if (!is_array($_hc_cotizaciones)) $_hc_cotizaciones = array();
    }
  } else {
    $_hc_cotizaciones = CotizacionesControlador::ctrMostrarCotizaciones(null, null);
    if (!is_array($_hc_cotizaciones)) $_hc_cotizaciones = array();
  }
} catch (Exception $e) {
  $_hc_cotizaciones = array();
}

// Precargar vendedores para mapear id → nombre
$_hc_vendedores = array();
try {
  $allAsesores = Controladorasesores::ctrMostrarAsesoresEleg(null, null);
  if (is_array($allAsesores)) {
    foreach ($allAsesores as $a) {
      if (isset($a["id"])) $_hc_vendedores[intval($a["id"])] = $a["nombre"];
    }
  }
} catch (Exception $e) {}

// ── Helper: parsear vigencia y determinar si expiró ──
// vigencia viene como texto libre: "Validez 8 días", "Validez 30 días", etc.
// Retorna: [expirada(bool), diasVigencia(int|null), fechaExpira(string|null), etiqueta, colorTexto, colorBg, icono]
function _hcEvalVigencia($vigenciaText, $fechaCotizacion) {
  $dias = null;
  // Extraer el número de días del texto (ej: "Validez 8 días", "30 dias", etc.)
  // Usamos solo \d+ ya que el formato siempre es "Validez N días"
  // Evitamos regex con caracteres UTF-8 (í) que fallan sin flag /u
  if (preg_match('/(\d+)/', $vigenciaText, $m)) {
    $dias = intval($m[1]);
  }
  if ($dias === null || empty($fechaCotizacion)) {
    // No se puede determinar, mostrar como texto neutral
    return array(false, null, null, $vigenciaText ?: '-', '#6366f1', '#eef2ff', 'fa-clock');
  }
  $fechaBase = strtotime($fechaCotizacion);
  if ($fechaBase === false) {
    return array(false, null, null, $vigenciaText, '#6366f1', '#eef2ff', 'fa-clock');
  }
  $fechaExpira = date('Y-m-d', strtotime("+{$dias} days", $fechaBase));
  $hoy = date('Y-m-d');
  $expirada = ($hoy > $fechaExpira);

  if ($expirada) {
    return array(true, $dias, $fechaExpira, 'Expirada', '#dc2626', '#fef2f2', 'fa-circle-exclamation');
  } else {
    // Calcular días restantes
    $restantes = (strtotime($fechaExpira) - strtotime($hoy)) / 86400;
    $restantes = max(0, intval($restantes));
    if ($restantes <= 3) {
      return array(false, $dias, $fechaExpira, $restantes . 'd restantes', '#f59e0b', '#fffbeb', 'fa-triangle-exclamation');
    }
    return array(false, $dias, $fechaExpira, 'Vigente (' . $restantes . 'd)', '#16a34a', '#f0fdf4', 'fa-circle-check');
  }
}

// URL base para QR
$_hc_baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
  . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
// Limpiar trailing slashes
$_hc_baseUrl = rtrim($_hc_baseUrl, '/') . '/';

// Estadísticas rápidas
$_hc_total = count($_hc_cotizaciones);
$_hc_montoTotal = 0;
$_hc_esteMes = 0;
$_hc_vigentes = 0;
$_hc_expiradas = 0;
$_hc_mesActual = date("Y-m");

foreach ($_hc_cotizaciones as $c) {
  $fecha = isset($c["fecha"]) ? $c["fecha"] : "";
  if (!empty($fecha) && substr($fecha, 0, 7) === $_hc_mesActual) {
    $_hc_esteMes++;
  }
  $vig = _hcEvalVigencia(isset($c["vigencia"]) ? $c["vigencia"] : "", $fecha);
  if ($vig[0]) {
    $_hc_expiradas++;
  } else {
    $_hc_vigentes++;
    $_hc_montoTotal += floatval($c["total"]); // Solo acumular monto de cotizaciones activas
  }
}
?>

<style>
/* ═══════════════════════════════════════════════════
   HISTORIAL COTIZACIONES — CRM Design System
   ═══════════════════════════════════════════════════ */
:root {
  --crm-bg: #f8fafc; --crm-surface: #ffffff; --crm-border: #e2e8f0;
  --crm-text: #0f172a; --crm-text2: #475569; --crm-muted: #94a3b8;
  --crm-accent: #6366f1; --crm-accent2: #818cf8;
  --crm-radius: 14px; --crm-radius-sm: 10px;
  --crm-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
  --crm-ease: cubic-bezier(.4,0,.2,1);
}

/* ─── Header ─── */
.content-wrapper .content-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);
  padding: 28px 30px 24px; margin: 0; border-bottom: none;
}
.content-wrapper .content-header h1 {
  color: #fff; font-size: 22px; font-weight: 800; letter-spacing: -.02em; margin: 0 0 4px;
}
.content-wrapper .content-header h1 small {
  color: rgba(255,255,255,.5); font-size: 13px; font-weight: 400; display: block; margin-top: 4px;
}
.content-wrapper .content-header .breadcrumb {
  background: rgba(255,255,255,.08); border-radius: 8px; padding: 6px 14px; margin: 0; top: 28px;
}
.content-wrapper .content-header .breadcrumb > li,
.content-wrapper .content-header .breadcrumb > li > a,
.content-wrapper .content-header .breadcrumb > li.active {
  color: rgba(255,255,255,.6); font-size: 12px; font-weight: 500;
}

/* ─── KPI Cards ─── */
.hc-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.hc-kpi {
  background: var(--crm-surface); border-radius: var(--crm-radius); padding: 20px 22px;
  box-shadow: var(--crm-shadow); border: 1px solid var(--crm-border);
  display: flex; align-items: center; gap: 16px; transition: transform .2s var(--crm-ease), box-shadow .2s;
}
.hc-kpi:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(15,23,42,.1); }
.hc-kpi-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
}
.hc-kpi-value { font-size: 24px; font-weight: 800; color: var(--crm-text); line-height: 1; }
.hc-kpi-label { font-size: 12px; color: var(--crm-muted); font-weight: 500; margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }

/* ─── Table Section ─── */
.hc-section {
  background: var(--crm-surface); border-radius: var(--crm-radius);
  box-shadow: var(--crm-shadow); border: 1px solid var(--crm-border); overflow: hidden;
}
.hc-toolbar {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
  padding: 18px 22px; border-bottom: 1px solid var(--crm-border);
}
.hc-toolbar-title { font-size: 15px; font-weight: 700; color: var(--crm-text); display: flex; align-items: center; gap: 8px; }
.hc-toolbar-title i { color: var(--crm-accent); }

.hc-btn-new {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--crm-accent); color: #fff; border: none;
  padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
  cursor: pointer; text-decoration: none; transition: all .2s var(--crm-ease);
  box-shadow: 0 2px 8px rgba(99,102,241,.3);
}
.hc-btn-new:hover { background: #4f46e5; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,.4); text-decoration: none; }

/* ─── DataTable overrides ─── */
.hc-section .dataTables_wrapper { padding: 0 22px 22px; }
.hc-section .dataTables_filter { margin: 16px 0 10px; }
.hc-section .dataTables_filter input {
  border: 1px solid var(--crm-border); border-radius: 8px; padding: 8px 14px;
  font-size: 13px; transition: border-color .2s; outline: none;
}
.hc-section .dataTables_filter input:focus { border-color: var(--crm-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.hc-section table.dataTable { border-collapse: separate; border-spacing: 0; }
.hc-section table.dataTable thead th {
  background: #f8fafc; color: var(--crm-text2); font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .06em; padding: 12px 14px;
  border-bottom: 2px solid var(--crm-border); border-top: none;
}
.hc-section table.dataTable tbody td {
  padding: 12px 14px; font-size: 13px; color: var(--crm-text);
  border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.hc-section table.dataTable tbody tr:hover td { background: #fafaff; }

/* ─── Badges ─── */
.hc-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
  white-space: nowrap;
}
.hc-id { font-weight: 700; color: var(--crm-accent); }
.hc-cliente { font-weight: 600; color: var(--crm-text); }
.hc-asunto { color: var(--crm-text2); font-size: 12px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.hc-monto { font-weight: 700; color: var(--crm-text); font-size: 14px; }
.hc-fecha { color: var(--crm-muted); font-size: 12px; }

/* ─── Action buttons ─── */
.hc-actions { display: flex; gap: 6px; }
.hc-act-btn {
  width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--crm-border);
  background: #fff; color: var(--crm-text2); font-size: 13px; cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center;
  transition: all .15s var(--crm-ease);
}
.hc-act-btn:hover { border-color: var(--crm-accent); color: var(--crm-accent); background: #eef2ff; }
.hc-act-btn.view:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
.hc-act-btn.print:hover { border-color: #22c55e; color: #22c55e; background: #f0fdf4; }
.hc-act-btn.copy:hover { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }

/* ─── Detail Modal ─── */
#hcDetailModal .modal-dialog { max-width: 720px; width: 95%; }
#hcDetailModal .modal-content {
  border: none; border-radius: 16px; overflow: hidden;
  box-shadow: 0 20px 50px rgba(0,0,0,.2);
}
#hcDetailModal .modal-body { padding: 0; }
.hc-detail-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 24px 28px; color: #fff;
}
.hc-detail-header h3 { margin: 0 0 4px; font-size: 18px; font-weight: 800; }
.hc-detail-header .hc-detail-meta { font-size: 12px; color: rgba(255,255,255,.6); display: flex; gap: 16px; flex-wrap: wrap; }

.hc-detail-body { padding: 24px 28px; }
.hc-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
.hc-detail-field label { display: block; font-size: 11px; font-weight: 600; color: var(--crm-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
.hc-detail-field span { font-size: 14px; color: var(--crm-text); font-weight: 500; }

.hc-detail-products { margin-top: 16px; }
.hc-detail-products table { width: 100%; border-collapse: collapse; font-size: 13px; }
.hc-detail-products thead th {
  background: #f8fafc; padding: 10px 12px; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .05em; color: var(--crm-text2);
  border-bottom: 2px solid var(--crm-border);
}
.hc-detail-products tbody td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }

.hc-detail-totals {
  margin-top: 16px; display: flex; justify-content: flex-end;
}
.hc-detail-totals table { width: 260px; font-size: 13px; }
.hc-detail-totals td { padding: 8px 12px; }
.hc-detail-totals tr:last-child td { font-weight: 800; font-size: 16px; background: #f8fafc; border-radius: 8px; }

.hc-detail-obs {
  margin-top: 16px; padding: 14px 16px; background: #f8fafc; border-radius: 10px;
  font-size: 13px; color: var(--crm-text2); line-height: 1.5;
}
.hc-detail-obs label { display: block; font-size: 11px; font-weight: 600; color: var(--crm-muted); text-transform: uppercase; margin-bottom: 6px; }

.hc-detail-footer {
  padding: 16px 28px; border-top: 1px solid var(--crm-border);
  display: flex; justify-content: flex-end; gap: 10px;
}
.hc-detail-footer .btn { border-radius: 10px; font-weight: 600; font-size: 13px; padding: 9px 18px; }

@media (max-width: 768px) {
  .hc-kpis { grid-template-columns: 1fr 1fr; }
  .hc-detail-grid { grid-template-columns: 1fr; }
  .hc-toolbar { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="content-wrapper">

  <section class="content-header">
    <h1><i class="fa-solid fa-file-invoice-dollar" style="margin-right:8px;color:#818cf8"></i>Historial de Cotizaciones
      <small>Gestiona y da seguimiento a todas las cotizaciones</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php?ruta=inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Historial de Cotizaciones</li>
    </ol>
  </section>

  <section class="content">

    <!-- ── KPI Cards ── -->
    <div class="hc-kpis">
      <div class="hc-kpi">
        <div class="hc-kpi-icon" style="background:#eef2ff;color:#6366f1">
          <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div>
          <div class="hc-kpi-value"><?php echo $_hc_total; ?></div>
          <div class="hc-kpi-label">Total cotizaciones</div>
        </div>
      </div>
      <div class="hc-kpi">
        <div class="hc-kpi-icon" style="background:#f0fdf4;color:#16a34a">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <div class="hc-kpi-value"><?php echo $_hc_vigentes; ?></div>
          <div class="hc-kpi-label">Vigentes</div>
        </div>
      </div>
      <div class="hc-kpi">
        <div class="hc-kpi-icon" style="background:#fef2f2;color:#dc2626">
          <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <div>
          <div class="hc-kpi-value"><?php echo $_hc_expiradas; ?></div>
          <div class="hc-kpi-label">Expiradas</div>
        </div>
      </div>
      <div class="hc-kpi">
        <div class="hc-kpi-icon" style="background:#fffbeb;color:#f59e0b">
          <i class="fa-solid fa-coins"></i>
        </div>
        <div>
          <div class="hc-kpi-value">$<?php echo number_format($_hc_montoTotal, 0, '.', ','); ?></div>
          <div class="hc-kpi-label">Monto vigente</div>
        </div>
      </div>
    </div>

    <!-- ── Tabla de cotizaciones ── -->
    <div class="hc-section">
      <div class="hc-toolbar">
        <div class="hc-toolbar-title">
          <i class="fa-solid fa-table-list"></i> Todas las cotizaciones
        </div>
        <a href="index.php?ruta=cotizacion" class="hc-btn-new">
          <i class="fa-solid fa-plus"></i> Nueva Cotización
        </a>
      </div>

      <div style="padding:0 22px 22px">
        <table class="table display compact hover" id="hcTable" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Asunto</th>
              <th>Vendedor</th>
              <th>Vigencia</th>
              <th>Total</th>
              <th style="text-align:center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($_hc_cotizaciones as $cot):
              $vendedorNombre = isset($_hc_vendedores[intval($cot["id_vendedor"])]) ? $_hc_vendedores[intval($cot["id_vendedor"])] : "Vendedor #".$cot["id_vendedor"];
              $fechaFmt = !empty($cot["fecha"]) ? date("d/m/Y", strtotime($cot["fecha"])) : "-";
              $vigRaw = isset($cot["vigencia"]) ? $cot["vigencia"] : "";
              $vigEval = _hcEvalVigencia($vigRaw, isset($cot["fecha"]) ? $cot["fecha"] : "");
              // $vigEval = [expirada, dias, fechaExpira, etiqueta, colorTexto, colorBg, icono]
              $qrValidUrl = $_hc_baseUrl . 'index.php?ruta=validar-cotizacion&codigo=' . urlencode($cot['codigo_qr']);
            ?>
            <tr>
              <td><span class="hc-id">#<?php echo $cot["id"]; ?></span></td>
              <td><span class="hc-fecha"><?php echo $fechaFmt; ?></span></td>
              <td><span class="hc-cliente"><?php echo htmlspecialchars($cot["nombre_cliente"]); ?></span></td>
              <td><span class="hc-asunto" title="<?php echo htmlspecialchars($cot["asunto"]); ?>"><?php echo htmlspecialchars($cot["asunto"]); ?></span></td>
              <td><?php echo htmlspecialchars($vendedorNombre); ?></td>
              <td>
                <span class="hc-badge" style="color:<?php echo $vigEval[4]; ?>;background:<?php echo $vigEval[5]; ?>">
                  <i class="fa-solid <?php echo $vigEval[6]; ?>" style="font-size:10px"></i>
                  <?php echo htmlspecialchars($vigEval[3]); ?>
                </span>
              </td>
              <td><span class="hc-monto">$<?php echo number_format($cot["total"], 2); ?></span></td>
              <td style="text-align:center">
                <div class="hc-actions" style="justify-content:center">
                  <button class="hc-act-btn view hcVerDetalle" title="Ver detalle"
                    data-id="<?php echo $cot['id']; ?>"
                    data-fecha="<?php echo $fechaFmt; ?>"
                    data-cliente="<?php echo htmlspecialchars($cot['nombre_cliente']); ?>"
                    data-vendedor="<?php echo htmlspecialchars($vendedorNombre); ?>"
                    data-empresa="<?php echo htmlspecialchars($cot['empresa']); ?>"
                    data-asunto="<?php echo htmlspecialchars($cot['asunto']); ?>"
                    data-vigencia="<?php echo htmlspecialchars($vigRaw); ?>"
                    data-vig-label="<?php echo htmlspecialchars($vigEval[3]); ?>"
                    data-vig-color="<?php echo $vigEval[4]; ?>"
                    data-vig-bg="<?php echo $vigEval[5]; ?>"
                    data-vig-icon="<?php echo $vigEval[6]; ?>"
                    data-vig-expirada="<?php echo $vigEval[0] ? '1' : '0'; ?>"
                    data-total="<?php echo number_format($cot['total'], 2); ?>"
                    data-neto="<?php echo number_format($cot['neto'], 2); ?>"
                    data-impuesto="<?php echo number_format($cot['impuesto'], 2); ?>"
                    data-descuento="<?php echo $cot['descuento_porcentaje']; ?>"
                    data-observaciones="<?php echo htmlspecialchars($cot['observaciones']); ?>"
                    data-productos="<?php echo htmlspecialchars($cot['productos']); ?>"
                    data-qr="<?php echo htmlspecialchars($cot['codigo_qr']); ?>"
                    data-qr-url="<?php echo htmlspecialchars($qrValidUrl); ?>"
                  ><i class="fa-solid fa-eye"></i></button>
                  <a class="hc-act-btn print" title="Imprimir / PDF"
                    href="index.php?ruta=imprimir-cotizacion&id=<?php echo $cot['id']; ?>"
                    target="_blank"
                  ><i class="fa-solid fa-print"></i></a>
                  <?php if ($vigEval[0]): ?>
                  <a class="hc-act-btn copy" title="Recotizar (nueva cotización con estos datos)"
                    href="index.php?ruta=cotizacion&recotizar=<?php echo $cot['id']; ?>"
                  ><i class="fa-solid fa-rotate-right"></i></a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</div>

<!-- ═══ Modal Detalle ═══ -->
<div class="modal fade" id="hcDetailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">

        <div class="hc-detail-header">
          <h3><i class="fa-solid fa-file-invoice" style="margin-right:6px;opacity:.7"></i> Cotización <span id="hcDetId"></span></h3>
          <div class="hc-detail-meta">
            <span><i class="fa-solid fa-calendar-day"></i> <span id="hcDetFecha"></span></span>
            <span><i class="fa-solid fa-building"></i> <span id="hcDetEmpresa"></span></span>
            <span id="hcDetVigWrap" style="display:inline-flex;align-items:center;gap:4px"></span>
          </div>
        </div>

        <div class="hc-detail-body">
          <div class="hc-detail-grid">
            <div class="hc-detail-field">
              <label>Cliente</label>
              <span id="hcDetCliente"></span>
            </div>
            <div class="hc-detail-field">
              <label>Vendedor</label>
              <span id="hcDetVendedor"></span>
            </div>
            <div class="hc-detail-field" style="grid-column:1/-1">
              <label>Asunto</label>
              <span id="hcDetAsunto"></span>
            </div>
          </div>

          <!-- Tabla de productos -->
          <div class="hc-detail-products">
            <table>
              <thead>
                <tr>
                  <th style="width:50%">Producto / Servicio</th>
                  <th style="width:12%;text-align:center">Cant.</th>
                  <th style="width:18%;text-align:right">P. Unitario</th>
                  <th style="width:20%;text-align:right">Total</th>
                </tr>
              </thead>
              <tbody id="hcDetProductos"></tbody>
            </table>
          </div>

          <!-- Totales -->
          <div class="hc-detail-totals">
            <table>
              <tr><td style="text-align:right;color:#64748b">Subtotal</td><td style="text-align:right" id="hcDetNeto"></td></tr>
              <tr id="hcDetDescRow" style="display:none"><td style="text-align:right;color:#64748b">Descuento</td><td style="text-align:right;color:#ef4444" id="hcDetDesc"></td></tr>
              <tr><td style="text-align:right;color:#64748b">IVA</td><td style="text-align:right" id="hcDetIva"></td></tr>
              <tr><td style="text-align:right;font-weight:800">Total</td><td style="text-align:right;font-weight:800;font-size:16px;color:#6366f1" id="hcDetTotal"></td></tr>
            </table>
          </div>

          <!-- Observaciones -->
          <div class="hc-detail-obs" id="hcDetObsWrap" style="display:none">
            <label>Observaciones</label>
            <div id="hcDetObs"></div>
          </div>

          <!-- Banner de expiración -->
          <div id="hcDetExpiredBanner" style="display:none;margin-top:16px;padding:16px 20px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;text-align:center">
            <i class="fa-solid fa-circle-exclamation" style="font-size:28px;color:#dc2626;display:block;margin-bottom:8px"></i>
            <div style="font-size:15px;font-weight:700;color:#dc2626;margin-bottom:4px">Cotización Expirada</div>
            <div style="font-size:13px;color:#991b1b">La vigencia de esta cotización ha finalizado. Es necesario generar una nueva cotización con precios actualizados.</div>
            <a href="index.php?ruta=cotizacion" class="btn" style="margin-top:12px;background:#dc2626;color:#fff;border-radius:8px;font-weight:600;font-size:12px;padding:8px 16px">
              <i class="fa-solid fa-rotate-right"></i> Nueva Cotización
            </a>
          </div>

          <!-- QR de verificación -->
          <div id="hcDetQrWrap" style="display:none;text-align:center;margin-top:20px">
            <label style="display:block;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">
              <i class="fa-solid fa-qrcode" style="margin-right:4px"></i> Código QR de Verificación
            </label>
            <div style="display:inline-block;padding:12px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.06)">
              <img id="hcDetQrImg" src="" alt="QR" style="width:160px;height:160px;display:block">
            </div>
            <div style="margin-top:8px;font-size:11px;color:#94a3b8">
              Escanea para verificar autenticidad
            </div>
          </div>
        </div>

        <div class="hc-detail-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <a id="hcDetRecotizar" href="#" class="btn btn-warning" style="display:none;border-radius:10px;font-weight:600;font-size:13px;padding:9px 18px">
            <i class="fa-solid fa-rotate-right"></i> Recotizar
          </a>
          <a id="hcDetPrint" href="#" target="_blank" class="btn btn-primary" style="background:#6366f1;border-color:#6366f1;border-radius:10px">
            <i class="fa-solid fa-print"></i> Imprimir
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

  // ── DataTable ──
  if ($.fn.DataTable.isDataTable('#hcTable')) {
    $('#hcTable').DataTable().destroy();
  }
  $('#hcTable').DataTable({
    order: [[0, 'desc']],
    pageLength: 25,
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron cotizaciones",
      sEmptyTable: "No hay cotizaciones registradas",
      sInfo: "Mostrando _START_ a _END_ de _TOTAL_",
      sInfoEmpty: "Sin registros",
      sInfoFiltered: "(filtrado de _MAX_ totales)",
      sSearch: '<i class="fa-solid fa-magnifying-glass" style="color:#94a3b8;margin-right:4px"></i>',
      oPaginate: { sFirst: "Primero", sLast: "Último", sNext: "›", sPrevious: "‹" }
    },
    columnDefs: [
      { targets: [7], orderable: false }
    ]
  });

  // ── Ver detalle ──
  $(document).on('click', '.hcVerDetalle', function(){
    var $b = $(this);
    $('#hcDetId').text('#' + $b.data('id'));
    $('#hcDetFecha').text($b.data('fecha'));
    $('#hcDetEmpresa').text($b.data('empresa'));
    $('#hcDetCliente').text($b.data('cliente'));
    $('#hcDetVendedor').text($b.data('vendedor'));
    $('#hcDetAsunto').text($b.data('asunto'));
    $('#hcDetNeto').text('$' + $b.data('neto'));
    $('#hcDetIva').text('$' + $b.data('impuesto'));
    $('#hcDetTotal').text('$' + $b.data('total'));

    // Vigencia con estado
    var vigText = $b.data('vigencia') || '-';
    var vigLabel = $b.data('vig-label') || vigText;
    var vigColor = $b.data('vig-color') || '#6366f1';
    var vigBg = $b.data('vig-bg') || '#eef2ff';
    var vigIcon = $b.data('vig-icon') || 'fa-clock';
    var vigExpirada = $b.data('vig-expirada') == '1';
    var vigHtml = '<i class="fa-solid fa-clock" style="font-size:11px;color:#818cf8"></i> ' + vigText;
    vigHtml += ' <span style="background:'+vigBg+';color:'+vigColor+';padding:2px 10px;border-radius:12px;font-size:10px;font-weight:700;margin-left:6px"><i class="fa-solid '+vigIcon+'" style="font-size:9px;margin-right:3px"></i>'+vigLabel+'</span>';
    $('#hcDetVigWrap').html(vigHtml);

    // Banner de expiración
    if (vigExpirada) {
      $('#hcDetExpiredBanner').show();
    } else {
      $('#hcDetExpiredBanner').hide();
    }

    // Descuento
    var desc = parseFloat($b.data('descuento'));
    if (desc > 0) {
      $('#hcDetDescRow').show();
      $('#hcDetDesc').text('-' + desc + '%');
    } else {
      $('#hcDetDescRow').hide();
    }

    // Observaciones
    var obs = $b.data('observaciones');
    if (obs && obs.trim() !== '') {
      $('#hcDetObs').text(obs);
      $('#hcDetObsWrap').show();
    } else {
      $('#hcDetObsWrap').hide();
    }

    // QR de verificación (imagen generada)
    var qrUrl = $b.data('qr-url');
    if (qrUrl && qrUrl.trim() !== '') {
      var qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=png&data=' + encodeURIComponent(qrUrl);
      $('#hcDetQrImg').attr('src', qrApiUrl);
      $('#hcDetQrWrap').show();
    } else {
      $('#hcDetQrWrap').hide();
    }

    // Productos
    var prods = [];
    try { prods = JSON.parse($b.attr('data-productos')); } catch(e){}
    var html = '';
    if (Array.isArray(prods) && prods.length > 0) {
      for (var i = 0; i < prods.length; i++) {
        var p = prods[i];
        var pdesc = p.descripcion || p.producto || p.nombre || '-';
        var qty  = p.cantidad || p.qty || 1;
        var prc  = parseFloat(p.precio || p.price || p.precioUnitario || 0);
        var tot  = parseFloat(p.total || (qty * prc));
        html += '<tr>';
        html += '<td>' + pdesc + '</td>';
        html += '<td style="text-align:center">' + qty + '</td>';
        html += '<td style="text-align:right">$' + prc.toFixed(2) + '</td>';
        html += '<td style="text-align:right;font-weight:600">$' + tot.toFixed(2) + '</td>';
        html += '</tr>';
      }
    } else {
      html = '<tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px">Sin productos registrados</td></tr>';
    }
    $('#hcDetProductos').html(html);

    // Print button → abrir formato de impresión profesional
    var cotId = $b.data('id');
    $('#hcDetPrint').attr('href', 'index.php?ruta=imprimir-cotizacion&id=' + cotId);

    // Recotizar button (solo si expirada)
    if (vigExpirada) {
      $('#hcDetRecotizar').attr('href', 'index.php?ruta=cotizacion&recotizar=' + cotId).show();
    } else {
      $('#hcDetRecotizar').hide();
    }

    $('#hcDetailModal').modal('show');
  });

});
</script>
