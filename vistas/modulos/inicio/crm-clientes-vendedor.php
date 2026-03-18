<?php
/*  ═══════════════════════════════════════════════════
    CRM — Mi Cartera de Clientes
    ═══════════════════════════════════════════════════ */

$_cli_idAsesor = isset($_crm_idAsesor) ? $_crm_idAsesor : 0;
$_cli_todos = array();

try {
    $_cli_todos = ControladorClientes::ctrMostrarClientesTabla("id_Asesor", $_cli_idAsesor);
    if (!is_array($_cli_todos)) $_cli_todos = array();
} catch (Exception $e) { $_cli_todos = array(); }

$_cli_total = count($_cli_todos);
$_cli_mostrar = array_slice($_cli_todos, 0, 8);

$_cli_grads = array(
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
);
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-address-book"></i> Mi Cartera</h4>
    <span class="crm-badge" style="background:#eef2ff;color:#4f46e5"><?php echo $_cli_total; ?> clientes</span>
  </div>
  <div class="crm-card-body-flush">

    <?php if (empty($_cli_mostrar)): ?>
      <div class="crm-card-body">
        <div class="crm-empty">
          <i class="fa-solid fa-users"></i>
          <strong>Sin clientes asignados</strong>
          <a href="index.php?ruta=clientes" style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none">
            <i class="fa-solid fa-plus"></i> Agregar cliente
          </a>
        </div>
      </div>
    <?php else: ?>

      <?php foreach ($_cli_mostrar as $idx => $cli):
        $nombre = isset($cli["nombre"]) ? $cli["nombre"] : "Sin nombre";
        $email = isset($cli["correo"]) ? $cli["correo"] : (isset($cli["email"]) ? $cli["email"] : "");
        $tel1raw = isset($cli["telefono"]) ? trim($cli["telefono"]) : "";
        $tel2raw = isset($cli["telefonoDos"]) ? trim($cli["telefonoDos"]) : "";
        $iniciales = mb_strtoupper(mb_substr($nombre, 0, 2));
        $grad = $_cli_grads[$idx % count($_cli_grads)];

        // Validar 10 dígitos
        $t1clean = preg_replace('/\D/', '', $tel1raw);
        $t2clean = preg_replace('/\D/', '', $tel2raw);
        $t1valid = (strlen($t1clean) === 10);
        $t2valid = (strlen($t2clean) === 10) && ($t2clean !== $t1clean);

        // Texto a mostrar
        $telDisplay = '';
        if ($t1valid) $telDisplay = $tel1raw;
        elseif ($t2valid) $telDisplay = $tel2raw;

        // WhatsApp links
        $wa1 = $t1valid ? '52' . $t1clean : '';
        $wa2 = $t2valid ? '52' . $t2clean : '';
        $waCliMsg = urlencode("Hola " . $nombre . ", le contactamos de EGS. ¿En qué podemos ayudarle?");
      ?>
        <div class="crm-client">
          <div class="crm-client-av" style="background:<?php echo $grad; ?>">
            <?php echo $iniciales; ?>
          </div>
          <div style="flex:1;min-width:0">
            <div class="crm-client-name"><?php echo htmlspecialchars($nombre); ?></div>
            <div class="crm-client-info">
              <?php if (!empty($telDisplay)): ?>
                <i class="fa-solid fa-phone" style="font-size:9px;margin-right:2px"></i> <?php echo htmlspecialchars($telDisplay); ?>
                <?php if ($t1valid && $t2valid): ?>
                  &middot; <?php echo htmlspecialchars($tel2raw); ?>
                <?php endif; ?>
              <?php elseif (!empty($email)): ?>
                <i class="fa-solid fa-envelope" style="font-size:9px;margin-right:2px"></i> <?php echo htmlspecialchars($email); ?>
              <?php else: ?>
                Sin datos de contacto
              <?php endif; ?>
            </div>
          </div>
          <div style="display:flex;gap:4px;flex-shrink:0">
            <?php if (!empty($wa1)): ?>
              <a href="https://wa.me/<?php echo $wa1; ?>?text=<?php echo $waCliMsg; ?>" target="_blank" title="WhatsApp <?php echo $tel1raw; ?>"
                 style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#25d366;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"
                 onmouseover="this.style.background='#1da851';this.style.transform='translateY(-1px)'"
                 onmouseout="this.style.background='#25d366';this.style.transform='none'">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            <?php endif; ?>
            <?php if (!empty($wa2)): ?>
              <a href="https://wa.me/<?php echo $wa2; ?>?text=<?php echo $waCliMsg; ?>" target="_blank" title="WhatsApp 2: <?php echo $tel2raw; ?>"
                 style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#128c7e;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"
                 onmouseover="this.style.background='#0d7a6e';this.style.transform='translateY(-1px)'"
                 onmouseout="this.style.background='#128c7e';this.style.transform='none'">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if ($_cli_total > 8): ?>
        <div style="text-align:center;padding:14px;border-top:1px solid #f1f5f9">
          <a href="index.php?ruta=clientes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
            Ver los <?php echo $_cli_total; ?> clientes <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
          </a>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>
