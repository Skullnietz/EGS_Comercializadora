<?php
/*  ═══════════════════════════════════════════════════
    DASHBOARD ADMINISTRADOR — Panel completo
    ═══════════════════════════════════════════════════ */

// ══════════════════════════════════════
// DATOS: KPIs principales
// ══════════════════════════════════════
$_adm_totalVentas = 0;
$_adm_totalEntregadas = 0;
$_adm_totalIngresadas = 0;
$_adm_totalProspectos = 0;
$_adm_totalVentasRevenue = 0;
$_adm_totalPedidos = 0;

try {
    $ordenes = controladorOrdenes::ctrMostrarOrdenesSuma();
    if (is_array($ordenes)) {
        foreach ($ordenes as $valueOrdenes) {
            $_adm_totalVentas += floatval(isset($valueOrdenes["total"]) ? $valueOrdenes["total"] : 0);
        }
        $_adm_totalEntregadas = count($ordenes);
    }
} catch (Exception $e) {}

try {
    $totalordenes = controladorOrdenes::ctrListarOrdenes();
    if (is_array($totalordenes)) {
        foreach ($totalordenes as $valueTotal) {
            $_adm_totalEntregadasGlobal = $valueTotal[0];
        }
    }
} catch (Exception $e) {}

try {
    $entradasMes = ControladorOrdenes::ctrMostrarOrdenesEntrada("id");
    if (is_array($entradasMes)) $_adm_totalIngresadas = count($entradasMes);
} catch (Exception $e) { $entradasMes = array(); }

try {
    $usuarios = ControladorUsuarios::ctrMostrarTotalUsuariosMes("id");
    if (is_array($usuarios)) $_adm_totalProspectos = count($usuarios);
} catch (Exception $e) {}

try {
    $ventas = ControladorVentas::ctrMostrarTotalVentasMes("id");
    if (is_array($ventas) && isset($ventas[0])) $_adm_totalVentasRevenue = floatval($ventas[0]);
} catch (Exception $e) {}

try {
    $pedidos = ControladorPedidos::ctrMostrarTotalPedidosMes("id");
    if (is_array($pedidos)) {
        foreach ($pedidos as $valuePedidos) {
            $_adm_totalPedidos += floatval(isset($valuePedidos["total"]) ? $valuePedidos["total"] : 0);
        }
    }
} catch (Exception $e) {}

// ══════════════════════════════════════
// DATOS: Alertas del día
// ══════════════════════════════════════
$_adm_entradasMes = is_array($entradasMes) ? $entradasMes : array();
$_adm_mesTotal = count($_adm_entradasMes);

$_adm_hoy = date('Y-m-d');
$_adm_hoyIngresadas = 0;
foreach ($_adm_entradasMes as $e) {
    if (isset($e['fecha_ingreso']) && substr($e['fecha_ingreso'], 0, 10) === $_adm_hoy) $_adm_hoyIngresadas++;
}

$_adm_entregasMes = array();
try {
    $_adm_entregasMes = controladorOrdenes::ctrMostrarOrdenesSuma();
    if (!is_array($_adm_entregasMes)) $_adm_entregasMes = array();
} catch (Exception $e) {}

$_adm_totalEntMes = count($_adm_entregasMes);
$_adm_hoyEntregadas = 0;
foreach ($_adm_entregasMes as $e) {
    if (isset($e['fecha_Salida']) && substr($e['fecha_Salida'], 0, 10) === $_adm_hoy) $_adm_hoyEntregadas++;
}

$_adm_pendientes = max(0, $_adm_mesTotal - $_adm_totalEntMes);
$_adm_pctPendientes = $_adm_mesTotal > 0 ? round($_adm_pendientes * 100 / $_adm_mesTotal) : 0;
$_adm_eficiencia = $_adm_mesTotal > 0 ? round($_adm_totalEntMes * 100 / $_adm_mesTotal) : 0;

if ($_adm_eficiencia >= 70) { $_adm_efColor = '#059669'; $_adm_efGrad = '#059669,#10b981'; }
elseif ($_adm_eficiencia >= 40) { $_adm_efColor = '#d97706'; $_adm_efGrad = '#d97706,#f59e0b'; }
else { $_adm_efColor = '#dc2626'; $_adm_efGrad = '#dc2626,#ef4444'; }

// ══════════════════════════════════════
// DATOS: Pipeline de todas las órdenes
// ══════════════════════════════════════
$_adm_allOrders = array();
try {
    // null,null en 3er y 4to param = traer TODAS las órdenes de la empresa sin filtro de asesor
    $_adm_allOrders = controladorOrdenes::ctrlMostrarordenesEmpresayPerfil(
        "id_empresa", $_SESSION["empresa"], null, null
    );
    if (!is_array($_adm_allOrders)) $_adm_allOrders = array();
} catch (Exception $e) {}

// Fallback: si falla, cargar con tope alto
if (empty($_adm_allOrders)) {
    try {
        $r = controladorOrdenes::ctrlTraerOrdenesConTope(0, 99999);
        if (is_array($r)) $_adm_allOrders = $r;
    } catch (Exception $e) {}
}

$_adm_pipe_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);

function _admPipeClasificar($est) {
    if (strpos($est, "AUT") !== false) return 'AUT';
    if (strpos($est, "REV") !== false || strpos($est, "revisión") !== false) return 'REV';
    if (strpos($est, "Aceptado") !== false || strpos($est, "ok") !== false) return 'OK';
    if (strpos($est, "Terminada") !== false || strpos($est, "ter") !== false) return 'TER';
    if (strpos($est, "Entregado") !== false || strpos($est, "Ent") !== false) return 'ENT';
    if (strpos($est, "Supervisión") !== false || strpos($est, "SUP") !== false) return 'SUP';
    if (strpos($est, "cancel") !== false || strpos($est, "can") !== false) return 'CAN';
    return 'REV';
}

$_adm_pipe_data = array();
foreach ($_adm_pipe_cortes as $periodo => $corte) {
    $_adm_pipe_data[$periodo] = array('REV'=>0, 'AUT'=>0, 'OK'=>0, 'TER'=>0, 'ENT'=>0, 'SUP'=>0, 'total'=>0);
    foreach ($_adm_allOrders as $ord) {
        $est = isset($ord["estado"]) ? $ord["estado"] : "";
        $clave = _admPipeClasificar($est);
        if ($clave === 'CAN') continue; // No contar canceladas en pipeline

        // Usar fecha_Salida para ENT/TER (alinea con KPI "Entregadas/Mes")
        // y fecha_ingreso para los demás estados
        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
        $fs = !empty($ord["fecha_Salida"]) ? substr($ord["fecha_Salida"], 0, 10) : "";
        if ($clave === 'ENT' || $clave === 'TER') {
            $fechaRef = !empty($fs) ? $fs : $fi;
        } else {
            $fechaRef = $fi;
        }

        if ($fechaRef >= $corte) {
            if (isset($_adm_pipe_data[$periodo][$clave])) {
                $_adm_pipe_data[$periodo][$clave]++;
            }
            $_adm_pipe_data[$periodo]['total']++;
        }
    }
}

$_adm_pipe_default = $_adm_pipe_data['1m'];

