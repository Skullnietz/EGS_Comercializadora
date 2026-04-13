<?php
$codigo = isset($_GET["codigo"]) ? trim($_GET["codigo"]) : null;
$cotizacion = null;

if ($codigo) {
    $cotizaciones = CotizacionesControlador::ctrMostrarCotizacion("codigo_qr", $codigo);
    $cotizacion = !empty($cotizaciones) ? $cotizaciones[0] : null;
}

// ── Evaluar vigencia ──
$_vc_expirada = false;
$_vc_fechaExpira = null;
if (is_array($cotizacion) && !empty($cotizacion)) {
    $vigText = isset($cotizacion["vigencia"]) ? $cotizacion["vigencia"] : "";
    $fechaCot = isset($cotizacion["fecha"]) ? $cotizacion["fecha"] : "";
    $dias = null;
    // Extraer número de días (ej: "Validez 8 días" → 8)
    if (preg_match('/(\d+)/', $vigText, $m)) {
        $dias = intval($m[1]);
    }
    if ($dias !== null && !empty($fechaCot)) {
        $fechaBase = strtotime($fechaCot);
        if ($fechaBase !== false) {
            $_vc_fechaExpira = date('d/m/Y', strtotime("+{$dias} days", $fechaBase));
            $_vc_expirada = (date('Y-m-d') > date('Y-m-d', strtotime("+{$dias} days", $fechaBase)));
        }
    }
}

$_vc_subtotal = (is_array($cotizacion) && isset($cotizacion["neto"])) ? floatval($cotizacion["neto"]) : 0;
$_vc_iva = (is_array($cotizacion) && isset($cotizacion["impuesto"])) ? floatval($cotizacion["impuesto"]) : 0;
$_vc_total = (is_array($cotizacion) && isset($cotizacion["total"])) ? floatval($cotizacion["total"]) : 0;
?>

<style>
/* ═══ Validar Cotización — Responsivo ═══ */
/* Ocultar sidebar y cabezote para página pública */
.main-header, .main-sidebar, .left-side,
.control-sidebar, .main-footer { display: none !important; }
body.sidebar-mini .content-wrapper,
body .content-wrapper { margin-left: 0 !important; }

.vc-wrapper {
    margin-left: 0 !important;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}
.vc-center {
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 90vh;
}
.vc-card {
    width: 100%;
    max-width: 620px;
}
.vc-box {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,.1);
}
.vc-header {
    padding: 28px 30px;
    text-align: center;
    color: #fff;
}
.vc-header-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: inline-flex;
    align-items: center; justify-content: center;
    margin-bottom: 12px;
}
.vc-header-icon i { font-size: 28px; }
.vc-header h3 { margin: 0 0 4px; font-size: 22px; font-weight: 800; }
.vc-header p { margin: 0; font-size: 13px; opacity: .85; }

.vc-body { padding: 28px 30px; }

