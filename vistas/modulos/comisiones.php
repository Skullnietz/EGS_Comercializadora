<?php
/*  ═══════════════════════════════════════════════════
    COMISIONES — Cálculo por quincena (diseño CRM)

    REGLAS DE CÁLCULO:
    ── Electrónica / Impresoras (órdenes):
       (Total − Inversión) ÷ 1.16 × 20%
    ── Sistemas (órdenes):
       Total ÷ 1.16 × 4%  (NO se descuenta la inversión)
       * En ventas de mostrador SÍ se descuenta la inversión:
         (Total − Inversión) ÷ 1.16 × 4%
    ── Asesor:
       (Total ÷ 1.16 − Inversión) × 4%
    ── Órdenes con 2 técnicos → fila amarilla "Necesita Revisión"
       Cada partida cobrada se asigna al técnico que la realizó.
       La comisión total de la orden se reparte sin duplicarse.
    ═══════════════════════════════════════════════════ */

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico" AND $_SESSION["perfil"] != "secretaria" AND $_SESSION["perfil"] != "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}

$_com_puedeResolver = in_array(
    $_SESSION["perfil"],
    array("administrador", "secretaria", "Super-Administrador"),
    true
);

$crmStyles = __DIR__ . '/partials/crm-styles.php';
if (file_exists($crmStyles)) { include $crmStyles; }

/* Fórmulas de comisión compartidas (misma fuente que los widgets de dashboard) */
require_once __DIR__ . "/../../config/comisiones.helper.php";

/* ══════════════════════════════════════
   HELPERS PROPIOS DE LA VISTA
   ══════════════════════════════════════ */
if (!function_exists('_comImg')) {

    function _comImg($o) {
        if (!empty($o["multimedia"])) {
            $album = json_decode($o["multimedia"], true);
            if (is_array($album)) {
                foreach ($album as $img) {
                    if (isset($img["foto"]) && !empty($img["foto"])) return $img["foto"];
                }
            }
        }
        return "vistas/img/default/default.png";
    }

    function _comLinkOrden($o) {
        return 'index.php?ruta=infoOrden&idOrden='.$o["id"]
            .'&empresa='.(isset($o["id_empresa"]) ? $o["id_empresa"] : '')
            .'&asesor='.(isset($o["id_Asesor"]) ? $o["id_Asesor"] : '')
            .'&cliente='.(isset($o["id_usuario"]) ? $o["id_usuario"] : '')
            .'&tecnico='.(isset($o["id_tecnico"]) ? $o["id_tecnico"] : '')
            .'&tecnicodos='.(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : '')
            .'&pedido='.(isset($o["id_pedido"]) ? $o["id_pedido"] : '');
    }

    function _comDepBadge($dep) {
        $dep = strtolower(trim($dep));
        $mapa = array(
            "electronica" => array("Electrónica", "#e0f2fe", "#0369a1"),
            "impresoras"  => array("Impresoras",  "#f3e8ff", "#7e22ce"),
            "sistemas"    => array("Sistemas",    "#dcfce7", "#15803d")
        );
        if (!isset($mapa[$dep])) {
            return '<span class="com-chip" style="background:#f1f5f9;color:#64748b">Sin depto.</span>';
        }
        $m = $mapa[$dep];
        return '<span class="com-chip" style="background:'.$m[1].';color:'.$m[2].'">'.$m[0].'</span>';
    }
}

/* ══════════════════════════════════════
   IDENTIFICAR MODO DE VISTA
   ══════════════════════════════════════ */
$_com_modo   = "admin";     // administrador y secretaria
$_com_tec    = null;
$_com_asesor = null;

if ($_SESSION["perfil"] == "tecnico") {

    $_com_modo = "tecnico";
    try {
        $r = ControladorTecnicos::ctrMostrarTecnicos("correo", $_SESSION["email"]);
        if (is_array($r) && isset($r["id"])) $_com_tec = $r;
    } catch (Exception $e) {}

} elseif ($_SESSION["perfil"] == "vendedor") {

    $_com_modo = "asesor";
    try {
        $r = Controladorasesores::ctrMostrarAsesoresEleg("correo", $_SESSION["email"]);
        if (is_array($r) && isset($r["id"])) $_com_asesor = $r;
    } catch (Exception $e) {}
}

/* ── Drill-down del administrador: ver las comisiones de un colaborador
      exactamente como las ve él (?verTec=ID o ?verAse=ID) ── */
$_com_drill = false;

if ($_com_modo == "admin") {

    if (isset($_GET["verTec"]) && intval($_GET["verTec"]) > 0) {

        try {
            $r = ControladorTecnicos::ctrMostrarTecnicos("id", intval($_GET["verTec"]));
            if (is_array($r) && isset($r["id"])) {
                $_com_tec   = $r;
                $_com_modo  = "tecnico";
                $_com_drill = true;
            }
        } catch (Exception $e) {}

    } elseif (isset($_GET["verAse"]) && intval($_GET["verAse"]) > 0) {

        try {
            $r = Controladorasesores::ctrMostrarAsesoresEleg("id", intval($_GET["verAse"]));
            if (is_array($r) && isset($r["id"])) {
                $_com_asesor = $r;
                $_com_modo   = "asesor";
                $_com_drill  = true;
            }
        } catch (Exception $e) {}
    }
}

/* ── Mes histórico seleccionado (?mes=YYYY-MM, dentro de los últimos 12 meses) ── */
$_com_mesSel   = null;
$_com_mesLabel = "";

if (isset($_GET["mes"]) && preg_match('/^\d{4}-\d{2}$/', $_GET["mes"]) && $_GET["mes"] != date("Y-m")) {

    $mesesValidos = _comMesesUltimos(12);
    if (isset($mesesValidos[$_GET["mes"]])) {
        $_com_mesSel   = $_GET["mes"];
        $_com_mesLabel = $mesesValidos[$_com_mesSel];
    }
}

/* Parámetros para conservar el estado (drill + mes) en los enlaces */
$_com_urlDrillParam = "";
if ($_com_drill) {
    $_com_urlDrillParam = ($_com_modo == "tecnico")
        ? '&verTec=' . intval($_com_tec["id"])
        : '&verAse=' . intval($_com_asesor["id"]);
}
$_com_urlMesParam = ($_com_mesSel != null) ? '&mes=' . $_com_mesSel : '';

/* ══════════════════════════════════════
   CARGAR ÓRDENES DEL MES (POR QUINCENA)
   ══════════════════════════════════════ */
$_com_q1 = array();
$_com_q2 = array();

try {

    if ($_com_mesSel != null) {

        // Mes histórico: una sola query del mes, dividida en quincenas en PHP
        $anioSel = intval(substr($_com_mesSel, 0, 4));
        $mesSelN = intval(substr($_com_mesSel, 5, 2));

        $filtroMes = "empresa";
        $idMes = intval($_SESSION["empresa"]);
        if ($_com_modo == "tecnico") {
            $filtroMes = "tecnico";
            $idMes = ($_com_tec != null) ? intval($_com_tec["id"]) : 0;
        } elseif ($_com_modo == "asesor") {
            $filtroMes = "asesor";
            $idMes = ($_com_asesor != null) ? intval($_com_asesor["id"]) : 0;
        }

        if ($idMes > 0) {
            $todasMes = controladorOrdenes::ctrMostrarComisionesDeMes($anioSel, $mesSelN, $filtroMes, $idMes);
            if (is_array($todasMes)) {
                foreach ($todasMes as $o) {
                    $dia = intval(substr($o["fecha_Salida"], 8, 2));
                    if ($dia <= 15) { $_com_q1[] = $o; } else { $_com_q2[] = $o; }
                }
            }
        }

    } elseif ($_com_modo == "tecnico") {

        if ($_com_tec != null) {
            $idTec = intval($_com_tec["id"]);
            $r1 = controladorOrdenes::ctrMostrarComisionesPorPersonaPrimera($idTec);
            $r2 = controladorOrdenes::ctrMostrarComisionesPorPersonaSegunda($idTec);
            if (is_array($r1)) $_com_q1 = $r1;
            if (is_array($r2)) $_com_q2 = $r2;
        }

    } else {

        $r1 = controladorOrdenes::ctrMostrarComisionesPrimera();
        $r2 = controladorOrdenes::ctrMostrarComisionesSegunda();
        if (is_array($r1)) $_com_q1 = $r1;
        if (is_array($r2)) $_com_q2 = $r2;
    }

} catch (Exception $e) {}

// La query de 2da quincena incluye el día 15 (DAY >= 15): se filtra aquí
// para que las quincenas queden 1–15 y 16–fin sin duplicar órdenes.
$_com_q2 = _comFiltrarSegundaQuincena($_com_q2);

// Filtros por modo: asesor → sus órdenes | admin → órdenes de su empresa
if ($_com_modo == "asesor") {

    $idAse = ($_com_asesor != null) ? intval($_com_asesor["id"]) : -1;
    $fAse = function($o) use ($idAse) {
        return intval(isset($o["id_Asesor"]) ? $o["id_Asesor"] : 0) == $idAse;
    };
    $_com_q1 = array_values(array_filter($_com_q1, $fAse));
    $_com_q2 = array_values(array_filter($_com_q2, $fAse));

} elseif ($_com_modo == "admin") {

    $idEmp = intval($_SESSION["empresa"]);
    $fEmp = function($o) use ($idEmp) {
        return intval(isset($o["id_empresa"]) ? $o["id_empresa"] : 0) == $idEmp;
    };
    $_com_q1 = array_values(array_filter($_com_q1, $fEmp));
    $_com_q2 = array_values(array_filter($_com_q2, $fEmp));
}

/* ══════════════════════════════════════
   MAPAS DE NOMBRES (consultas en batch)
   ══════════════════════════════════════ */