$_adm_stages_def = array(
    'REV' => array('label'=>'Revisión',      'icon'=>'fa-magnifying-glass', 'color'=>'#ef4444'),
    'AUT' => array('label'=>'Por Autorizar',  'icon'=>'fa-hourglass-half',   'color'=>'#f59e0b'),
    'OK'  => array('label'=>'Aceptadas',      'icon'=>'fa-circle-check',     'color'=>'#3b82f6'),
    'TER' => array('label'=>'Terminadas',     'icon'=>'fa-flag-checkered',   'color'=>'#06b6d4'),
    'ENT' => array('label'=>'Entregadas',     'icon'=>'fa-handshake',        'color'=>'#22c55e'),
    'SUP' => array('label'=>'Supervisión',    'icon'=>'fa-eye',              'color'=>'#8b5cf6'),
);

// ══════════════════════════════════════
// DATOS: Rendimiento de Técnicos — Scoring Justo
// ══════════════════════════════════════
// REGLAS DEL NEGOCIO:
// ✅ ENT (Entregadas) y TER (Terminadas) = puntos A FAVOR
// ⏳ REV (Revisión) y OK (Aceptadas) = trabajo PENDIENTE (contexto, no suma ni resta)
// ❌ GAR (Garantía) = PENALIZACIÓN (producto regresado por insatisfacción)
// ⚖️ Justo para pocos y muchos: usa Score Normalizado por volumen

$_adm_tecList = array();
try {
    $_adm_tecList = ControladorTecnicos::ctrMostrarTecnicosDeEmpresas("id_empresa", $_SESSION["empresa"]);
    if (!is_array($_adm_tecList)) $_adm_tecList = array();
} catch (Exception $e) {}

$_adm_mapaTec = array();
foreach ($_adm_tecList as $t) {
    if (isset($t['id'])) $_adm_mapaTec[$t['id']] = isset($t['nombre']) ? $t['nombre'] : 'Técnico #'.$t['id'];
}

$_adm_tecStats = array();
foreach ($_adm_mapaTec as $tid => $tn) {
    $_adm_tecStats[$tid] = array(
        'nombre' => $tn,
        'REV' => 0, 'OK' => 0, 'TER' => 0, 'ENT' => 0,
        'AUT' => 0, 'SUP' => 0, 'GAR' => 0, 'CAN' => 0,
        'total' => 0,
    );
}

// Analizar últimos 3 meses de órdenes
$_adm_corteTec = date("Y-m-d", strtotime("-3 months"));
foreach ($_adm_allOrders as $ord) {
    $tid = isset($ord["id_tecnico"]) ? $ord["id_tecnico"] : null;
    if (!$tid || !isset($_adm_mapaTec[$tid])) continue;

    // Usar fecha más relevante según estado
    $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
    $fs = !empty($ord["fecha_Salida"]) ? substr($ord["fecha_Salida"], 0, 10) : "";
    $fechaRef = !empty($fs) ? $fs : $fi;
    if ($fechaRef < $_adm_corteTec) continue;

    $est = isset($ord["estado"]) ? $ord["estado"] : "";
    $estL = strtolower($est);

    // Garantías
    if (strpos($estL, 'garantia') !== false || strpos($estL, 'garantía') !== false) {
        $_adm_tecStats[$tid]['GAR']++;
        $_adm_tecStats[$tid]['total']++;
        continue;
    }
    // Canceladas
    if (strpos($estL, 'cancel') !== false || strpos($estL, 'can') !== false) {
        $_adm_tecStats[$tid]['CAN']++;
        $_adm_tecStats[$tid]['total']++;
        continue;
    }

    $clave = _admPipeClasificar($est);
    if (isset($_adm_tecStats[$tid][$clave])) {
        $_adm_tecStats[$tid][$clave]++;
    }
    $_adm_tecStats[$tid]['total']++;
}

// FÓRMULA DE SCORING JUSTO:
// Puntos = (ENT × 3) + (TER × 2) - (GAR × 5)
// Score Normalizado = Puntos / max(total_ordenes, 1) × 10  (escala 0-10 por orden)
// Score Final = Puntos_brutos × (1 + Score_Normalizado/20)
// Esto premia: volumen (más órdenes = más puntos brutos)
//              calidad (mejor ratio = mejor multiplicador)
//              penaliza garantías proporcionalmente
$_adm_ranking = array();
foreach ($_adm_tecStats as $tid => $st) {
    $puntosA_favor = ($st['ENT'] * 3) + ($st['TER'] * 2);
    $penalizacion  = $st['GAR'] * 5;
    $puntosBrutos  = max(0, $puntosA_favor - $penalizacion);

    $pendientes    = $st['REV'] + $st['OK'];
    $completadas   = $st['TER'] + $st['ENT'];
    $totalElegible = $st['total'] - $st['CAN'];

    // Ratio de calidad: completadas vs total elegible
    $ratioCalidad = $totalElegible > 0 ? round($completadas * 100 / $totalElegible) : 0;

    // Score normalizado por volumen (0-10 escala)
    $scoreNorm = $totalElegible > 0 ? round(($puntosBrutos / $totalElegible) * 10, 1) : 0;

    // Multiplicador de calidad: de 1.0 a 1.5 según ratio
    $multiplicador = 1.0 + (min($ratioCalidad, 100) / 100) * 0.5;

    // Score final: combina volumen bruto × calidad
    $scoreFinal = round($puntosBrutos * $multiplicador, 1);

    // Nivel
    if ($scoreFinal >= 100) { $nivel = 'Élite'; $nivelColor = '#f59e0b'; $nivelIcon = 'fa-crown'; }
    elseif ($scoreFinal >= 40) { $nivel = 'Pro'; $nivelColor = '#6366f1'; $nivelIcon = 'fa-gem'; }
    elseif ($scoreFinal >= 15) { $nivel = 'Activo'; $nivelColor = '#22c55e'; $nivelIcon = 'fa-bolt'; }
    elseif ($puntosBrutos > 0) { $nivel = 'Inicial'; $nivelColor = '#64748b'; $nivelIcon = 'fa-seedling'; }
    else { $nivel = '—'; $nivelColor = '#cbd5e1'; $nivelIcon = 'fa-minus'; }

    $_adm_ranking[] = array(
        'nombre'       => $st['nombre'],
        'score'        => $scoreFinal,
        'puntosBrutos' => $puntosBrutos,
        'ratioCalidad' => $ratioCalidad,
        'multiplicador'=> $multiplicador,
        'totalOrd'     => $st['total'],
        'entregadas'   => $st['ENT'],
        'terminadas'   => $st['TER'],
        'pendientes'   => $pendientes,
        'revision'     => $st['REV'],
        'aceptadas'    => $st['OK'],
        'garantias'    => $st['GAR'],
        'nivel'        => $nivel,
        'nivelColor'   => $nivelColor,
        'nivelIcon'    => $nivelIcon,
    );
}
usort($_adm_ranking, function($a, $b) {
    if ($b['score'] != $a['score']) return $b['score'] > $a['score'] ? 1 : -1;
    return $b['totalOrd'] - $a['totalOrd'];
});
$_adm_ranking = array_slice($_adm_ranking, 0, 10);
$_adm_maxScore = (!empty($_adm_ranking) && $_adm_ranking[0]['score'] > 0) ? $_adm_ranking[0]['score'] : 1;

