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

// ══════════════════════════════════════
// JSON trimmed para drill-down en JavaScript
// ══════════════════════════════════════
$_adm_ordJS = array();
foreach ($_adm_allOrders as $_o) {
    $_adm_ordJS[] = array(
        'id'    => $_o['id'],
        'est'   => isset($_o['estado']) ? $_o['estado'] : '',
        'fi'    => isset($_o['fecha_ingreso']) ? substr($_o['fecha_ingreso'], 0, 10) : '',
        'fs'    => !empty($_o['fecha_Salida']) ? substr($_o['fecha_Salida'], 0, 10) : '',
        'tec'   => isset($_o['id_tecnico']) ? $_o['id_tecnico'] : '',
        'nom'   => isset($_o['nombre']) ? $_o['nombre'] : '',
        'eq'    => isset($_o['equipo']) ? $_o['equipo'] : '',
        'marca' => isset($_o['marca']) ? $_o['marca'] : '',
        'total' => isset($_o['total']) ? floatval($_o['total']) : 0,
        'emp'   => isset($_o['id_empresa']) ? $_o['id_empresa'] : '',
        'ase'   => isset($_o['id_Asesor']) ? $_o['id_Asesor'] : '',
        'usr'   => isset($_o['id_usuario']) ? $_o['id_usuario'] : '',
        'ped'   => isset($_o['id_pedido']) ? $_o['id_pedido'] : '',
        'td'    => isset($_o['id_tecnicoDos']) ? $_o['id_tecnicoDos'] : '',
    );
}

