<?php
/*  ═══════════════════════════════════════════════════
    HELPER DE COMISIONES — fuente única de las fórmulas
    Usado por: vistas/modulos/comisiones.php y los widgets
    de dashboard (tec-dashboard, crm-kpis-vendedor).

    REGLAS:
    ── Electrónica / Impresoras (órdenes):
       (Total − Inversión) ÷ 1.16 × 20%
    ── Sistemas (órdenes):
       Total ÷ 1.16 × 4%  (NO se descuenta la inversión)
       * En ventas de mostrador SÍ se descuenta la inversión.
    ── Asesor:
       (Total ÷ 1.16 − Inversión) × 4%
    ── Órdenes con 2 técnicos → "Necesita Revisión"
       (no se suman a totales hasta revisarse)
    ═══════════════════════════════════════════════════ */

if (!function_exists('_comCalcTecnico')) {

    function _comCalcTecnico($total, $inversion, $departamento) {

        $dep = strtolower(trim($departamento));

        if ($dep == "sistemas") {
            // Sistemas (órdenes): se quita el IVA y se paga 4%, SIN descontar inversión
            $base = $total / 1.16;
            return array("base" => $base, "pct" => 4, "comision" => $base * 0.04);
        }

        // Electrónica e Impresoras: (Total − Inversión) ÷ 1.16 × 20%
        $base = ($total - $inversion) / 1.16;
        return array("base" => $base, "pct" => 20, "comision" => $base * 0.20);
    }

    function _comCalcAsesor($total, $inversion) {
        // Asesor: (precio sin IVA − inversión) × 4%
        $base = ($total / 1.16) - $inversion;
        return array("base" => $base, "pct" => 4, "comision" => $base * 0.04);
    }

    function _comMoney($x) {
        return '$ ' . number_format($x, 2);
    }

    function _comEsDoble($o) {
        $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
        $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);
        return ($t2 > 0 && $t2 != $t1);
    }

    /* La query de 2da quincena del modelo usa DAY >= 15; este filtro deja
       las quincenas como 1–15 y 16–fin sin duplicar el día 15. */
    function _comFiltrarSegundaQuincena($lista) {
        if (!is_array($lista)) return array();
        return array_values(array_filter($lista, function($o) {
            $f = isset($o["fecha_Salida"]) ? substr($o["fecha_Salida"], 8, 2) : "00";
            return intval($f) >= 16;
        }));
    }

    /* Formato compacto de dinero para gráficas ($1.2k) */
    function _comMoneyCorto($x) {
        if (abs($x) >= 1000) return '$' . number_format($x / 1000, 1) . 'k';
        return '$' . number_format($x, 0);
    }

    /* Últimos N meses como mapa 'YYYY-MM' => etiqueta corta ('Ene 26'), del más antiguo al actual */
    function _comMesesUltimos($n) {

        $nombres = array(1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
        $meses = array();

        for ($i = $n - 1; $i >= 0; $i--) {
            $ts = strtotime(date('Y-m-01') . " -$i months");
            $meses[date('Y-m', $ts)] = $nombres[intval(date('n', $ts))] . ' ' . date('y', $ts);
        }

        return $meses;
    }

    /* Historial mensual de comisiones a partir de órdenes entregadas.
       $modo: "tecnico" | "asesor" | "admin" ($mapaTec requerido en admin).
       Devuelve por mes ('YYYY-MM'): label, ordenes, confirmado (téc. en admin),
       asesores (solo admin), revision, revision_monto. */
    function _comHistorialMensual($ordenes, $modo, $viewer, $mapaTec, $nMeses) {

        $hist = array();
        foreach (_comMesesUltimos($nMeses) as $k => $label) {
            $hist[$k] = array(
                "label" => $label, "ordenes" => 0, "confirmado" => 0.0,
                "asesores" => 0.0, "revision" => 0, "revision_monto" => 0.0
            );
        }

        if (!is_array($ordenes)) return $hist;

        foreach ($ordenes as $o) {

            $k = isset($o["fecha_Salida"]) ? substr($o["fecha_Salida"], 0, 7) : "";
            if (!isset($hist[$k])) continue;

            $total = floatval(isset($o["total"]) ? $o["total"] : 0);
            $inv   = floatval(isset($o["totalInversion"]) ? $o["totalInversion"] : 0);
            $doble = _comEsDoble($o);
            $hist[$k]["ordenes"]++;

            if ($modo == "tecnico") {

                $dep  = (is_array($viewer) && isset($viewer["departamento"])) ? $viewer["departamento"] : "";
                $calc = _comCalcTecnico($total, $inv, $dep);

                if ($doble) {
                    $hist[$k]["revision"]++;
                    $hist[$k]["revision_monto"] += $calc["comision"];
                } else {
                    $hist[$k]["confirmado"] += $calc["comision"];
                }

            } elseif ($modo == "asesor") {

                $calc = _comCalcAsesor($total, $inv);
                $hist[$k]["confirmado"] += $calc["comision"];

            } else { // admin: técnicos + asesores de cada orden

                $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
                $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);

                $ids = array();
                if ($t1 > 0) $ids[] = $t1;
                if ($t2 > 0 && $t2 != $t1) $ids[] = $t2;

                $depDesconocido = false;
                $sumaTec = 0.0;
                foreach ($ids as $idT) {
                    $dep = (is_array($mapaTec) && isset($mapaTec[$idT]["departamento"])) ? $mapaTec[$idT]["departamento"] : "";
                    if (!in_array(strtolower(trim($dep)), array("electronica", "impresoras", "sistemas"))) $depDesconocido = true;
                    $calcT = _comCalcTecnico($total, $inv, $dep);
                    $sumaTec += $calcT["comision"];
                }

                if ($doble || $depDesconocido) {
                    $hist[$k]["revision"]++;
                    $hist[$k]["revision_monto"] += $sumaTec;
                } else {
                    $hist[$k]["confirmado"] += $sumaTec;
                }

                $calcA = _comCalcAsesor($total, $inv);
                $hist[$k]["asesores"] += $calcA["comision"];
            }
        }

        return $hist;
    }

    /* Resumen del mes para widgets de dashboard.
       $modo: "tecnico" (usa departamento de $viewer y marca dobles) o "asesor".
       Devuelve: confirmado (sin órdenes por revisar), ordenes, revision y
       revision_monto (monto aproximado de las órdenes por revisar, aparte). */
    function _comResumenMes($q1, $q2, $modo, $viewer) {

        $r = array("confirmado" => 0.0, "ordenes" => 0, "revision" => 0, "revision_monto" => 0.0);
        if (!is_array($q1)) $q1 = array();
        if (!is_array($q2)) $q2 = array();

        foreach (array_merge($q1, $q2) as $o) {

            $total = floatval(isset($o["total"]) ? $o["total"] : 0);
            $inv   = floatval(isset($o["totalInversion"]) ? $o["totalInversion"] : 0);
            $r["ordenes"]++;

            if ($modo == "tecnico") {

                $dep  = (is_array($viewer) && isset($viewer["departamento"])) ? $viewer["departamento"] : "";
                $calc = _comCalcTecnico($total, $inv, $dep);

                if (_comEsDoble($o)) {
                    $r["revision"]++;
                    $r["revision_monto"] += $calc["comision"];
                    continue;
                }

                $r["confirmado"] += $calc["comision"];

            } else {

                $calc = _comCalcAsesor($total, $inv);
                $r["confirmado"] += $calc["comision"];
            }
        }

        return $r;
    }
}