$_com_clientes = array();
$_com_mapaTec  = array();
$_com_mapaAse  = array();

$_com_cliIds = array();
foreach (array_merge($_com_q1, $_com_q2) as $_o) {
    if (!empty($_o["id_usuario"])) $_com_cliIds[] = intval($_o["id_usuario"]);
}
if (!empty($_com_cliIds)) {
    try {
        $_com_clientes = ControladorClientes::ctrMostrarClientesPorIds($_com_cliIds);
    } catch (Exception $e) { $_com_clientes = array(); }
}

try {
    $todosTec = ControladorTecnicos::ctrMostrarTecnicos(null, null);
    if (is_array($todosTec)) {
        foreach ($todosTec as $t) { if (isset($t["id"])) $_com_mapaTec[intval($t["id"])] = $t; }
    }
} catch (Exception $e) {}

if ($_com_modo == "admin") {
    try {
        $todosAse = Controladorasesores::ctrMostrarAsesoresEleg(null, null);
        if (is_array($todosAse)) {
            foreach ($todosAse as $a) { if (isset($a["id"])) $_com_mapaAse[intval($a["id"])] = $a; }
        }
    } catch (Exception $e) {}
}

if (!function_exists('_comNombreCliente')) {
    function _comNombreCliente($o, $mapa) {
        $id = intval(isset($o["id_usuario"]) ? $o["id_usuario"] : 0);
        if ($id > 0 && isset($mapa[$id]["nombre"])) return $mapa[$id]["nombre"];
        return "—";
    }
}

/* ══════════════════════════════════════
   CONSTRUIR FILAS + TOTALES POR QUINCENA
   ══════════════════════════════════════ */
if (!function_exists('_comProcesar')) {

    function _comProcesar($lista, $modo, $viewer, $mapaCli, $mapaTec, $mapaAse) {

        $out = array(
            "filas"          => array(),
            "confirmado"     => 0.0,   // comisión de órdenes sin bandera (técnico/asesor) o téc. confirmadas (admin)
            "asesores"       => 0.0,   // solo admin: total comisiones de asesores
            "revision"       => 0,     // órdenes marcadas "Necesita Revisión"
            "revision_monto" => 0.0,   // monto aproximado de las órdenes por revisar (referencia, aparte)
            "ordenes"        => 0
        );

        foreach ($lista as $o) {

            $total     = floatval(isset($o["total"]) ? $o["total"] : 0);
            $inversion = floatval(isset($o["totalInversion"]) ? $o["totalInversion"] : 0);
            $doble     = _comEsDoble($o);
            $distribucion = $doble
                ? _comDistribucionTecnicos($o)
                : array("resuelta" => false, "tecnicos" => array(), "partidas" => array(), "asignaciones" => array());

            $fila = array(
                "orden"     => $o,
                "cliente"   => _comNombreCliente($o, $mapaCli),
                "total"     => $total,
                "inversion" => $inversion,
                "totalOrden" => $total,
                "inversionOrden" => $inversion,
                "doble"     => $doble,
                "asignacion_resuelta" => $doble && !empty($distribucion["resuelta"]),
                "distribucion" => $distribucion,
                "revision"  => false,
                "tecnicos"  => array(),
                "asesor"    => null
            );

            if ($modo == "tecnico") {

                $dep  = isset($viewer["departamento"]) ? $viewer["departamento"] : "";
                $idYo = intval(isset($viewer["id"]) ? $viewer["id"] : 0);

                if ($fila["asignacion_resuelta"] && isset($distribucion["tecnicos"][$idYo])) {
                    $fila["total"] = $distribucion["tecnicos"][$idYo]["total"];
                    $fila["inversion"] = $distribucion["tecnicos"][$idYo]["inversion"];
                    $fila["monto_partidas"] = $distribucion["tecnicos"][$idYo]["monto_partidas"];
                }

                $calc = _comCalcTecnico($fila["total"], $fila["inversion"], $dep);

                // Nombre del otro técnico (para órdenes compartidas)
                $otro = "";
                if ($doble) {
                    $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
                    $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);
                    $idOtro = ($t1 == $idYo) ? $t2 : $t1;
                    $otro = isset($mapaTec[$idOtro]["nombre"]) ? $mapaTec[$idOtro]["nombre"] : "Técnico #".$idOtro;
                }

                $fila["calc"] = $calc;
                $fila["dep"]  = $dep;
                $fila["otro"] = $otro;
                $fila["revision"] = ($doble && !$fila["asignacion_resuelta"]);

            } elseif ($modo == "asesor") {

                $fila["calc"] = _comCalcAsesor($total, $inversion);

            } else { // admin

                $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
                $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);

                $ids = array();
                if ($t1 > 0) $ids[] = $t1;
                if ($t2 > 0 && $t2 != $t1) $ids[] = $t2;

                $depDesconocido = false;
                foreach ($ids as $idT) {
                    $nom = isset($mapaTec[$idT]["nombre"]) ? $mapaTec[$idT]["nombre"] : "Técnico #".$idT;
                    $dep = isset($mapaTec[$idT]["departamento"]) ? $mapaTec[$idT]["departamento"] : "";
                    if (!in_array(strtolower(trim($dep)), array("electronica", "impresoras", "sistemas"))) $depDesconocido = true;

                    $totalTec = $total;
                    $inversionTec = $inversion;
                    $montoPartidas = null;
                    if ($fila["asignacion_resuelta"] && isset($distribucion["tecnicos"][$idT])) {
                        $totalTec = $distribucion["tecnicos"][$idT]["total"];
                        $inversionTec = $distribucion["tecnicos"][$idT]["inversion"];
                        $montoPartidas = $distribucion["tecnicos"][$idT]["monto_partidas"];
                    }

                    $fila["tecnicos"][] = array(
                        "id"     => $idT,
                        "nombre" => $nom,
                        "dep"    => $dep,
                        "total"  => $totalTec,
                        "inversion" => $inversionTec,
                        "monto_partidas" => $montoPartidas,
                        "calc"   => _comCalcTecnico($totalTec, $inversionTec, $dep)
                    );
                }

                /*
                 * Para el total de la tabla se contabiliza una sola comisión
                 * por orden. Antes de resolver una orden doble se usa la regla
                 * del técnico principal como referencia; una vez resuelta, la
                 * comisión de la orden es la suma de sus porciones asignadas.
                 */
                $fila["comision_orden"] = 0.0;
                if ($doble && !$fila["asignacion_resuelta"]) {
                    $depPrincipal = isset($mapaTec[$t1]["departamento"]) ? $mapaTec[$t1]["departamento"] : "";
                    $fila["comision_orden"] = _comCalcTecnico($total, $inversion, $depPrincipal)["comision"];
                } else {
                    foreach ($fila["tecnicos"] as $tecnicoCalculado) {
                        $fila["comision_orden"] += $tecnicoCalculado["calc"]["comision"];
                    }
                }

                $idA = intval(isset($o["id_Asesor"]) ? $o["id_Asesor"] : 0);
                $fila["asesor"] = array(
                    "id"     => $idA,
                    "nombre" => isset($mapaAse[$idA]["nombre"]) ? $mapaAse[$idA]["nombre"] : "—",
                    "calc"   => _comCalcAsesor($total, $inversion)
                );

                // Una orden doble deja de estar pendiente al asignar todas sus
                // partidas cobradas. Un departamento desconocido sigue siendo
                // una incidencia independiente que requiere atención.
                $fila["revision"] = (($doble && !$fila["asignacion_resuelta"]) || $depDesconocido);
                $fila["departamento_desconocido"] = $depDesconocido;
            }

            // Totales
            $out["ordenes"]++;
            if ($fila["revision"]) {
                $out["revision"]++;
                if ($modo == "admin") {
                    $out["revision_monto"] += isset($fila["comision_orden"]) ? $fila["comision_orden"] : 0;
                } elseif ($modo == "tecnico") {
                    $out["revision_monto"] += $fila["calc"]["comision"];
                }
            } else {
                if ($modo == "admin") {
                    $out["confirmado"] += isset($fila["comision_orden"]) ? $fila["comision_orden"] : 0;
                } else {
                    $out["confirmado"] += $fila["calc"]["comision"];
                }
            }
            if ($modo == "admin" && $fila["asesor"] != null) {
                $out["asesores"] += $fila["asesor"]["calc"]["comision"];
            }

            $out["filas"][] = $fila;
        }

        return $out;
    }
}

$viewer = ($_com_modo == "tecnico") ? $_com_tec : $_com_asesor;
$_com_r1 = _comProcesar($_com_q1, $_com_modo, $viewer, $_com_clientes, $_com_mapaTec, $_com_mapaAse);
$_com_r2 = _comProcesar($_com_q2, $_com_modo, $viewer, $_com_clientes, $_com_mapaTec, $_com_mapaAse);

$_com_revTotal = $_com_r1["revision"] + $_com_r2["revision"];
$_com_revMonto = $_com_r1["revision_monto"] + $_com_r2["revision_monto"];
$_com_ordTotal = $_com_r1["ordenes"] + $_com_r2["ordenes"];
$_com_quincenaActual = ($_com_mesSel != null) ? 1 : ((intval(date("j")) <= 15) ? 1 : 2);

/* ══════════════════════════════════════
   RESUMEN POR COLABORADOR (solo admin)
   ══════════════════════════════════════ */
$_com_personal = array();