$_adm_pipe_cortes = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '6m'  => date("Y-m-d", strtotime("-6 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
);

function _admPipeClasificar($est) {
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

$_adm_pipe_data = array();
foreach ($_adm_pipe_cortes as $periodo => $corte) {
    $_adm_pipe_data[$periodo] = array('REV'=>0, 'AUT'=>0, 'OK'=>0, 'TER'=>0, 'ENT'=>0, 'SUP'=>0, 'total'=>0);
    foreach ($_adm_allOrders as $ord) {
        $est = isset($ord["estado"]) ? $ord["estado"] : "";
        $clave = _admPipeClasificar($est);
        if ($clave === 'CAN' || $clave === 'SR' || $clave === 'PV' || $clave === 'OTR') continue;

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
// DATOS: Rendimiento de Técnicos — Scoring Justo por Periodo
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

// Periodos disponibles para el filtro
$_adm_tecPeriodos = array(
    '1m'  => date("Y-m-d", strtotime("-1 month")),
    '3m'  => date("Y-m-d", strtotime("-3 months")),
    '12m' => date("Y-m-d", strtotime("-12 months")),
    'all' => '1900-01-01',
);
$_adm_tecPeriodoLabels = array('1m'=>'Mes','3m'=>'3M','12m'=>'Año','all'=>'Todos');

// Función para calcular ranking dado un corte de fecha
function _admCalcRanking($allOrders, $mapaTec, $corte) {
    $tecStats = array();
    foreach ($mapaTec as $tid => $tn) {
        $tecStats[$tid] = array(
            'nombre' => $tn,
            'REV' => 0, 'OK' => 0, 'TER' => 0, 'ENT' => 0,
            'AUT' => 0, 'SUP' => 0, 'GAR' => 0, 'CAN' => 0,
            'total' => 0,
        );
    }

    foreach ($allOrders as $ord) {
        $tid = isset($ord["id_tecnico"]) ? $ord["id_tecnico"] : null;
        if (!$tid || !isset($mapaTec[$tid])) continue;

        $fi = isset($ord["fecha_ingreso"]) ? substr($ord["fecha_ingreso"], 0, 10) : "";
        $fs = !empty($ord["fecha_Salida"]) ? substr($ord["fecha_Salida"], 0, 10) : "";
        $fechaRef = !empty($fs) ? $fs : $fi;
        if ($fechaRef < $corte) continue;

        $est = isset($ord["estado"]) ? $ord["estado"] : "";
        $estL = strtolower($est);

        if (strpos($estL, 'garantia') !== false || strpos($estL, 'garantía') !== false) {
            $tecStats[$tid]['GAR']++;
            $tecStats[$tid]['total']++;
            continue;
        }
        if (strpos($estL, 'cancel') !== false || strpos($estL, 'can') !== false) {
            $tecStats[$tid]['CAN']++;
            $tecStats[$tid]['total']++;
            continue;
        }

        $clave = _admPipeClasificar($est);
        if ($clave === 'SR' || $clave === 'PV' || $clave === 'OTR') continue;
        if (isset($tecStats[$tid][$clave])) {
            $tecStats[$tid][$clave]++;
        }
        $tecStats[$tid]['total']++;
    }

    $ranking = array();
    foreach ($tecStats as $tid => $st) {
        $puntosA_favor = ($st['ENT'] * 3) + ($st['TER'] * 2);
        $penalizacion  = $st['GAR'] * 5;
        $puntosBrutos  = max(0, $puntosA_favor - $penalizacion);

        $pendientes    = $st['REV'] + $st['OK'];
        $completadas   = $st['TER'] + $st['ENT'];
        $totalElegible = $st['total'] - $st['CAN'];

        $ratioCalidad = $totalElegible > 0 ? round($completadas * 100 / $totalElegible) : 0;
        $multiplicador = 1.0 + (min($ratioCalidad, 100) / 100) * 0.5;
        $scoreFinal = round($puntosBrutos * $multiplicador, 1);

        if ($scoreFinal >= 100) { $nivel = 'Élite'; $nivelColor = '#f59e0b'; $nivelIcon = 'fa-crown'; }
        elseif ($scoreFinal >= 40) { $nivel = 'Pro'; $nivelColor = '#6366f1'; $nivelIcon = 'fa-gem'; }
        elseif ($scoreFinal >= 15) { $nivel = 'Activo'; $nivelColor = '#22c55e'; $nivelIcon = 'fa-bolt'; }
        elseif ($puntosBrutos > 0) { $nivel = 'Inicial'; $nivelColor = '#64748b'; $nivelIcon = 'fa-seedling'; }
        else { $nivel = '—'; $nivelColor = '#cbd5e1'; $nivelIcon = 'fa-minus'; }

        $ranking[] = array(
            'id'           => $tid,
            'nombre'       => $st['nombre'],
            'score'        => $scoreFinal,
            'puntosBrutos' => $puntosBrutos,
            'ratioCalidad' => $ratioCalidad,
            'multiplicador'=> $multiplicador,
            'totalOrd'     => $totalElegible,
            'entregadas'   => $st['ENT'],
            'terminadas'   => $st['TER'],
            'autorizacion' => $st['AUT'],
            'supervision'  => $st['SUP'],
            'pendientes'   => $pendientes,
            'revision'     => $st['REV'],
            'aceptadas'    => $st['OK'],
            'garantias'    => $st['GAR'],
            'nivel'        => $nivel,
            'nivelColor'   => $nivelColor,
            'nivelIcon'    => $nivelIcon,
        );
    }
    usort($ranking, function($a, $b) {
        if ($b['score'] != $a['score']) return $b['score'] > $a['score'] ? 1 : -1;
        return $b['totalOrd'] - $a['totalOrd'];
    });
    $ranking = array_slice($ranking, 0, 10);
    return $ranking;
}

// Pre-calcular ranking para cada periodo
$_adm_tecRankByPeriod = array();
foreach ($_adm_tecPeriodos as $pk => $corte) {
    $_adm_tecRankByPeriod[$pk] = _admCalcRanking($_adm_allOrders, $_adm_mapaTec, $corte);
}

// Default: Mes
$_adm_ranking = $_adm_tecRankByPeriod['1m'];
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

<!-- Datos para drill-down -->
<script>
var _ord=<?php echo json_encode($_adm_ordJS); ?>;
var _tecMap=<?php echo json_encode($_adm_mapaTec); ?>;
var _pipeCortes=<?php echo json_encode($_adm_pipe_cortes); ?>;
var _tecCortes=<?php echo json_encode($_adm_tecPeriodos); ?>;
</script>

<!-- ══════════════════════════════════════════
     KPIs PRINCIPALES
══════════════════════════════════════════ -->
<div class="row">
  <!-- Ventas del Mes -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8);cursor:pointer" onclick="drillKpi('ventas')">
      <i class="fa-solid fa-dollar-sign crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ventas del Mes</div>
      <div class="crm-kpi-value">$<?php echo number_format($_adm_totalVentas, 0); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-chart-line"></i> Órdenes entregadas</div>
      <div class="crm-kpi-bar"><span style="width:75%"></span></div>
    </div>
  </div>

  <!-- Ingresadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);cursor:pointer" onclick="drillKpi('ingresadas')">
      <i class="fa-solid fa-file-circle-plus crm-kpi-icon"></i>
      <div class="crm-kpi-label">Ingresadas / Mes</div>
      <div class="crm-kpi-value"><?php echo number_format($_adm_totalIngresadas); ?></div>
      <div class="crm-kpi-sub"><i class="fa-solid fa-calendar-day"></i> <?php echo $_adm_hoyIngresadas; ?> hoy</div>
      <div class="crm-kpi-bar"><span style="width:<?php echo min($_adm_hoyIngresadas * 10, 100); ?>%"></span></div>
    </div>
  </div>

  <!-- Entregadas -->
  <div class="col-lg-3 col-sm-6 col-xs-12" style="margin-bottom:16px">
    <div class="crm-kpi" style="background:linear-gradient(135deg,#22c55e,#4ade80);cursor:pointer" onclick="drillKpi('ventas')">
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
    <div class="crm-card" style="border-left:4px solid <?php echo $_adm_pctPendientes > 50 ? '#ef4444' : ($_adm_pctPendientes > 20 ? '#f59e0b' : '#22c55e'); ?>;cursor:pointer" onclick="drillKpi('pendientes')">
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
    <div class="crm-card" style="border-left:4px solid #3b82f6;cursor:pointer" onclick="drillKpi('hoyIngresadas')">
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
    <div class="crm-card" style="border-left:4px solid #22c55e;cursor:pointer" onclick="drillKpi('hoyEntregadas')">
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
    <div class="crm-card" style="border-left:4px solid <?php echo $_adm_efColor; ?>;cursor:pointer" onclick="drillKpi('ingresadas')">
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
        <div class="crm-pipe-stage" style="cursor:pointer" onclick="drillPipe('<?php echo $k; ?>')">
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
    <?php include __DIR__ . "/admin-ultimos-movimientos.php"; ?>
  </div>
  <div class="col-lg-4 col-md-5 col-xs-12">
    <?php include __DIR__ . "/admin-acciones-rapidas.php"; ?>
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

  // Renderizar al cargar el DOM completo para que el contenedor tenga dimensiones
  $(function(){
    setTimeout(function(){ renderVentasChart('1m'); }, 150);
  });

  $('#admVentasFilter').on('click', '.adm-vent-btn', function(){
    var $btn = $(this), period = $btn.data('period');
    $('#admVentasFilter .adm-vent-btn').css({background:'transparent',color:'#64748b'}).removeClass('active');
    $btn.css({background:'#6366f1',color:'#fff'}).addClass('active');
    renderVentasChart(period);
  });
})();
</script>

