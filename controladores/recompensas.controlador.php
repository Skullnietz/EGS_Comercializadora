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
    CALCULAR PORCENTAJE DE RECOMPENSA
    Desde el 2026-04-07 el porcentaje es 1% fijo
    para todos los clientes.
    =============================================*/
    static public function ctrCalcularPorcentaje($idCliente)
    {
        return 1;
    }

    /*=============================================
    CALCULAR PORCENTAJE HISTÓRICO (antes del cambio)
    Se usa internamente para respetar el saldo
    acumulado con los porcentajes anteriores.
    - 1% para clientes con 0-3 órdenes entregadas
    - 2% para clientes con más de 3 órdenes entregadas
    - 3% para clientes con más de 5 órdenes entregadas
    =============================================*/
    static public function ctrCalcularPorcentajeHistorico($idCliente)
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
        $porcentajeHistorico = self::ctrCalcularPorcentajeHistorico($idCliente);
        $saldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);

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
    CANJEAR DINERO ELECTRÓNICO EN ORDEN
    Valida contra el saldo dinámico antes de registrar.
    =============================================*/
    static public function ctrCanjearRecompensa($idCliente, $idOrden, $montoCanje)
    {
        self::ctrCrearTablas();

        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $porcentajeHistorico = self::ctrCalcularPorcentajeHistorico($idCliente);
        $saldoDisponible = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);

        if ($montoCanje > $saldoDisponible || $montoCanje <= 0) {
            return false;
        }

        $descripcion = "Canje en Orden #" . $idOrden;
        ModeloRecompensas::mdlCanjearRecompensa($idCliente, $idOrden, $montoCanje, $descripcion);

        $nuevoSaldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);

        return array(
            "monto_canjeado" => $montoCanje,
            "saldo_nuevo" => $nuevoSaldo
        );
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO EN PEDIDO
    =============================================*/
    static public function ctrCanjearRecompensaPedido($idCliente, $idPedido, $montoCanje)
    {
        return false;
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO EN VENTA RÁPIDA
    =============================================*/
    static public function ctrCanjearRecompensaVenta($idCliente, $idVenta, $montoCanje)
    {
        self::ctrCrearTablas();

        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $porcentajeHistorico = self::ctrCalcularPorcentajeHistorico($idCliente);
        $saldoDisponible = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);

        if ($montoCanje > $saldoDisponible || $montoCanje <= 0) {
            return false;
        }

        $descripcion = "Canje en Venta #" . $idVenta;
        ModeloRecompensas::mdlCanjearRecompensa($idCliente, $idVenta, $montoCanje, $descripcion);

        $nuevoSaldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);

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
        $porcentajeHistorico = self::ctrCalcularPorcentajeHistorico($idCliente);
        $saldo = ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);
        $nombreCliente = ModeloRecompensas::mdlObtenerNombreCliente($idCliente);

        // Construir historial combinando órdenes, pedidos, ventas + canjes
        $ordenesRecomp = ModeloRecompensas::mdlObtenerOrdenesConRecompensa($idCliente, $porcentaje, $porcentajeHistorico);
        $canjes = ModeloRecompensas::mdlObtenerCanjes($idCliente);

        // Unir en un solo array de movimientos para la vista
        $movimientos = array();
        foreach ($ordenesRecomp as $ord) {
            $fuenteLabel = "Orden";
            if ($ord["fuente"] == "venta") $fuenteLabel = "Venta";

            $pctAplicado = isset($ord["porcentaje_aplicado"]) ? $ord["porcentaje_aplicado"] : $porcentaje;
            $movimientos[] = array(
                "tipo" => "acumulacion",
                "monto" => $ord["recompensa"],
                "descripcion" => "Recompensa " . $pctAplicado . "% por " . $fuenteLabel . " #" . $ord["id_orden"],
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
    OBTENER CANJE DE UN PEDIDO
    =============================================*/
    static public function ctrObtenerCanjePedido($idPedido)
    {
        return false;
    }

    /*=============================================
    OBTENER CANJE DE UNA VENTA RÁPIDA
    =============================================*/
    static public function ctrObtenerCanjeVenta($idVenta)
    {
        self::ctrCrearTablas();
        return ModeloRecompensas::mdlObtenerCanjeVenta($idVenta);
    }

    /*=============================================
    OBTENER SALDO DISPONIBLE (dinámico)
    =============================================*/
    static public function ctrObtenerSaldo($idCliente)
    {
        self::ctrCrearTablas();
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $porcentajeHistorico = self::ctrCalcularPorcentajeHistorico($idCliente);
        return ModeloRecompensas::mdlCalcularSaldoDinamico($idCliente, $porcentaje, $porcentajeHistorico);
    }

    /*=============================================
    VERIFICAR SI LAS RECOMPENSAS EN VENTAS ESTÁN ACTIVAS
    =============================================*/
    static public function ctrRecompensasVentasActivas()
    {
        ModeloRecompensas::mdlCrearTablaConfiguracion();
        $valor = ModeloRecompensas::mdlObtenerConfiguracion('recompensas_ventas_activo');
        return ($valor === '1');
    }

    /*=============================================
    TOGGLE RECOMPENSAS EN VENTAS
    =============================================*/
    static public function ctrToggleRecompensasVentas($activo)
    {
        ModeloRecompensas::mdlCrearTablaConfiguracion();
        return ModeloRecompensas::mdlActualizarConfiguracion('recompensas_ventas_activo', $activo ? '1' : '0');
    }
}
