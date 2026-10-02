<?php
require_once __DIR__ . '/recompensas.modelo.php';
require_once __DIR__ . '/../controladores/recompensas.controlador.php';

/** Entrega y consumo comparten conexión, bloqueo y commit. No ejecutar DDL aquí. */
class MonederoPersistencia
{
    public static function esEntrega($estado)
    {
        return is_string($estado) && stripos(trim($estado), 'Entregado') === 0;
    }
    public static function centavos($valor)
    {
        if (!is_scalar($valor) || !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', (string)$valor)) {
            throw new InvalidArgumentException('Captura un importe válido con hasta dos decimales.');
        }
        return (int)round((float)$valor * 100);
    }

    private static function sesion($admin = false)
    {
        if (empty($_SESSION['id']) || empty($_SESSION['empresa']) || empty($_SESSION['perfil'])) {
            throw new RuntimeException('Inicia sesión para guardar la entrega.');
        }
        if ($admin && $_SESSION['perfil'] !== 'administrador') {
            throw new RuntimeException('Solo el administrador desde su sesión puede autorizar la entrega y el canje.');
        }
    }

    private static function iniciar($db, $conOrden = true)
    {
        if ($db->inTransaction()) throw new RuntimeException('Hay otro guardado en curso. Vuelve a intentar.');
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $tablas = $conOrden ? ['ordenes', 'dinero_electronico', 'dinero_electronico_movimientos'] : ['dinero_electronico', 'dinero_electronico_movimientos'];
            foreach ($tablas as $tabla) {
                $stmt = $db->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
                $stmt->execute([$tabla]);
                if (strtoupper((string)$stmt->fetchColumn()) !== 'INNODB') {
                    throw new RuntimeException('Soporte debe aplicar la migración 20261002_entrega_monedero.sql antes de guardar.');
                }
            }
        }
        $db->beginTransaction();
    }

    private static function fila($db, $sql, $params, $bloquear = false)
    {
        if ($bloquear && $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') $sql .= ' FOR UPDATE';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Todas las escrituras de consumo bloquean primero el monedero del cliente. */
    private static function bloquearMonedero($db, $cliente)
    {
        $sql = 'INSERT INTO dinero_electronico (id_cliente, saldo, token) VALUES (?, 0, ?)';
        $sql .= $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql'
            ? ' ON DUPLICATE KEY UPDATE id_cliente = id_cliente' : ' ON CONFLICT(id_cliente) DO NOTHING';
        $db->prepare($sql)->execute([$cliente, bin2hex(random_bytes(32))]);
        if (!self::fila($db, 'SELECT * FROM dinero_electronico WHERE id_cliente = ?', [$cliente], true)) {
            throw new RuntimeException('No se pudo bloquear el monedero del cliente.');
        }
    }

    private static function comprobar($fila, $campos, $importes = [])
    {
        foreach ($campos as $campo => $valor) {
            if (!$fila || !array_key_exists($campo, $fila)) throw new RuntimeException('No se pudo verificar el registro del monedero.');
            $igual = in_array($campo, $importes, true)
                ? is_numeric($fila[$campo]) && abs((float)$fila[$campo] - (float)$valor) < 0.000001
                : (string)$fila[$campo] === (string)$valor;
            if (!$igual) throw new RuntimeException('La base de datos alteró el campo ' . $campo . '. No se aplicaron cambios.');
        }
    }

    private static function consumir($db, $cliente, $tipo, $id, $monto, $bruto, $neto)
    {
        $saldo = ModeloRecompensas::mdlCalcularSaldoDinamico($cliente, 1, ControladorRecompensas::ctrCalcularPorcentajeHistorico($cliente));
        $saldoCentavos = (int)round($saldo * 100);
        if ($monto > $saldoCentavos) throw new RuntimeException('El saldo disponible ya no alcanza para el canje solicitado. La entrega no se guardó.');
        $campos = [
            'id_cliente' => $cliente, 'id_orden' => $id, 'tipo' => 'canje', 'monto' => -$monto / 100,
            'saldo_anterior' => $saldoCentavos / 100, 'saldo_nuevo' => ($saldoCentavos - $monto) / 100,
            'descripcion' => 'Canje en ' . ucfirst($tipo) . ' #' . $id,
            'referencia_tipo' => $tipo, 'referencia_id' => $id,
            'id_empresa' => (int)$_SESSION['empresa'], 'id_usuario_aplico' => (int)$_SESSION['id'],
            'origen_total_bruto' => $bruto / 100, 'origen_total_neto' => $neto / 100, 'monto_aplicado' => $monto / 100
        ];
        $db->prepare('INSERT INTO dinero_electronico_movimientos (' . implode(',', array_keys($campos)) . ') VALUES (' . implode(',', array_fill(0, count($campos), '?')) . ')')->execute(array_values($campos));
        $movimiento = self::fila($db, 'SELECT * FROM dinero_electronico_movimientos WHERE id = ?', [$db->lastInsertId()]);
        self::comprobar($movimiento, $campos, ['monto', 'saldo_anterior', 'saldo_nuevo', 'origen_total_bruto', 'origen_total_neto', 'monto_aplicado']);
        return $movimiento;
    }

    /** $guardar usa los modelos existentes; su UPDATE queda dentro de esta transacción. */
    public static function guardarOrden($id, $estado, $total, $monto, callable $guardar, $clienteEsperado = null)
    {
        self::sesion();
        if (self::esEntrega($estado) && $estado !== 'Entregado (Ent)') throw new InvalidArgumentException('El estado de entrega no es válido.');
        if (!ctype_digit((string)$id) || (int)$id <= 0) throw new InvalidArgumentException('Orden inválida.');
        $id = (int)$id;
        $bruto = self::centavos($total);
        $canje = self::centavos($monto);
        if ($canje > 0) {
            self::sesion(true);
            if ($estado !== 'Entregado (Ent)') throw new RuntimeException('El monedero solo se aplica al entregar la orden.');
            if (empty($_SESSION['tokenMonederoOrden']) || !is_string($_POST['tokenMonederoOrden'] ?? null)
                || !hash_equals($_SESSION['tokenMonederoOrden'], $_POST['tokenMonederoOrden'])) {
                throw new RuntimeException('La autorización de monedero venció. Recarga la orden y vuelve a capturar el importe.');
            }
            if ($canje > $bruto) throw new RuntimeException('El canje no puede exceder el total del servicio.');
        }
        $db = ConexionWP::conectarWP();
        // Esta lectura ocurre fuera de la transacción: el primer snapshot de saldo se crea después del bloqueo.
        $previa = self::fila($db, 'SELECT * FROM ordenes WHERE id = ?', [$id]);
        if (!$previa || (int)$previa['id_empresa'] !== (int)$_SESSION['empresa']) throw new RuntimeException('La orden no pertenece a tu empresa.');
        $cliente = (int)$previa['id_usuario'];
        self::iniciar($db);
        try {
            if ($cliente > 0) self::bloquearMonedero($db, $cliente);
            $orden = self::fila($db, 'SELECT * FROM ordenes WHERE id = ?', [$id], true);
            if (!$orden || (int)$orden['id_usuario'] !== $cliente || (int)$orden['id_empresa'] !== (int)$_SESSION['empresa']) {
                throw new RuntimeException('La orden cambió mientras se guardaba. Recárgala.');
            }
            if ($clienteEsperado !== null && (int)$clienteEsperado !== $cliente) throw new RuntimeException('El cliente de la orden cambió. Recárgala antes de aplicar el monedero.');
            $stmtCanjes = $db->prepare("SELECT * FROM dinero_electronico_movimientos WHERE tipo = 'canje' AND
                ((referencia_tipo = 'orden' AND referencia_id = ?) OR (referencia_tipo IS NULL AND id_orden = ? AND descripcion LIKE '%Orden%'))");
            $stmtCanjes->execute([$id, $id]);
            $canjes = $stmtCanjes->fetchAll(PDO::FETCH_ASSOC);
            if (count($canjes) > 1) throw new RuntimeException('Hay consumos duplicados en esta orden. Soporte debe conciliarlos.');
            $existente = $canjes ? $canjes[0] : null;
            if ($existente) {
                if ($estado !== 'Entregado (Ent)' || self::centavos($orden['total']) !== $bruto) {
                    throw new RuntimeException('Esta orden ya tiene un canje. Soporte debe revisar su reversión antes de cambiar el importe o reabrirla.');
                }
                self::comprobar($existente, ['id_cliente' => $cliente, 'monto' => -(float)$orden['monto_monedero_aplicado'],
                    'monto_aplicado' => (float)$orden['monto_monedero_aplicado'], 'origen_total_bruto' => $bruto / 100,
                    'origen_total_neto' => (float)$orden['total_pagado_cliente']], ['monto', 'monto_aplicado', 'origen_total_bruto', 'origen_total_neto']);
                self::comprobar($orden, ['estado' => 'Entregado (Ent)', 'total_bruto_monedero' => $bruto / 100,
                    'total_pagado_cliente' => ($bruto - (int)round(abs((float)$existente['monto']) * 100)) / 100], ['total_bruto_monedero', 'total_pagado_cliente']);
                if ($canje > 0) {
                    if ($canje !== (int)round(abs((float)$existente['monto']) * 100)) throw new RuntimeException('La orden ya se entregó con otro importe de monedero.');
                    $db->commit();
                    return ['reintento' => true, 'movimiento' => $existente];
                }
            }
            $entregaNueva = $estado === 'Entregado (Ent)' && $orden['estado'] !== 'Entregado (Ent)';
            if ($entregaNueva) self::sesion(true);
            if (self::esEntrega($orden['estado']) && ($estado !== $orden['estado'] || $bruto !== self::centavos($orden['total']))) self::sesion(true);
            if ($canje > 0 && !$entregaNueva) throw new RuntimeException('La orden ya está entregada. No se puede agregar un canje después de la entrega.');
            if ($entregaNueva && (float)($orden['monto_monedero_aplicado'] ?? 0) > 0 && !$existente) {
                throw new RuntimeException('La orden tiene un descuento sin registro de consumo. Soporte debe conciliarla.');
            }
            $movimiento = null;
            if ($canje > 0) {
                if ($cliente <= 0) throw new RuntimeException('La orden debe tener un cliente para aplicar monedero.');
                $movimiento = self::consumir($db, $cliente, 'orden', $id, $canje, $bruto, $bruto - $canje);
            }
            if ($guardar() !== 'ok') throw new RuntimeException('No se pudo guardar la orden. No se aplicó el consumo.');
            $esperado = ['estado' => $estado, 'total' => $bruto / 100, 'id_usuario' => $cliente, 'id_empresa' => (int)$_SESSION['empresa']];
            if ($existente) {
                foreach (['total_bruto_monedero', 'monto_monedero_aplicado', 'total_pagado_cliente', 'fecha_canje_monedero', 'fecha_Salida'] as $campo) {
                    $esperado[$campo] = $orden[$campo];
                }
            }
            if ($entregaNueva) {
                $fecha = (new DateTimeImmutable('now', new DateTimeZone('America/Mexico_City')))->format('Y-m-d H:i:s');
                $db->prepare('UPDATE ordenes SET fecha_Salida = ?, total_bruto_monedero = ?, monto_monedero_aplicado = ?, total_pagado_cliente = ?, fecha_canje_monedero = ? WHERE id = ?')
                    ->execute([$fecha, $bruto / 100, $canje / 100, ($bruto - $canje) / 100, $canje > 0 ? $fecha : null, $id]);
                $esperado += ['fecha_Salida' => $fecha, 'total_bruto_monedero' => $bruto / 100, 'monto_monedero_aplicado' => $canje / 100,
                    'total_pagado_cliente' => ($bruto - $canje) / 100, 'fecha_canje_monedero' => $canje > 0 ? $fecha : null];
            }
            self::comprobar(self::fila($db, 'SELECT * FROM ordenes WHERE id = ?', [$id]), $esperado,
                ['total', 'total_bruto_monedero', 'monto_monedero_aplicado', 'total_pagado_cliente']);
            if ($movimiento) self::comprobar(self::fila($db, 'SELECT * FROM dinero_electronico_movimientos WHERE id = ?', [$movimiento['id']]), $movimiento);
            $db->commit();
            return ['reintento' => false, 'movimiento' => $movimiento];
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /** Ventas comparten el bloqueo para que no gasten simultáneamente el saldo de una entrega. */
    public static function canjearVenta($cliente, $id, $monto, $bruto, $neto)
    {
        self::sesion();
        $canje = self::centavos($monto);
        $bruto = self::centavos($bruto);
        $neto = self::centavos($neto);
        if ($canje <= 0 || $bruto - $canje !== $neto) throw new RuntimeException('El desglose de monedero de la venta no es válido.');
        $db = ConexionWP::conectarWP();
        self::iniciar($db, false);
        try {
            self::bloquearMonedero($db, (int)$cliente);
            $existente = ModeloRecompensas::mdlObtenerCanje('venta', $id);
            if ($existente) {
                self::comprobar($existente, ['id_cliente' => $cliente, 'monto_aplicado' => $canje / 100,
                    'origen_total_bruto' => $bruto / 100, 'origen_total_neto' => $neto / 100], ['monto_aplicado', 'origen_total_bruto', 'origen_total_neto']);
                $db->commit();
                return $existente;
            }
            $resultado = self::consumir($db, (int)$cliente, 'venta', (int)$id, $canje, $bruto, $neto);
            $db->commit();
            return $resultado;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }
}