<!-- ══════════════════════════════════════════
     RENDIMIENTO DE TÉCNICOS — Scoring Justo con Filtro Periodo
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
    <i class="fa-solid fa-ranking-star"></i>
  </div>
  <div>
    <h3>Rendimiento de Técnicos</h3>
    <p>Garantías penalizan, pendientes como contexto</p>
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
    <h4 class="crm-card-title"><i class="fa-solid fa-trophy"></i> Ranking</h4>
    <div style="display:flex;align-items:center;gap:6px">
      <span class="crm-badge" id="admTecBadge" style="background:#fef3c7;color:#92400e"><?php echo count($_adm_ranking); ?> técnicos</span>
      <div style="display:inline-flex;background:#f1f5f9;border-radius:8px;padding:2px;gap:2px" id="admTecFilter">
        <?php foreach ($_adm_tecPeriodoLabels as $pk => $pl):
          $isActive = ($pk === '1m');
        ?>
          <button type="button" class="adm-tec-btn<?php echo $isActive ? ' active' : ''; ?>" data-period="<?php echo $pk; ?>"
                  style="padding:4px 10px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;background:<?php echo $isActive ? '#f59e0b' : 'transparent'; ?>;color:<?php echo $isActive ? '#fff' : '#64748b'; ?>"><?php echo $pl; ?></button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="crm-card-body-flush">
    <div id="admTecContent">
      <?php if (empty($_adm_ranking) || $_adm_ranking[0]['score'] == 0): ?>
        <div class="crm-empty" style="padding:30px">
          <i class="fa-solid fa-trophy" style="font-size:32px"></i>
          <strong>Sin actividad reciente</strong>
          <span style="font-size:12px">Los técnicos no tienen órdenes completadas en este periodo</span>
        </div>
      <?php else: ?>
        <!-- Header -->
        <div style="display:flex;align-items:center;padding:10px 20px;border-bottom:2px solid #e2e8f0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted)">
          <div style="width:36px;text-align:center">#</div>
          <div style="flex:1;min-width:0">Técnico</div>
          <div style="width:50px;text-align:center" title="Total de órdenes">Total</div>
          <div style="width:50px;text-align:center;color:#22c55e" title="Entregadas (+3 pts c/u)">ENT</div>
          <div style="width:50px;text-align:center;color:#06b6d4" title="Terminadas (+2 pts c/u)">TER</div>
          <div style="width:40px;text-align:center;color:#f59e0b" title="Pendiente de Autorización">AUT</div>
          <div style="width:40px;text-align:center;color:#8b5cf6" title="En Supervisión">SUP</div>
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

            <div style="width:50px;text-align:center;font-size:13px;font-weight:700;color:var(--crm-text);cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'total','1m')"><?php echo $tec['totalOrd']; ?></div>

            <div style="width:50px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'ENT','1m')"><span style="font-size:12px;font-weight:700;color:#22c55e"><?php echo $tec['entregadas']; ?></span></div>

            <div style="width:50px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'TER','1m')"><span style="font-size:12px;font-weight:700;color:#06b6d4"><?php echo $tec['terminadas']; ?></span></div>

            <div style="width:40px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'AUT','1m')"><span style="font-size:11px;font-weight:600;color:#f59e0b"><?php echo $tec['autorizacion']; ?></span></div>

            <div style="width:40px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'SUP','1m')"><span style="font-size:11px;font-weight:600;color:#8b5cf6"><?php echo $tec['supervision']; ?></span></div>

            <div style="width:70px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'pendientes','1m')">
              <span style="font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 6px;border-radius:4px" title="REV:<?php echo $tec['revision']; ?> + OK:<?php echo $tec['aceptadas']; ?>">
                <?php echo $tec['pendientes']; ?> <i class="fa-solid fa-clock" style="font-size:8px;color:#94a3b8"></i>
              </span>
            </div>

            <div style="width:50px;text-align:center;cursor:pointer" onclick="drillTec(<?php echo $tec['id']; ?>,'GAR','1m')">
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
  </div>
  <div style="text-align:center;padding:12px;border-top:1px solid #f1f5f9">
    <a href="index.php?ruta=tecnicos" style="color:#6366f1;font-size:12px;font-weight:600;text-decoration:none">
      Ver todos los técnicos <i class="fa-solid fa-arrow-right" style="font-size:10px"></i>
    </a>
  </div>
