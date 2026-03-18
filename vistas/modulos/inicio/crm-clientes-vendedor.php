<?php
/*  ═══════════════════════════════════════════════════
    CRM — Mi Cartera de Clientes (con búsqueda)
    ═══════════════════════════════════════════════════ */

$_cli_idAsesor = isset($_crm_idAsesor) ? $_crm_idAsesor : 0;
$_cli_todos = array();

try {
    $_cli_todos = ControladorClientes::ctrMostrarClientesTabla("id_Asesor", $_cli_idAsesor);
    if (!is_array($_cli_todos)) $_cli_todos = array();
} catch (Exception $e) { $_cli_todos = array(); }

$_cli_total = count($_cli_todos);

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

// Pre-procesar todos los clientes para JSON
$_cli_json = array();
foreach ($_cli_todos as $idx => $cli) {
    $nombre  = isset($cli["nombre"]) ? $cli["nombre"] : "Sin nombre";
    $email   = isset($cli["correo"]) ? $cli["correo"] : (isset($cli["email"]) ? $cli["email"] : "");
    $tel1raw = isset($cli["telefono"]) ? trim($cli["telefono"]) : "";
    $tel2raw = isset($cli["telefonoDos"]) ? trim($cli["telefonoDos"]) : "";

    $t1clean = preg_replace('/\D/', '', $tel1raw);
    $t2clean = preg_replace('/\D/', '', $tel2raw);
    $t1valid = (strlen($t1clean) === 10);
    $t2valid = (strlen($t2clean) === 10) && ($t2clean !== $t1clean);

    $_cli_json[] = array(
        'id'       => isset($cli["id"]) ? $cli["id"] : 0,
        'nombre'   => $nombre,
        'email'    => $email,
        'tel1'     => $tel1raw,
        'tel2'     => $tel2raw,
        'wa1'      => $t1valid ? '52' . $t1clean : '',
        'wa2'      => $t2valid ? '52' . $t2clean : '',
        't1valid'  => $t1valid,
        't2valid'  => $t2valid,
        'iniciales'=> mb_strtoupper(mb_substr($nombre, 0, 2)),
        'grad'     => $_cli_grads[$idx % count($_cli_grads)],
    );
}
?>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-address-book"></i> Mi Cartera</h4>
    <span class="crm-badge" id="cliCountBadge" style="background:#eef2ff;color:#4f46e5"><?php echo $_cli_total; ?> clientes</span>
  </div>

  <?php if ($_cli_total > 0): ?>
  <!-- Search -->
  <div style="padding:12px 18px 0">
    <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;transition:border-color .2s,box-shadow .2s"
         id="cliSearchWrap">
      <i class="fa-solid fa-magnifying-glass" style="color:#94a3b8;font-size:12px;flex-shrink:0"></i>
      <input type="text" id="cliSearchInput" placeholder="Buscar por nombre, teléfono o correo..."
             autocomplete="off"
             style="border:none;outline:none;background:transparent;color:#0f172a;font-size:12px;width:100%;padding:0;margin:0;box-shadow:none">
    </div>
  </div>
  <?php endif; ?>

  <div class="crm-card-body-flush">

    <?php if ($_cli_total === 0): ?>
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

      <div id="cliList" style="max-height:380px;overflow-y:auto">
        <!-- Rendered by JS -->
      </div>

      <div id="cliEmpty" style="display:none">
        <div class="crm-empty" style="padding:24px 16px">
          <i class="fa-solid fa-user-slash" style="font-size:24px"></i>
          <strong>Sin resultados</strong>
          <span style="font-size:12px;color:#94a3b8">Intenta con otro nombre o número</span>
        </div>
      </div>

      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=clientes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
          Ver todos los clientes <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>

    <?php endif; ?>

  </div>
</div>