if ($_com_modo == "admin") {

    foreach (array_merge($_com_r1["filas"], $_com_r2["filas"]) as $f) {

        foreach ($f["tecnicos"] as $t) {

            $k = "t" . $t["id"];
            if (!isset($_com_personal[$k])) {
                $_com_personal[$k] = array(
                    "id" => $t["id"], "nombre" => $t["nombre"], "tipo" => "tecnico",
                    "dep" => $t["dep"], "total" => 0.0, "ordenes" => 0,
                    "revision" => 0, "revision_monto" => 0.0
                );
            }
            $_com_personal[$k]["ordenes"]++;
            if ($f["revision"]) {
                $_com_personal[$k]["revision"]++;
                // Sin reparto todavía no se atribuye el monto completo a cada
                // técnico; hacerlo duplicaría visualmente la comisión.
                if (!$f["doble"] || !empty($f["asignacion_resuelta"])) {
                    $_com_personal[$k]["revision_monto"] += $t["calc"]["comision"];
                }
            } else {
                $_com_personal[$k]["total"] += $t["calc"]["comision"];
            }
        }

        if ($f["asesor"] != null && $f["asesor"]["id"] > 0) {

            $k = "a" . $f["asesor"]["id"];
            if (!isset($_com_personal[$k])) {
                $_com_personal[$k] = array(
                    "id" => $f["asesor"]["id"], "nombre" => $f["asesor"]["nombre"], "tipo" => "asesor",
                    "dep" => "", "total" => 0.0, "ordenes" => 0,
                    "revision" => 0, "revision_monto" => 0.0
                );
            }
            $_com_personal[$k]["ordenes"]++;
            $_com_personal[$k]["total"] += $f["asesor"]["calc"]["comision"];
        }
    }

    // Ordenar por comisión pendiente, de mayor a menor
    uasort($_com_personal, function($a, $b) {
        if ($a["total"] == $b["total"]) return 0;
        return ($a["total"] < $b["total"]) ? 1 : -1;
    });
}

/* ══════════════════════════════════════
   HISTORIAL MENSUAL (últimos 12 meses)
   ══════════════════════════════════════ */
$_com_histMeses   = 12;
$_com_histOrdenes = array();

try {

    if ($_com_modo == "tecnico" && $_com_tec != null) {
        $_com_histOrdenes = controladorOrdenes::ctrComisionesHistorial($_com_histMeses, "tecnico", intval($_com_tec["id"]));
    } elseif ($_com_modo == "asesor" && $_com_asesor != null) {
        $_com_histOrdenes = controladorOrdenes::ctrComisionesHistorial($_com_histMeses, "asesor", intval($_com_asesor["id"]));
    } elseif ($_com_modo == "admin") {
        $_com_histOrdenes = controladorOrdenes::ctrComisionesHistorial($_com_histMeses, "empresa", intval($_SESSION["empresa"]));
    }

} catch (Exception $e) {}

$_com_hist = _comHistorialMensual($_com_histOrdenes, $_com_modo, $viewer, $_com_mapaTec, $_com_histMeses);

// Totales y escala de la gráfica
$_com_histTotConf   = 0.0;  // confirmado (técnicos en admin)
$_com_histTotAse    = 0.0;  // asesores (solo admin)
$_com_histTotRev    = 0.0;  // por revisar acumulado
$_com_histTotOrd    = 0;
$_com_histMax       = 0.0;  // barra más alta (para escalar)
$_com_histMejorK    = null;
$_com_histMejorVal  = null;
$_com_histActivos   = 0;    // meses con actividad

foreach ($_com_hist as $k => $m) {

    $_com_histTotConf += $m["confirmado"];
    $_com_histTotAse  += $m["asesores"];
    $_com_histTotRev  += $m["revision_monto"];
    $_com_histTotOrd  += $m["ordenes"];
    if ($m["ordenes"] > 0) $_com_histActivos++;

    $barraTec = max(0, $m["confirmado"]) + max(0, $m["revision_monto"]);
    $barraAse = max(0, $m["asesores"]);
    if ($barraTec > $_com_histMax) $_com_histMax = $barraTec;
    if ($barraAse > $_com_histMax) $_com_histMax = $barraAse;

    $medida = $m["confirmado"] + $m["asesores"];
    if ($_com_histMejorVal === null || $medida > $_com_histMejorVal) {
        $_com_histMejorVal = $medida;
        $_com_histMejorK   = $k;
    }
}

$_com_histPromedio = $_com_histActivos > 0
    ? ($_com_histTotConf + $_com_histTotAse) / $_com_histActivos : 0;

/* ══════════════════════════════════════
   RENDER DE FILAS
   ══════════════════════════════════════ */
if (!function_exists('_comBotonAsignacion')) {

    function _comBotonAsignacion($f) {

        global $_com_puedeResolver;

        if (empty($f["doble"])) return "";

        $tecnicos = array();
        foreach ($f["tecnicos"] as $tecnico) {
            $tecnicos[] = array(
                "id" => intval($tecnico["id"]),
                "nombre" => $tecnico["nombre"],
                "departamento" => $tecnico["dep"]
            );
        }

        $payload = array(
            "idOrden" => intval($f["orden"]["id"]),
            "cliente" => $f["cliente"],
            "tecnicos" => $tecnicos,
            "partidas" => _comPartidasOrden($f["orden"]),
            "asignaciones" => !empty($f["asignacion_resuelta"])
                ? $f["distribucion"]["asignaciones"] : array(),
            "resuelta" => !empty($f["asignacion_resuelta"]),
            "editable" => !empty($_com_puedeResolver)
        );

        $json = htmlspecialchars(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES,
            "UTF-8"
        );

        if (!empty($f["asignacion_resuelta"])) {
            return '<button type="button" class="com-chip com-chip-ok btnRevisionComision" data-comision="'.$json.'">
                        <i class="fas fa-check-circle"></i> Asignación revisada
                    </button>';
        }

        return '<button type="button" class="com-chip com-chip-rev btnRevisionComision" data-comision="'.$json.'">
                    <i class="fas fa-exclamation-triangle"></i> Necesita Revisión
                </button>';
    }
}

if (!function_exists('_comFilaPersonal')) {

    // Tabla para técnico y asesor (vista personal)
    function _comFilaPersonal($res, $modo) {

        foreach ($res["filas"] as $key => $f) {

            $o     = $f["orden"];
            $calc  = $f["calc"];
            $clase = $f["revision"] ? ' class="com-rev"' : '';
            $fecha = isset($o["fecha_Salida"]) ? substr($o["fecha_Salida"], 0, 10) : "—";

            echo '<tr'.$clase.' data-atencion="'.($f["revision"] ? "1" : "0").'">
                    <td>'.($key + 1).'</td>
                    <td><a href="'._comLinkOrden($o).'" class="com-orden">#'.$o["id"].'</a></td>
                    <td style="font-weight:600;color:var(--crm-text)">'.htmlspecialchars($f["cliente"]).'</td>
                    <td><img loading="lazy" src="'.htmlspecialchars(_comImg($o)).'" onerror="this.onerror=null;this.src=\'vistas/img/default/default.png\';" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid var(--crm-border)"></td>
                    <td>'._comMoney($f["total"]).'</td>
                    <td>'._comMoney($f["inversion"]).'</td>
                    <td>'._comMoney($calc["base"]).'</td>';

            // Comisión
            $alerta = '';
            if ($modo == "tecnico" && $f["inversion"] == 0 && strtolower(trim($f["dep"])) != "sistemas" && $calc["comision"] > 100) {
                $alerta = '<br><small style="color:#b45309"><i class="fas fa-exclamation-triangle"></i> Sin inversión registrada</small>';
            }
            if ($calc["base"] < 0) {
                $alerta = '<br><small style="color:#dc2626"><i class="fas fa-exclamation-circle"></i> Revisar inversión</small>';
            }

            echo '<td><b style="color:'.($calc["base"] < 0 ? '#dc2626' : '#15803d').'">'._comMoney($calc["comision"]).'</b> <span class="com-chip com-chip-pct">'.$calc["pct"].'%</span>'.$alerta.'</td>';

            // Estatus (solo técnico: el asesor es fijo y no lleva revisión)
            if ($modo == "tecnico") {
                if ($f["revision"]) {
                    echo '<td><span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> Necesita Revisión</span><br><small style="color:#92400e">Con: '.htmlspecialchars($f["otro"]).'</small></td>';
                } elseif (!empty($f["asignacion_resuelta"])) {
                    echo '<td><span class="com-chip com-chip-ok"><i class="fas fa-check-circle"></i> Asignación revisada</span><br><small style="color:#166534">Solo incluye sus partidas asignadas</small></td>';
                } else {
                    echo '<td><span class="com-chip com-chip-ok"><i class="fas fa-check"></i> Correcta</span></td>';
                }
            }

            echo '<td style="white-space:nowrap">'.$fecha.'</td>
                </tr>';
        }
    }

    // Tabla global para administrador / secretaria
    function _comFilaAdmin($res) {

        foreach ($res["filas"] as $key => $f) {

            $o     = $f["orden"];
            $clase = $f["revision"] ? ' class="com-rev"' : '';
            $fecha = isset($o["fecha_Salida"]) ? substr($o["fecha_Salida"], 0, 10) : "—";

            // Celdas de técnico(s) y su comisión
            $celTec = '—';
            $celCom = '—';
            if (!empty($f["tecnicos"])) {
                $nombres = array();
                $montos  = array();
                foreach ($f["tecnicos"] as $t) {
                    $nombres[] = '<div style="margin:2px 0">'.htmlspecialchars($t["nombre"]).' '._comDepBadge($t["dep"]).'</div>';
                    if (!$f["doble"] || !empty($f["asignacion_resuelta"])) {
                        $detalleAsignado = ($f["doble"] && $t["monto_partidas"] !== null)
                            ? '<br><small style="color:var(--crm-muted)">Partidas: '._comMoney($t["monto_partidas"]).'</small>'
                            : '';
                        $montos[]  = '<div style="margin:2px 0"><b style="color:'.($t["calc"]["base"] < 0 ? '#dc2626' : '#15803d').'">'._comMoney($t["calc"]["comision"]).'</b> <span class="com-chip com-chip-pct">'.$t["calc"]["pct"].'%</span>'.$detalleAsignado.'</div>';
                    }
                }
                $celTec = implode('', $nombres);
                if ($f["doble"] && empty($f["asignacion_resuelta"])) {
                    $celCom = '<div><small style="color:var(--crm-muted)">Comisión total de la orden</small><br><b style="color:#b45309">≈ '._comMoney($f["comision_orden"]).'</b></div>';
                } else {
                    $celCom = implode('', $montos);
                }
            }

            if ($f["doble"]) {
                $celCom .= '<div style="margin-top:6px">'._comBotonAsignacion($f).'</div>';
            } elseif ($f["revision"]) {
                $celCom .= '<div style="margin-top:4px"><span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> Revisar departamento</span></div>';
            }

            echo '<tr'.$clase.' data-atencion="'.($f["revision"] ? "1" : "0").'">
                    <td>'.($key + 1).'</td>
                    <td><a href="'._comLinkOrden($o).'" class="com-orden">#'.$o["id"].'</a></td>
                    <td style="font-weight:600;color:var(--crm-text)">'.htmlspecialchars($f["cliente"]).'</td>
                    <td>'.$celTec.'</td>
                    <td>'.$celCom.'</td>
                    <td>'.htmlspecialchars($f["asesor"]["nombre"]).'</td>
                    <td><b style="color:'.($f["asesor"]["calc"]["base"] < 0 ? '#dc2626' : '#15803d').'">'._comMoney($f["asesor"]["calc"]["comision"]).'</b> <span class="com-chip com-chip-pct">4%</span></td>
                    <td>'._comMoney($f["total"]).'</td>
                    <td>'._comMoney($f["inversion"]).'</td>
                    <td style="white-space:nowrap">'.$fecha.'</td>
                </tr>';
        }
    }
}
?>