// ══════════════════════════════════════
// DATOS: Últimas órdenes (actividad reciente — últimos 30 días)
// ══════════════════════════════════════
// Filtrar de $_adm_allOrders las órdenes con actividad reciente
// (ordenar por fecha más reciente: fecha_Salida si existe, sino fecha_ingreso)
$_adm_ultimasOrd = array();
$_adm_corteReciente = date("Y-m-d", strtotime("-30 days"));
$_adm_ordRecientes = array();
foreach ($_adm_allOrders as $ord) {
    $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
    $fs = !empty($ord["fecha_Salida"]) ? substr($ord["fecha_Salida"], 0, 10) : "";
    // Usar la fecha más reciente de la orden
    $fechaMax = ($fs > $fi) ? $fs : $fi;
    if ($fechaMax >= $_adm_corteReciente) {
        $ord['_fechaSort'] = $fechaMax;
        $_adm_ordRecientes[] = $ord;
    }
}
// Ordenar por fecha más reciente primero
usort($_adm_ordRecientes, function($a, $b) {
    return strcmp($b['_fechaSort'], $a['_fechaSort']);
});
$_adm_ultimasOrd = array_slice($_adm_ordRecientes, 0, 8);

function _admEstadoBadge($estado) {
    $e = strtolower(trim($estado));
    if (strpos($e, 'entregado') !== false) return array('#22c55e', '#f0fdf4');
    if (strpos($e, 'aceptado') !== false || strpos($e, 'ok') !== false) return array('#3b82f6', '#dbeafe');
    if (strpos($e, 'revisión') !== false || strpos($e, 'revision') !== false || strpos($e, 'rev') !== false) return array('#f59e0b', '#fef3c7');
    if (strpos($e, 'terminada') !== false || strpos($e, 'ter') !== false) return array('#06b6d4', '#cffafe');
    if (strpos($e, 'aut') !== false) return array('#8b5cf6', '#ede9fe');
    if (strpos($e, 'cancel') !== false) return array('#ef4444', '#fef2f2');
    if (strpos($e, 'supervisión') !== false || strpos($e, 'sup') !== false) return array('#ec4899', '#fce7f3');
    return array('#64748b', '#f1f5f9');
}

function _admGetImg($ord) {
    if (!empty($ord["multimedia"])) {
        $album = json_decode($ord["multimedia"], true);
        if (is_array($album)) {
            foreach ($album as $img) {
                if (isset($img["foto"]) && !empty($img["foto"])) return $img["foto"];
            }
        }
    }
    return "";
}

// ══════════════════════════════════════
// DATOS: Equipo
// ══════════════════════════════════════
$_adm_asesores = array();
try {
    $_adm_asesores = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol(
        "id_empresa", $_SESSION["empresa"], "perfil", "vendedor"
    );
    if (!is_array($_adm_asesores)) $_adm_asesores = array();
} catch (Exception $e) {}

$_adm_tecnicos = array();
try {
    $_adm_tecnicos = ControladorAdministradores::ctrlMostrarAdministradoresPorEmpresaRol(
        "id_empresa", $_SESSION["empresa"], "perfil", "tecnico"
    );
    if (!is_array($_adm_tecnicos)) $_adm_tecnicos = array();
} catch (Exception $e) {}

// Colores para avatares
$_adm_grads = array(
    'linear-gradient(135deg,#6366f1,#818cf8)',
    'linear-gradient(135deg,#3b82f6,#60a5fa)',
    'linear-gradient(135deg,#06b6d4,#22d3ee)',
    'linear-gradient(135deg,#22c55e,#4ade80)',
    'linear-gradient(135deg,#f59e0b,#fbbf24)',
    'linear-gradient(135deg,#ef4444,#f87171)',
    'linear-gradient(135deg,#8b5cf6,#a78bfa)',
    'linear-gradient(135deg,#ec4899,#f472b6)',
);

// Gráfico de ventas — Datos por periodo
// FUENTE: Tabla ventas (pagos registrados), filtrada por empresa
$_adm_ventasRaw = array();
try {
    $respuesta = ControladorVentas::ctrRangoFechasVentas(null, null, "id_empresa", $_SESSION["empresa"]);
    if (is_array($respuesta)) $_adm_ventasRaw = $respuesta;
} catch (Exception $e) {}

// Pre-calcular datos para cada periodo
$_adm_chart_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);
$_adm_chartPeriods = array();
foreach ($_adm_chart_cortes as $pk => $corte) {
    $sumaPagosMes = array();
    $totalPeriodo = 0;
    $maxMes = ''; $maxVal = 0;
    foreach ($_adm_ventasRaw as $value) {
        $fechaVenta = isset($value["fecha"]) ? substr($value["fecha"], 0, 10) : "";
        if ($fechaVenta < $corte) continue;
        $mes = substr($fechaVenta, 0, 7);
        if (!isset($sumaPagosMes[$mes])) $sumaPagosMes[$mes] = 0;
        $sumaPagosMes[$mes] += floatval(isset($value["pago"]) ? $value["pago"] : 0);
        $totalPeriodo += floatval(isset($value["pago"]) ? $value["pago"] : 0);
    }
    $chartData = array();
    foreach ($sumaPagosMes as $mes => $total) {
        $chartData[] = array('y' => $mes, 'ventas' => $total);
        if ($total > $maxVal) { $maxVal = $total; $maxMes = $mes; }
    }
    $_adm_chartPeriods[$pk] = array(
        'data' => $chartData,
        'total' => $totalPeriodo,
        'maxMes' => $maxMes,
        'maxVal' => $maxVal,
        'count' => count($chartData),
    );
}
$_adm_chartDefault = isset($_adm_chartPeriods['1m']) ? $_adm_chartPeriods['1m'] : array('data'=>array(),'total'=>0,'maxMes'=>'','maxVal'=>0,'count'=>0);

// Productos más vendidos
$_adm_productos = array();
$_adm_totalProdVentas = 1;
try {
    $_adm_productos = ControladorProductos::ctrMostrarTotalProductos("ventas");
    if (!is_array($_adm_productos)) $_adm_productos = array();
    $tv = ControladorProductos::ctrMostrarSumaVentas();
    if (is_array($tv) && isset($tv["total"]) && $tv["total"] > 0) $_adm_totalProdVentas = $tv["total"];
} catch (Exception $e) {}
$_adm_prodColors = array('#ef4444','#22c55e','#f59e0b','#06b6d4','#8b5cf6');
?>

<!-- ══════════════════════════════════════════
     KPIs PRINCIPALES