</div>

<script>
(function(){
  var tecRankData = <?php echo json_encode($_adm_tecRankByPeriod); ?>;
  var medals = ['🥇','🥈','🥉'];
  var _tecPeriod = '1m';

  function getNivelInfo(score, puntosBrutos) {
    if (score >= 100) return {nivel:'Élite', color:'#f59e0b', icon:'fa-crown'};
    if (score >= 40) return {nivel:'Pro', color:'#6366f1', icon:'fa-gem'};
    if (score >= 15) return {nivel:'Activo', color:'#22c55e', icon:'fa-bolt'};
    if (puntosBrutos > 0) return {nivel:'Inicial', color:'#64748b', icon:'fa-seedling'};
    return {nivel:'—', color:'#cbd5e1', icon:'fa-minus'};
  }

  function getQColor(ratio) {
    if (ratio >= 70) return {c:'#22c55e', bg:'#f0fdf4'};
    if (ratio >= 40) return {c:'#f59e0b', bg:'#fef3c7'};
    return {c:'#ef4444', bg:'#fef2f2'};
  }

  function renderTecRanking(ranking) {
    var $cont = $('#admTecContent');
    if (!ranking || ranking.length === 0 || ranking[0].score === 0) {
      $cont.html('<div class="crm-empty" style="padding:30px"><i class="fa-solid fa-trophy" style="font-size:32px"></i><strong>Sin actividad en este periodo</strong><span style="font-size:12px">No hay órdenes completadas</span></div>');
      $('#admTecBadge').text('0 técnicos');
      return;
    }
    var maxScore = ranking[0].score > 0 ? ranking[0].score : 1;
    var count = 0;
    var html = '<div style="display:flex;align-items:center;padding:10px 20px;border-bottom:2px solid #e2e8f0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--crm-muted)">'
      + '<div style="width:36px;text-align:center">#</div>'
      + '<div style="flex:1;min-width:0">Técnico</div>'
      + '<div style="width:50px;text-align:center">Total</div>'
      + '<div style="width:50px;text-align:center;color:#22c55e">ENT</div>'
      + '<div style="width:50px;text-align:center;color:#06b6d4">TER</div>'
      + '<div style="width:40px;text-align:center;color:#f59e0b">AUT</div>'
      + '<div style="width:40px;text-align:center;color:#8b5cf6">SUP</div>'
      + '<div style="width:70px;text-align:center;color:#64748b">Pend.</div>'
      + '<div style="width:50px;text-align:center;color:#dc2626">GAR</div>'
      + '<div style="width:55px;text-align:center">Calidad</div>'
      + '<div style="width:75px;text-align:center">Score</div>'
      + '<div style="width:65px;text-align:center">Nivel</div>'
      + '</div>';

    for (var i = 0; i < ranking.length; i++) {
      var t = ranking[i];
      if (t.score === 0 && t.totalOrd === 0) continue;
      count++;
      var pctBar = Math.round(t.score * 100 / maxScore);
      var niv = getNivelInfo(t.score, t.puntosBrutos);
      var q = getQColor(t.ratioCalidad);
      var bgRow = i === 0 ? 'background:linear-gradient(90deg,#fefce8,#fff);' : (i % 2 === 0 ? '' : 'background:#fafbfc;');
      var rank = (i < 3 && t.score > 0) ? '<span style="font-size:16px">' + medals[i] + '</span>' : '<span style="font-size:12px;font-weight:700;color:#94a3b8">' + (i+1) + '</span>';
      var gar = t.garantias > 0 ? '<span style="font-size:11px;font-weight:700;color:#dc2626;background:#fef2f2;padding:2px 6px;border-radius:4px">-' + t.garantias + '</span>' : '<span style="font-size:11px;color:#cbd5e1">0</span>';

      html += '<div style="display:flex;align-items:center;padding:12px 20px;border-bottom:1px solid #f1f5f9;' + bgRow + '">'
        + '<div style="width:36px;text-align:center;flex-shrink:0">' + rank + '</div>'
        + '<div style="flex:1;min-width:0">'
        +   '<div style="font-size:13px;font-weight:700;color:var(--crm-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px">' + $('<span>').text(t.nombre).html() + '</div>'
        +   '<div style="height:4px;background:#f1f5f9;border-radius:2px;overflow:hidden;max-width:180px"><div style="height:100%;width:' + pctBar + '%;background:linear-gradient(90deg,' + niv.color + ',' + niv.color + 'aa);border-radius:2px"></div></div>'
        + '</div>'
        + '<div onclick="drillTec('+t.id+',\'Total\',\''+_tecPeriod+'\')" style="cursor:pointer;width:50px;text-align:center;font-size:13px;font-weight:700;color:var(--crm-text)">' + t.totalOrd + '</div>'
        + '<div onclick="drillTec('+t.id+',\'ENT\',\''+_tecPeriod+'\')" style="cursor:pointer;width:50px;text-align:center"><span style="font-size:12px;font-weight:700;color:#22c55e">' + t.entregadas + '</span></div>'
        + '<div onclick="drillTec('+t.id+',\'TER\',\''+_tecPeriod+'\')" style="cursor:pointer;width:50px;text-align:center"><span style="font-size:12px;font-weight:700;color:#06b6d4">' + t.terminadas + '</span></div>'
        + '<div onclick="drillTec('+t.id+',\'AUT\',\''+_tecPeriod+'\')" style="cursor:pointer;width:40px;text-align:center"><span style="font-size:11px;font-weight:600;color:#f59e0b">' + (t.autorizacion||0) + '</span></div>'
        + '<div onclick="drillTec('+t.id+',\'SUP\',\''+_tecPeriod+'\')" style="cursor:pointer;width:40px;text-align:center"><span style="font-size:11px;font-weight:600;color:#8b5cf6">' + (t.supervision||0) + '</span></div>'
        + '<div onclick="drillTec('+t.id+',\'Pend\',\''+_tecPeriod+'\')" style="cursor:pointer;width:70px;text-align:center"><span style="font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 6px;border-radius:4px">' + t.pendientes + ' <i class="fa-solid fa-clock" style="font-size:8px;color:#94a3b8"></i></span></div>'
        + '<div onclick="drillTec('+t.id+',\'GAR\',\''+_tecPeriod+'\')" style="cursor:pointer;width:50px;text-align:center">' + gar + '</div>'
        + '<div style="width:55px;text-align:center"><span style="font-size:11px;font-weight:700;color:' + q.c + ';background:' + q.bg + ';padding:2px 6px;border-radius:4px">' + t.ratioCalidad + '%</span></div>'
        + '<div style="width:75px;text-align:center"><span style="font-size:15px;font-weight:800;color:var(--crm-text)">' + t.score + '</span><span style="font-size:9px;color:#94a3b8;display:block;margin-top:-2px">pts</span></div>'
        + '<div style="width:65px;text-align:center"><span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;color:' + niv.color + ';background:' + niv.color + '18;padding:3px 8px;border-radius:8px"><i class="fa-solid ' + niv.icon + '" style="font-size:9px"></i> ' + niv.nivel + '</span></div>'
        + '</div>';
    }

    html += '<div style="padding:12px 20px;background:#f8fafc;display:flex;flex-wrap:wrap;gap:14px;font-size:11px;color:var(--crm-muted);border-top:2px solid #e2e8f0">'
      + '<span><i class="fa-solid fa-triangle-exclamation" style="color:#dc2626"></i> Cada <strong>garantía</strong> resta <strong>5 pts</strong></span>'
      + '<span><i class="fa-solid fa-clock" style="color:#64748b"></i> <strong>Pendientes</strong> (REV+OK) son contexto</span>'
      + '<span style="margin-left:auto"><i class="fa-solid fa-scale-balanced" style="color:#6366f1"></i> Score = Pts brutos × mult. calidad</span>'
      + '</div>';

    $cont.html(html);
    $('#admTecBadge').text(count + ' técnicos');
  }

  $('#admTecFilter').on('click', '.adm-tec-btn', function(){
    var $btn = $(this), period = $btn.data('period');
    var data = tecRankData[period];
    if (!data) return;
    _tecPeriod = period;
    $('#admTecFilter .adm-tec-btn').css({background:'transparent',color:'#64748b'}).removeClass('active');
    $btn.css({background:'#f59e0b',color:'#fff'}).addClass('active');
    renderTecRanking(data);
  });
})();
</script>

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
     CAMBIOS DE ESTADO DEL DÍA
