<?php
/* ── datos del usuario ── */
$_av   = !empty($_SESSION["foto"]) ? htmlspecialchars($_SESSION["foto"])
                                   : "vistas/img/perfiles/default/anonymous.png";
$_nom  = htmlspecialchars($_SESSION["nombre"]     ?? "Usuario");
$_rol  = htmlspecialchars(ucfirst($_SESSION["perfil"] ?? ""));

/* ── ranking del técnico (si aplica) ── */
$_egs_rankBadge   = '';   // icono sobre avatar en trigger
$_egs_rankDropBadges = array(); // menciones en dropdown

if (isset($_SESSION["perfil"]) && $_SESSION["perfil"] === "tecnico" && !empty($_SESSION["email"])) {
    try {
        // Obtener id del técnico por su correo
        $_egs_tecData = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
        if (is_array($_egs_tecData) && !empty($_egs_tecData["id"])) {
            $_egs_myTecId = $_egs_tecData["id"];

            // Obtener todos los técnicos de la empresa
            $_egs_tecAll = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
            if (!is_array($_egs_tecAll)) $_egs_tecAll = array();
            $_egs_mapaTec = array();
            foreach ($_egs_tecAll as $_t) {
                if (isset($_t['id'])) $_egs_mapaTec[$_t['id']] = isset($_t['nombre']) ? $_t['nombre'] : 'Técnico';
            }

            // Obtener todas las órdenes (el ranking filtra por técnicos de la empresa)
            $_egs_allOrd = array();
            try {
                $_egs_allOrd = controladorOrdenes::ctrlTraerOrdenesConTope(0, 99999);
                if (!is_array($_egs_allOrd)) $_egs_allOrd = array();
            } catch (Exception $e) {}

            // Función local de clasificación de estado
            if (!function_exists('_egsMenuClasificar')) {
                function _egsMenuClasificar($est) {
                    if (stripos($est, "sin reparación") !== false || strpos($est, "SR") !== false) return 'SR';
                    if (stripos($est, "producto para venta") !== false || strpos($est, "PV") !== false) return 'PV';
                    if (strpos($est, "AUT") !== false) return 'AUT';
                    if (strpos($est, "REV") !== false || strpos($est, "revisión") !== false) return 'REV';
                    if (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) return 'OK';
                    if (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) return 'TER';
                    if (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) return 'ENT';
                    if (strpos($est, "Supervisión") !== false || strpos($est, "SUP") !== false) return 'SUP';
                    if (strpos($est, "cancel") !== false || strpos($est, "can") !== false) return 'CAN';
                    return 'OTR';
                }
            }

            // Función local de ranking (misma lógica que admin-dashboard)
            if (!function_exists('_egsMenuCalcRanking')) {
                function _egsMenuCalcRanking($allOrders, $mapaTec, $corte) {
                    $tecStats = array();
                    foreach ($mapaTec as $tid => $tn) {
                        $tecStats[$tid] = array('ENT'=>0,'TER'=>0,'REV'=>0,'OK'=>0,'AUT'=>0,'SUP'=>0,'GAR'=>0,'CAN'=>0,'total'=>0);
                    }
                    foreach ($allOrders as $ord) {
                        $tid = isset($ord["id_tecnico"]) ? $ord["id_tecnico"] : null;
                        if (!$tid || !isset($tecStats[$tid])) continue;
                        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"],0,10) : "";
                        $fs = !empty($ord["fecha_Salida"]) ? substr($ord["fecha_Salida"],0,10) : "";
                        $fechaRef = !empty($fs) ? $fs : $fi;
                        if ($fechaRef < $corte) continue;
                        $est = isset($ord["estado"]) ? $ord["estado"] : "";
                        $estL = strtolower($est);
                        if (strpos($estL,'garantia')!==false || strpos($estL,'garantía')!==false) {
                            $tecStats[$tid]['GAR']++; $tecStats[$tid]['total']++; continue;
                        }
                        if (strpos($estL,'cancel')!==false || strpos($estL,'can')!==false) {
                            $tecStats[$tid]['CAN']++; $tecStats[$tid]['total']++; continue;
                        }
                        $clave = _egsMenuClasificar($est);
                        if ($clave==='SR'||$clave==='PV'||$clave==='OTR') continue;
                        if (isset($tecStats[$tid][$clave])) $tecStats[$tid][$clave]++;
                        $tecStats[$tid]['total']++;
                    }
                    $ranking = array();
                    foreach ($tecStats as $tid => $st) {
                        $favor = ($st['ENT']*3)+($st['TER']*2);
                        $pen   = $st['GAR']*5;
                        $brutos = max(0, $favor-$pen);
                        $totalE = $st['total']-$st['CAN'];
                        $completadas = $st['TER']+$st['ENT'];
                        $ratio = $totalE>0 ? round($completadas*100/$totalE) : 0;
                        $mult  = 1.0+(min($ratio,100)/100)*0.5;
                        $score = round($brutos*$mult,1);
                        $ranking[] = array('id'=>$tid,'score'=>$score,'totalOrd'=>$totalE);
                    }
                    usort($ranking, function($a,$b){
                        if ($b['score']!=$a['score']) return $b['score']>$a['score']?1:-1;
                        return $b['totalOrd']-$a['totalOrd'];
                    });
                    return $ranking;
                }
            }

            // Calcular posición para cada periodo
            $_egs_periodos = array(
                '1m'  => array('corte' => date("Y-m-d", strtotime("-1 month")),  'label' => 'del Mes'),
                '3m'  => array('corte' => date("Y-m-d", strtotime("-3 months")), 'label' => 'de los últimos 3 meses'),
                '12m' => array('corte' => date("Y-m-d", strtotime("-12 months")),'label' => 'del Año'),
            );

            $_egs_bestPos1m = 0; // posición en ranking del mes (0 = no está en top 3)
            foreach ($_egs_periodos as $_pk => $_pv) {
                $_egs_rank = _egsMenuCalcRanking($_egs_allOrd, $_egs_mapaTec, $_pv['corte']);
                // Buscar posición del técnico en el top 3
                for ($_ri = 0; $_ri < min(3, count($_egs_rank)); $_ri++) {
                    if ($_egs_rank[$_ri]['id'] == $_egs_myTecId && $_egs_rank[$_ri]['score'] > 0) {
                        $_pos = $_ri + 1; // 1, 2 o 3
                        $_egs_rankDropBadges[] = array(
                            'periodo' => $_pv['label'],
                            'key'     => $_pk,
                            'pos'     => $_pos,
                        );
                        if ($_pk === '1m') $_egs_bestPos1m = $_pos;
                        break;
                    }
                }
            }

            // Corona/medalla en avatar según posición del mes
            if ($_egs_bestPos1m === 1) {
                $_egs_rankBadge = '<span class="egs-rank-crown egs-rank-pos1" title="¡#1 del mes!"><i class="fa-solid fa-crown"></i></span>';
            } elseif ($_egs_bestPos1m === 2) {
                $_egs_rankBadge = '<span class="egs-rank-crown egs-rank-pos2" title="¡#2 del mes!"><i class="fa-solid fa-medal"></i></span>';
            } elseif ($_egs_bestPos1m === 3) {
                $_egs_rankBadge = '<span class="egs-rank-crown egs-rank-pos3" title="¡#3 del mes!"><i class="fa-solid fa-award"></i></span>';
            }
        }
    } catch (Exception $e) {}
}
?>
<!-- DEBUG-RANK: perfil=<?= $_SESSION["perfil"] ?? '?' ?> | badges=<?= count($_egs_rankDropBadges) ?> | badge=<?= !empty($_egs_rankBadge) ? 'SI' : 'NO' ?> -->
<!-- user-menu -->
<li class="dropdown user user-menu">

  <!-- Trigger: pill style Tailwind -->
  <a href="#" class="dropdown-toggle egs-trigger" data-toggle="dropdown">
    <span class="egs-av-wrap">
      <img src="<?= $_av ?>" class="egs-av" alt="<?= $_nom ?>">
      <?= $_egs_rankBadge ?>
    </span>
    <span class="egs-name hidden-xs"><?= $_nom ?></span>
    <i class="fa-solid fa-chevron-down egs-caret hidden-xs"></i>
  </a>

  <!-- Panel Tailwind -->
  <ul class="dropdown-menu egs-drop">

    <!-- Sección usuario -->
    <li class="egs-drop-user">
      <span class="egs-av-wrap egs-av-wrap-lg">
        <img src="<?= $_av ?>" class="egs-av-lg" alt="<?= $_nom ?>">
        <?php if (!empty($_egs_rankBadge)): ?>
          <?= str_replace('egs-rank-crown"', 'egs-rank-crown egs-rank-crown-lg"', $_egs_rankBadge) ?>
        <?php endif; ?>
      </span>
      <div class="egs-user-info">
        <strong><?= $_nom ?></strong>
        <span><?= $_rol ?></span>
      </div>
      <span class="egs-dot" title="En línea"></span>
    </li>

    <?php if (!empty($_egs_rankDropBadges)): ?>
    <!-- Logros del técnico -->
    <li class="egs-drop-achievements">
      <?php foreach ($_egs_rankDropBadges as $_badge):
        // Icono según posición
        if ($_badge['pos'] === 1) { $_bIcon = 'fa-crown'; $_bLabel = '#1'; }
        elseif ($_badge['pos'] === 2) { $_bIcon = 'fa-medal'; $_bLabel = '#2'; }
        else { $_bIcon = 'fa-award'; $_bLabel = '#3'; }
      ?>
        <div class="egs-achievement-item egs-achievement-pos<?= $_badge['pos'] ?>">
          <i class="fa-solid <?= $_bIcon ?>"></i>
          <span><?= $_bLabel ?> técnico <?= $_badge['periodo'] ?></span>
        </div>
      <?php endforeach; ?>
    </li>
    <?php endif; ?>

    <!-- Links -->
    <li style="padding:0">
      <ul class="egs-drop-nav">
        <li>
          <a href="index.php?ruta=perfiles">
            <i class="fa-solid fa-circle-user"></i> Mi Cuenta
          </a>
        </li>
        <li>
          <a href="index.php?ruta=preguntas">
            <i class="fa-regular fa-circle-question"></i> Ayuda
          </a>
        </li>
      </ul>
    </li>

    <li class="egs-drop-divider"></li>

    <!-- Logout -->
    <li class="egs-drop-foot">
      <a href="index.php?ruta=salir" class="egs-drop-logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        Cerrar sesión
      </a>
    </li>

  </ul>
</li>
<!-- /user-menu -->