<style>
  .com-chip {
    display: inline-block; padding: 2px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; white-space: nowrap;
  }
  .com-chip-pct { background: #eef2ff; color: var(--crm-accent); }
  .com-chip-ok  { background: #dcfce7; color: #15803d; }
  .com-chip-rev { background: #fde68a; color: #92400e; }
  button.com-chip {
    border: 0; cursor: pointer; font-family: inherit; line-height: 1.6;
  }
  button.com-chip:hover, button.com-chip:focus { filter: brightness(.96); outline: 2px solid rgba(99,102,241,.18); }
  tr.com-rev, tr.com-rev:hover, .crm-table tbody tr.com-rev:hover { background: #fef9c3 !important; }
  .com-orden { font-weight: 700; color: var(--crm-accent); }
  .com-orden:hover { text-decoration: underline; color: var(--crm-accent); }

  .com-banner {
    display: flex; align-items: center; gap: 14px;
    background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--crm-radius);
    padding: 14px 18px; margin-bottom: 18px; color: #92400e; font-size: 13px;
  }
  .com-banner i { font-size: 20px; color: #d97706; flex-shrink: 0; }

  .com-drill-banner {
    display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
    background: #eef2ff; border: 1px solid #c7d2fe; border-radius: var(--crm-radius);
    padding: 14px 18px; margin-bottom: 18px; color: #3730a3; font-size: 13px;
  }
  .com-drill-banner .com-volver {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
    color: #fff; font-weight: 700; font-size: 13px;
    padding: 8px 18px; border-radius: 10px; text-decoration: none;
  }
  .com-drill-banner .com-volver:hover { opacity: .92; color: #fff; }

  .com-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 800; font-size: 14px;
  }
  .com-ver-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 9px; white-space: nowrap;
    border: 1px solid var(--crm-border); background: var(--crm-surface);
    color: var(--crm-accent); font-size: 12px; font-weight: 700; text-decoration: none;
    transition: all .15s var(--crm-ease);
  }
  .com-ver-btn:hover {
    border-color: var(--crm-accent); background: #eef2ff;
    color: var(--crm-accent); text-decoration: none;
  }

  /* ── Gráfica de historial (CSS puro) ── */
  .com-dot {
    display: inline-block; width: 9px; height: 9px;
    border-radius: 3px; margin-right: 4px; vertical-align: middle;
  }
  .com-hist-chips { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
  .com-hist-chip {
    background: #f8fafc; border: 1px solid var(--crm-border); border-radius: 12px;
    padding: 10px 16px; font-size: 12px; color: var(--crm-text2); min-width: 140px;
  }
  .com-hist-chip b { display: block; font-size: 17px; color: var(--crm-text); margin-top: 2px; }
  .com-hist-chart {
    display: flex; align-items: flex-end; gap: 6px;
    height: 210px; padding: 4px 2px 0; overflow-x: auto;
  }
  .com-hist-col {
    flex: 1 1 0; min-width: 34px; height: 100%;
    display: flex; flex-direction: column; align-items: center; justify-content: flex-end;
    text-decoration: none; cursor: pointer; border-radius: 8px 8px 0 0;
    transition: background .15s var(--crm-ease);
  }
  .com-hist-col:hover, .com-hist-col:focus { text-decoration: none; background: #f1f5f9; }
  .com-hist-col.sel { background: #eef2ff; }
  .com-hist-col.sel .com-hist-lbl { color: var(--crm-accent); font-weight: 700; }
  .com-hist-val { font-size: 10px; font-weight: 700; color: var(--crm-text2); margin-bottom: 3px; white-space: nowrap; }
  .com-hist-bars { display: flex; align-items: flex-end; gap: 3px; width: 100%; justify-content: center; }
  .com-hist-stack {
    width: 100%; max-width: 26px; border-radius: 6px 6px 0 0; overflow: hidden;
    display: flex; flex-direction: column; justify-content: flex-end;
    transition: opacity .15s var(--crm-ease);
  }
  .com-hist-col:hover .com-hist-stack, .com-hist-col:hover .com-hist-ase { opacity: .8; }
  .com-hist-seg-rev  { background: #fbbf24; }
  .com-hist-seg-conf { background: linear-gradient(180deg, #6366f1, #818cf8); }
  .com-hist-ase {
    width: 100%; max-width: 26px; border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, #8b5cf6, #a78bfa);
    transition: opacity .15s var(--crm-ease);
  }
  .com-hist-lbl {
    font-size: 10px; color: var(--crm-muted); margin-top: 6px;
    text-align: center; line-height: 1.25; white-space: nowrap;
  }
  .com-hist-base { border-top: 2px solid var(--crm-border); margin-top: 0; }

  .com-toggle { display: inline-flex; background: #eef2ff; border-radius: 12px; padding: 4px; gap: 4px; }
  .com-toggle button {
    border: none; background: transparent; padding: 9px 22px; border-radius: 9px;
    font-size: 13px; font-weight: 700; color: var(--crm-text2); cursor: pointer;
    transition: all .15s var(--crm-ease);
  }
  .com-toggle button.activo {
    background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
    color: #fff; box-shadow: var(--crm-shadow);
  }

  .com-kpis { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; }
  .com-kpis .crm-kpi { flex: 1 1 200px; min-width: 200px; }

  .com-regla { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; font-size: 13px; color: var(--crm-text2); }
  .com-regla code {
    background: #f1f5f9; color: var(--crm-text); border-radius: 6px;
    padding: 2px 8px; font-size: 12px; white-space: nowrap;
  }

  .com-filtro-atencion {
    display: inline-flex; align-items: center; gap: 8px;
    background: #fff; border: 1px solid var(--crm-border); border-radius: 10px;
    padding: 6px 10px; color: var(--crm-text2); font-size: 12px; font-weight: 700;
  }
  .com-filtro-atencion select {
    border: 0; background: transparent; color: var(--crm-text); font-weight: 700;
    min-width: 190px; outline: none;
  }

  #modalAsignacionComision .modal-dialog { max-width: 880px; width: calc(100% - 30px); }
  #modalAsignacionComision .modal-content { border: 0; border-radius: 16px; overflow: hidden; }
  #modalAsignacionComision .modal-header {
    background: linear-gradient(135deg,#4338ca,#6366f1); color:#fff; border:0; padding:18px 22px;
  }
  #modalAsignacionComision .modal-title { font-weight: 800; }
  #modalAsignacionComision .close { color:#fff; opacity:.9; text-shadow:none; }
  .com-modal-ayuda {
    background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe;
    border-radius:10px; padding:10px 12px; font-size:12px; margin-bottom:14px;
  }
  .com-partida-asignacion {
    display:grid; grid-template-columns:minmax(210px,1fr) 115px minmax(280px,1.1fr);
    gap:12px; align-items:center; padding:12px 0; border-bottom:1px solid #eef2f7;
  }
  .com-partida-desc { font-weight:700; color:var(--crm-text); line-height:1.35; }
  .com-partida-desc small { display:block; color:var(--crm-muted); font-weight:500; margin-top:2px; }
  .com-partida-monto { text-align:right; font-weight:800; color:#15803d; }
  .com-tecnico-opciones { display:grid; grid-template-columns:1fr 1fr; gap:7px; }
  .com-tecnico-opcion {
    display:flex; align-items:center; justify-content:center; gap:6px; margin:0;
    border:1px solid #cbd5e1; border-radius:9px; padding:8px 9px;
    cursor:pointer; color:#475569; font-size:11px; font-weight:700; text-align:center;
  }
  .com-tecnico-opcion:has(input:checked) { border-color:#6366f1; background:#eef2ff; color:#4338ca; }
  .com-tecnico-opcion input { margin:0; }
  .com-resumen-reparto {
    display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:16px;
  }
  .com-resumen-tecnico {
    border:1px solid #dbeafe; background:#f8fafc; border-radius:10px; padding:10px 12px;
    color:var(--crm-text2); font-size:12px;
  }
  .com-resumen-tecnico b { display:block; color:var(--crm-text); font-size:15px; margin-top:2px; }
  .com-partidas-cero-wrap { margin-top:14px; border-top:1px dashed #cbd5e1; padding-top:12px; }
  .com-partida-cero { opacity:.68; }
  .com-partida-cero .com-partida-monto { color:var(--crm-muted); }
  @media (max-width: 700px) {
    .com-partida-asignacion { grid-template-columns:1fr 90px; }
    .com-tecnico-opciones { grid-column:1 / -1; }
    .com-resumen-reparto { grid-template-columns:1fr; }
  }
  .crm-table tfoot td {
    padding: 14px; background: #f8fafc; border-top: 2px solid var(--crm-border);
    font-size: 13px; color: var(--crm-text);
  }
</style>

<div class="content-wrapper" style="background: var(--crm-bg);">

  <section class="content-header">
    <h1>Comisiones</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fas fa-dashboard"></i> Inicio</a></li>
      <li class="active">Comisiones</li>
    </ol>
  </section>

  <section class="content">

    <!-- ═════ AVISO ═════ -->
    <div class="com-banner">
      <i class="fas fa-triangle-exclamation fa-exclamation-triangle"></i>
      <div>
        <b>Las comisiones se reinician al terminar el mes.</b>
        Es responsabilidad de cada colaborador descargarlas antes del cierre.
        Los montos mostrados son aproximados y están sujetos a cambios.
      </div>
    </div>

    <?php if ($_com_drill):
        $_com_drillNombre = ($_com_modo == "tecnico")
            ? (isset($_com_tec["nombre"]) ? $_com_tec["nombre"] : "")
            : (isset($_com_asesor["nombre"]) ? $_com_asesor["nombre"] : "");
    ?>
    <!-- ═════ MODO SUPERVISIÓN (drill-down del admin) ═════ -->
    <div class="com-drill-banner">
      <div>
        <i class="fas fa-eye"></i>
        <b>Vista de colaborador:</b> estás viendo las comisiones de
        <b><?php echo htmlspecialchars($_com_drillNombre); ?></b>
        exactamente como las ve en su pantalla.
      </div>
      <a href="index.php?ruta=comisiones<?php echo $_com_urlMesParam; ?>" class="com-volver">
        <i class="fas fa-arrow-left"></i> Volver al resumen general
      </a>
    </div>
    <?php endif; ?>

    <?php if ($_com_mesSel != null): ?>
    <!-- ═════ MES HISTÓRICO SELECCIONADO ═════ -->
    <div class="com-drill-banner" style="background:#f0f9ff; border-color:#bae6fd; color:#075985;">
      <div>
        <i class="fas fa-calendar-alt"></i>
        <b>Mes histórico:</b> estás viendo el detalle de
        <b><?php echo htmlspecialchars($_com_mesLabel); ?></b>.
        Montos de referencia calculados con las reglas vigentes.
      </div>
      <a href="index.php?ruta=comisiones<?php echo $_com_urlDrillParam; ?>" class="com-volver" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
        <i class="fas fa-rotate-left fa-undo"></i> Volver al mes actual
      </a>
    </div>
    <?php endif; ?>

    <?php if ($_com_modo == "tecnico" && $_com_tec == null): ?>

    <div class="crm-card">
      <div class="crm-card-body" style="text-align:center; padding:60px 20px; color:var(--crm-muted)">
        <i class="fas fa-user-slash" style="font-size:40px; display:block; margin-bottom:14px"></i>
        No se encontró tu registro de técnico asociado al correo <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b>.<br>
        Contacta al administrador para vincular tu perfil.
      </div>
    </div>

    <?php elseif ($_com_modo == "asesor" && $_com_asesor == null): ?>

    <div class="crm-card">
      <div class="crm-card-body" style="text-align:center; padding:60px 20px; color:var(--crm-muted)">
        <i class="fas fa-user-slash" style="font-size:40px; display:block; margin-bottom:14px"></i>
        No se encontró tu registro de asesor asociado al correo <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b>.<br>
        Contacta al administrador para vincular tu perfil.
      </div>
    </div>

    <?php else: ?>

    <!-- ═════ KPIs ═════ -->
    <div class="com-kpis">

      <?php if ($_com_modo == "admin"): ?>

      <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
        <i class="fas fa-tools crm-kpi-icon"></i>
        <div class="crm-kpi-label">Comisiones Técnicos / Mes</div>
        <div class="crm-kpi-value"><?php echo _comMoney($_com_r1["confirmado"] + $_com_r2["confirmado"]); ?></div>
        <div class="crm-kpi-sub"><?php echo $_com_revMonto > 0 ? 'Confirmado · aparte ≈ '._comMoney($_com_revMonto).' por revisar' : 'Monto confirmado del mes'; ?></div>
      </div>

      <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
        <i class="fas fa-handshake crm-kpi-icon"></i>
        <div class="crm-kpi-label">Comisiones Asesores / Mes</div>
        <div class="crm-kpi-value"><?php echo _comMoney($_com_r1["asesores"] + $_com_r2["asesores"]); ?></div>
        <div class="crm-kpi-sub">4% fijo por orden</div>
      </div>

      <?php else: ?>

      <div class="crm-kpi" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
        <i class="fas fa-coins crm-kpi-icon"></i>
        <div class="crm-kpi-label">1ra Quincena · Confirmado</div>
        <div class="crm-kpi-value"><?php echo _comMoney($_com_r1["confirmado"]); ?></div>
        <div class="crm-kpi-sub"><?php echo $_com_r1["ordenes"]; ?> órdenes<?php echo $_com_r1["revision"] > 0 ? ' · aparte ≈ '._comMoney($_com_r1["revision_monto"]).' por revisar' : ''; ?></div>
      </div>

      <div class="crm-kpi" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
        <i class="fas fa-coins crm-kpi-icon"></i>
        <div class="crm-kpi-label">2da Quincena · Confirmado</div>
        <div class="crm-kpi-value"><?php echo _comMoney($_com_r2["confirmado"]); ?></div>
        <div class="crm-kpi-sub"><?php echo $_com_r2["ordenes"]; ?> órdenes<?php echo $_com_r2["revision"] > 0 ? ' · aparte ≈ '._comMoney($_com_r2["revision_monto"]).' por revisar' : ''; ?></div>
      </div>

      <?php endif; ?>

      <div class="crm-kpi" style="background:linear-gradient(135deg,#22c55e,#4ade80)">
        <i class="fas fa-clipboard-check crm-kpi-icon"></i>
        <div class="crm-kpi-label">Órdenes <?php echo $_com_mesSel != null ? '· '.htmlspecialchars($_com_mesLabel) : 'del Mes'; ?></div>
        <div class="crm-kpi-value"><?php echo $_com_ordTotal; ?></div>
        <div class="crm-kpi-sub"><?php echo $_com_mesSel != null ? 'Entregadas en '.htmlspecialchars($_com_mesLabel) : 'Entregadas en el mes en curso'; ?></div>
      </div>

      <div class="crm-kpi" style="background:linear-gradient(135deg,<?php echo $_com_revTotal > 0 ? '#d97706,#f59e0b' : '#64748b,#94a3b8'; ?>)">
        <i class="fas fa-exclamation-triangle crm-kpi-icon"></i>
        <div class="crm-kpi-label">Necesitan Revisión</div>
        <div class="crm-kpi-value"><?php echo $_com_revTotal; ?></div>
        <div class="crm-kpi-sub"><?php echo $_com_revMonto > 0 ? '≈ '._comMoney($_com_revMonto).' en juego por confirmar' : 'Órdenes con 2 técnicos participando'; ?></div>
      </div>

    </div>

    <?php if ($_com_modo == "admin" && !empty($_com_personal)): ?>
    <!-- ═════ RESUMEN POR COLABORADOR ═════ -->
    <div class="crm-card" style="margin-bottom:20px;">

      <div class="crm-card-head">
        <h3 class="crm-card-title"><i class="fas fa-users"></i> Resumen por colaborador · <?php echo $_com_mesSel != null ? htmlspecialchars($_com_mesLabel) : 'mes en curso'; ?></h3>
        <span style="font-size:12px; color:var(--crm-muted)">Clic en "Ver detalle" para ver lo mismo que ve el colaborador</span>
      </div>

      <div class="crm-card-body" style="overflow-x:auto;">
        <table class="crm-table" style="width:100%">
          <thead>
            <tr>
              <th>Colaborador</th>
              <th>Perfil</th>
              <th>Órdenes</th>
              <th>Por revisar</th>
              <th>Comisión confirmada</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($_com_personal as $p):

                $nombreP = trim($p["nombre"]);
                $inicial = function_exists('mb_substr')
                    ? mb_strtoupper(mb_substr($nombreP, 0, 1, 'UTF-8'), 'UTF-8')
                    : strtoupper(substr($nombreP, 0, 1));

                if ($p["tipo"] == "tecnico") {
                    $gradAv  = "linear-gradient(135deg,#6366f1,#818cf8)";
                    $badgeP  = _comDepBadge($p["dep"]);
                    $linkVer = 'index.php?ruta=comisiones&verTec=' . $p["id"] . $_com_urlMesParam;
                } else {
                    $gradAv  = "linear-gradient(135deg,#8b5cf6,#a78bfa)";
                    $badgeP  = '<span class="com-chip" style="background:#ede9fe;color:#6d28d9">Asesor</span>';
                    $linkVer = 'index.php?ruta=comisiones&verAse=' . $p["id"] . $_com_urlMesParam;
                }
            ?>
            <tr>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <span class="com-avatar" style="background:<?php echo $gradAv; ?>"><?php echo htmlspecialchars($inicial); ?></span>
                  <span style="font-weight:600; color:var(--crm-text)"><?php echo htmlspecialchars($nombreP); ?></span>
                </div>
              </td>
              <td><?php echo $badgeP; ?></td>
              <td><?php echo $p["ordenes"]; ?></td>
              <td>
                <?php if ($p["revision"] > 0): ?>
                <span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> <?php echo $p["revision"]; ?></span>
                <?php else: ?>
                <span style="color:var(--crm-muted)">—</span>
                <?php endif; ?>
              </td>
              <td>
                <b style="color:<?php echo $p["total"] < 0 ? '#dc2626' : '#15803d'; ?>"><?php echo _comMoney($p["total"]); ?></b>
                <?php if ($p["revision_monto"] > 0): ?>
                <br><small style="color:#92400e">+ ≈ <?php echo _comMoney($p["revision_monto"]); ?> por revisar</small>
                <?php endif; ?>
              </td>
              <td style="text-align:right">
                <a href="<?php echo $linkVer; ?>" class="com-ver-btn"><i class="fas fa-eye"></i> Ver detalle</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
    <?php endif; ?>

    <!-- ═════ SELECTOR DE QUINCENA ═════ -->
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">

      <div class="com-toggle">
        <button type="button" id="btnQ1" onclick="comVerQuincena(1)"><i class="far fa-calendar"></i> 1ra Quincena (1–15)</button>
        <button type="button" id="btnQ2" onclick="comVerQuincena(2)"><i class="far fa-calendar"></i> 2da Quincena (16–fin)</button>
      </div>

      <label class="com-filtro-atencion" for="filtroAtencionComisiones">
        <i class="fas fa-filter"></i>
        <select id="filtroAtencionComisiones">
          <option value="todas">Todas las órdenes</option>
          <option value="atencion">Necesitan atención (<?php echo $_com_revTotal; ?>)</option>
          <option value="resueltas">Sin atención pendiente</option>
        </select>
      </label>

      <?php if ($_com_modo == "tecnico" && $_com_tec != null): ?>
      <div style="font-size:13px; color:var(--crm-text2)">
        <?php echo htmlspecialchars($_com_tec["nombre"]); ?> &nbsp;<?php echo _comDepBadge(isset($_com_tec["departamento"]) ? $_com_tec["departamento"] : ""); ?>
      </div>
      <?php elseif ($_com_modo == "asesor" && $_com_asesor != null): ?>
      <div style="font-size:13px; color:var(--crm-text2)">
        <?php echo htmlspecialchars($_com_asesor["nombre"]); ?> &nbsp;<span class="com-chip" style="background:#ede9fe;color:#6d28d9">Asesor</span>
      </div>
      <?php endif; ?>

    </div>

    <!-- ═════ TABLAS POR QUINCENA ═════ -->
    <?php
    $sufijoMes = ($_com_mesSel != null) ? ' · ' . $_com_mesLabel : '';
    $quincenas = array(
        1 => array("res" => $_com_r1, "titulo" => "Comisiones · 1ra Quincena" . $sufijoMes),
        2 => array("res" => $_com_r2, "titulo" => "Comisiones · 2da Quincena" . $sufijoMes)
    );

    foreach ($quincenas as $numQ => $q):
        $res = $q["res"];
    ?>

    <div class="crm-card" id="panelQ<?php echo $numQ; ?>" style="margin-bottom:20px; display:none;">

      <div class="crm-card-head">
        <h3 class="crm-card-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo $q["titulo"]; ?></h3>
        <?php if ($res["revision"] > 0): ?>
        <span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> <?php echo $res["revision"]; ?> por revisar</span>
        <?php endif; ?>
      </div>

      <div class="crm-card-body" style="overflow-x:auto;">

        <?php if ($_com_modo == "admin"): ?>

        <table class="crm-table dtComisiones" id="tablaComisiones<?php echo $numQ; ?>" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Orden</th>
              <th>Cliente</th>
              <th class="no-sort">Técnico(s)</th>
              <th class="no-sort">Com. Técnico</th>
              <th>Asesor</th>
              <th class="no-sort">Com. Asesor</th>
              <th>Total</th>
              <th>Inversión</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php _comFilaAdmin($res); ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" style="text-align:right; font-weight:700">Totales:</td>
              <td><b>Comisión de órdenes confirmada: <?php echo _comMoney($res["confirmado"]); ?></b><?php echo $res["revision"] > 0 ? '<br><small style="color:#92400e">Aparte, por revisar: ≈ '._comMoney($res["revision_monto"]).' en '.$res["revision"].' órden(es)</small>' : ''; ?><br><small style="color:var(--crm-muted)">Cada orden compartida se contabiliza una sola vez.</small></td>
              <td></td>
              <td><b>Asesores: <?php echo _comMoney($res["asesores"]); ?></b></td>
              <td colspan="3"><small style="color:var(--crm-muted)">*Montos aproximados, sujetos a cambios</small></td>
            </tr>
          </tfoot>
        </table>

        <?php else: ?>

        <table class="crm-table dtComisiones" id="tablaComisiones<?php echo $numQ; ?>" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Orden</th>
              <th>Cliente</th>
              <th class="no-sort">Imagen</th>
              <th>Total</th>
              <th>Inversión</th>
              <th>Base (s/IVA)</th>
              <th class="no-sort">Comisión</th>
              <?php if ($_com_modo == "tecnico"): ?><th class="no-sort">Estatus</th><?php endif; ?>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php _comFilaPersonal($res, $_com_modo); ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7" style="text-align:right; font-weight:700">Total confirmado:</td>
              <td colspan="<?php echo ($_com_modo == "tecnico") ? 3 : 2; ?>">
                <b style="color:#15803d"><?php echo _comMoney($res["confirmado"]); ?></b>
                <?php echo $res["revision"] > 0 ? '<br><small style="color:#92400e">Aparte, por revisar: ≈ '._comMoney($res["revision_monto"]).' en '.$res["revision"].' órden(es) — no incluido en el total</small>' : ''; ?>
                <br><small style="color:var(--crm-muted)">*Comisión aproximada, sujeta a cambios</small>
              </td>
            </tr>
          </tfoot>
        </table>

        <?php endif; ?>

      </div>

    </div>

    <?php endforeach; ?>

    <!-- ═════ HISTORIAL DE MESES + GRÁFICA ═════ -->
    <div class="crm-card" style="margin-bottom:20px;">

      <div class="crm-card-head">
        <h3 class="crm-card-title">
          <i class="fas fa-chart-bar"></i>
          Historial de comisiones · últimos <?php echo $_com_histMeses; ?> meses
          <?php if ($_com_drill || $_com_modo != "admin"): ?>
          <span style="font-weight:400; color:var(--crm-muted); font-size:12px;">(personal)</span>
          <?php else: ?>
          <span style="font-weight:400; color:var(--crm-muted); font-size:12px;">(global de la empresa)</span>
          <?php endif; ?>
        </h3>
        <div style="display:flex; gap:14px; font-size:11px; color:var(--crm-text2); flex-wrap:wrap;">
          <span><span class="com-dot" style="background:#6366f1"></span>Confirmado<?php echo $_com_modo == "admin" ? " técnicos" : ""; ?></span>
          <?php if ($_com_modo == "admin"): ?>
          <span><span class="com-dot" style="background:#8b5cf6"></span>Asesores</span>
          <?php endif; ?>
          <span><span class="com-dot" style="background:#fbbf24"></span>Por revisar</span>
        </div>
      </div>

      <div class="crm-card-body">

        <?php if ($_com_histTotOrd == 0): ?>

        <div style="text-align:center; padding:40px 20px; color:var(--crm-muted)">
          <i class="fas fa-chart-bar" style="font-size:36px; display:block; margin-bottom:12px; opacity:.4"></i>
          Sin órdenes entregadas en los últimos <?php echo $_com_histMeses; ?> meses.
        </div>

        <?php else: ?>

        <!-- Chips de resumen -->
        <div class="com-hist-chips">

          <?php if ($_com_modo == "admin"): ?>
          <div class="com-hist-chip">Técnicos · <?php echo $_com_histMeses; ?> meses<b style="color:#4338ca"><?php echo _comMoney($_com_histTotConf); ?></b></div>
          <div class="com-hist-chip">Asesores · <?php echo $_com_histMeses; ?> meses<b style="color:#6d28d9"><?php echo _comMoney($_com_histTotAse); ?></b></div>
          <?php else: ?>
          <div class="com-hist-chip">Total confirmado · <?php echo $_com_histMeses; ?> meses<b style="color:#15803d"><?php echo _comMoney($_com_histTotConf); ?></b></div>
          <?php endif; ?>

          <div class="com-hist-chip">Promedio mensual<b><?php echo _comMoney($_com_histPromedio); ?></b><small style="color:var(--crm-muted)"><?php echo $_com_histActivos; ?> mes(es) con actividad</small></div>

          <?php if ($_com_histMejorK !== null && $_com_histMejorVal > 0): ?>
          <div class="com-hist-chip">Mejor mes<b><?php echo $_com_hist[$_com_histMejorK]["label"]; ?> · <?php echo _comMoneyCorto($_com_histMejorVal); ?></b></div>
          <?php endif; ?>

          <?php if ($_com_histTotRev > 0): ?>
          <div class="com-hist-chip" style="background:#fffbeb; border-color:#fde68a;">Por revisar acumulado<b style="color:#b45309">≈ <?php echo _comMoney($_com_histTotRev); ?></b></div>
          <?php endif; ?>

        </div>

        <!-- Gráfica de barras (CSS puro, sin librerías) · clic en un mes para ver su detalle -->
        <div class="com-hist-chart">
          <?php
          $escala = ($_com_histMax > 0) ? 150 / $_com_histMax : 0;
          $mesEnCursoK = date("Y-m");
          $mesVistaK = ($_com_mesSel != null) ? $_com_mesSel : $mesEnCursoK;

          foreach ($_com_hist as $k => $m) {

              $hConf = max(0, $m["confirmado"]) * $escala;
              $hRev  = max(0, $m["revision_monto"]) * $escala;
              $hAse  = max(0, $m["asesores"]) * $escala;
              if ($m["confirmado"] > 0 && $hConf < 2) $hConf = 2;
              if ($m["revision_monto"] > 0 && $hRev < 2) $hRev = 2;
              if ($m["asesores"] > 0 && $hAse < 2) $hAse = 2;

              $tip = $m["label"] . ": Confirmado " . _comMoney($m["confirmado"]);
              if ($_com_modo == "admin") $tip .= " · Asesores " . _comMoney($m["asesores"]);
              if ($m["revision_monto"] > 0) $tip .= " · Por revisar ≈ " . _comMoney($m["revision_monto"]);
              $tip .= " · " . $m["ordenes"] . " órdenes · Clic para ver el detalle";

              $valTop = ($_com_modo != "admin" && ($m["confirmado"] + $m["revision_monto"]) != 0)
                  ? _comMoneyCorto($m["confirmado"] + $m["revision_monto"]) : "&nbsp;";

              $linkMes = 'index.php?ruta=comisiones' . $_com_urlDrillParam . ($k == $mesEnCursoK ? '' : '&mes=' . $k);
              $claseSel = ($k == $mesVistaK) ? ' sel' : '';

              echo '<a class="com-hist-col' . $claseSel . '" href="' . $linkMes . '" title="' . htmlspecialchars($tip) . '">
                      <div class="com-hist-val">' . $valTop . '</div>
                      <div class="com-hist-bars">
                        <div class="com-hist-stack">
                          <div class="com-hist-seg-rev" style="height:' . round($hRev) . 'px"></div>
                          <div class="com-hist-seg-conf" style="height:' . round($hConf) . 'px"></div>
                        </div>';
              if ($_com_modo == "admin") {
                  echo '  <div class="com-hist-ase" style="height:' . round($hAse) . 'px"></div>';
              }
              echo '  </div>
                      <div class="com-hist-lbl">' . str_replace(" ", "<br>", $m["label"]) . '</div>
                    </a>';
          }
          ?>
        </div>
        <div class="com-hist-base"></div>

        <!-- Tabla del historial (mes más reciente primero) -->
        <div style="overflow-x:auto; margin-top:18px;">
          <table class="crm-table" style="width:100%">
            <thead>
              <tr>
                <th>Mes</th>
                <th>Órdenes</th>
                <?php if ($_com_modo == "admin"): ?>
                <th>Técnicos (confirmado)</th>
                <th>Por revisar</th>
                <th>Asesores</th>
                <th>Total del mes</th>
                <?php else: ?>
                <th>Confirmado</th>
                <th>Por revisar</th>
                <?php endif; ?>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $mesActualK = date("Y-m");
              $mesVistaK = ($_com_mesSel != null) ? $_com_mesSel : $mesActualK;

              foreach (array_reverse($_com_hist, true) as $k => $m) {

                  if ($m["ordenes"] == 0 && $k != $mesActualK) continue;

                  $badgeMes = ($k == $mesActualK) ? ' <span class="com-chip" style="background:#dcfce7;color:#15803d">En curso</span>' : '';
                  if ($k == $mesVistaK) $badgeMes .= ' <span class="com-chip" style="background:#eef2ff;color:#4338ca">Viendo</span>';
                  $celRev = $m["revision_monto"] > 0
                      ? '<span style="color:#b45309">≈ ' . _comMoney($m["revision_monto"]) . ' <small>(' . $m["revision"] . ' órd.)</small></span>'
                      : '<span style="color:var(--crm-muted)">—</span>';

                  $linkMes = 'index.php?ruta=comisiones' . $_com_urlDrillParam . ($k == $mesActualK ? '' : '&mes=' . $k);
                  $estiloSel = ($k == $mesVistaK) ? ' style="background:#f5f7ff"' : '';

                  echo '<tr' . $estiloSel . '>
                          <td style="font-weight:600; color:var(--crm-text)"><a href="' . $linkMes . '" class="com-orden" title="Ver el detalle de ' . htmlspecialchars($m["label"]) . '">' . $m["label"] . '</a>' . $badgeMes . '</td>
                          <td>' . $m["ordenes"] . '</td>';

                  if ($_com_modo == "admin") {
                      echo '<td><b style="color:#4338ca">' . _comMoney($m["confirmado"]) . '</b></td>
                            <td>' . $celRev . '</td>
                            <td><b style="color:#6d28d9">' . _comMoney($m["asesores"]) . '</b></td>
                            <td><b>' . _comMoney($m["confirmado"] + $m["asesores"]) . '</b></td>';
                  } else {
                      echo '<td><b style="color:#15803d">' . _comMoney($m["confirmado"]) . '</b></td>
                            <td>' . $celRev . '</td>';
                  }

                  echo '<td style="text-align:right"><a href="' . $linkMes . '" class="com-ver-btn"><i class="fas fa-eye"></i> Ver mes</a></td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>

        <p style="margin:14px 0 0; font-size:11px; color:var(--crm-muted)">
          <i class="fas fa-info-circle"></i>
          Calculado con las reglas vigentes sobre órdenes entregadas de meses anteriores; los montos son de referencia.
        </p>

        <?php endif; ?>

      </div>

    </div>

    <!-- ═════ REGLAS DE CÁLCULO ═════ -->
    <div class="crm-card" style="margin-bottom:20px;">

      <div class="crm-card-head">
        <h3 class="crm-card-title"><i class="fas fa-calculator"></i> Reglas de cálculo vigentes</h3>
      </div>

      <div class="crm-card-body">

        <div class="com-regla">
          <?php echo _comDepBadge("electronica"); ?>
          <div>Por orden: se descuenta la inversión, se quita el IVA y se paga el 20%. &nbsp;<code>(Total − Inversión) ÷ 1.16 × 20%</code></div>
        </div>

        <div class="com-regla">
          <?php echo _comDepBadge("impresoras"); ?>
          <div>Mismo esquema que Electrónica. &nbsp;<code>(Total − Inversión) ÷ 1.16 × 20%</code></div>
        </div>

        <div class="com-regla">
          <?php echo _comDepBadge("sistemas"); ?>
          <div>
            En órdenes: se quita el IVA y se paga el 4% del total, <b>sin descontar la inversión</b>. &nbsp;<code>Total ÷ 1.16 × 4%</code><br>
            <small style="color:var(--crm-muted)">En ventas de mostrador donde participe Sistemas <b>sí</b> se descuenta la inversión: <code>(Total − Inversión) ÷ 1.16 × 4%</code>. Las ventas de mostrador aún no se listan en este módulo.</small>
          </div>
        </div>

        <div class="com-regla">
          <span class="com-chip" style="background:#ede9fe;color:#6d28d9">Asesor</span>
          <div>Precio sin IVA menos inversión, y el 4% del resultado. &nbsp;<code>(Total ÷ 1.16 − Inversión) × 4%</code></div>
        </div>

        <div class="com-regla">
          <span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> Necesita Revisión</span>
          <div>Órdenes donde participan <b>2 técnicos</b>: la comisión se marca en amarillo y <b>no se suma al total confirmado</b> hasta asignar cada partida cobrada. Mientras está pendiente se muestra <b>una sola comisión total de la orden</b>, sin duplicarla por técnico. Al resolverla, el total y la inversión se distribuyen proporcionalmente entre ambos según sus partidas. La comisión del asesor no se ve afectada.</div>
        </div>

      </div>

    </div>

    <?php endif; ?>

  </section>

</div>

<!-- Modal único para resolver la asignación de partidas en órdenes compartidas -->
<div class="modal fade" id="modalAsignacionComision" tabindex="-1" role="dialog" aria-labelledby="tituloModalAsignacionComision">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="tituloModalAsignacionComision">
          <i class="fas fa-people-arrows"></i> Asignar partidas de la orden
        </h4>
        <div id="subtituloModalAsignacionComision" style="font-size:12px;opacity:.86;margin-top:4px"></div>
      </div>
      <div class="modal-body" style="padding:20px 22px">
        <div class="com-modal-ayuda">
          <i class="fas fa-info-circle"></i>
          Elige quién realizó cada partida con monto mayor a $0. Las partidas sin monto quedan solo como referencia y no bloquean el guardado.
        </div>

        <div id="comListaPartidasPositivas"></div>

        <div class="com-partidas-cero-wrap" id="comPartidasCeroWrap" style="display:none">
          <button type="button" class="btn btn-default btn-xs" id="comTogglePartidasCero">
            <i class="fas fa-eye"></i> Ver partidas en $0 (<span id="comCantidadPartidasCero">0</span>)
          </button>
          <div id="comListaPartidasCero" style="display:none;margin-top:8px"></div>
        </div>

        <div class="com-resumen-reparto" id="comResumenReparto"></div>
        <div id="comAsignacionPendiente" style="margin-top:10px;color:#b45309;font-size:12px;font-weight:700"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarAsignacionComision" disabled>
          <i class="fas fa-save"></i> Guardar asignación
        </button>
      </div>
    </div>
  </div>
</div>

<script>

var comEsHistorico = <?php echo $_com_mesSel != null ? 'true' : 'false'; ?>;
var comAsignacionActual = null;

function comEscapar(texto) {
  return $("<div>").text(texto == null ? "" : String(texto)).html();
}

function comMoneyJs(monto) {
  return "$ " + Number(monto || 0).toLocaleString("es-MX", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

function comActualizarResumenAsignacion() {

  if (!comAsignacionActual) return;

  var totales = {};
  var pendientes = 0;
  $.each(comAsignacionActual.tecnicos, function (_, tecnico) {
    totales[String(tecnico.id)] = 0;
  });

  $("#comListaPartidasPositivas .com-partida-asignacion").each(function () {
    var monto = Number($(this).attr("data-monto")) || 0;
    var elegido = $(this).find("input[type=radio]:checked").val();
    if (!elegido) {
      pendientes++;
    } else {
      totales[String(elegido)] = (totales[String(elegido)] || 0) + monto;
    }
  });

  var resumen = "";
  $.each(comAsignacionActual.tecnicos, function (_, tecnico) {
    resumen += '<div class="com-resumen-tecnico">' +
      comEscapar(tecnico.nombre) +
      '<b>' + comMoneyJs(totales[String(tecnico.id)] || 0) + ' en partidas</b>' +
      '</div>';
  });
  $("#comResumenReparto").html(resumen);

  if (pendientes > 0) {
    $("#comAsignacionPendiente").html('<i class="fas fa-exclamation-circle"></i> Faltan ' + pendientes + ' partida(s) por asignar.');
  } else {
    $("#comAsignacionPendiente").html('<span style="color:#15803d"><i class="fas fa-check-circle"></i> Todas las partidas cobradas están asignadas.</span>');
  }

  $("#btnGuardarAsignacionComision").prop("disabled", pendientes > 0 || !comAsignacionActual.editable);
}

function comAbrirModalAsignacion(datos) {

  comAsignacionActual = datos;
  $("#subtituloModalAsignacionComision").text(
    "Orden #" + datos.idOrden + (datos.cliente ? " · " + datos.cliente : "")
  );

  var positivas = "";
  var ceros = "";
  var cantidadCeros = 0;

  $.each(datos.partidas || [], function (indice, partida) {

    var monto = Number(partida.monto) || 0;
    if (monto <= 0) {
      cantidadCeros++;
      ceros += '<div class="com-partida-asignacion com-partida-cero">' +
        '<div class="com-partida-desc">' + comEscapar(partida.descripcion) +
          '<small>' + comEscapar(partida.origen) + '</small></div>' +
        '<div class="com-partida-monto">' + comMoneyJs(monto) + '</div>' +
        '<div style="font-size:11px;color:var(--crm-muted)">No requiere asignación</div>' +
      '</div>';
      return;
    }

    var opciones = "";
    $.each(datos.tecnicos || [], function (iTec, tecnico) {
      var seleccionada = String((datos.asignaciones || {})[partida.key] || "") === String(tecnico.id);
      opciones += '<label class="com-tecnico-opcion">' +
        '<input type="radio" name="comPartida' + indice + '" data-partida="' + comEscapar(partida.key) + '" value="' + Number(tecnico.id) + '"' + (seleccionada ? " checked" : "") + '>' +
        '<span>' + comEscapar(tecnico.nombre) + '</span>' +
      '</label>';
    });

    positivas += '<div class="com-partida-asignacion" data-monto="' + monto + '">' +
      '<div class="com-partida-desc">' + comEscapar(partida.descripcion) +
        '<small>' + comEscapar(partida.origen) + '</small></div>' +
      '<div class="com-partida-monto">' + comMoneyJs(monto) + '</div>' +
      '<div class="com-tecnico-opciones">' + opciones + '</div>' +
    '</div>';
  });

  if (!positivas) {
    positivas = '<div style="padding:25px;text-align:center;color:var(--crm-muted)">La orden no tiene partidas con monto mayor a cero.</div>';
  }

  $("#comListaPartidasPositivas").html(positivas);
  $("#comListaPartidasCero").html(ceros).hide();
  $("#comCantidadPartidasCero").text(cantidadCeros);
  $("#comPartidasCeroWrap").toggle(cantidadCeros > 0);
  $("#comTogglePartidasCero").html('<i class="fas fa-eye"></i> Ver partidas en $0 (<span id="comCantidadPartidasCero">' + cantidadCeros + '</span>)');
  $("#btnGuardarAsignacionComision").toggle(!!datos.editable);
  $("#modalAsignacionComision .modal-footer .btn-default").text(datos.editable ? "Cancelar" : "Cerrar");

  comActualizarResumenAsignacion();
  $("#modalAsignacionComision").modal("show");
}

function comVerQuincena(n) {

  $("#panelQ1").toggle(n == 1);
  $("#panelQ2").toggle(n == 2);
  $("#btnQ1").toggleClass("activo", n == 1);
  $("#btnQ2").toggleClass("activo", n == 2);

  // La preferencia de quincena solo se recuerda para el mes en curso
  if (!comEsHistorico) {
    try { localStorage.setItem("comQuincena", n); } catch (e) {}
  }

  // Recalcular anchos de columnas al mostrar la tabla oculta
  if ($.fn.DataTable) {
    $(".dtComisiones").each(function () {
      if ($.fn.DataTable.isDataTable(this)) {
        $(this).DataTable().columns.adjust();
      }
    });
  }
}

$(document).ready(function () {

  if ($.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.search) {
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      if (!$(settings.nTable).hasClass("dtComisiones")) return true;

      var filtro = $("#filtroAtencionComisiones").val() || "todas";
      if (filtro === "todas") return true;

      var fila = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
      var necesitaAtencion = fila && $(fila).attr("data-atencion") === "1";
      return filtro === "atencion" ? necesitaAtencion : !necesitaAtencion;
    });
  }

  var idiomaDT = {
    "decimal": ",",
    "thousands": ".",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "loadingRecords": "Cargando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" },
    "processing": "Procesando...",
    "search": "Buscar:",
    "searchPlaceholder": "Término de búsqueda",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "No hay comisiones registradas en esta quincena"
  };

  $(".dtComisiones").each(function () {
    $(this).DataTable({
      "language": idiomaDT,
      "pageLength": 25,
      "order": [],
      "columnDefs": [{ "orderable": false, "targets": "no-sort" }]
    });
  });

  try {
    var filtroGuardado = localStorage.getItem("comFiltroAtencion");
    if (filtroGuardado === "atencion" || filtroGuardado === "resueltas") {
      $("#filtroAtencionComisiones").val(filtroGuardado);
      $(".dtComisiones").DataTable().draw();
    }
  } catch (e) {}

  $("#filtroAtencionComisiones").on("change", function () {
    try { localStorage.setItem("comFiltroAtencion", this.value); } catch (e) {}
    $(".dtComisiones").each(function () {
      if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().draw();
    });
  });

  $(document).on("click", ".btnRevisionComision", function () {
    try {
      var datos = JSON.parse($(this).attr("data-comision"));
      comAbrirModalAsignacion(datos);
    } catch (e) {
      if (window.swal) {
        swal({ type: "error", title: "No fue posible abrir la asignación", text: "Recarga la página e inténtalo de nuevo." });
      }
    }
  });

  $(document).on("change", "#comListaPartidasPositivas input[type=radio]", comActualizarResumenAsignacion);

  $("#comTogglePartidasCero").on("click", function () {
    var lista = $("#comListaPartidasCero");
    var mostrar = !lista.is(":visible");
    lista.toggle(mostrar);
    $(this).find("i").toggleClass("fa-eye", !mostrar).toggleClass("fa-eye-slash", mostrar);
  });

  $("#btnGuardarAsignacionComision").on("click", function () {

    if (!comAsignacionActual || !comAsignacionActual.editable) return;

    var asignaciones = {};
    var incompletas = 0;
    $("#comListaPartidasPositivas input[type=radio]:checked").each(function () {
      asignaciones[$(this).attr("data-partida")] = Number($(this).val());
    });
    $("#comListaPartidasPositivas .com-partida-asignacion").each(function () {
      if (!$(this).find("input[type=radio]:checked").length) incompletas++;
    });
    if (incompletas > 0) {
      comActualizarResumenAsignacion();
      return;
    }

    var boton = $(this);
    boton.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
      url: "ajax/comisiones.ajax.php",
      method: "POST",
      dataType: "json",
      data: {
        accion: "guardarAsignacionPartidas",
        idOrden: comAsignacionActual.idOrden,
        asignaciones: JSON.stringify(asignaciones)
      }
    }).done(function (respuesta) {
      if (!respuesta || !respuesta.ok) {
        var mensaje = respuesta && respuesta.mensaje ? respuesta.mensaje : "No fue posible guardar la asignación.";
        if (window.swal) swal({ type: "error", title: "Asignación no guardada", text: mensaje });
        boton.prop("disabled", false).html('<i class="fas fa-save"></i> Guardar asignación');
        return;
      }

      if (window.swal) {
        swal({ type: "success", title: "Asignación guardada", text: respuesta.mensaje, showConfirmButton: false, timer: 900 });
      }
      setTimeout(function () { window.location.reload(); }, 950);
    }).fail(function (xhr) {
      var mensaje = "No fue posible guardar la asignación.";
      if (xhr.responseJSON && xhr.responseJSON.mensaje) mensaje = xhr.responseJSON.mensaje;
      if (window.swal) swal({ type: "error", title: "Asignación no guardada", text: mensaje });
      boton.prop("disabled", false).html('<i class="fas fa-save"></i> Guardar asignación');
    });
  });

  // Mostrar por defecto la quincena en curso (o la última elegida);
  // en meses históricos siempre se abre la 1ra quincena
  var q = <?php echo $_com_quincenaActual; ?>;
  if (!comEsHistorico) {
    try {
      var guardada = localStorage.getItem("comQuincena");
      if (guardada == "1" || guardada == "2") q = parseInt(guardada, 10);
    } catch (e) {}
  }
  comVerQuincena(q);

});

</script>