<?php if ($_cli_total > 0): ?>
<script>
(function(){
  var clientes = <?php echo json_encode($_cli_json, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  var $list    = $('#cliList');
  var $empty   = $('#cliEmpty');
  var $badge   = $('#cliCountBadge');
  var $input   = $('#cliSearchInput');
  var $wrap    = $('#cliSearchWrap');
  var timer    = null;

  function escHtml(s) {
    return $('<span>').text(s).html();
  }

  function renderClients(filtered) {
    if (!filtered.length) {
      $list.hide();
      $empty.show();
      $badge.text('0 resultados');
      return;
    }
    $empty.hide();
    $list.show();
    $badge.text(filtered.length + (filtered.length === clientes.length ? ' clientes' : ' resultado' + (filtered.length > 1 ? 's' : '')));

    var html = '';
    for (var i = 0; i < filtered.length; i++) {
      var c = filtered[i];
      var waMsg = encodeURIComponent('Hola ' + c.nombre + ', le contactamos de EGS. ¿En qué podemos ayudarle?');

      // Info line
      var infoHtml = '';
      if (c.t1valid) {
        infoHtml = '<i class="fa-solid fa-phone" style="font-size:9px;margin-right:2px"></i> ' + escHtml(c.tel1);
        if (c.t2valid) infoHtml += ' &middot; ' + escHtml(c.tel2);
      } else if (c.t2valid) {
        infoHtml = '<i class="fa-solid fa-phone" style="font-size:9px;margin-right:2px"></i> ' + escHtml(c.tel2);
      } else if (c.email) {
        infoHtml = '<i class="fa-solid fa-envelope" style="font-size:9px;margin-right:2px"></i> ' + escHtml(c.email);
      } else {
        infoHtml = '<span style="opacity:.5">Sin datos de contacto</span>';
      }

      // Action buttons
      var actionBtns = '';
      if (c.wa1) {
        actionBtns += '<a href="https://wa.me/' + c.wa1 + '?text=' + waMsg + '" target="_blank" title="WhatsApp ' + escHtml(c.tel1) + '"' +
          ' style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#25d366;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"' +
          ' onmouseover="this.style.background=\'#1da851\';this.style.transform=\'translateY(-1px)\'"' +
          ' onmouseout="this.style.background=\'#25d366\';this.style.transform=\'none\'">' +
          '<i class="fa-brands fa-whatsapp"></i></a>';
      }
      if (c.wa2) {
        actionBtns += '<a href="https://wa.me/' + c.wa2 + '?text=' + waMsg + '" target="_blank" title="WhatsApp 2: ' + escHtml(c.tel2) + '"' +
          ' style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#128c7e;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"' +
          ' onmouseover="this.style.background=\'#0d7a6e\';this.style.transform=\'translateY(-1px)\'"' +
          ' onmouseout="this.style.background=\'#128c7e\';this.style.transform=\'none\'">' +
          '<i class="fa-brands fa-whatsapp"></i></a>';
      }
      // Historial button
      var histUrl = 'index.php?ruta=Historialdecliente&idCliente=' + c.id + '&nombreCliente=' + encodeURIComponent(c.nombre);
      actionBtns += '<a href="' + histUrl + '" target="_blank" title="Historial de ' + escHtml(c.nombre) + '"' +
        ' style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#6366f1;color:#fff;font-size:12px;text-decoration:none;transition:background .15s,transform .15s"' +
        ' onmouseover="this.style.background=\'#4f46e5\';this.style.transform=\'translateY(-1px)\'"' +
        ' onmouseout="this.style.background=\'#6366f1\';this.style.transform=\'none\'">' +
        '<i class="fa-solid fa-clock-rotate-left"></i></a>';

      html += '<div class="crm-client">' +
        '<div class="crm-client-av" style="background:' + c.grad + '">' + escHtml(c.iniciales) + '</div>' +
        '<div style="flex:1;min-width:0">' +
          '<div class="crm-client-name">' + escHtml(c.nombre) + '</div>' +
          '<div class="crm-client-info">' + infoHtml + '</div>' +
        '</div>' +
        '<div style="display:flex;gap:4px;flex-shrink:0">' + actionBtns + '</div>' +
      '</div>';
    }
    $list.html(html);
  }

  // Initial render
  renderClients(clientes);

  // Focus style
  $input.on('focus', function(){ $wrap.css({ borderColor: '#6366f1', boxShadow: '0 0 0 3px rgba(99,102,241,.12)' }); });
  $input.on('blur',  function(){ $wrap.css({ borderColor: '#e2e8f0', boxShadow: 'none' }); });

  // Search
  $input.on('input', function(){
    clearTimeout(timer);
    var q = $(this).val().trim().toLowerCase();
    timer = setTimeout(function(){
      if (!q) { renderClients(clientes); return; }
      var filtered = clientes.filter(function(c){
        return c.nombre.toLowerCase().indexOf(q) > -1 ||
               c.email.toLowerCase().indexOf(q) > -1 ||
               c.tel1.indexOf(q) > -1 ||
               c.tel2.indexOf(q) > -1;
      });
      renderClients(filtered);
    }, 120);
  });

})();
</script>
<?php endif; ?>
