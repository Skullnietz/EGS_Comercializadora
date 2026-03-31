<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

header('Content-Type: application/json; charset=utf-8');

$ordenesMap = [];
$estadoMap = [];
$recogidaMap = [];
$fechaRegMap = [];
try { $ordenesMap = ControladorClientes::ctrContarOrdenesClientesBulk(); } catch(Exception $e) {}
try { $estadoMap = ControladorClientes::ctrContarOrdenesEstadoBulk(); } catch(Exception $e) {}
try { $recogidaMap = ControladorClientes::ctrPromedioRecogidaBulk(); } catch(Exception $e) {}
try { $fechaRegMap = ControladorClientes::ctrFechaRegistroClientesBulk(); } catch(Exception $e) {}
$seisAntes = date('Y-m-d H:i:s', strtotime('-6 months'));

$badges = [];
// Recopilar todos los IDs de clientes (incluidos los que tienen 0 ordenes en estadoMap)
$allClientIds = array_unique(array_merge(array_keys($ordenesMap), array_keys($estadoMap)));
foreach ($allClientIds as $cliId) {
    $totalOrd = isset($ordenesMap[$cliId]) ? $ordenesMap[$cliId] : 0;
    $ent = isset($estadoMap[$cliId]) ? $estadoMap[$cliId]["entregadas"] : 0;
    $can = isset($estadoMap[$cliId]) ? $estadoMap[$cliId]["canceladas"] : 0;
    $b = [];
    $esNuevo = ($totalOrd < 3 && isset($fechaRegMap[$cliId]) && $fechaRegMap[$cliId] >= $seisAntes);
    if ($esNuevo) {
        $b["n"] = ["fa-seedling","#fff","#8b5cf6",$totalOrd];
    } elseif (($ent + $can) > 0) {
        $r = $ent / ($ent + $can) * 100;
        if ($r >= 90)      $b["c"] = ["fa-star","#16a34a","#f0fdf4",round($r)];
        elseif ($r >= 70)  $b["c"] = ["fa-thumbs-up","#2563eb","#eff6ff",round($r)];
        elseif ($r >= 50)  $b["c"] = ["fa-minus-circle","#d97706","#fffbeb",round($r)];
        else               $b["c"] = ["fa-thumbs-down","#dc2626","#fef2f2",round($r)];
    }
    if (isset($recogidaMap[$cliId])) {
        $d = $recogidaMap[$cliId];
        if ($d <= 7)       $b["r"] = ["fa-bolt","#16a34a","#f0fdf4",$d];
        elseif ($d <= 14)  $b["r"] = ["fa-clock","#2563eb","#eff6ff",$d];
        elseif ($d <= 30)  $b["r"] = ["fa-hourglass-half","#d97706","#fffbeb",$d];
        else               $b["r"] = ["fa-hourglass-end","#dc2626","#fef2f2",$d];
    }
    if (!empty($b)) $badges[$cliId] = $b;
}

echo json_encode($badges, JSON_UNESCAPED_UNICODE);
