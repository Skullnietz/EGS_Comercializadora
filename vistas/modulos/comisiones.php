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
       (más adelante se ajustará según lo que hace cada técnico)
    ═══════════════════════════════════════════════════ */

if($_SESSION["perfil"] != "administrador" AND $_SESSION["perfil"] != "vendedor" AND $_SESSION["perfil"] != "tecnico" AND $_SESSION["perfil"] != "secretaria" AND $_SESSION["perfil"] != "Super-Administrador"){

  echo '<script>

  window.location = "inicio";

  </script>';

  return;
}

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

/* ══════════════════════════════════════
   CARGAR ÓRDENES DEL MES (POR QUINCENA)
   ══════════════════════════════════════ */
$_com_q1 = array();
$_com_q2 = array();

try {

    if ($_com_modo == "tecnico") {

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

            $fila = array(
                "orden"     => $o,
                "cliente"   => _comNombreCliente($o, $mapaCli),
                "total"     => $total,
                "inversion" => $inversion,
                "doble"     => $doble,
                "revision"  => false,
                "tecnicos"  => array(),
                "asesor"    => null
            );

            if ($modo == "tecnico") {

                $dep  = isset($viewer["departamento"]) ? $viewer["departamento"] : "";
                $calc = _comCalcTecnico($total, $inversion, $dep);

                // Nombre del otro técnico (para órdenes compartidas)
                $otro = "";
                if ($doble) {
                    $idYo = intval($viewer["id"]);
                    $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
                    $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);
                    $idOtro = ($t1 == $idYo) ? $t2 : $t1;
                    $otro = isset($mapaTec[$idOtro]["nombre"]) ? $mapaTec[$idOtro]["nombre"] : "Técnico #".$idOtro;
                }

                $fila["calc"] = $calc;
                $fila["dep"]  = $dep;
                $fila["otro"] = $otro;
                $fila["revision"] = $doble;

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
                    $fila["tecnicos"][] = array(
                        "id"     => $idT,
                        "nombre" => $nom,
                        "dep"    => $dep,
                        "calc"   => _comCalcTecnico($total, $inversion, $dep)
                    );
                }

                $idA = intval(isset($o["id_Asesor"]) ? $o["id_Asesor"] : 0);
                $fila["asesor"] = array(
                    "id"     => $idA,
                    "nombre" => isset($mapaAse[$idA]["nombre"]) ? $mapaAse[$idA]["nombre"] : "—",
                    "calc"   => _comCalcAsesor($total, $inversion)
                );

                // Necesita revisión: 2 técnicos, o técnico sin departamento reconocido
                $fila["revision"] = ($doble || $depDesconocido);
            }

            // Totales
            $out["ordenes"]++;
            if ($fila["revision"]) {
                $out["revision"]++;
                if ($modo == "admin") {
                    foreach ($fila["tecnicos"] as $t) $out["revision_monto"] += $t["calc"]["comision"];
                } elseif ($modo == "tecnico") {
                    $out["revision_monto"] += $fila["calc"]["comision"];
                }
            } else {
                if ($modo == "admin") {
                    foreach ($fila["tecnicos"] as $t) $out["confirmado"] += $t["calc"]["comision"];
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
$_com_quincenaActual = (intval(date("j")) <= 15) ? 1 : 2;

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
                $_com_personal[$k]["revision_monto"] += $t["calc"]["comision"];
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
   RENDER DE FILAS
   ══════════════════════════════════════ */
if (!function_exists('_comFilaPersonal')) {

    // Tabla para técnico y asesor (vista personal)
    function _comFilaPersonal($res, $modo) {

        foreach ($res["filas"] as $key => $f) {

            $o     = $f["orden"];
            $calc  = $f["calc"];
            $clase = $f["revision"] ? ' class="com-rev"' : '';
            $fecha = isset($o["fecha_Salida"]) ? substr($o["fecha_Salida"], 0, 10) : "—";

            echo '<tr'.$clase.'>
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
                    $montos[]  = '<div style="margin:2px 0"><b style="color:'.($t["calc"]["base"] < 0 ? '#dc2626' : '#15803d').'">'._comMoney($t["calc"]["comision"]).'</b> <span class="com-chip com-chip-pct">'.$t["calc"]["pct"].'%</span></div>';
                }
                $celTec = implode('', $nombres);
                $celCom = implode('', $montos);
            }

            if ($f["revision"]) {
                $celCom .= '<div style="margin-top:4px"><span class="com-chip com-chip-rev"><i class="fas fa-exclamation-triangle"></i> Necesita Revisión</span></div>';
            }

            echo '<tr'.$clase.'>
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
      <a href="index.php?ruta=comisiones" class="com-volver">
        <i class="fas fa-arrow-left"></i> Volver al resumen general
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
        <div class="crm-kpi-label">Órdenes del Mes</div>
        <div class="crm-kpi-value"><?php echo $_com_ordTotal; ?></div>
        <div class="crm-kpi-sub">Entregadas en el mes en curso</div>
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
        <h3 class="crm-card-title"><i class="fas fa-users"></i> Resumen por colaborador · mes en curso</h3>
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
                    $linkVer = 'index.php?ruta=comisiones&verTec=' . $p["id"];
                } else {
                    $gradAv  = "linear-gradient(135deg,#8b5cf6,#a78bfa)";
                    $badgeP  = '<span class="com-chip" style="background:#ede9fe;color:#6d28d9">Asesor</span>';
                    $linkVer = 'index.php?ruta=comisiones&verAse=' . $p["id"];
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
    $quincenas = array(
        1 => array("res" => $_com_r1, "titulo" => "Comisiones · 1ra Quincena"),
        2 => array("res" => $_com_r2, "titulo" => "Comisiones · 2da Quincena")
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
              <td><b>Técnicos confirmado: <?php echo _comMoney($res["confirmado"]); ?></b><?php echo $res["revision"] > 0 ? '<br><small style="color:#92400e">Aparte, por revisar: ≈ '._comMoney($res["revision_monto"]).' en '.$res["revision"].' órden(es)</small>' : ''; ?></td>
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
          <div>Órdenes donde participan <b>2 técnicos</b>: la comisión se marca en amarillo y <b>no se suma al total confirmado</b>; su monto aproximado se muestra aparte como referencia de lo que está en juego. Más adelante se ajustará según el trabajo realizado por cada técnico. La comisión del asesor no se ve afectada.</div>
        </div>

      </div>

    </div>

    <?php endif; ?>

  </section>

</div>

<script>

function comVerQuincena(n) {

  $("#panelQ1").toggle(n == 1);
  $("#panelQ2").toggle(n == 2);
  $("#btnQ1").toggleClass("activo", n == 1);
  $("#btnQ2").toggleClass("activo", n == 2);

  try { localStorage.setItem("comQuincena", n); } catch (e) {}

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

  // Mostrar por defecto la quincena en curso (o la última elegida)
  var q = <?php echo $_com_quincenaActual; ?>;
  try {
    var guardada = localStorage.getItem("comQuincena");
    if (guardada == "1" || guardada == "2") q = parseInt(guardada, 10);
  } catch (e) {}
  comVerQuincena(q);

});

</script>
