<?php

class ControladorRecompensas
{
    /*=============================================
    CREAR TABLAS (idempotente)
    =============================================*/
    static public function ctrCrearTablas()
    {
        return ModeloRecompensas::mdlCrearTablas();
    }

    /*=============================================
    CALCULAR PORCENTAJE SEGÚN ÓRDENES ENTREGADAS
    - 1% para clientes con 0-3 órdenes entregadas
    - 2% para clientes con más de 3 órdenes entregadas
    - 3% para clientes con más de 5 órdenes entregadas
    =============================================*/
    static public function ctrCalcularPorcentaje($idCliente)
    {
        $entregadas = ModeloRecompensas::mdlContarOrdenesEntregadas($idCliente);

        if ($entregadas > 5) {
            return 3;
        } elseif ($entregadas > 3) {
            return 2;
        } else {
            return 1;
        }
    }

    /*=============================================
    OBTENER INFO COMPLETA DE RECOMPENSAS PARA UN CLIENTE
    El saldo se calcula dinámicamente desde las órdenes
    entregadas de los últimos 6 meses menos los canjes.
    =============================================*/
    static public function ctrObtenerInfoRecompensas($idCliente)
    {
        self::ctrCrearTablas();

        $monedero = ModeloRecompensas::mdlObtenerMonedero($idCliente);
        $entregadas = ModeloRecompensas::mdlContarOrdenesEntregadas($idCliente);
        $ordenesEnPrograma = ModeloRecompensas::mdlContarOrdenesEnPrograma($idCliente);
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $saldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje);

        return array(
            "monedero" => $monedero,
            "saldo" => $saldo,
            "entregadas" => $entregadas,
            "ordenes_en_programa" => $ordenesEnPrograma,
            "porcentaje" => $porcentaje,
            "token" => $monedero["token"],
            "es_nuevo" => ($entregadas == 0)
        );
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO
    Valida contra el saldo dinámico antes de registrar.
    =============================================*/
    static public function ctrCanjearRecompensa($idCliente, $idOrden, $montoCanje)
    {
        self::ctrCrearTablas();

        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $saldoDisponible = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje);

        if ($montoCanje > $saldoDisponible || $montoCanje <= 0) {
            return false;
        }

        $descripcion = "Canje en Orden #" . $idOrden;
        ModeloRecompensas::mdlCanjearRecompensa($idCliente, $idOrden, $montoCanje, $descripcion);

        $nuevoSaldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje);

        return array(
            "monto_canjeado" => $montoCanje,
            "saldo_nuevo" => $nuevoSaldo
        );
    }

    /*=============================================
    OBTENER INFO POR TOKEN (para vista pública)
    =============================================*/
    static public function ctrObtenerMonederoPorToken($token)
    {
        self::ctrCrearTablas();

        $monedero = ModeloRecompensas::mdlObtenerMonederoPorToken($token);
        if (!$monedero) {
            return false;
        }

        $idCliente = intval($monedero["id_cliente"]);
        $entregadas = ModeloRecompensas::mdlContarOrdenesEntregadas($idCliente);
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $saldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje);
        $nombreCliente = ModeloRecompensas::mdlObtenerNombreCliente($idCliente);

        // Construir historial combinando órdenes + canjes
        $ordenesRecomp = ModeloRecompensas::mdlObtenerOrdenesConRecompensa($idCliente, $porcentaje);
        $canjes = ModeloRecompensas::mdlObtenerCanjes($idCliente);

        // Unir en un solo array de movimientos para la vista
        $movimientos = array();
        foreach ($ordenesRecomp as $ord) {
            $movimientos[] = array(
                "tipo" => "acumulacion",
                "monto" => $ord["recompensa"],
                "descripcion" => "Recompensa " . $porcentaje . "% por Orden #" . $ord["id_orden"],
                "fecha" => $ord["fecha_entrega"],
                "fecha_expiracion" => date('Y-m-d', strtotime($ord["fecha_entrega"] . ' +6 months')),
                "expirado" => 0
            );
        }
        foreach ($canjes as $c) {
            $movimientos[] = array(
                "tipo" => "canje",
                "monto" => $c["monto"],
                "descripcion" => $c["descripcion"],
                "fecha" => $c["fecha"],
                "fecha_expiracion" => null,
                "expirado" => 0
            );
        }

        // Ordenar por fecha desc
        usort($movimientos, function($a, $b) {
            return strtotime($b["fecha"]) - strtotime($a["fecha"]);
        });

        return array(
            "monedero" => $monedero,
            "saldo" => $saldo,
            "movimientos" => $movimientos,
            "entregadas" => $entregadas,
            "porcentaje" => $porcentaje,
            "nombre_cliente" => $nombreCliente
        );
    }

    /*=============================================
    OBTENER CANJE DE UNA ORDEN
    =============================================*/
    static public function ctrObtenerCanjeOrden($idOrden)
    {
        self::ctrCrearTablas();
        return ModeloRecompensas::mdlObtenerCanjeOrden($idOrden);
    }

    /*=============================================
    OBTENER SALDO DISPONIBLE (dinámico)
    =============================================*/
    static public function ctrObtenerSaldo($idCliente)
    {
        self::ctrCrearTablas();
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        return ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje);
    }
}
