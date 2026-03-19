<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMIN — Equipo (Asesores + Tecnicos, estilo CRM)
    ═══════════════════════════════════════════════════ */

$_eq_item = "id_empresa";
$_eq_val  = $_SESSION["empresa"];

// Asesores
$_eq_asesores = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($_eq_item, $_eq_val, "perfil", "vendedor");
if (!is_array($_eq_asesores)) $_eq_asesores = array();

// Tecnicos
$_eq_tecnicos = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol($_eq_item, $_eq_val, "perfil", "tecnico");
if (!is_array($_eq_tecnicos)) $_eq_tecnicos = array();

$_eq_av_grads = array(
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
);
?>

<div class="row dash-row-equal">
  <!-- Asesores -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-headset"></i> Asesores</h4>
        <span class="crm-badge" style="background:#f1f5f9;color:#475569"><?php echo count($_eq_asesores); ?> registrados</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:340px;overflow-y:auto">
        <?php if (empty($_eq_asesores)): ?>
          <div class="crm-empty" style="padding:24px">
            <i class="fa-solid fa-users"></i>
            <strong>Sin asesores registrados</strong>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($_eq_asesores, 0, 8) as $i => $a):
            $nombre = isset($a['nombre']) ? $a['nombre'] : '';
            $foto = isset($a['foto']) ? $a['foto'] : '';
            $fecha = isset($a['fecha']) ? $a['fecha'] : '';
            $initial = mb_strtoupper(mb_substr($nombre, 0, 1));
            $grad = $_eq_av_grads[$i % count($_eq_av_grads)];
          ?>
            <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-bottom:1px solid #f8fafc;transition:background .12s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($foto)): ?>
                <img src="<?php echo htmlspecialchars($foto); ?>"
                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0">
                <div style="display:none;width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12)">
                  <?php echo $initial; ?>
                </div>
              <?php else: ?>
                <div style="width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12)">
                  <?php echo $initial; ?>
                </div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?php echo htmlspecialchars($nombre); ?>
                </div>
                <div style="font-size:11px;color:#94a3b8">
                  <i class="fa-solid fa-calendar" style="font-size:9px;margin-right:2px"></i>
                  <?php echo htmlspecialchars($fecha); ?>
                </div>
              </div>
              <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#6366f1;background:#eef2ff;padding:3px 8px;border-radius:10px">
                <i class="fa-solid fa-headset" style="font-size:8px"></i> Asesor
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=asesores" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
          Ver todos los asesores <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Tecnicos -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-wrench"></i> Tecnicos</h4>
        <span class="crm-badge" style="background:#f1f5f9;color:#475569"><?php echo count($_eq_tecnicos); ?> registrados</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:340px;overflow-y:auto">
        <?php if (empty($_eq_tecnicos)): ?>
          <div class="crm-empty" style="padding:24px">
            <i class="fa-solid fa-users"></i>
            <strong>Sin tecnicos registrados</strong>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($_eq_tecnicos, 0, 8) as $i => $t):
            $nombre = isset($t['nombre']) ? $t['nombre'] : '';
            $foto = isset($t['foto']) ? $t['foto'] : '';
            $fecha = isset($t['fecha']) ? $t['fecha'] : '';
            $initial = mb_strtoupper(mb_substr($nombre, 0, 1));
            $grad = $_eq_av_grads[($i + 3) % count($_eq_av_grads)];
          ?>
            <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-bottom:1px solid #f8fafc;transition:background .12s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($foto)): ?>
                <img src="<?php echo htmlspecialchars($foto); ?>"
                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'"
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0">
                <div style="display:none;width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12)">
                  <?php echo $initial; ?>
                </div>
              <?php else: ?>
                <div style="width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12)">
                  <?php echo $initial; ?>
                </div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?php echo htmlspecialchars($nombre); ?>
                </div>
                <div style="font-size:11px;color:#94a3b8">
                  <i class="fa-solid fa-calendar" style="font-size:9px;margin-right:2px"></i>
                  <?php echo htmlspecialchars($fecha); ?>
                </div>
              </div>
              <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#06b6d4;background:#ecfeff;padding:3px 8px;border-radius:10px">
                <i class="fa-solid fa-wrench" style="font-size:8px"></i> Tecnico
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=tecnicos" style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none">
          Ver todos los tecnicos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>
    </div>
  </div>
</div>