══════════════════════════════════════════ -->
<div class="row">
  <!-- Ventas del Mes -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
      <i class="fa-solid fa-dollar-sign crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ventas del Mes</div>
      <div class="crm-kpi-value">$<?php echo number_format($_adm_totalVentas, 0); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-chart-line"></i> Órdenes entregadas</div>
      <div class="crm-kpi-bar"><span style="width:75%"></span></div>
    </div>
  </div>

  <!-- Ingresadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
      <i class="fa-solid fa-file-circle-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ingresadas / Mes</div>
      <div class="crm-kpi-value"><?php echo number_format($_adm_totalIngresadas); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-calendar-day"></i> <?php echo $_adm_hoyIngresadas; ?> hoy</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_adm_hoyIngresadas * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Entregadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#22c55e,#4ade80)">
      <i class="fa-solid fa-circle-check crm-kpi-icon"></i>
      <div class="crm-kpi-label">Entregadas / Mes</div>
      <div class="crm-kpi-value"><?php echo number_format($_adm_totalEntregadas); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-truck"></i> <?php echo $_adm_hoyEntregadas; ?> entregadas hoy</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo $_adm_mesTotal > 0 ? round($_adm_totalEntregadas * 100 / max($_adm_mesTotal, 1)) : 0; ?>%"></span></div>
    </div>
  </div>

  <!-- Prospectos -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
      <i class="fa-solid fa-user-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Prospectos / Mes</div>
      <div class="crm-kpi-value"><?php echo number_format($_adm_totalProspectos); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-users"></i> Nuevos registros</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_adm_totalProspectos * 5, 100); ?>%"></span></div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     ALERTAS: Pendientes + Eficiencia
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
    <i class="fa-solid fa-bolt"></i>
  </div>
  <div>
    <h3>Estado del Día</h3>
    <p>Métricas críticas y eficiencia operativa</p>
  </div>
</div>

<div class="row">
  <!-- Pendientes -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-card" style="border-left:4px solid <?php echo $_adm_pctPendientes > 50 ? '#ef4444' : ($_adm_pctPendientes > 20 ? '#f59e0b' : '#22c55e'); ?>">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <span style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted);letter-spacing:.5px">Pendientes</span>
          <span style="width:32px;height:32px;border-radius:8px;background:<?php echo $_adm_pctPendientes > 50 ? '#fef2f2' : '#fef3c7'; ?>;display:flex;align-items:center;justify-content:center;font-size:14px;color:<?php echo $_adm_pctPendientes > 50 ? '#ef4444' : '#f59e0b'; ?>">
            <i class="fa-solid fa-hourglass-half"></i>
          </span>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--crm-text);line-height:1.1"><?php echo number_format($_adm_pendientes); ?></div>
        <div style="margin-top:8px;height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden">
          <div style="height:100%;width:<?php echo $_adm_pctPendientes; ?>%;background:<?php echo $_adm_pctPendientes > 50 ? '#ef4444' : '#f59e0b'; ?>;border-radius:2px;transition:width .5s"></div>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);margin-top:6px"><?php echo $_adm_pctPendientes; ?>% sin entregar</div>
      </div>
    </div>
  </div>

  <!-- Ingresadas Hoy -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-card" style="border-left:4px solid #3b82f6">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <span style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted);letter-spacing:.5px">Ingresadas Hoy</span>
          <span style="width:32px;height:32px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:14px;color:#3b82f6">
            <i class="fa-solid fa-file-circle-plus"></i>
          </span>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--crm-text);line-height:1.1"><?php echo $_adm_hoyIngresadas; ?></div>
        <div style="margin-top:8px;height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden">
          <div style="height:100%;width:<?php echo $_adm_mesTotal > 0 ? round($_adm_hoyIngresadas * 100 / $_adm_mesTotal) : 0; ?>%;background:#3b82f6;border-radius:2px;transition:width .5s"></div>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);margin-top:6px">Total mes: <?php echo $_adm_mesTotal; ?></div>
      </div>
    </div>
  </div>

  <!-- Entregadas Hoy -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-card" style="border-left:4px solid #22c55e">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <span style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted);letter-spacing:.5px">Entregadas Hoy</span>
          <span style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:14px;color:#22c55e">
            <i class="fa-solid fa-circle-check"></i>
          </span>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--crm-text);line-height:1.1"><?php echo $_adm_hoyEntregadas; ?></div>
        <div style="margin-top:8px;height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden">
          <div style="height:100%;width:<?php echo $_adm_totalEntMes > 0 ? round($_adm_hoyEntregadas * 100 / $_adm_totalEntMes) : 0; ?>%;background:#22c55e;border-radius:2px;transition:width .5s"></div>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);margin-top:6px">Total entregadas: <?php echo $_adm_totalEntMes; ?></div>
      </div>
    </div>
  </div>

  <!-- Eficiencia -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-card" style="border-left:4px solid <?php echo $_adm_efColor; ?>">
      <div class="crm-card-body" style="padding:18px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <span style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--crm-muted);letter-spacing:.5px">Eficiencia</span>
          <span style="width:32px;height:32px;border-radius:8px;background:<?php echo $_adm_eficiencia >= 70 ? '#f0fdf4' : '#fef3c7'; ?>;display:flex;align-items:center;justify-content:center;font-size:14px;color:<?php echo $_adm_efColor; ?>">
            <i class="fa-solid fa-gauge-high"></i>
          </span>
        </div>
        <div style="font-size:28px;font-weight:800;color:<?php echo $_adm_efColor; ?>;line-height:1.1"><?php echo $_adm_eficiencia; ?>%</div>
        <div style="margin-top:8px;height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden">
          <div style="height:100%;width:<?php echo $_adm_eficiencia; ?>%;background:<?php echo $_adm_efColor; ?>;border-radius:2px;transition:width .5s"></div>
        </div>
        <div style="font-size:11px;color:var(--crm-muted);margin-top:6px"><?php echo $_adm_totalEntMes; ?> de <?php echo $_adm_mesTotal; ?> ingresadas</div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     PIPELINE con filtro de periodo
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
    <i class="fa-solid fa-diagram-project"></i>
  </div>
  <div>
    <h3>Pipeline General</h3>
    <p>Flujo completo de todas las órdenes de la empresa</p>
  </div>
