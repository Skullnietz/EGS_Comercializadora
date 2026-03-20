<?php
/*  ═══════════════════════════════════════════════════
    NOTIFICACIONES — Bell dropdown + Push controlado
    ═══════════════════════════════════════════════════
    FIX: Push.create() solo se dispara 1 vez por sesión
    del navegador, máximo 3 órdenes, solo del último mes.
    ═══════════════════════════════════════════════════ */

date_default_timezone_set("America/Mexico_City");

$_noti_ordenes = array();
$_noti_perfil  = $_SESSION["perfil"];
$_noti_limite6m = date("Y-m-d", strtotime("-6 months"));
$_noti_limite1m = date("Y-m-d", strtotime("-1 month"));

// ── Admin: todas las órdenes de la empresa ──
if ($_noti_perfil == "administrador") {

    $_noti_raw = controladorOrdenes::ctrMostrarOrdenes("id_empresa", $_SESSION["empresa"]);
    if (is_array($_noti_raw)) {
        foreach ($_noti_raw as $o) {
            $est = isset($o["estado"]) ? $o["estado"] : "";
            $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
            // Solo órdenes no entregadas/canceladas/SR/PV y de los últimos 6 meses
            if (strpos($est, "Ent") === false && strpos($est, "can") === false
                && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                && $fi >= $_noti_limite6m) {
                // Verificar si tiene +5 días (atraso)
                if (strtotime($fi . "+ 5 days") <= time()) {
                    $_noti_ordenes[] = $o;
                }
            }
        }
    }

// ── Vendedor: órdenes del asesor ──
} elseif ($_noti_perfil == "vendedor") {

    try {
        $Asesores = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
        if (is_array($Asesores) && isset($Asesores["id"])) {
            $_noti_raw = controladorOrdenes::ctrMostrarOrdenesDelAsesor($Asesores["id"]);
            if (is_array($_noti_raw)) {
                foreach ($_noti_raw as $o) {
                    $est = isset($o["estado"]) ? $o["estado"] : "";
                    $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
                    if (strpos($est, "Ent") === false && strpos($est, "can") === false
                        && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                        && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                        && $fi >= $_noti_limite6m) {
                        if (strtotime($fi . "+ 5 days") <= time()) {
                            $_noti_ordenes[] = $o;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {}

// ── Técnico: órdenes del técnico ──
} elseif ($_noti_perfil == "tecnico") {

    try {
        $tecnico = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
        if (is_array($tecnico) && isset($tecnico["id"])) {
            $ordenesDelTecnico = controladorOrdenes::ctrMostrarOrdenesDelTecncio($tecnico["id"]);
            if (is_array($ordenesDelTecnico)) {
                foreach ($ordenesDelTecnico as $o) {
                    $est = isset($o["estado"]) ? $o["estado"] : "";
                    $fi  = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
                    if (strpos($est, "Ent") === false
                        && strpos($est, "can") === false
                        && strpos($est, "AUT") === false
                        && stripos($est, "sin reparación") === false && strpos($est, "SR") === false
                        && stripos($est, "producto para venta") === false && strpos($est, "PV") === false
                        && $fi >= $_noti_limite6m
                    ) {
                        if (strtotime($fi . "+ 5 days") <= time()) {
                            $_noti_ordenes[] = $o;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {}
}

// Ordenar: más recientes primero
usort($_noti_ordenes, function($a, $b) {
    return strtotime(isset($b["fecha_ingreso"]) ? $b["fecha_ingreso"] : "now")
         - strtotime(isset($a["fecha_ingreso"]) ? $a["fecha_ingreso"] : "now");
});

// Limitar dropdown a 20 máximo
$_noti_mostrar = array_slice($_noti_ordenes, 0, 20);
$_noti_total = count($_noti_ordenes);

// Para push: solo las del último mes, máx 3
$_noti_push = array();
foreach ($_noti_ordenes as $o) {
    $fi = isset($o["fecha_ingreso"]) ? $o["fecha_ingreso"] : "";
    if ($fi >= $_noti_limite1m) {
        $_noti_push[] = $o;
        if (count($_noti_push) >= 3) break;
    }
}
?>

<!-- notifications-menu -->
<li class="dropdown notifications-menu">

  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa-solid fa-bell"></i>
    <?php if ($_noti_total > 0): ?>
      <span class="label label-warning"><?php echo $_noti_total > 99 ? '99+' : $_noti_total; ?></span>
    <?php endif; ?>
  </a>

  <ul class="dropdown-menu">

    <li class="header" style="font-size:12px;font-weight:600">
      <?php if ($_noti_total > 0): ?>
        <?php echo $_noti_total; ?> orden<?php echo $_noti_total > 1 ? 'es' : ''; ?> con atraso
      <?php else: ?>
        Sin notificaciones pendientes
      <?php endif; ?>
    </li>

    <?php if ($_noti_total > 0): ?>
    <li>
      <ul class="menu">
        <?php foreach ($_noti_mostrar as $o):
          $dias = max(0, floor((time() - strtotime($o["fecha_ingreso"])) / 86400));
          $urgColor = $dias >= 30 ? '#ef4444' : ($dias >= 15 ? '#f59e0b' : '#3b82f6');
        ?>
        <li>
          <a class="btnVerInfoOrden"
             idOrden="<?php echo $o["id"]; ?>"
             cliente="<?php echo isset($o["id_usuario"]) ? $o["id_usuario"] : ''; ?>"
             tecnico="<?php echo isset($o["id_tecnico"]) ? $o["id_tecnico"] : ''; ?>"
             asesor="<?php echo isset($o["id_Asesor"]) ? $o["id_Asesor"] : ''; ?>"
             empresa="<?php echo isset($o["id_empresa"]) ? $o["id_empresa"] : ''; ?>"
             pedido="<?php echo isset($o["id_pedido"]) ? $o["id_pedido"] : ''; ?>"
             tecnicodos="<?php echo isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : ''; ?>"
             item="nuevasVisitas"
             style="display:flex;align-items:center;gap:8px;padding:8px 12px">
            <span style="width:8px;height:8px;border-radius:50%;background:<?php echo $urgColor; ?>;flex-shrink:0"></span>
            <span style="flex:1">
              <strong>#<?php echo $o["id"]; ?></strong> Atraso de entrega
              <small style="display:block;color:#94a3b8;font-size:11px"><?php echo $dias; ?> días — <?php echo date("d/m/Y", strtotime($o["fecha_ingreso"])); ?></small>
            </span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </li>
    <?php endif; ?>

    <li class="footer">
      <a href="index.php?ruta=ordenesnew" style="font-size:12px">Ver todas las órdenes</a>
    </li>

  </ul>

</li>
<!-- /notifications-menu -->

<?php if (!empty($_noti_push)): ?>
<!-- Push notifications: SOLO 1 vez por sesión del navegador -->
<script>
(function(){
  // Clave única por sesión — solo disparar 1 vez
  var pushKey = 'egs_push_shown_' + <?php echo json_encode(session_id()); ?>;

  if (sessionStorage.getItem(pushKey)) return; // Ya se mostró esta sesión

  // Marcar como mostrado INMEDIATAMENTE
  sessionStorage.setItem(pushKey, '1');

  // Esperar 3s después de cargar la página
  setTimeout(function(){
    if (typeof Push === 'undefined') return;

    <?php
    // Solo la primera orden para push (no bombardear)
    $pushOrd = $_noti_push[0];
    $pushImg = '';
    $album = json_decode(isset($pushOrd["multimedia"]) ? $pushOrd["multimedia"] : '[]', true);
    if (is_array($album)) {
        foreach ($album as $img) {
            if (isset($img["foto"])) { $pushImg = $img["foto"]; break; }
        }
    }
    ?>

    try {
      Push.create("Tienes <?php echo count($_noti_push); ?> orden<?php echo count($_noti_push) > 1 ? 'es' : ''; ?> con atraso", {
        body: "Orden #<?php echo $pushOrd['id']; ?><?php echo count($_noti_push) > 1 ? ' y ' . (count($_noti_push) - 1) . ' más' : ''; ?> — Requiere atención",
        <?php if (!empty($pushImg)): ?>
        icon: "<?php echo $pushImg; ?>",
        <?php endif; ?>
        timeout: 8000,
        onClick: function(){
          window.focus();
          window.location = "index.php?ruta=inicio";
          this.close();
        }
      });
    } catch(e) {}

  }, 3000);
})();
</script>
<?php endif; ?>
