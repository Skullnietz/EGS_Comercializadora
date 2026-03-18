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
        $tel = isset($cli["telefono"]) ? $cli["telefono"] : "";
        $iniciales = mb_strtoupper(mb_substr($nombre, 0, 2));
        $grad = $_cli_grads[$idx % count($_cli_grads)];
      ?>
        <div class="crm-client">
          <div class="crm-client-av" style="background:<?php echo $grad; ?>">
            <?php echo $iniciales; ?>
          </div>
          <div style="flex:1;min-width:0">
            <div class="crm-client-name"><?php echo htmlspecialchars($nombre); ?></div>
            <div class="crm-client-info">
              <?php if (!empty($tel)): ?>
                <i class="fa-solid fa-phone" style="font-size:9px;margin-right:2px"></i> <?php echo htmlspecialchars($tel); ?>
              <?php elseif (!empty($email)): ?>
                <i class="fa-solid fa-envelope" style="font-size:9px;margin-right:2px"></i> <?php echo htmlspecialchars($email); ?>
              <?php else: ?>
                Sin datos de contacto
              <?php endif; ?>
            </div>
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