══════════════════════════════════════════ -->
<div class="crm-section">
  <div class="crm-section-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
    <i class="fa-solid fa-arrow-right-arrow-left"></i>
  </div>
  <div>
    <h3>Cambios de Estado</h3>
    <p>Movimientos de órdenes del día</p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12">
    <?php include __DIR__ . "/admin-actividad-estado.php"; ?>
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

<!-- ══════════════════════════════════════════
     MODAL DRILL-DOWN (reutilizable)
══════════════════════════════════════════ -->
<div class="modal fade" id="admDrillModal" tabindex="-1">
  <div class="modal-dialog modal-lg" style="width:90%;max-width:960px">
    <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 8px 40px rgba(15,23,42,.18)">
      <div class="modal-header" style="border-bottom:1px solid #f1f5f9;padding:18px 24px">
        <button type="button" class="close" data-dismiss="modal" style="font-size:22px;opacity:.6">&times;</button>
        <h4 class="modal-title" id="admDrillTitle" style="font-weight:800;font-size:16px;color:#0f172a;margin:0"></h4>
        <div id="admDrillCriteria" style="font-size:11px;color:#94a3b8;margin-top:4px"></div>
      </div>
      <div class="modal-body" style="padding:0;max-height:60vh;overflow-y:auto">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="background:#f8fafc;position:sticky;top:0;z-index:1">
              <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Orden</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Cliente</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Equipo</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Estado</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Ingreso</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Salida</th>
              <th style="padding:10px 8px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Técnico</th>
              <th style="padding:10px 14px;text-align:right;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px">Total</th>
            </tr>
          </thead>
          <tbody id="admDrillBody"></tbody>
        </table>
      </div>
      <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:12px 24px;display:flex;justify-content:space-between;align-items:center">
        <span id="admDrillCount" style="font-size:12px;font-weight:600;color:#94a3b8"></span>
        <span id="admDrillSum" style="font-size:13px;font-weight:700;color:#0f172a"></span>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     MOTOR JAVASCRIPT DE DRILL-DOWN
