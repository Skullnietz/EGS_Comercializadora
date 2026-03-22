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
    if (preg_match('/(\d+)\s*d[ií]as?/i', $vigText, $m)) {
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
?>
<div class="content-wrapper" style="margin-left: 0 !important; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 100vh;">
    <section class="content"
        style="padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 90vh;">

        <div style="width: 100%; max-width: 620px;">

            <?php if (is_array($cotizacion) && !empty($cotizacion)): ?>

                <?php if ($_vc_expirada): ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN EXPIRADA
                     ═══════════════════════════════════════ -->
                <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.1);border:1px solid #fecaca">

                    <!-- Header expirada -->
                    <div style="background:linear-gradient(135deg,#dc2626 0%,#991b1b 100%);padding:28px 30px;text-align:center;color:#fff">
                        <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                            <i class="fa fa-exclamation-triangle" style="font-size:28px"></i>
                        </div>
                        <h3 style="margin:0 0 4px;font-size:22px;font-weight:800">Cotización Expirada</h3>
                        <p style="margin:0;font-size:13px;opacity:.85">La vigencia de esta cotización ha finalizado</p>
                    </div>

                    <div style="padding:28px 30px">
                        <!-- Logo -->
                        <div style="text-align:center;margin-bottom:20px">
                            <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" style="max-height:70px">
                            <h4 style="margin-top:8px;font-weight:700;color:#0f172a;font-size:15px">COMERCIALIZADORA EGS</h4>
                        </div>

                        <!-- Info básica -->
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;margin-bottom:20px">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-user" style="color:#dc2626;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Cliente:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo htmlspecialchars($cotizacion["nombre_cliente"]); ?></strong>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-calendar" style="color:#dc2626;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Fecha:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo date("d/m/Y", strtotime($cotizacion["fecha"])); ?></strong>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-clock" style="color:#dc2626;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Vigencia:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo htmlspecialchars($cotizacion["vigencia"]); ?></strong>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px">
                                <i class="fa fa-calendar-times" style="color:#dc2626;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Expiró:</span>
                                <strong style="font-size:13px;color:#dc2626"><?php echo $_vc_fechaExpira; ?></strong>
                            </div>
                        </div>

                        <!-- Mensaje -->
                        <div style="text-align:center;padding:16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px">
                            <i class="fa fa-info-circle" style="font-size:20px;color:#f59e0b;margin-bottom:8px;display:block"></i>
                            <p style="margin:0;font-size:14px;color:#92400e;font-weight:500;line-height:1.5">
                                Los precios y condiciones de esta cotización ya no son válidos.<br>
                                <strong>Es necesario solicitar una nueva cotización</strong> con precios actualizados.
                            </p>
                        </div>

                        <!-- Monto original (tachado) -->
                        <div style="text-align:center;margin-top:16px">
                            <span style="font-size:12px;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Total cotizado (ya no vigente)</span>
                            <div style="font-size:24px;font-weight:800;color:#cbd5e1;text-decoration:line-through">$<?php echo number_format($cotizacion["total"], 2); ?></div>
                        </div>

                        <!-- Contacto -->
                        <div style="text-align:center;margin-top:20px">
                            <p style="font-size:12px;color:#94a3b8;margin-bottom:8px">Contáctanos para una nueva cotización</p>
                            <a href="inicio" style="display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;padding:12px 24px;border-radius:10px;font-weight:600;font-size:13px;text-decoration:none;transition:all .2s">
                                <i class="fa fa-arrow-left"></i> Ir al Inicio
                            </a>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN VÁLIDA
                     ═══════════════════════════════════════ -->
                <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.1);border:1px solid #bbf7d0">

                    <!-- Header válida -->
                    <div style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);padding:28px 30px;text-align:center;color:#fff">
                        <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                            <i class="fa fa-check-circle" style="font-size:28px"></i>
                        </div>
                        <h3 style="margin:0 0 4px;font-size:22px;font-weight:800">Cotización Válida</h3>
                        <p style="margin:0;font-size:13px;opacity:.85">Esta cotización ha sido verificada exitosamente</p>
                    </div>

                    <div style="padding:28px 30px">
                        <!-- Logo -->
                        <div style="text-align:center;margin-bottom:20px">
                            <img src="vistas/img/plantilla/Captura3.PNG" alt="Logo" style="max-height:70px">
                            <h4 style="margin-top:8px;font-weight:700;color:#0f172a;font-size:15px">COMERCIALIZADORA EGS</h4>
                        </div>

                        <!-- Info -->
                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-user" style="color:#16a34a;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Cliente:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo htmlspecialchars($cotizacion["nombre_cliente"]); ?></strong>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-calendar" style="color:#16a34a;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Fecha:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo date("d/m/Y", strtotime($cotizacion["fecha"])); ?></strong>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <i class="fa fa-clock" style="color:#16a34a;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Vigencia:</span>
                                <strong style="font-size:13px;color:#0f172a"><?php echo htmlspecialchars($cotizacion["vigencia"]); ?></strong>
                            </div>
                            <?php if ($_vc_fechaExpira): ?>
                            <div style="display:flex;align-items:center;gap:10px">
                                <i class="fa fa-calendar-check" style="color:#16a34a;width:16px;text-align:center"></i>
                                <span style="font-size:13px;color:#64748b">Válida hasta:</span>
                                <strong style="font-size:13px;color:#16a34a"><?php echo $_vc_fechaExpira; ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Productos -->
                        <h5 style="font-weight:700;color:#0f172a;font-size:14px;margin-bottom:10px">
                            <i class="fa fa-box" style="color:#16a34a;margin-right:6px"></i> Productos
                        </h5>
                        <div style="border-radius:10px;overflow:hidden;border:1px solid #e2e8f0">
                            <table class="table" style="margin:0;font-size:13px">
                                <thead>
                                    <tr style="background:#f8fafc">
                                        <th style="border:none;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b">Descripción</th>
                                        <th style="border:none;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;text-align:center">Cant</th>
                                        <th style="border:none;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;text-align:right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $productos = json_decode($cotizacion["productos"], true);
                                    if (is_array($productos)):
                                        foreach ($productos as $producto):
                                    ?>
                                    <tr>
                                        <td style="padding:10px 14px;border-top:1px solid #f1f5f9"><?php echo htmlspecialchars($producto["descripcion"]); ?></td>
                                        <td style="padding:10px 14px;border-top:1px solid #f1f5f9;text-align:center"><?php echo $producto["cantidad"]; ?></td>
                                        <td style="padding:10px 14px;border-top:1px solid #f1f5f9;text-align:right;font-weight:600">$<?php echo number_format($producto["total"], 2); ?></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div style="text-align:right;margin-top:16px;padding:16px 20px;background:#f0fdf4;border-radius:12px;border:1px solid #bbf7d0">
                            <span style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.05em">Total</span>
                            <div style="font-size:28px;font-weight:800;color:#16a34a">$<?php echo number_format($cotizacion["total"], 2); ?></div>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

            <?php else: ?>
                <!-- ═══════════════════════════════════════
                     COTIZACIÓN NO ENCONTRADA
                     ═══════════════════════════════════════ -->
                <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.1);border:1px solid #e2e8f0">

                    <div style="background:linear-gradient(135deg,#475569 0%,#1e293b 100%);padding:28px 30px;text-align:center;color:#fff">
                        <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                            <i class="fa fa-times-circle" style="font-size:28px"></i>
                        </div>
                        <h3 style="margin:0 0 4px;font-size:22px;font-weight:800">Cotización Inválida</h3>
                        <p style="margin:0;font-size:13px;opacity:.85">No se encontró la cotización</p>
                    </div>

                    <div style="padding:28px 30px;text-align:center">
                        <div style="padding:20px;background:#f8fafc;border-radius:12px;margin-bottom:20px">
                            <i class="fa fa-search" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:10px"></i>
                            <p style="font-size:14px;color:#64748b;margin:0;line-height:1.5">
                                El código proporcionado no corresponde a ninguna cotización registrada en nuestro sistema.
                            </p>
                        </div>
                        <a href="inicio" style="display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;padding:12px 24px;border-radius:10px;font-weight:600;font-size:13px;text-decoration:none">
                            <i class="fa fa-arrow-left"></i> Ir al Inicio
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>