</div>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-diagram-project"></i> Estado de Órdenes</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="admPipeBadge" style="background:#f1f5f9;color:#475569;margin-right:4px">
        <?php echo $_adm_pipe_default['total']; ?> órdenes
      </span>
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="admPipeFilter">
        <button type="button" class="adm-pipe-btn active" data-period="1m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">Mes</button>
        <button type="button" class="adm-pipe-btn" data-period="3m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">3M</button>
        <button type="button" class="adm-pipe-btn" data-period="6m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">6M</button>
        <button type="button" class="adm-pipe-btn" data-period="12m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">Año</button>
      </div>
    </div>
  </div>
  <div class="crm-card-body">
    <div class="crm-pipe-track" id="admPipeTrack">
      <?php foreach ($_adm_stages_def as $k => $s):
        $cnt = isset($_adm_pipe_default[$k]) ? $_adm_pipe_default[$k] : 0;
        $pct = $_adm_pipe_default['total'] > 0 ? max(4, round(($cnt / $_adm_pipe_default['total']) * 100)) : 0;
      ?>
        <div id="admTrack_<?php echo $k; ?>" style="background:<?php echo $s['color']; ?>;width:<?php echo $cnt > 0 ? $pct : 0; ?>%;transition:width .4s cubic-bezier(.4,0,.2,1)"></div>
      <?php endforeach; ?>
    </div>
    <div class="crm-pipe-stages" id="admPipeStages" style="grid-template-columns:repeat(6,1fr)">
      <?php foreach ($_adm_stages_def as $k => $s):
        $cnt = isset($_adm_pipe_default[$k]) ? $_adm_pipe_default[$k] : 0;
        $pct = $_adm_pipe_default['total'] > 0 ? round(($cnt / $_adm_pipe_default['total']) * 100, 1) : 0;
      ?>
        <div class="crm-pipe-stage">
          <div class="crm-pipe-stage-icon" style="background:<?php echo $s['color']; ?>">
            <i class="fa-solid <?php echo $s['icon']; ?>"></i>
          </div>
          <div class="crm-pipe-stage-num" id="admNum_<?php echo $k; ?>"><?php echo $cnt; ?></div>
          <div class="crm-pipe-stage-lbl"><?php echo $s['label']; ?></div>
          <div id="admPct_<?php echo $k; ?>" style="font-size:10px;color:#94a3b8;margin-top:2px"><?php echo $pct; ?>%</div>
        </div>
      <?php endforeach; ?>
    </div>
    <div id="admPipeEmpty" style="display:none">
      <div class="crm-empty">
        <i class="fa-solid fa-inbox"></i>
        <strong>Sin órdenes en este periodo</strong>
        <span style="font-size:12px">Prueba con un rango más amplio</span>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var admPipeData = <?php echo json_encode($_adm_pipe_data); ?>;
  var stages = ['REV','AUT','OK','TER','ENT','SUP'];

  $('#admPipeFilter').on('click', '.adm-pipe-btn', function(){
    var $btn = $(this), period = $btn.data('period'), data = admPipeData[period];
    if (!data) return;
    $('#admPipeFilter .adm-pipe-btn').css({background:'transparent',color:'#64748b'}).removeClass('active');
    $btn.css({background:'#6366f1',color:'#fff'}).addClass('active');
    var total = data.total;
    $('#admPipeBadge').text(total + ' órdenes');
    if (total === 0) { $('#admPipeTrack,#admPipeStages').hide(); $('#admPipeEmpty').show(); return; }
    else { $('#admPipeTrack,#admPipeStages').show(); $('#admPipeEmpty').hide(); }
    for (var i = 0; i < stages.length; i++) {
      var k = stages[i], cnt = data[k] || 0;
      var pct = total > 0 ? Math.round((cnt/total)*1000)/10 : 0;
      var tw = cnt > 0 ? Math.max(4, Math.round((cnt/total)*100)) : 0;
      $('#admNum_'+k).text(cnt);
      $('#admPct_'+k).text(pct+'%');
      $('#admTrack_'+k).css('width', tw+'%');
    }
  });
})();
</script>

<!-- ══════════════════════════════════════════
     ÚLTIMOS MOVIMIENTOS + ACCIONES RÁPIDAS
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
    <i class="fa-solid fa-clock-rotate-left"></i>
  </div>
  <div>
    <h3>Últimos Movimientos</h3>
    <p>Observaciones recientes en órdenes de servicio</p>
  </div>
</div>

<div class="row">
  <div class="col-lg-8 col-md-7 col-xs-12">
    <?php include "inicio/admin-ultimos-movimientos.php"; ?>
  </div>
  <div class="col-lg-4 col-md-5 col-xs-12">
    <?php include "inicio/admin-acciones-rapidas.php"; ?>
  </div>
</div>

<!-- ══════════════════════════════════════════
     GRÁFICO DE VENTAS
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#22c55e,#06b6d4)">
    <i class="fa-solid fa-chart-line"></i>
  </div>
  <div>
    <h3>Análisis de Ventas</h3>
    <p>Histórico de ventas mensuales de la empresa</p>
  </div>
</div>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-chart-area"></i> Ventas Mensuales</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="admVentasBadge" style="background:#f0fdf4;color:#16a34a">
        $<?php echo number_format($_adm_chartDefault['total'], 0); ?>
      </span>
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="admVentasFilter">
        <button type="button" class="adm-vent-btn active" data-period="1m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:#6366f1;color:#fff">Mes</button>
        <button type="button" class="adm-vent-btn" data-period="3m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">3M</button>
        <button type="button" class="adm-vent-btn" data-period="6m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">6M</button>
        <button type="button" class="adm-vent-btn" data-period="12m"
                style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#64748b">Año</button>
      </div>
    </div>
  </div>
  <div class="crm-card-body" style="padding:16px 20px">
    <div id="admChartVentas" style="height:260px"></div>
    <!-- Dato significativo -->
    <div style="display:flex;align-items:center;gap:12px;margin-top:12px;padding:10px 14px;background:#f8fafc;border-radius:8px;font-size:11px">
      <span style="color:var(--crm-muted)"><i class="fa-solid fa-database" style="margin-right:3px"></i> <strong>Fuente:</strong> Tabla de Ventas (pagos registrados)</span>
      <span style="margin-left:auto;color:#6366f1;font-weight:600" id="admVentasMax">
        <?php if (!empty($_adm_chartDefault['maxMes'])): ?>
          Mejor mes: <?php echo $_adm_chartDefault['maxMes']; ?> — $<?php echo number_format($_adm_chartDefault['maxVal'], 0); ?>
        <?php else: ?>
          Sin datos en este periodo
        <?php endif; ?>
      </span>
    </div>
  </div>
</div>

<script>
(function(){
  var admVentasData = <?php echo json_encode($_adm_chartPeriods); ?>;
  var admChart = null;

  function renderVentasChart(period) {
    var info = admVentasData[period];
    if (!info) return;
    var data = info.data;
    $('#admVentasBadge').text('$' + Number(info.total).toLocaleString('en'));
    if (info.maxMes) {
      $('#admVentasMax').html('Mejor mes: ' + info.maxMes + ' — $' + Number(info.maxVal).toLocaleString('en'));
    } else {
      $('#admVentasMax').text('Sin datos en este periodo');
    }
    $('#admChartVentas').empty();
    if (data.length === 0) data = [{y:'0', ventas:0}];
    if (typeof Morris !== 'undefined') {
      admChart = new Morris.Line({
        element: 'admChartVentas', resize: true, data: data,
        xkey:'y', ykeys:['ventas'], labels:['Ventas'],
        lineColors:['#6366f1'], lineWidth:2, hideHover:'auto',
        gridTextColor:'#94a3b8', gridStrokeWidth:0.3, pointSize:4,
        pointStrokeColors:['#6366f1'], gridLineColor:'#e2e8f0',
        gridTextFamily:'inherit', preUnits:'$', gridTextSize:10, fillOpacity:0.08
      });
    }
  }

  renderVentasChart('1m');

  $('#admVentasFilter').on('click', '.adm-vent-btn', function(){
    var $btn = $(this), period = $btn.data('period');
    $('#admVentasFilter .adm-vent-btn').css({background:'transparent',color:'#64748b'}).removeClass('active');
    $btn.css({background:'#6366f1',color:'#fff'}).addClass('active');
    renderVentasChart(period);
  });
})();
</script>

<!-- ══════════════════════════════════════════
     RENDIMIENTO DE TÉCNICOS — Scoring Justo
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
    <i class="fa-solid fa-ranking-star"></i>
  </div>
  <div>
    <h3>Rendimiento de Técnicos</h3>
    <p>Últimos 3 meses &mdash; Garantías penalizan, pendientes como contexto</p>
  </div>
</div>

