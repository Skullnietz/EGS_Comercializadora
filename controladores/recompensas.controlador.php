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
    =============================================*/
    static public function ctrObtenerInfoRecompensas($idCliente)
    {
        self::ctrCrearTablas();
        ModeloRecompensas::mdlExpirarMovimientos();

        $monedero = ModeloRecompensas::mdlObtenerMonedero($idCliente);
        $entregadas = ModeloRecompensas::mdlContarOrdenesEntregadas($idCliente);
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $saldo = floatval($monedero["saldo"]);

        return array(
            "monedero" => $monedero,
            "saldo" => $saldo,
            "entregadas" => $entregadas,
            "porcentaje" => $porcentaje,
            "token" => $monedero["token"],
            "es_nuevo" => ($entregadas == 0)
        );
    }

    /*=============================================
    ACUMULAR RECOMPENSA AL ENTREGAR ORDEN
    =============================================*/
    static public function ctrAcumularRecompensa($idCliente, $idOrden, $totalOrden)
    {
        self::ctrCrearTablas();

        if (ModeloRecompensas::mdlExisteAcumulacionOrden($idOrden)) {
            return false;
        }

        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $monto = round(floatval($totalOrden) * ($porcentaje / 100), 2);

        if ($monto <= 0) {
            return false;
        }

        $descripcion = "Recompensa " . $porcentaje . "% por Orden #" . $idOrden;
        $nuevoSaldo = ModeloRecompensas::mdlAcumularRecompensa($idCliente, $idOrden, $monto, $porcentaje, $descripcion);

        return array(
            "monto_acumulado" => $monto,
            "porcentaje" => $porcentaje,
            "saldo_nuevo" => $nuevoSaldo
        );
    }

    /*=============================================
    CANJEAR DINERO ELECTRÓNICO
    =============================================*/
    static public function ctrCanjearRecompensa($idCliente, $idOrden, $montoCanje)
    {
        self::ctrCrearTablas();

        $saldoDisponible = ModeloRecompensas::mdlObtenerSaldoDisponible($idCliente);
        if ($montoCanje > $saldoDisponible || $montoCanje <= 0) {
            return false;
        }

        $descripcion = "Canje en Orden #" . $idOrden;
        $nuevoSaldo = ModeloRecompensas::mdlCanjearRecompensa($idCliente, $idOrden, $montoCanje, $descripcion);

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
        ModeloRecompensas::mdlExpirarMovimientos();

        $monedero = ModeloRecompensas::mdlObtenerMonederoPorToken($token);
        if (!$monedero) {
            return false;
        }

        $idCliente = intval($monedero["id_cliente"]);
        $movimientos = ModeloRecompensas::mdlObtenerMovimientos($idCliente);
        $entregadas = ModeloRecompensas::mdlContarOrdenesEntregadas($idCliente);
        $porcentaje = self::ctrCalcularPorcentaje($idCliente);
        $nombreCliente = ModeloRecompensas::mdlObtenerNombreCliente($idCliente);

        return array(
            "monedero" => $monedero,
            "saldo" => floatval($monedero["saldo"]),
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
    OBTENER SALDO DISPONIBLE
    =============================================*/
    static public function ctrObtenerSaldo($idCliente)
    {
        self::ctrCrearTablas();
        return ModeloRecompensas::mdlObtenerSaldoDisponible($idCliente);
    }
}
