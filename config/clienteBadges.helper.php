<?php
/**
 * Helper centralizado para badges de clientes.
 *
 * Uso:
 *   require_once "config/clienteBadges.helper.php";          // desde raíz
 *   require_once "../config/clienteBadges.helper.php";       // desde ajax/
 *
 *   $bh = ClienteBadgesHelper::getInstance();
 *   echo $bh->render($idCliente);           // íconos circulares inline
 *   echo $bh->renderWithName($nombre, $id); // nombre truncado + badges
 */
class ClienteBadgesHelper
{
    private static $instance = null;

    private $ordenesMap   = [];
    private $estadoMap    = [];
    private $recogidaMap  = [];
    private $fechaRegMap  = [];
    private $seisAntes    = '';

    private function __construct()
    {
        // Asegurar que controlador y modelo estén disponibles
        $base = defined('EGS_ROOT') ? EGS_ROOT . '/' : '';
        if (!class_exists('ControladorClientes')) {
            @require_once $base . "controladores/clientes.controlador.php";
            @require_once $base . "modelos/clientes.modelo.php";
        }

        try { $this->ordenesMap  = ControladorClientes::ctrContarOrdenesClientesBulk(); } catch(\Exception $e) {}
        try { $this->estadoMap   = ControladorClientes::ctrContarOrdenesEstadoBulk(); }   catch(\Exception $e) {}
        try { $this->recogidaMap = ControladorClientes::ctrPromedioRecogidaBulk(); }      catch(\Exception $e) {}
        try { $this->fechaRegMap = ControladorClientes::ctrFechaRegistroClientesBulk(); } catch(\Exception $e) {}
        $this->seisAntes = date('Y-m-d H:i:s', strtotime('-6 months'));
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Genera HTML de badges circulares para un cliente.
     * @param int $id  ID del cliente
     * @return string  HTML con spans de badges (puede estar vacío)
     */
    public function render($id)
    {
        $id  = intval($id);
        $html = '';

        $cOrd = isset($this->ordenesMap[$id])  ? $this->ordenesMap[$id]  : 0;
        $cEnt = isset($this->estadoMap[$id])   ? $this->estadoMap[$id]["entregadas"] : 0;
        $cCan = isset($this->estadoMap[$id])   ? $this->estadoMap[$id]["canceladas"] : 0;

        // Badge cliente nuevo: < 3 órdenes Y registrado hace < 6 meses
        $esNuevo = ($cOrd < 3 && isset($this->fechaRegMap[$id]) && $this->fechaRegMap[$id] >= $this->seisAntes);
        if ($esNuevo) {
            $html .= $this->circle('fa-seedling', '#fff', '#8b5cf6', "Cliente nuevo ({$cOrd} órdenes)");
        } elseif ($cOrd >= 3 && ($cEnt + $cCan) > 0) {
            $r = $cEnt / ($cEnt + $cCan) * 100;
            if ($r >= 90)      { $ico='fa-star';         $fg='#fff'; $bg='#16a34a'; }
            elseif ($r >= 70)  { $ico='fa-thumbs-up';    $fg='#fff'; $bg='#2563eb'; }
            elseif ($r >= 50)  { $ico='fa-minus-circle';  $fg='#fff'; $bg='#d97706'; }
            else               { $ico='fa-thumbs-down';  $fg='#fff'; $bg='#dc2626'; }
            $html .= $this->circle($ico, $fg, $bg, 'Calif: ' . round($r) . '%');
        }

        // Badge tiempo de recolección
        if (isset($this->recogidaMap[$id])) {
            $d = $this->recogidaMap[$id];
            if ($d <= 7)       { $ico='fa-bolt';            $fg='#fff'; $bg='#16a34a'; }
            elseif ($d <= 14)  { $ico='fa-clock';           $fg='#fff'; $bg='#2563eb'; }
            elseif ($d <= 30)  { $ico='fa-hourglass-half';  $fg='#fff'; $bg='#d97706'; }
            else               { $ico='fa-hourglass-end';   $fg='#fff'; $bg='#dc2626'; }
            $html .= $this->circle($ico, $fg, $bg, "Recoge: ~{$d} días", 3);
        }

        return $html;
    }

    /**
     * Nombre truncado + badges, listo para insertar en una celda de tabla.
     */
    public function renderWithName($nombre, $id)
    {
        $safe = htmlspecialchars($nombre);
        $badges = $this->render($id);
        return "<span style='display:inline-flex;align-items:center;max-width:100%;white-space:nowrap'>"
             . "<span style='overflow:hidden;text-overflow:ellipsis;max-width:120px;display:inline-block;vertical-align:middle' title='{$safe}'>{$safe}</span>"
             . $badges
             . "</span>";
    }

    /**
     * Devuelve el mapa completo de badges como array asociativo (para JSON / JS).
     */
    public function toArray()
    {
        $allIds = array_unique(array_merge(
            array_keys($this->ordenesMap),
            array_keys($this->estadoMap),
            array_keys($this->fechaRegMap)
        ));

        $badges = [];
        foreach ($allIds as $cliId) {
            $totalOrd = isset($this->ordenesMap[$cliId]) ? $this->ordenesMap[$cliId] : 0;
            $ent = isset($this->estadoMap[$cliId]) ? $this->estadoMap[$cliId]["entregadas"] : 0;
            $can = isset($this->estadoMap[$cliId]) ? $this->estadoMap[$cliId]["canceladas"] : 0;
            $b = [];

            $esNuevo = ($totalOrd < 3 && isset($this->fechaRegMap[$cliId]) && $this->fechaRegMap[$cliId] >= $this->seisAntes);
            if ($esNuevo) {
                $b["n"] = ["fa-seedling","#fff","#8b5cf6",$totalOrd];
            } elseif ($totalOrd >= 3 && ($ent + $can) > 0) {
                $r = $ent / ($ent + $can) * 100;
                if ($r >= 90)      $b["c"] = ["fa-star","#16a34a","#f0fdf4",round($r)];
                elseif ($r >= 70)  $b["c"] = ["fa-thumbs-up","#2563eb","#eff6ff",round($r)];
                elseif ($r >= 50)  $b["c"] = ["fa-minus-circle","#d97706","#fffbeb",round($r)];
                else               $b["c"] = ["fa-thumbs-down","#dc2626","#fef2f2",round($r)];
            }

            if (isset($this->recogidaMap[$cliId])) {
                $d = $this->recogidaMap[$cliId];
                if ($d <= 7)       $b["r"] = ["fa-bolt","#16a34a","#f0fdf4",$d];
                elseif ($d <= 14)  $b["r"] = ["fa-clock","#2563eb","#eff6ff",$d];
                elseif ($d <= 30)  $b["r"] = ["fa-hourglass-half","#d97706","#fffbeb",$d];
                else               $b["r"] = ["fa-hourglass-end","#dc2626","#fef2f2",$d];
            }

            if (!empty($b)) $badges[$cliId] = $b;
        }
        return $badges;
    }

    /* ── Interno ── */
    private function circle($icon, $fg, $bg, $title, $ml = 4)
    {
        return "<span style='display:inline-flex;align-items:center;justify-content:center;"
             . "width:22px;height:22px;border-radius:50%;background:{$bg};margin-left:{$ml}px'"
             . " title='" . htmlspecialchars($title) . "'>"
             . "<i class='fas {$icon}' style='font-size:10px;color:{$fg}'></i></span>";
    }
}