<!-- Leyenda -->
<div class="crm-card" style="margin-bottom:10px">
  <div class="crm-card-body" style="padding:12px 18px">
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;font-size:11px;color:var(--crm-muted)">
      <span style="font-weight:700;color:var(--crm-text)"><i class="fa-solid fa-calculator" style="margin-right:4px"></i> Puntos:</span>
      <span style="background:#f0fdf4;color:#16a34a;padding:3px 8px;border-radius:6px;font-weight:600"><i class="fa-solid fa-plus" style="font-size:8px"></i> Entregadas ×3</span>
      <span style="background:#cffafe;color:#0891b2;padding:3px 8px;border-radius:6px;font-weight:600"><i class="fa-solid fa-plus" style="font-size:8px"></i> Terminadas ×2</span>
      <span style="background:#fef2f2;color:#dc2626;padding:3px 8px;border-radius:6px;font-weight:600"><i class="fa-solid fa-minus" style="font-size:8px"></i> Garantías ×5</span>
      <span style="background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:6px;font-weight:600"><i class="fa-solid fa-clock" style="font-size:8px"></i> REV + OK = Pendientes</span>
      <span style="margin-left:auto;font-size:10px;color:#94a3b8">× Multiplicador calidad</span>
    </div>
  </div>
</div>

