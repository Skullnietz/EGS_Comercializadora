<?php
/**
 * Vista pública del Monedero EGS (Dinero Electrónico)
 * Accesible sin login mediante token único desde el QR del ticket.
 * URL: /monedero.php?token=XXXXX
 */

require_once __DIR__ . "/config/env.php";
require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/modelos/recompensas.modelo.php";
require_once __DIR__ . "/controladores/recompensas.controlador.php";

$token = isset($_GET["token"]) ? trim($_GET["token"]) : '';

if (empty($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $error = true;
    $errorMsg = "Enlace inválido. Escanea el código QR de tu ticket para acceder a tu monedero.";
} else {
    $data = ControladorRecompensas::ctrObtenerMonederoPorToken($token);
    if (!$data) {
        $error = true;
        $errorMsg = "No se encontró el monedero. Verifica que el código QR sea correcto.";
    } else {
        $error = false;
        $saldo = $data["saldo"];
        $movimientos = $data["movimientos"];
        $entregadas = $data["entregadas"];
        $porcentaje = $data["porcentaje"];
        $nombreCliente = $data["nombre_cliente"];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monedero EGS - Dinero Electr&oacute;nico</title>
    <link rel="icon" href="https://backend.comercializadoraegs.com/vistas/img/perfiles/651.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}

        .wallet-wrap{max-width:420px;width:100%}

        /* ── Tarjeta principal (simula tarjeta física) ── */
        .credit-card{
            position:relative;
            background:linear-gradient(135deg,#1e3a5f 0%,#0d253f 40%,#1a1a2e 100%);
            border-radius:20px;
            padding:28px 24px 24px;
            color:#fff;
            box-shadow:0 20px 60px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.08);
            overflow:hidden;
            margin-bottom:16px;
        }
        .credit-card::before{
            content:'';position:absolute;top:-60%;right:-30%;width:300px;height:300px;
            background:radial-gradient(circle,rgba(99,102,241,.25) 0%,transparent 70%);
            pointer-events:none;
        }
        .credit-card::after{
            content:'';position:absolute;bottom:-40%;left:-20%;width:250px;height:250px;
            background:radial-gradient(circle,rgba(16,185,129,.15) 0%,transparent 70%);
            pointer-events:none;
        }
        .card-top{display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1;margin-bottom:24px}
        .card-logo{width:48px;height:48px;border-radius:12px;object-fit:contain;background:rgba(255,255,255,.12);padding:4px}
        .card-brand{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2.5px;color:rgba(255,255,255,.6)}

        .card-chip{position:relative;z-index:1;margin-bottom:20px}
        .chip-icon{width:44px;height:32px;background:linear-gradient(135deg,#daa520,#f0c060,#daa520);border-radius:6px;display:flex;align-items:center;justify-content:center}
        .chip-icon::after{content:'';width:20px;height:16px;border:1.5px solid rgba(139,90,0,.4);border-radius:3px}

        .card-saldo{position:relative;z-index:1;margin-bottom:20px}
        .card-saldo-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:2px;color:rgba(255,255,255,.5);margin-bottom:6px}
        .card-saldo-amount{font-size:42px;font-weight:900;letter-spacing:-1px;line-height:1;
            background:linear-gradient(135deg,#fff 0%,#a5b4fc 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .card-saldo-amount.zero{background:linear-gradient(135deg,#64748b 0%,#475569 100%);-webkit-background-clip:text;background-clip:text}

        .card-bottom{display:flex;align-items:flex-end;justify-content:space-between;position:relative;z-index:1}
        .card-holder-label{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.4);margin-bottom:3px}
        .card-holder-name{font-size:14px;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
        .card-nivel{text-align:right}
        .card-nivel-badge{
            display:inline-flex;align-items:center;gap:5px;
            background:rgba(99,102,241,.3);backdrop-filter:blur(8px);
            border:1px solid rgba(99,102,241,.4);
            padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;color:#c7d2fe;
        }
        .card-nivel-dot{width:7px;height:7px;border-radius:50%;background:#818cf8;box-shadow:0 0 8px rgba(129,140,248,.6)}
        .nivel-2 .card-nivel-dot{background:#a78bfa;box-shadow:0 0 8px rgba(167,139,250,.6)}
        .nivel-3 .card-nivel-dot{background:#34d399;box-shadow:0 0 8px rgba(52,211,153,.6)}

        .contactless{position:absolute;bottom:24px;right:24px;z-index:1;opacity:.3}
        .contactless svg{width:24px;height:24px}

        /* ── Panel de info debajo de la tarjeta ── */
        .panel{background:#1e293b;border-radius:20px;padding:0;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.3);margin-bottom:16px}

        .stats-grid{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid rgba(255,255,255,.06)}
        .stat-item{padding:20px;text-align:center;position:relative}
        .stat-item+.stat-item{border-left:1px solid rgba(255,255,255,.06)}
        .stat-value{font-size:26px;font-weight:900;color:#fff;line-height:1}
        .stat-label{font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:1.2px;color:#64748b;margin-top:4px}

        /* ── Niveles ── */
        .levels-section{padding:20px}
        .section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;margin-bottom:14px}
        .level-row{display:flex;align-items:center;justify-content:space-between;padding:11px 14px;border-radius:12px;margin-bottom:6px;font-size:13px;transition:all .2s}
        .level-row.active{background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3)}
        .level-row:not(.active){background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05)}
        .level-name{font-weight:600;color:#cbd5e1}
        .level-pct{font-weight:800;color:#818cf8;font-size:14px}
        .level-check{color:#34d399;font-weight:800;font-size:15px}

        /* ── Movimientos ── */
        .movimientos-section{padding:0 20px 20px}
        .mov-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
        .mov-count{font-size:10px;color:#475569;font-weight:500}
        .mov-item{display:flex;align-items:center;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.04)}
        .mov-item:last-child{border-bottom:none}
        .mov-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;margin-right:12px;flex-shrink:0}
        .mov-icon.acum{background:rgba(16,185,129,.15);color:#34d399}
        .mov-icon.canje{background:rgba(99,102,241,.15);color:#818cf8}
        .mov-icon.exp{background:rgba(239,68,68,.15);color:#f87171}
        .mov-info{flex:1;min-width:0}
        .mov-desc{font-size:12px;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .mov-fecha{font-size:10px;color:#475569;margin-top:2px}
        .mov-monto{font-size:14px;font-weight:800;white-space:nowrap;margin-left:10px}
        .mov-monto.pos{color:#34d399}
        .mov-monto.neg{color:#f87171}
        .vence-tag{display:inline-block;background:rgba(245,158,11,.15);color:#fbbf24;padding:2px 7px;border-radius:4px;font-size:8px;font-weight:600;margin-left:4px}

        .empty-state{text-align:center;padding:30px 20px;color:#475569}
        .empty-state .icon{font-size:40px;margin-bottom:10px;opacity:.5}
        .empty-state h3{font-size:14px;color:#64748b;margin-bottom:6px;font-weight:600}
        .empty-state p{font-size:12px;line-height:1.5;color:#475569}

        /* ── Footer ── */
        .footer-bar{text-align:center;padding:12px;opacity:.5}
        .footer-bar a{color:#818cf8;text-decoration:none;font-weight:600;font-size:12px}
        .footer-bar p{font-size:9px;color:#475569;margin-top:4px}

        /* ── Error ── */
        .error-wrap{max-width:420px;width:100%}
        .error-card-dark{background:#1e293b;border-radius:20px;padding:40px 28px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.4)}
        .error-card-dark .err-logo{width:56px;height:56px;border-radius:16px;object-fit:contain;background:rgba(255,255,255,.08);padding:6px;margin-bottom:20px}
        .error-card-dark .err-icon{font-size:48px;margin-bottom:16px}
        .error-card-dark h2{font-size:18px;color:#f1f5f9;font-weight:800;margin-bottom:8px}
        .error-card-dark p{font-size:13px;color:#64748b;line-height:1.6}
        .error-card-dark .err-footer{margin-top:24px;padding-top:16px;border-top:1px solid rgba(255,255,255,.06)}
        .error-card-dark .err-footer a{color:#818cf8;text-decoration:none;font-weight:600;font-size:13px}
    </style>
</head>
<body>

<?php if ($error): ?>
<div class="error-wrap">
    <div class="error-card-dark">
        <img src="https://backend.comercializadoraegs.com/vistas/img/perfiles/651.png" class="err-logo" alt="EGS">
        <div class="err-icon">&#128683;</div>
        <h2>Acceso no disponible</h2>
        <p><?php echo htmlspecialchars($errorMsg); ?></p>
        <div class="err-footer">
            <a href="https://comercializadoraegs.com">comercializadoraegs.com</a>
        </div>
    </div>
</div>

<?php else: ?>
<div class="wallet-wrap">

    <!-- ═══ TARJETA DIGITAL ═══ -->
    <div class="credit-card">
        <div class="card-top">
            <img src="https://backend.comercializadoraegs.com/vistas/img/perfiles/651.png" class="card-logo" alt="EGS">
            <span class="card-brand">Dinero Electr&oacute;nico</span>
        </div>
        <div class="card-chip"><div class="chip-icon"></div></div>
        <div class="card-saldo">
            <div class="card-saldo-label">Saldo disponible</div>
            <div class="card-saldo-amount <?php echo $saldo <= 0 ? 'zero' : ''; ?>">$<?php echo number_format($saldo, 2); ?></div>
        </div>
        <div class="card-bottom">
            <div>
                <div class="card-holder-label">Titular</div>
                <div class="card-holder-name"><?php echo htmlspecialchars($nombreCliente); ?></div>
            </div>
            <div class="card-nivel nivel-<?php echo $porcentaje; ?>">
                <div class="card-nivel-badge">
                    <span class="card-nivel-dot"></span>
                    Nivel <?php echo $porcentaje; ?>%
                </div>
            </div>
        </div>
        <div class="contactless"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 17.5a8.5 8.5 0 0 1 0-11"/><path d="M10 15a5 5 0 0 1 0-6"/><path d="M13.5 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" fill="currentColor" stroke="none"/></svg></div>
    </div>

    <!-- ═══ PANEL DE DETALLES ═══ -->
    <div class="panel">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo $entregadas; ?></div>
                <div class="stat-label">&Oacute;rdenes entregadas</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $porcentaje; ?>%</div>
                <div class="stat-label">Tu recompensa</div>
            </div>
        </div>

        <div class="levels-section">
            <div class="section-title">Niveles de recompensa</div>
            <div class="level-row <?php echo $porcentaje == 1 ? 'active' : ''; ?>">
                <span class="level-name">Nivel Inicial</span>
                <span class="level-pct">1%</span>
                <?php if ($porcentaje >= 1): ?><span class="level-check">&#10003;</span><?php endif; ?>
            </div>
            <div class="level-row <?php echo $porcentaje == 2 ? 'active' : ''; ?>">
                <span class="level-name">+3 &oacute;rdenes entregadas</span>
                <span class="level-pct">2%</span>
                <?php if ($porcentaje >= 2): ?><span class="level-check">&#10003;</span><?php endif; ?>
            </div>
            <div class="level-row <?php echo $porcentaje == 3 ? 'active' : ''; ?>">
                <span class="level-name">+5 &oacute;rdenes entregadas</span>
                <span class="level-pct">3%</span>
                <?php if ($porcentaje >= 3): ?><span class="level-check">&#10003;</span><?php endif; ?>
            </div>
        </div>

        <div class="movimientos-section">
            <div class="mov-header">
                <span class="section-title" style="margin:0">&Uacute;ltimos movimientos</span>
                <span class="mov-count"><?php echo count($movimientos); ?> registro<?php echo count($movimientos) != 1 ? 's' : ''; ?></span>
            </div>

            <?php if (empty($movimientos)): ?>
            <div class="empty-state">
                <div class="icon">&#128176;</div>
                <h3>Sin movimientos a&uacute;n</h3>
                <p>Cuando se entregue tu primera orden, aqu&iacute; ver&aacute;s tu dinero electr&oacute;nico acumulado.</p>
            </div>
            <?php else: ?>
            <?php foreach ($movimientos as $mov): ?>
            <div class="mov-item">
                <div class="mov-icon <?php echo $mov["tipo"] == 'acumulacion' ? 'acum' : ($mov["tipo"] == 'canje' ? 'canje' : 'exp'); ?>">
                    <?php echo $mov["tipo"] == 'acumulacion' ? '&#8593;' : ($mov["tipo"] == 'canje' ? '&#8595;' : '&#8635;'); ?>
                </div>
                <div class="mov-info">
                    <div class="mov-desc"><?php echo htmlspecialchars($mov["descripcion"]); ?></div>
                    <div class="mov-fecha">
                        <?php echo date('d/m/Y H:i', strtotime($mov["fecha"])); ?>
                        <?php if ($mov["tipo"] == 'acumulacion' && !empty($mov["fecha_expiracion"]) && !$mov["expirado"]): ?>
                        <span class="vence-tag">Vence: <?php echo date('d/m/Y', strtotime($mov["fecha_expiracion"])); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mov-monto <?php echo floatval($mov["monto"]) >= 0 ? 'pos' : 'neg'; ?>">
                    <?php echo floatval($mov["monto"]) >= 0 ? '+' : ''; ?>$<?php echo number_format(abs(floatval($mov["monto"])), 2); ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bar">
        <a href="https://comercializadoraegs.com">comercializadoraegs.com</a>
        <p>Comercializadora EGS &middot; Tu dinero electr&oacute;nico vence cada 6 meses</p>
    </div>
</div>
<?php endif; ?>

</body>
</html>