══════════════════════════════════════════ -->
<script>
(function(){
  var orders = window._ord || [];
  var tecMap = window._tecMap || {};

  // Clasificar estado (replica _admPipeClasificar de PHP)
  function classify(est) {
    if (!est) return 'OTR';
    var e = est, el = est.toLowerCase();
    if (el.indexOf('garantia') !== -1 || el.indexOf('garantía') !== -1) return 'GAR';
    if (el.indexOf('sin reparación') !== -1 || el.indexOf('sin reparacion') !== -1 || e.indexOf('SR') !== -1) return 'SR';
    if (el.indexOf('producto para venta') !== -1 || e.indexOf('PV') !== -1) return 'PV';
    if (el.indexOf('cancel') !== -1) return 'CAN';
    if (e.indexOf('AUT') !== -1) return 'AUT';
    if (e.indexOf('REV') !== -1 || e.indexOf('revisión') !== -1) return 'REV';
    if (e.indexOf('Aceptado') !== -1 || e.indexOf('ok') !== -1) return 'OK';
    if (e.indexOf('Terminada') !== -1 || e.indexOf('ter') !== -1) return 'TER';
    if (e.indexOf('Entregado') !== -1 || e.indexOf('Ent') !== -1) return 'ENT';
    if (e.indexOf('Supervisión') !== -1 || e.indexOf('SUP') !== -1) return 'SUP';
    return 'OTR';
  }

  function esc(s) { return $('<span>').text(s || '—').html(); }

  function orderLink(o) {
    return 'index.php?ruta=infoOrden&idOrden=' + o.id
      + '&empresa=' + (o.emp||'') + '&asesor=' + (o.ase||'')
      + '&cliente=' + (o.usr||'') + '&tecnico=' + (o.tec||'')
      + '&tecnicodos=' + (o.td||'') + '&pedido=' + (o.ped||'');
  }

  function isMonth(ds) {
    if (!ds) return false;
    var now = new Date();
    return parseInt(ds.substring(0,4),10) === now.getFullYear()
        && parseInt(ds.substring(5,7),10) === (now.getMonth()+1);
  }

  function today() {
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
  }

  function cutoffStr(days) {
    var d = new Date(); d.setDate(d.getDate() - days);
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
  }

  // Mostrar modal con lista filtrada
  window.admDrill = function(title, criteria, filtered) {
    $('#admDrillTitle').text(title);
    $('#admDrillCriteria').html('<i class="fa-solid fa-filter" style="margin-right:4px"></i> ' + criteria);
    var html = '', sum = 0;
    filtered.sort(function(a,b){ return (b.fi||'').localeCompare(a.fi||''); });
    for (var i = 0; i < filtered.length; i++) {
      var o = filtered[i];
      sum += o.total;
      html += '<tr style="border-bottom:1px solid #f1f5f9">'
        + '<td style="padding:10px 14px"><a href="' + orderLink(o) + '" target="_blank" style="color:#6366f1;font-weight:700;text-decoration:none">#' + o.id + '</a></td>'
        + '<td style="padding:10px 8px">' + esc(o.nom) + '</td>'
        + '<td style="padding:10px 8px;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(o.eq || o.marca) + '</td>'
        + '<td style="padding:10px 8px"><span style="font-size:10px;font-weight:600;background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:6px;white-space:nowrap">' + esc(o.est) + '</span></td>'
        + '<td style="padding:10px 8px;white-space:nowrap">' + (o.fi||'—') + '</td>'
        + '<td style="padding:10px 8px;white-space:nowrap">' + (o.fs||'—') + '</td>'
        + '<td style="padding:10px 8px">' + esc(tecMap[o.tec]) + '</td>'
        + '<td style="padding:10px 14px;text-align:right;font-weight:600">$' + Number(o.total||0).toLocaleString('en') + '</td>'
        + '</tr>';
    }
    if (!filtered.length) {
      html = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8"><i class="fa-solid fa-inbox" style="font-size:24px;display:block;margin-bottom:8px"></i>Sin órdenes para este filtro</td></tr>';
    }
    $('#admDrillBody').html(html);
    $('#admDrillCount').text(filtered.length + ' orden' + (filtered.length !== 1 ? 'es' : ''));
    $('#admDrillSum').text('Suma totales: $' + Number(sum).toLocaleString('en'));
    $('#admDrillModal').modal('show');
  };

  // ── KPI Drills ──
  window.drillKpi = function(type) {
    var hoy = today(), f = [];
    if (type === 'ventas') {
      f = orders.filter(function(o){ return o.est === 'Entregado (Ent)' && isMonth(o.fs); });
      admDrill('Órdenes Entregadas del Mes', 'estado = "Entregado (Ent)" Y mes(fecha_Salida) = mes actual', f);
    } else if (type === 'ingresadas') {
      f = orders.filter(function(o){ return isMonth(o.fi); });
      admDrill('Órdenes Ingresadas del Mes', 'mes(fecha_ingreso) = mes actual (todos los estados)', f);
    } else if (type === 'pendientes') {
      f = orders.filter(function(o){ return isMonth(o.fi) && o.est !== 'Entregado (Ent)'; });
      admDrill('Pendientes del Mes', 'mes(fecha_ingreso) = mes actual Y estado ≠ "Entregado (Ent)"', f);
    } else if (type === 'hoyIngresadas') {
      f = orders.filter(function(o){ return o.fi === hoy; });
      admDrill('Ingresadas Hoy', 'fecha_ingreso = ' + hoy, f);
    } else if (type === 'hoyEntregadas') {
      f = orders.filter(function(o){ return o.fs === hoy && o.est === 'Entregado (Ent)'; });
      admDrill('Entregadas Hoy', 'fecha_Salida = ' + hoy + ' Y estado = "Entregado (Ent)"', f);
    }
  };

  // ── Pipeline Drill ──
  window.drillPipe = function(stage) {
    var $active = $('#admPipeFilter .adm-pipe-btn.active');
    var period = $active.length ? $active.data('period') : '1m';
    var cut = (typeof _pipeCortes !== 'undefined' && _pipeCortes[period]) ? _pipeCortes[period] : cutoffStr(30);
    var labels = {REV:'Revisión',AUT:'Por Autorizar',OK:'Aceptadas',TER:'Terminadas',ENT:'Entregadas',SUP:'Supervisión'};
    var f = orders.filter(function(o) {
      var cl = classify(o.est);
      if (cl === 'CAN' || cl === 'SR' || cl === 'PV' || cl === 'OTR') return false;
      if (cl !== stage) return false;
      var ref = (cl==='ENT'||cl==='TER') ? (o.fs||o.fi) : o.fi;
      return ref >= cut;
    });
    admDrill('Pipeline: ' + (labels[stage]||stage), 'Clasificación = ' + stage + ', fecha_ref >= ' + cut + ' (periodo ' + period + ')', f);
  };

  // ── Technician Drill ──
  window.drillTec = function(tecId, column, period) {
    var cut = (typeof _tecCortes !== 'undefined' && _tecCortes[period]) ? _tecCortes[period] : cutoffStr(30);
    var cols = {total:'Todas',ENT:'Entregadas',TER:'Terminadas',AUT:'Autorización',SUP:'Supervisión',pendientes:'Pendientes (REV+OK)',GAR:'Garantías'};
    var f = orders.filter(function(o) {
      if (String(o.tec) !== String(tecId)) return false;
      var fs = o.fs||'', fi = o.fi||'';
      var ref = fs || fi;
      if (ref < cut) return false;
      var cl = classify(o.est);
      var el = (o.est||'').toLowerCase();
      var isGar = el.indexOf('garantia')!==-1 || el.indexOf('garantía')!==-1;
      var isCan = el.indexOf('cancel')!==-1;
      if (cl === 'SR' || cl === 'PV' || cl === 'OTR') return false;
      if (column === 'total') return !isCan;
      if (column === 'ENT') return cl==='ENT' && !isGar;
      if (column === 'TER') return cl==='TER' && !isGar;
      if (column === 'AUT') return cl==='AUT';
      if (column === 'SUP') return cl==='SUP';
      if (column === 'pendientes') return (cl==='REV'||cl==='OK') && !isGar && !isCan;
      if (column === 'GAR') return isGar;
      return false;
    });
    var tecName = tecMap[tecId] || 'Técnico #' + tecId;
    admDrill(tecName + ' — ' + (cols[column]||column), 'id_tecnico = ' + tecId + ', columna = ' + column + ', fecha_ref >= ' + cut + ' (' + period + ')', f);
  };

  // Exponer periodo activo de técnicos para drill-down
  window._admTecPeriod = function() {
    var $a = $('#admTecFilter .adm-tec-btn.active');
    return $a.length ? $a.data('period') : '1m';
  };
})();
</script>