.vc-logo { text-align: center; margin-bottom: 20px; }
.vc-logo img { max-height: 70px; width: auto; }
.vc-logo h4 { margin-top: 8px; font-weight: 700; color: #0f172a; font-size: 15px; }

.vc-info {
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.vc-info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.vc-info-row:last-child { margin-bottom: 0; }
.vc-info-row i { width: 16px; text-align: center; flex-shrink: 0; }
.vc-info-row span { font-size: 13px; color: #64748b; white-space: nowrap; }
.vc-info-row strong { font-size: 13px; color: #0f172a; word-break: break-word; }

.vc-productos-title {
    font-weight: 700; color: #0f172a; font-size: 14px;
    margin-bottom: 10px;
}
.vc-table-wrap {
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.vc-table-wrap table {
    margin: 0; font-size: 13px; width: 100%; min-width: 300px;
}
.vc-table-wrap thead tr { background: #f8fafc; }
.vc-table-wrap thead th {
    border: none; padding: 10px 14px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; color: #64748b;
    white-space: nowrap;
}
.vc-table-wrap tbody td {
    padding: 10px 14px;
    border-top: 1px solid #f1f5f9;
}

.vc-total-box {
    text-align: right;
    margin-top: 16px;
    padding: 16px 20px;
    border-radius: 12px;
}
.vc-total-label {
    font-size: 12px; color: #64748b;
    text-transform: uppercase; letter-spacing: .05em;
}
.vc-total-amount { font-size: 28px; font-weight: 800; }
.vc-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 16px;
}
.vc-summary-card {
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}
.vc-summary-card .label {
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 4px;
}
.vc-summary-card .amount {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
}
.vc-summary-card.total {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.vc-summary-card.total .amount { color: #16a34a; }

.vc-msg-box {
    text-align: center;
    padding: 16px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 12px;
}
.vc-msg-box i { font-size: 20px; color: #f59e0b; margin-bottom: 8px; display: block; }
.vc-msg-box p { margin: 0; font-size: 14px; color: #92400e; font-weight: 500; line-height: 1.5; }

.vc-tachado {
    text-align: center; margin-top: 16px;
}
.vc-tachado .label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
.vc-tachado .amount { font-size: 24px; font-weight: 800; color: #cbd5e1; text-decoration: line-through; }

.vc-contact { text-align: center; margin-top: 20px; }
.vc-contact p { font-size: 12px; color: #94a3b8; margin-bottom: 8px; }
.vc-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: #0f172a; color: #fff;
    padding: 12px 24px; border-radius: 10px;
    font-weight: 600; font-size: 13px;
    text-decoration: none; transition: all .2s;
    border: none; cursor: pointer;
}
.vc-btn:hover { background: #1e293b; color: #fff; text-decoration: none; }

/* ─── Responsivo móvil ─── */
@media (max-width: 576px) {
    .vc-center { padding: 12px; align-items: flex-start; min-height: auto; padding-top: 20px; }
    .vc-header { padding: 20px 16px; }
    .vc-header-icon { width: 52px; height: 52px; }
    .vc-header-icon i { font-size: 22px; }
    .vc-header h3 { font-size: 18px; }
    .vc-header p { font-size: 12px; }
    .vc-body { padding: 20px 16px; }
    .vc-info { padding: 14px 16px; }
    .vc-info-row { gap: 8px; }
    .vc-info-row span, .vc-info-row strong { font-size: 12px; }
    .vc-table-wrap thead th { padding: 8px 10px; font-size: 10px; }
    .vc-table-wrap tbody td { padding: 8px 10px; font-size: 12px; }
    .vc-total-amount { font-size: 22px; }
    .vc-tachado .amount { font-size: 20px; }
    .vc-summary { grid-template-columns: 1fr; }
    .vc-summary-card .amount { font-size: 20px; }
    .vc-btn { padding: 10px 20px; font-size: 12px; width: 100%; justify-content: center; }
    .vc-msg-box p { font-size: 13px; }
    .vc-box { border-radius: 12px; }
}
</style>

<div class="content-wrapper vc-wrapper">
    <section class="content vc-center">
        <div class="vc-card">

            <?php if (is_array($cotizacion) && !empty($cotizacion)): ?>

                <?php if ($_vc_expirada): ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN EXPIRADA
                     ═══════════════════════════════════════ -->
                <div class="vc-box" style="border:1px solid #fecaca">

                    <div class="vc-header" style="background:linear-gradient(135deg,#dc2626 0%,#991b1b 100%)">
                        <div class="vc-header-icon">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <h3>Cotización Expirada</h3>
                        <p>La vigencia de esta cotización ha finalizado</p>
                    </div>

                    <div class="vc-body">
                        <div class="vc-logo">
                            <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo">
                            <h4>COMERCIALIZADORA EGS</h4>
                        </div>

                        <div class="vc-info" style="background:#fef2f2;border:1px solid #fecaca">
                            <div class="vc-info-row">
                                <i class="fa fa-user" style="color:#dc2626"></i>
                                <span>Cliente:</span>
                                <strong><?php echo htmlspecialchars($cotizacion["nombre_cliente"]); ?></strong>
                            </div>
                            <div class="vc-info-row">
                                <i class="fa fa-calendar" style="color:#dc2626"></i>
                                <span>Fecha:</span>
                                <strong><?php echo date("d/m/Y", strtotime($cotizacion["fecha"])); ?></strong>
                            </div>
                            <div class="vc-info-row">
                                <i class="fa fa-clock" style="color:#dc2626"></i>
                                <span>Vigencia:</span>
                                <strong><?php echo htmlspecialchars($cotizacion["vigencia"]); ?></strong>
                            </div>
                            <div class="vc-info-row">
                                <i class="fa fa-calendar-times" style="color:#dc2626"></i>
                                <span>Expiró:</span>
                                <strong style="color:#dc2626"><?php echo $_vc_fechaExpira; ?></strong>
                            </div>
                        </div>

                        <div class="vc-msg-box">
                            <i class="fa fa-info-circle"></i>
                            <p>
                                Los precios y condiciones de esta cotización ya no son válidos.<br>
                                <strong>Es necesario solicitar una nueva cotización</strong> con precios actualizados.
                            </p>
                        </div>

                        <div class="vc-summary">
                            <div class="vc-summary-card">
                                <div class="label">Subtotal</div>
                                <div class="amount">$<?php echo number_format($_vc_subtotal, 2); ?></div>
                            </div>
                            <div class="vc-summary-card">
                                <div class="label">IVA</div>
                                <div class="amount">$<?php echo number_format($_vc_iva, 2); ?></div>
                            </div>
                            <div class="vc-summary-card total">
                                <div class="label">Total cotizado</div>
                                <div class="amount">$<?php echo number_format($_vc_total, 2); ?></div>
                            </div>
                        </div>

                        <div class="vc-contact">
                            <p>Contáctanos para una nueva cotización</p>
                            <a href="inicio" class="vc-btn">
                                <i class="fa fa-arrow-left"></i> Ir al Inicio
                            </a>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN VÁLIDA
                     ═══════════════════════════════════════ -->
                <div class="vc-box" style="border:1px solid #bbf7d0">

                    <div class="vc-header" style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%)">
                        <div class="vc-header-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h3>Cotización Válida</h3>
                        <p>Esta cotización ha sido verificada exitosamente</p>
                    </div>

                    <div class="vc-body">
                        <div class="vc-logo">
                            <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo">
                            <h4>COMERCIALIZADORA EGS</h4>
                        </div>

                        <div class="vc-info" style="background:#f0fdf4;border:1px solid #bbf7d0">
                            <div class="vc-info-row">
                                <i class="fa fa-user" style="color:#16a34a"></i>
                                <span>Cliente:</span>
                                <strong><?php echo htmlspecialchars($cotizacion["nombre_cliente"]); ?></strong>
                            </div>
                            <div class="vc-info-row">
                                <i class="fa fa-calendar" style="color:#16a34a"></i>
                                <span>Fecha:</span>
                                <strong><?php echo date("d/m/Y", strtotime($cotizacion["fecha"])); ?></strong>
                            </div>
                            <div class="vc-info-row">
                                <i class="fa fa-clock" style="color:#16a34a"></i>
                                <span>Vigencia:</span>
                                <strong><?php echo htmlspecialchars($cotizacion["vigencia"]); ?></strong>
                            </div>
                            <?php if ($_vc_fechaExpira): ?>
                            <div class="vc-info-row">
                                <i class="fa fa-calendar-check" style="color:#16a34a"></i>
                                <span>Válida hasta:</span>
                                <strong style="color:#16a34a"><?php echo $_vc_fechaExpira; ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h5 class="vc-productos-title">
                            <i class="fa fa-box" style="color:#16a34a;margin-right:6px"></i> Productos
                        </h5>
                        <div class="vc-table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th style="text-align:center">Cant</th>
                                        <th style="text-align:right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $productos = json_decode($cotizacion["productos"], true);
                                    if (is_array($productos)):
                                        foreach ($productos as $producto):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto["descripcion"]); ?></td>
                                        <td style="text-align:center"><?php echo $producto["cantidad"]; ?></td>
                                        <td style="text-align:right;font-weight:600">$<?php echo number_format($producto["total"], 2); ?></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="vc-summary">
                            <div class="vc-summary-card">
                                <div class="label">Subtotal</div>
                                <div class="amount">$<?php echo number_format($_vc_subtotal, 2); ?></div>
                            </div>
                            <div class="vc-summary-card">
                                <div class="label">IVA</div>
                                <div class="amount">$<?php echo number_format($_vc_iva, 2); ?></div>
                            </div>
                            <div class="vc-summary-card total">
                                <div class="label">Total</div>
                                <div class="amount">$<?php echo number_format($_vc_total, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

            <?php else: ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN NO ENCONTRADA
                     ═══════════════════════════════════════ -->
                <div class="vc-box" style="border:1px solid #e2e8f0">

                    <div class="vc-header" style="background:linear-gradient(135deg,#475569 0%,#1e293b 100%)">
                        <div class="vc-header-icon">
                            <i class="fa fa-times-circle"></i>
                        </div>
                        <h3>Cotización Inválida</h3>
                        <p>No se encontró la cotización</p>
                    </div>

                    <div class="vc-body" style="text-align:center">
                        <div style="padding:20px;background:#f8fafc;border-radius:12px;margin-bottom:20px">
                            <i class="fa fa-search" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:10px"></i>
                            <p style="font-size:14px;color:#64748b;margin:0;line-height:1.5">
                                El código proporcionado no corresponde a ninguna cotización registrada en nuestro sistema.
                            </p>
                        </div>
                        <a href="inicio" class="vc-btn">
                            <i class="fa fa-arrow-left"></i> Ir al Inicio
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>
