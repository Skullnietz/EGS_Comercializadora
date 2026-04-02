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
    <title>Monedero EGS - Dinero Electrónico</title>
    <link rel="icon" href="extensiones/tcpdf/pdf/images/logoEGS (1).png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,.2);
            max-width: 420px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            color: #fff;
            padding: 28px 24px 20px;
            text-align: center;
        }
        .card-header .logo { width: 60px; margin-bottom: 12px; filter: brightness(0) invert(1); }
        .card-header h1 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .card-header p { font-size: 13px; opacity: .85; }
        .card-header .nombre { font-size: 15px; font-weight: 600; margin-top: 10px; background: rgba(255,255,255,.15); padding: 6px 16px; border-radius: 20px; display: inline-block; }

        .saldo-section {
            text-align: center;
            padding: 30px 24px 20px;
        }
        .saldo-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
        }
        .saldo-amount {
            font-size: 48px;
            font-weight: 900;
            color: #16a34a;
            margin: 6px 0;
            line-height: 1;
        }
        .saldo-amount.zero { color: #94a3b8; }
        .nivel-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #4338ca;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .nivel-badge .dot { width: 8px; height: 8px; border-radius: 50%; }
        .nivel-1 { background: #dbeafe; }
        .nivel-2 { background: #c7d2fe; }
        .nivel-3 { background: #a5b4fc; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 0 24px 20px;
        }
        .info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px;
            text-align: center;
        }
        .info-item .value { font-size: 22px; font-weight: 800; color: #1e293b; }
        .info-item .label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }

        .levels-section {
            padding: 0 24px 20px;
        }
        .levels-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .level-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .level-row.active { background: #eef2ff; border: 1.5px solid #6366f1; }
        .level-row:not(.active) { background: #f8fafc; border: 1px solid #e2e8f0; }
        .level-row .level-name { font-weight: 600; color: #1e293b; }
        .level-row .level-pct { font-weight: 800; color: #6366f1; }
        .level-row .level-check { color: #16a34a; font-weight: 800; }

        .movimientos-section {
            padding: 0 24px 24px;
        }
        .movimientos-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .mov-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .mov-item:last-child { border-bottom: none; }
        .mov-icon {
            width: 34px; height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .mov-icon.acum { background: #dcfce7; color: #16a34a; }
        .mov-icon.canje { background: #dbeafe; color: #2563eb; }
        .mov-icon.exp { background: #fef2f2; color: #dc2626; }
        .mov-info { flex: 1; min-width: 0; }
        .mov-desc { font-size: 12px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mov-fecha { font-size: 10px; color: #94a3b8; margin-top: 1px; }
        .mov-monto { font-size: 14px; font-weight: 800; white-space: nowrap; margin-left: 8px; }
        .mov-monto.pos { color: #16a34a; }
        .mov-monto.neg { color: #dc2626; }

        .empty-state {
            text-align: center;
            padding: 30px 24px;
            color: #94a3b8;
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
        .empty-state h3 { font-size: 16px; color: #64748b; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; line-height: 1.5; }

        .footer {
            text-align: center;
            padding: 16px 24px 24px;
            border-top: 1px solid #f1f5f9;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        .footer p { font-size: 10px; color: #94a3b8; margin-top: 6px; }
        .vence-tag {
            display: inline-block;
            background: #fffbeb;
            color: #92400e;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 2px;
        }

        .error-card {
            text-align: center;
            padding: 40px 24px;
        }
        .error-card .icon { font-size: 56px; margin-bottom: 16px; }
        .error-card h2 { font-size: 18px; color: #1e293b; margin-bottom: 8px; }
        .error-card p { font-size: 14px; color: #64748b; line-height: 1.5; }
    </style>
</head>
<body>
<div class="card">
    <?php if ($error): ?>
    <div class="card-header">
        <h1>Monedero EGS</h1>
        <p>Dinero Electrónico</p>
    </div>
    <div class="error-card">
        <div class="icon">&#128683;</div>
        <h2>Acceso no disponible</h2>
        <p><?php echo htmlspecialchars($errorMsg); ?></p>
    </div>
    <div class="footer">
        <a href="https://comercializadoraegs.com">comercializadoraegs.com</a>
        <p>Comercializadora EGS</p>
    </div>

    <?php else: ?>
    <div class="card-header">
        <img src="extensiones/tcpdf/pdf/images/logoEGS (1).png" class="logo" alt="EGS">
        <h1>Monedero EGS</h1>
        <p>Tu tarjeta de dinero electrónico</p>
        <div class="nombre"><?php echo htmlspecialchars($nombreCliente); ?></div>
    </div>

    <div class="saldo-section">
        <div class="saldo-label">Saldo disponible</div>
        <div class="saldo-amount <?php echo $saldo <= 0 ? 'zero' : ''; ?>">
            $<?php echo number_format($saldo, 2); ?>
        </div>
        <div class="nivel-badge">
            <span class="dot nivel-<?php echo $porcentaje; ?>"></span>
            Nivel <?php echo $porcentaje; ?>% &middot; <?php echo $entregadas; ?> orden<?php echo $entregadas != 1 ? 'es' : ''; ?> entregada<?php echo $entregadas != 1 ? 's' : ''; ?>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="value"><?php echo $entregadas; ?></div>
            <div class="label">Órdenes entregadas</div>
        </div>
        <div class="info-item">
            <div class="value"><?php echo $porcentaje; ?>%</div>
            <div class="label">Tu recompensa</div>
        </div>
    </div>

    <div class="levels-section">
        <div class="levels-title">Niveles de recompensa</div>
        <div class="level-row <?php echo $porcentaje == 1 ? 'active' : ''; ?>">
            <span class="level-name">Nivel Inicial</span>
            <span class="level-pct">1%</span>
            <?php if ($porcentaje == 1): ?><span class="level-check">&#10003;</span><?php endif; ?>
        </div>
        <div class="level-row <?php echo $porcentaje == 2 ? 'active' : ''; ?>">
            <span class="level-name">+3 órdenes entregadas</span>
            <span class="level-pct">2%</span>
            <?php if ($porcentaje >= 2): ?><span class="level-check">&#10003;</span><?php endif; ?>
        </div>
        <div class="level-row <?php echo $porcentaje == 3 ? 'active' : ''; ?>">
            <span class="level-name">+5 órdenes entregadas</span>
            <span class="level-pct">3%</span>
            <?php if ($porcentaje >= 3): ?><span class="level-check">&#10003;</span><?php endif; ?>
        </div>
    </div>

    <div class="movimientos-section">
        <div class="movimientos-title">
            <span>Últimos movimientos</span>
            <span style="font-size:10px;color:#94a3b8;font-weight:500"><?php echo count($movimientos); ?> registro<?php echo count($movimientos) != 1 ? 's' : ''; ?></span>
        </div>

        <?php if (empty($movimientos)): ?>
        <div class="empty-state" style="padding:20px 0">
            <div class="icon">&#128176;</div>
            <h3>Sin movimientos aún</h3>
            <p>Cuando se entregue tu primera orden, aquí verás tu dinero electrónico acumulado.</p>
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

    <div class="footer">
        <a href="https://comercializadoraegs.com">comercializadoraegs.com</a>
        <p>Comercializadora EGS &middot; Tu dinero electrónico vence cada 6 meses</p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