<div class="crm-card" style="margin-bottom:20px">
  <div class="crm-card-head">
    <h4 class="crm-card-title"><i class="fa-solid fa-trophy"></i> Ranking (3 meses)</h4>
    <span class="crm-badge" style="background:#fef3c7;color:#92400e"><?php echo count($_adm_ranking); ?> técnicos</span>
  </div>
  <div class="crm-card-body-flush">
    <?php if (empty($_adm_ranking) || $_adm_ranking[0]['score'] == 0): ?>
      <div class="crm-empty" style="padding:30px">
        <i class="fa-solid fa-trophy" style="font-size:32px"></i>
        <strong>Sin actividad reciente</strong>
        <span style="font-size:12px">Los técnicos no tienen órdenes completadas en los últimos 3 meses</span>
      </div>
    <?php else: ?>
      <!-- Header -->
      <div style="display:flex;align-items:center;padding:10px 20px;border-bottom:2px solid #e2e8f0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted)">
        <div style="width:36px;text-align:center">#</div>
        <div style="flex:1;min-width:0">Técnico</div>
        <div style="width:50px;text-align:center" title="Total de órdenes">Total</div>
        <div style="width:50px;text-align:center;color:#22c55e" title="Entregadas (+3 pts c/u)">ENT</div>
        <div style="width:50px;text-align:center;color:#06b6d4" title="Terminadas (+2 pts c/u)">TER</div>
        <div style="width:70px;text-align:center;color:#64748b" title="Pendientes (REV + OK)">Pend.</div>
        <div style="width:50px;text-align:center;color:#dc2626" title="Garantías (-5 pts c/u)">GAR</div>
        <div style="width:55px;text-align:center" title="% completadas vs total">Calidad</div>
        <div style="width:75px;text-align:center">Score</div>
        <div style="width:65px;text-align:center">Nivel</div>
      </div>

      <?php
      $_rankMedals = array('🥇','🥈','🥉');
      foreach ($_adm_ranking as $i => $tec):
        if ($tec['score'] == 0 && $tec['totalOrd'] == 0) continue;
        $pctBar = round($tec['score'] * 100 / $_adm_maxScore);
        $bgRow = $i === 0 ? 'background:linear-gradient(90deg,#fefce8,#fff);' : ($i % 2 === 0 ? '' : 'background:#fafbfc;');
      ?>
        <div style="display:flex;align-items:center;padding:12px 20px;border-bottom:1px solid #f1f5f9;transition:background .12s;<?php echo $bgRow; ?>"
             onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='<?php echo $i === 0 ? 'linear-gradient(90deg,#fefce8,#fff)' : ($i % 2 === 0 ? '' : '#fafbfc'); ?>'">

          <div style="width:36px;text-align:center;font-size:16px;flex-shrink:0">
            <?php if ($i < 3 && $tec['score'] > 0): echo $_rankMedals[$i];
            else: ?><span style="font-size:12px;font-weight:700;color:#94a3b8"><?php echo ($i + 1); ?></span><?php endif; ?>
          </div>

          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:700;color:var(--crm-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px"><?php echo htmlspecialchars($tec['nombre']); ?></div>
            <div style="height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden;max-width:180px">
              <div style="height:100%;width:<?php echo $pctBar; ?>%;background:linear-gradient(90deg,<?php echo $tec['nivelColor']; ?>,<?php echo $tec['nivelColor']; ?>aa);border-radius:2px;transition:width .6s"></div>
            </div>
          </div>

          <div style="width:50px;text-align:center;font-size:13px;font-weight:700;color:var(--crm-text)"><?php echo $tec['totalOrd']; ?></div>

          <div style="width:50px;text-align:center"><span style="font-size:12px;font-weight:700;color:#22c55e"><?php echo $tec['entregadas']; ?></span></div>

          <div style="width:50px;text-align:center"><span style="font-size:12px;font-weight:700;color:#06b6d4"><?php echo $tec['terminadas']; ?></span></div>

          <div style="width:70px;text-align:center">
            <span style="font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 6px;border-radius:4px" title="REV:<?php echo $tec['revision']; ?> + OK:<?php echo $tec['aceptadas']; ?>">
              <?php echo $tec['pendientes']; ?> <i class="fa-solid fa-clock" style="font-size:8px;color:#94a3b8"></i>
            </span>
          </div>

          <div style="width:50px;text-align:center">
            <?php if ($tec['garantias'] > 0): ?>
              <span style="font-size:11px;font-weight:700;color:#dc2626;background:#fef2f2;padding:2px 6px;border-radius:4px" title="-<?php echo $tec['garantias'] * 5; ?> pts">
                -<?php echo $tec['garantias']; ?>
              </span>
            <?php else: ?>
              <span style="font-size:11px;color:#cbd5e1">0</span>
            <?php endif; ?>
          </div>

          <div style="width:55px;text-align:center">
            <?php
            $qc = $tec['ratioCalidad'] >= 70 ? '#22c55e' : ($tec['ratioCalidad'] >= 40 ? '#f59e0b' : '#ef4444');
            $qbg = $tec['ratioCalidad'] >= 70 ? '#f0fdf4' : ($tec['ratioCalidad'] >= 40 ? '#fef3c7' : '#fef2f2');
            ?>
            <span style="font-size:11px;font-weight:700;color:<?php echo $qc; ?>;background:<?php echo $qbg; ?>;padding:2px 6px;border-radius:4px">
              <?php echo $tec['ratioCalidad']; ?>%
            </span>
          </div>

          <div style="width:75px;text-align:center">
            <span style="font-size:15px;font-weight:800;color:var(--crm-text)"><?php echo $tec['score']; ?></span>
            <span style="font-size:9px;color:#94a3b8;display:block;margin-top:-2px">pts</span>
          </div>

          <div style="width:65px;text-align:center">
            <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;color:<?php echo $tec['nivelColor']; ?>;background:<?php echo $tec['nivelColor']; ?>18;padding:3px 8px;border-radius:8px">
              <i class="fa-solid <?php echo $tec['nivelIcon']; ?>" style="font-size:9px"></i>
              <?php echo $tec['nivel']; ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>

      <div style="padding:12px 20px;background:#f8fafc;display:flex;flex-wrap:wrap;gap:14px;font-size:11px;color:var(--crm-muted);border-top:2px solid #e2e8f0">
        <span><i class="fa-solid fa-triangle-exclamation" style="color:#dc2626"></i> Cada <strong>garantía</strong> resta <strong>5 pts</strong> al score</span>
        <span><i class="fa-solid fa-clock" style="color:#64748b"></i> <strong>Pendientes</strong> (REV+OK) son contexto, no suman ni restan</span>
        <span style="margin-left:auto"><i class="fa-solid fa-scale-balanced" style="color:#6366f1"></i> Score = Pts brutos × multiplicador calidad</span>
      </div>
    <?php endif; ?>
  </div>
  <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
    <a href="index.php?ruta=tecnicos" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
      Ver todos los técnicos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
    </a>
  </div>
</div>

<script>
(function(){
  if (typeof Morris !== 'undefined') {
    new Morris.Line({
      element: 'admChartVentas',
      resize: true,
      data: <?php echo !empty($_adm_chartData) ? json_encode($_adm_chartData) : "[{y:'0',ventas:0}]"; ?>,
      xkey: 'y',
      ykeys: ['ventas'],
      labels: ['Ventas'],
      lineColors: ['#6366f1'],
      lineWidth: 2,
      hideHover: 'auto',
      gridTextColor: '#94a3b8',
      gridStrokeWidth: 0.3,
      pointSize: 4,
      pointStrokeColors: ['#6366f1'],
      gridLineColor: '#e2e8f0',
      gridTextFamily: 'inherit',
      preUnits: '$',
      gridTextSize: 10,
      fillOpacity: 0.08
    });
  }
})();
</script>

<!-- ══════════════════════════════════════════
     ÚLTIMAS ÓRDENES + PRODUCTOS
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)">
    <i class="fa-solid fa-list-check"></i>
  </div>
  <div>
    <h3>Actividad Reciente</h3>
    <p>Órdenes con actividad en los últimos 30 días</p>
  </div>
</div>

<div class="row">
  <!-- Últimas Órdenes -->
  <div class="col-lg-7 col-md-7 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-rectangle-list"></i> Últimas Órdenes</h4>
        <span class="crm-badge" style="background:#f1f5f9;color:#475569"><?php echo count($_adm_ultimasOrd); ?> recientes</span>
      </div>
      <div class="crm-card-body-flush">
        <?php if (empty($_adm_ultimasOrd)): ?>
          <div class="crm-empty"><i class="fa-solid fa-inbox"></i><strong>Sin órdenes registradas</strong></div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="crm-table">
              <thead><tr>
                <th></th>
                <th>Orden</th>
                <th>Título / Equipo</th>
                <th>Estado</th>
                <th style="text-align:right">Total</th>
                <th style="text-align:center">Fecha</th>
                <th></th>
              </tr></thead>
              <tbody>
                <?php foreach ($_adm_ultimasOrd as $o):
                  $badge = _admEstadoBadge(isset($o['estado']) ? $o['estado'] : '');
                  $img = _admGetImg($o);
                  $link = 'index.php?ruta=infoOrden&idOrden='.$o["id"]
                    .'&empresa='.(isset($o["id_empresa"]) ? $o["id_empresa"] : '')
                    .'&asesor='.(isset($o["id_Asesor"]) ? $o["id_Asesor"] : '')
                    .'&cliente='.(isset($o["id_usuario"]) ? $o["id_usuario"] : '')
                    .'&tecnico='.(isset($o["id_tecnico"]) ? $o["id_tecnico"] : '')
                    .'&tecnicodos='.(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : '')
                    .'&pedido='.(isset($o["id_pedido"]) ? $o["id_pedido"] : '');
                ?>
                <tr>
                  <td style="padding:6px 4px;width:40px">
                    <?php if (!empty($img)): ?>
                      <img src="<?php echo htmlspecialchars($img); ?>" style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0"
                           onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div style="display:none;width:36px;height:36px;border-radius:6px;background:#f1f5f9;align-items:center;justify-content:center;color:#cbd5e1;font-size:12px"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <?php else: ?>
                      <div style="width:36px;height:36px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:12px"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <?php endif; ?>
                  </td>
                  <td><span style="font-weight:700;color:#6366f1">#<?php echo htmlspecialchars($o['id']); ?></span></td>
                  <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <?php echo htmlspecialchars(isset($o['titulo']) ? $o['titulo'] : '—'); ?>
                  </td>
                  <td>
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:<?php echo $badge[0]; ?>;background:<?php echo $badge[1]; ?>;padding:3px 10px;border-radius:12px">
                      <?php echo htmlspecialchars(isset($o['estado']) ? $o['estado'] : '—'); ?>
                    </span>
                  </td>
                  <td style="text-align:right;font-weight:700">$<?php echo number_format(floatval(isset($o['total']) ? $o['total'] : 0), 0); ?></td>
                  <?php
                    $fMostrar = isset($o['_fechaSort']) ? $o['_fechaSort'] : (isset($o['fecha_ingreso']) ? substr($o['fecha_ingreso'], 0, 10) : '');
                    $fTipo = (!empty($o['fecha_Salida']) && substr($o['fecha_Salida'], 0, 10) === $fMostrar) ? 'Salida' : 'Ingreso';
                  ?>
                  <td style="text-align:center;font-size:12px;color:var(--crm-muted)">
                    <div><?php echo htmlspecialchars($fMostrar); ?></div>
                    <div style="font-size:9px;color:#94a3b8"><?php echo $fTipo; ?></div>
                  </td>
                  <td style="text-align:center">
                    <a href="<?php echo $link; ?>" target="_blank"
                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:#6366f1;color:#fff;font-size:11px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
            <a href="index.php?ruta=ordenes" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
              Ver todas las órdenes <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Productos más vendidos -->
  <div class="col-lg-5 col-md-5 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-fire"></i> Productos Top</h4>
        <span class="crm-badge" style="background:#fef2f2;color:#dc2626">Más vendidos</span>
      </div>
      <div class="crm-card-body-flush">
        <?php if (empty($_adm_productos)): ?>
          <div class="crm-empty"><i class="fa-solid fa-box-open"></i><strong>Sin datos de productos</strong></div>
        <?php else: ?>
          <?php foreach (array_slice($_adm_productos, 0, 5) as $i => $prod):
            $prodVentas = floatval(isset($prod["ventas"]) ? $prod["ventas"] : 0);
            $prodPct = $_adm_totalProdVentas > 0 ? round($prodVentas * 100 / $_adm_totalProdVentas) : 0;
            $prodColor = $_adm_prodColors[$i % count($_adm_prodColors)];
            $prodNombre = isset($prod["titulo"]) ? $prod["titulo"] : "Producto";
          ?>
            <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f8fafc;transition:background .12s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <div style="width:36px;height:36px;border-radius:8px;background:<?php echo $prodColor; ?>15;display:flex;align-items:center;justify-content:center;color:<?php echo $prodColor; ?>;font-size:14px;font-weight:800;flex-shrink:0">
                <?php echo ($i + 1); ?>
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:var(--crm-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo htmlspecialchars($prodNombre); ?></div>
                <div style="margin-top:4px;height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden">
                  <div style="height:100%;width:<?php echo $prodPct; ?>%;background:<?php echo $prodColor; ?>;border-radius:2px"></div>
                </div>
              </div>
              <div style="font-size:12px;font-weight:700;color:<?php echo $prodColor; ?>;flex-shrink:0"><?php echo $prodPct; ?>%</div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     EQUIPO: Asesores + Técnicos
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#ef4444,#f59e0b)">
    <i class="fa-solid fa-users"></i>
  </div>
  <div>
    <h3>Equipo</h3>
    <p>Asesores y técnicos registrados en la empresa</p>
  </div>
</div>

<div class="row">
  <!-- Asesores -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-headset"></i> Asesores</h4>
        <span class="crm-badge" style="background:#eef2ff;color:#4f46e5"><?php echo count($_adm_asesores); ?> registrados</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:340px;overflow-y:auto">
        <?php if (empty($_adm_asesores)): ?>
          <div class="crm-empty" style="padding:24px"><i class="fa-solid fa-user-slash"></i><strong>Sin asesores</strong></div>
        <?php else: ?>
          <?php foreach (array_slice($_adm_asesores, 0, 8) as $idx => $asesor):
            $nombre = isset($asesor["nombre"]) ? $asesor["nombre"] : "Sin nombre";
            $foto = isset($asesor["foto"]) ? $asesor["foto"] : "";
            $fecha = isset($asesor["fecha"]) ? $asesor["fecha"] : "";
            $iniciales = mb_strtoupper(mb_substr($nombre, 0, 2));
            $grad = $_adm_grads[$idx % count($_adm_grads)];
          ?>
            <div style="display:flex;align-items:center;gap:14px;padding:12px 18px;border-bottom:1px solid #f8fafc;transition:background .12s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($foto)): ?>
                <img src="<?php echo htmlspecialchars($foto); ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e2e8f0"
                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div style="display:none;width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0"><?php echo $iniciales; ?></div>
              <?php else: ?>
                <div style="width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0"><?php echo $iniciales; ?></div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:var(--crm-text)"><?php echo htmlspecialchars($nombre); ?></div>
                <div style="font-size:11px;color:var(--crm-muted)"><?php echo htmlspecialchars($fecha); ?></div>
              </div>
              <span style="font-size:10px;font-weight:600;color:#6366f1;background:#eef2ff;padding:3px 8px;border-radius:10px">Vendedor</span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=asesores" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
          Ver todos los asesores <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Técnicos -->
  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="crm-card" style="margin-bottom:20px">
      <div class="crm-card-head">
        <h4 class="crm-card-title"><i class="fa-solid fa-screwdriver-wrench"></i> Técnicos</h4>
        <span class="crm-badge" style="background:#f0fdf4;color:#16a34a"><?php echo count($_adm_tecnicos); ?> registrados</span>
      </div>
      <div class="crm-card-body-flush" style="max-height:340px;overflow-y:auto">
        <?php if (empty($_adm_tecnicos)): ?>
          <div class="crm-empty" style="padding:24px"><i class="fa-solid fa-user-slash"></i><strong>Sin técnicos</strong></div>
        <?php else: ?>
          <?php foreach (array_slice($_adm_tecnicos, 0, 8) as $idx => $tecnico):
            $nombre = isset($tecnico["nombre"]) ? $tecnico["nombre"] : "Sin nombre";
            $foto = isset($tecnico["foto"]) ? $tecnico["foto"] : "";
            $fecha = isset($tecnico["fecha"]) ? $tecnico["fecha"] : "";
            $iniciales = mb_strtoupper(mb_substr($nombre, 0, 2));
            $grad = $_adm_grads[($idx + 3) % count($_adm_grads)];
          ?>
            <div style="display:flex;align-items:center;gap:14px;padding:12px 18px;border-bottom:1px solid #f8fafc;transition:background .12s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              <?php if (!empty($foto)): ?>
                <img src="<?php echo htmlspecialchars($foto); ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e2e8f0"
                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div style="display:none;width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0"><?php echo $iniciales; ?></div>
              <?php else: ?>
                <div style="width:40px;height:40px;border-radius:50%;background:<?php echo $grad; ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0"><?php echo $iniciales; ?></div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:var(--crm-text)"><?php echo htmlspecialchars($nombre); ?></div>
                <div style="font-size:11px;color:var(--crm-muted)"><?php echo htmlspecialchars($fecha); ?></div>
              </div>
              <span style="font-size:10px;font-weight:600;color:#16a34a;background:#f0fdf4;padding:3px 8px;border-radius:10px">Técnico</span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
        <a href="index.php?ruta=tecnicos" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
          Ver todos los técnicos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     ACCIONES RÁPIDAS ADMIN
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
    <i class="fa-solid fa-rocket"></i>
  </div>
  <div>
    <h3>Acceso Rápido</h3>
    <p>Navega directamente a las secciones más utilizadas</p>
  </div>
</div>

<div class="row" style="margin-bottom:24px">
  <?php
  $_adm_acciones = array(
      array('ruta'=>'ordenes',   'icon'=>'fa-clipboard-list',  'grad'=>'#ef4444,#f87171',   'label'=>'Órdenes',        'desc'=>'Gestionar órdenes'),
      array('ruta'=>'clientes',  'icon'=>'fa-users',           'grad'=>'#3b82f6,#60a5fa',   'label'=>'Clientes',       'desc'=>'Base de clientes'),
      array('ruta'=>'ventasR',   'icon'=>'fa-chart-bar',       'grad'=>'#22c55e,#4ade80',   'label'=>'Ventas',         'desc'=>'Reportes de ventas'),
      array('ruta'=>'pedidos',   'icon'=>'fa-truck',           'grad'=>'#f59e0b,#fbbf24',   'label'=>'Pedidos',        'desc'=>'Control de pedidos'),
      array('ruta'=>'tecnicos',  'icon'=>'fa-screwdriver-wrench','grad'=>'#8b5cf6,#a78bfa', 'label'=>'Técnicos',       'desc'=>'Equipo técnico'),
      array('ruta'=>'asesores',  'icon'=>'fa-headset',         'grad'=>'#06b6d4,#22d3ee',   'label'=>'Asesores',       'desc'=>'Equipo comercial'),
  );
  foreach ($_adm_acciones as $acc): ?>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6" style="margin-bottom:12px">
      <a href="index.php?ruta=<?php echo $acc['ruta']; ?>" style="display:block;text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:var(--crm-radius);padding:20px 14px;text-align:center;transition:all .18s cubic-bezier(.4,0,.2,1)"
         onmouseover="this.style.borderColor='#6366f1';this.style.boxShadow='0 4px 16px rgba(99,102,241,.12)';this.style.transform='translateY(-2px)'"
         onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';this.style.transform='none'">
        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,<?php echo $acc['grad']; ?>);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;color:#fff;font-size:18px">
          <i class="fa-solid <?php echo $acc['icon']; ?>"></i>
        </div>
        <div style="font-size:13px;font-weight:700;color:var(--crm-text)"><?php echo $acc['label']; ?></div>
        <div style="font-size:11px;color:var(--crm-muted);margin-top:2px"><?php echo $acc['desc']; ?></div>
      </a>
    </div>
  <?php endforeach; ?>
</div>
