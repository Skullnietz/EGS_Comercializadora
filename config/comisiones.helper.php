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
       Se asigna cada partida cobrada y se prorratean total e inversión.
       Antes de resolver se contabiliza una sola comisión total de referencia.
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
        return ($t1 > 0 && $t2 > 0 && $t2 != $t1);
    }

    /*
     * Devuelve todas las partidas cobrables de una orden con una llave estable.
     * Las partidas en cero se conservan para mostrarlas como referencia en el
     * modal, pero no requieren asignación y no participan en el reparto.
     */
    function _comPartidasOrden($o) {

        $partidas = array();
        $nombres = array(
            "Uno", "Dos", "Tres", "Cuatro", "Cinco",
            "Seis", "Siete", "Ocho", "Nueve", "Diez"
        );

        foreach ($nombres as $indice => $nombre) {
            $descripcion = trim((string) (isset($o["partida".$nombre]) ? $o["partida".$nombre] : ""));
            $monto = floatval(isset($o["precio".$nombre]) ? $o["precio".$nombre] : 0);
            if ($descripcion === "" && abs($monto) < 0.00001) continue;

            $partidas[] = array(
                "key" => "fija-".($indice + 1),
                "descripcion" => $descripcion !== "" ? $descripcion : "Partida ".($indice + 1),
                "monto" => $monto,
                "origen" => "Partida ".($indice + 1)
            );
        }

        $adicionales = json_decode(isset($o["partidas"]) ? $o["partidas"] : "", true);
        if (is_array($adicionales)) {
            foreach ($adicionales as $indice => $partida) {
                if (!is_array($partida)) continue;
                $descripcion = trim((string) (isset($partida["descripcion"]) ? $partida["descripcion"] : ""));
                $monto = floatval(isset($partida["precioPartida"]) ? $partida["precioPartida"] : 0);
                if ($descripcion === "" && abs($monto) < 0.00001) continue;

                $partidas[] = array(
                    "key" => "adicional-".$indice,
                    "descripcion" => $descripcion !== "" ? $descripcion : "Partida adicional",
                    "monto" => $monto,
                    "origen" => "Partida adicional"
                );
            }
        }

        $descripcionRecarga = trim((string) (isset($o["recargaCartucho"]) ? $o["recargaCartucho"] : ""));
        $montoRecarga = floatval(isset($o["totalRecargaDeCartucho"]) ? $o["totalRecargaDeCartucho"] : 0);
        if ($descripcionRecarga !== "" || abs($montoRecarga) >= 0.00001) {
            $partidas[] = array(
                "key" => "recarga",
                "descripcion" => $descripcionRecarga !== "" ? $descripcionRecarga : "Recarga de cartucho",
                "monto" => $montoRecarga,
                "origen" => "Recarga de cartucho"
            );
        }

        // Se incluyen las partidas históricas capturadas específicamente para
        // el segundo técnico, pues también forman parte del total de la orden.
        $partidasTecDos = json_decode(isset($o["partidasTecnicoDos"]) ? $o["partidasTecnicoDos"] : "", true);
        if (is_array($partidasTecDos)) {
            foreach ($partidasTecDos as $indice => $partida) {
                if (!is_array($partida)) continue;
                $descripcion = trim((string) (isset($partida["descripcion"]) ? $partida["descripcion"] : ""));
                $monto = floatval(isset($partida["precioPartida"]) ? $partida["precioPartida"] : 0);
                if ($descripcion === "" && abs($monto) < 0.00001) continue;

                $partidas[] = array(
                    "key" => "tecnico-dos-".$indice,
                    "descripcion" => $descripcion !== "" ? $descripcion : "Partida 2do técnico",
                    "monto" => $monto,
                    "origen" => "Partida 2do técnico"
                );
            }
        }

        usort($partidas, function($a, $b) {
            $aPositiva = floatval($a["monto"]) > 0 ? 1 : 0;
            $bPositiva = floatval($b["monto"]) > 0 ? 1 : 0;
            if ($aPositiva !== $bPositiva) return $bPositiva - $aPositiva;
            return 0;
        });

        return $partidas;
    }

    /*
     * La huella detecta cambios que invalidan el reparto: técnicos, concepto o
     * monto de una partida. El total y la inversión pueden ajustarse sin perder
     * la asignación; se prorratean de nuevo al calcular.
     */
    function _comHuellaAsignacion($o) {

        $datos = array(
            "tecnico1" => intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0),
            "tecnico2" => intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0),
            "partidas" => array()
        );

        foreach (_comPartidasOrden($o) as $partida) {
            $datos["partidas"][] = array(
                "key" => $partida["key"],
                "descripcion" => trim((string) $partida["descripcion"]),
                "monto" => round(floatval($partida["monto"]), 4)
            );
        }

        return hash("sha256", json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    function _comLeerAsignacion($o) {
        $json = isset($o["asignacionComisionTecnicos"]) ? $o["asignacionComisionTecnicos"] : "";
        $datos = is_array($json) ? $json : json_decode((string) $json, true);
        return is_array($datos) ? $datos : array();
    }

    /*
     * Resuelve el reparto de total e inversión de la orden en proporción al
     * monto de las partidas asignadas. Esto conserva exactamente el total
     * cobrado aun cuando exista descuento, monedero o una diferencia de
     * redondeo entre la suma de partidas y el total final.
     */
    function _comDistribucionTecnicos($o) {

        $resultado = array(
            "resuelta" => false,
            "partidas" => _comPartidasOrden($o),
            "asignaciones" => array(),
            "tecnicos" => array(),
            "total_partidas" => 0.0,
            "huella" => _comHuellaAsignacion($o)
        );

        if (!_comEsDoble($o)) return $resultado;

        $t1 = intval(isset($o["id_tecnico"]) ? $o["id_tecnico"] : 0);
        $t2 = intval(isset($o["id_tecnicoDos"]) ? $o["id_tecnicoDos"] : 0);
        $permitidos = array($t1, $t2);
        $guardada = _comLeerAsignacion($o);
        $asignaciones = isset($guardada["asignaciones"]) && is_array($guardada["asignaciones"])
            ? $guardada["asignaciones"] : array();

        if (
            !isset($guardada["huella"]) ||
            !hash_equals($resultado["huella"], (string) $guardada["huella"])
        ) {
            return $resultado;
        }

        $montos = array($t1 => 0.0, $t2 => 0.0);
        $positivas = 0;

        foreach ($resultado["partidas"] as $partida) {
            $monto = floatval($partida["monto"]);
            if ($monto <= 0) continue;
            $positivas++;

            $idAsignado = isset($asignaciones[$partida["key"]])
                ? intval($asignaciones[$partida["key"]]) : 0;
            if (!in_array($idAsignado, $permitidos, true)) return $resultado;

            $resultado["asignaciones"][$partida["key"]] = $idAsignado;
            $montos[$idAsignado] += $monto;
            $resultado["total_partidas"] += $monto;
        }

        if ($positivas === 0 || $resultado["total_partidas"] <= 0) return $resultado;

        $totalOrden = floatval(isset($o["total"]) ? $o["total"] : 0);
        $inversionOrden = floatval(isset($o["totalInversion"]) ? $o["totalInversion"] : 0);

        foreach ($permitidos as $idTecnico) {
            $proporcion = $montos[$idTecnico] / $resultado["total_partidas"];
            $resultado["tecnicos"][$idTecnico] = array(
                "monto_partidas" => $montos[$idTecnico],
                "proporcion" => $proporcion,
                "total" => $totalOrden * $proporcion,
                "inversion" => $inversionOrden * $proporcion
            );
        }

        $resultado["resuelta"] = true;
        return $resultado;
    }

    function _comAsignacionResuelta($o) {
        $distribucion = _comDistribucionTecnicos($o);
        return !empty($distribucion["resuelta"]);
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
            $distribucion = $doble ? _comDistribucionTecnicos($o) : array("resuelta" => false, "tecnicos" => array());
            $hist[$k]["ordenes"]++;

            if ($modo == "tecnico") {

                $dep  = (is_array($viewer) && isset($viewer["departamento"])) ? $viewer["departamento"] : "";
                $idViewer = (is_array($viewer) && isset($viewer["id"])) ? intval($viewer["id"]) : 0;

                if ($doble && empty($distribucion["resuelta"])) {
                    $calc = _comCalcTecnico($total, $inv, $dep);
                    $hist[$k]["revision"]++;
                    $hist[$k]["revision_monto"] += $calc["comision"];
                } else {
                    $totalTec = $total;
                    $invTec = $inv;
                    if ($doble && isset($distribucion["tecnicos"][$idViewer])) {
                        $totalTec = $distribucion["tecnicos"][$idViewer]["total"];
                        $invTec = $distribucion["tecnicos"][$idViewer]["inversion"];
                    }
                    $calc = _comCalcTecnico($totalTec, $invTec, $dep);
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
                    $totalTec = $total;
                    $invTec = $inv;
                    if ($doble && !empty($distribucion["resuelta"]) && isset($distribucion["tecnicos"][$idT])) {
                        $totalTec = $distribucion["tecnicos"][$idT]["total"];
                        $invTec = $distribucion["tecnicos"][$idT]["inversion"];
                    }
                    $calcT = _comCalcTecnico($totalTec, $invTec, $dep);
                    $sumaTec += $calcT["comision"];
                }

                // Mientras no exista reparto se contabiliza una sola comisión
                // total de la orden (regla del técnico principal), no una por
                // cada técnico participante.
                if ($doble && empty($distribucion["resuelta"])) {
                    $depPrincipal = (is_array($mapaTec) && isset($mapaTec[$t1]["departamento"]))
                        ? $mapaTec[$t1]["departamento"] : "";
                    $sumaTec = _comCalcTecnico($total, $inv, $depPrincipal)["comision"];
                }

                if (($doble && empty($distribucion["resuelta"])) || $depDesconocido) {
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
                $idViewer = (is_array($viewer) && isset($viewer["id"])) ? intval($viewer["id"]) : 0;
                $doble = _comEsDoble($o);
                $distribucion = $doble ? _comDistribucionTecnicos($o) : array("resuelta" => false, "tecnicos" => array());

                if ($doble && empty($distribucion["resuelta"])) {
                    $calc = _comCalcTecnico($total, $inv, $dep);
                    $r["revision"]++;
                    $r["revision_monto"] += $calc["comision"];
                    continue;
                }

                if ($doble && isset($distribucion["tecnicos"][$idViewer])) {
                    $total = $distribucion["tecnicos"][$idViewer]["total"];
                    $inv = $distribucion["tecnicos"][$idViewer]["inversion"];
                }
                $calc = _comCalcTecnico($total, $inv, $dep);
                $r["confirmado"] += $calc["comision"];

            } else {

                $calc = _comCalcAsesor($total, $inv);
                $r["confirmado"] += $calc["comision"];
            }
        }

        return $r;
    }
}
