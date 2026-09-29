<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/conexionWordpress.php';

/** Escrituras de pedidos con validación, bloqueo y detección de formularios antiguos. */
class PedidosPersistencia
{
    public static function estados()
    {
        return ['Pedido Pendiente', 'Pedido Adquirido', 'Producto en Almacen', 'Entregado al asesor', 'Entregado/Pagado', 'Entregado/Credito', 'cancelado'];
    }

    public static function estado($estado)
    {
        if ($estado === 'Entregado al Asesor') $estado = 'Entregado al asesor';
        if (!in_array($estado, self::estados(), true)) throw new InvalidArgumentException('El estado del pedido no es válido.');
        return $estado;
    }

    public static function numero($valor, $decimales = 2)
    {
        if (!is_numeric($valor) || !is_finite((float)$valor) || (float)$valor < 0) {
            throw new InvalidArgumentException('Los importes y cantidades deben ser números positivos o cero.');
        }
        return round((float)$valor, $decimales);
    }

    public static function lista($json)
    {
        if ($json === null || $json === '') return [];
        $lista = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($lista) || array_values($lista) !== $lista) {
            throw new InvalidArgumentException('La lista enviada no es válida. Recarga el pedido.');
        }
        foreach ($lista as $fila) if (!is_array($fila)) throw new InvalidArgumentException('La lista contiene datos no válidos.');
        return $lista;
    }

    public static function productos($json)
    {
        $resultado = [];
        foreach (self::lista($json) as $fila) {
            $descripcion = trim((string)($fila['Descripcion'] ?? ''));
            $cantidad = self::numero($fila['cantidad'] ?? '', 6);
            if ($cantidad <= 0) throw new InvalidArgumentException('Cada producto necesita una cantidad mayor a cero.');
            // El formato histórico guarda "precio" como subtotal, incluido el ticket.
            $unitario = isset($fila['precioUnitario']) ? self::numero($fila['precioUnitario'], 6) : self::numero($fila['precio'] ?? '') / $cantidad;
            $resultado[] = ['Descripcion' => $descripcion, 'cantidad' => $cantidad, 'precioUnitario' => $unitario, 'precio' => round($unitario * $cantidad, 2)];
        }
        return $resultado;
    }

    public static function pagos($json)
    {
        $resultado = [];
        foreach (self::lista($json) as $fila) {
            $monto = $fila['pago'] ?? '';
            $fecha = trim((string)($fila['fecha'] ?? ''));
            if (($monto === '' || $monto === null || (is_numeric($monto) && (float)$monto == 0)) && $fecha === '') continue;
            $monto = self::numero($monto);
            // Abonos anteriores pueden tener fechas vacías o en otro formato; se conservan tal cual.
            if ($monto > 0) $resultado[] = ['pago' => $monto, 'fecha' => $fecha];
        }
        return $resultado;
    }

    public static function pagosAnteriores($pedido)
    {
        $pagos = [];
        foreach (['pagoPedido' => null, 'abonoUno' => 'fechaAbonoUno', 'abonoDos' => 'fechaAbonoDos', 'abonoTres' => 'fechaAbonoTres', 'abonoCuatro' => 'fechaAbonoCuatro', 'abonoCinco' => 'fechaAbonoCinco'] as $campo => $fecha) {
            if ((float)($pedido[$campo] ?? 0) > 0) $pagos[] = ['pago' => (float)$pedido[$campo], 'fecha' => $fecha ? ($pedido[$fecha] ?? '') : '', 'campo' => $campo];
        }
        return $pagos;
    }

    public static function totalAnterior($pedido)
    {
        $total = 0;
        foreach ([['productoUno', 'cantidaProductoUno', 'precioProductoUno'], ['ProductoDos', 'cantidadProductoDos', 'precioProductoDos'], ['ProductoTres', 'cantidadProductoTres', 'precioProductoTres'], ['ProductoCuatro', 'cantidadProductoCuatro', 'precioProductoCuatro'], ['ProductoCinco', 'cantidadProductoCinco', 'precioProductoCinco']] as $campos) {
            if (!empty($pedido[$campos[0]]) && $pedido[$campos[0]] !== 'undefined') $total += (float)($pedido[$campos[1]] ?? 0) * (float)($pedido[$campos[2]] ?? 0);
        }
        return round($total, 2);
    }

    public static function version($pedido, $observaciones = false)
    {
        if ($observaciones) return hash('sha256', (string)($pedido['observaciones'] ?? ''));
        $campos = ['estado', 'productos', 'pagos', 'total', 'adeudo', 'pagoPedido', 'abonoUno', 'abonoDos', 'abonoTres', 'abonoCuatro', 'abonoCinco'];
        $datos = [];
        foreach ($campos as $campo) $datos[$campo] = (string)($pedido[$campo] ?? '');
        return hash('sha256', json_encode($datos));
    }

    public static function autorizar($pedido, $finanzas = false)
    {
        $perfil = $_SESSION['perfil'] ?? '';
        $perfiles = $finanzas ? ['administrador', 'Super-Administrador'] : ['administrador', 'Super-Administrador', 'vendedor'];
        if (!in_array($perfil, $perfiles, true) || ($perfil !== 'Super-Administrador' && (int)($pedido['id_empresa'] ?? 0) !== (int)($_SESSION['empresa'] ?? 0))) {
            throw new RuntimeException('No tienes permiso para modificar este pedido.');
        }
    }

    private static function leer($db, $id, $tabla = 'pedidos')
    {
        if ((int)$id <= 0) throw new InvalidArgumentException('Selecciona un registro válido.');
        $lock = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $stmt = $db->prepare("SELECT * FROM $tabla WHERE id = :id" . $lock);
        $stmt->execute(['id' => (int)$id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) throw new RuntimeException('El registro ya no existe. Recarga la página.');
        return $fila;
    }

    private static function comprobarVersion($pedido, $version, $observaciones = false)
    {
        if (!is_string($version) || !hash_equals(self::version($pedido, $observaciones), $version)) {
            throw new RuntimeException('El pedido cambió desde que abriste la página. Copia tus cambios y recarga antes de guardar.');
        }
    }

    private static function actualizar($db, $id, $datos)
    {
        $sets = [];
        // Va antes de "estado": MySQL evalúa las asignaciones en orden y aquí se necesita el estado anterior.
        if (isset($datos['estado']) && strpos($datos['estado'], 'Entregado') === 0) {
            $sets[] = "fechaEntrega = CASE WHEN fechaEntrega IS NULL OR estado IS NULL OR estado NOT LIKE 'Entregado%' THEN CURRENT_TIMESTAMP ELSE fechaEntrega END";
        }
        foreach ($datos as $campo => $valor) $sets[] = "$campo = :$campo";
        $stmt = $db->prepare('UPDATE pedidos SET ' . implode(', ', $sets) . ' WHERE id = :id');
        $datos['id'] = (int)$id;
        if (!$stmt->execute($datos)) throw new RuntimeException('No se pudo guardar el pedido.');
    }

    public static function guardar($id, $entrada, $version, $versionObservaciones)
    {
        $db = Conexion::conectar();
        $db->beginTransaction();
        try {
            $pedido = self::leer($db, $id);
            $finanzas = isset($entrada['productos']);
            self::autorizar($pedido, $finanzas);
            $datos = [];
            if ($finanzas) {
                self::comprobarVersion($pedido, $version);
                $productos = self::productos($entrada['productos']);
                $pagos = self::pagos($entrada['pagos']);
                $total = self::totalAnterior($pedido) + array_sum(array_column($productos, 'precio'));
                if (!$productos && self::totalAnterior($pedido) <= 0) throw new InvalidArgumentException('El pedido debe tener productos.');
                $pagado = array_sum(array_column($pagos, 'pago')) + array_sum(array_column(self::pagosAnteriores($pedido), 'pago'));
                $datos = ['estado' => self::estado($entrada['estado']), 'productos' => json_encode($productos, JSON_UNESCAPED_UNICODE), 'pagos' => json_encode($pagos), 'total' => round($total, 2), 'adeudo' => round(max(0, $total - $pagado), 2)];
            }
            if (array_key_exists('observaciones', $entrada)) {
                self::comprobarVersion($pedido, $versionObservaciones, true);
                $observaciones = self::lista($entrada['observaciones']);
                foreach ($observaciones as $obs) {
                    if (!isset($obs['observacion'], $obs['creador'], $obs['fecha']) || !is_string($obs['observacion']) || trim($obs['observacion']) === '') throw new InvalidArgumentException('La observación está incompleta.');
                }
                $datos['observaciones'] = json_encode($observaciones, JSON_UNESCAPED_UNICODE);
            }
            if (!$datos) throw new InvalidArgumentException('No hay cambios para guardar.');
            self::actualizar($db, $id, $datos);
            $actual = self::leer($db, $id);
            $db->commit();
            return $actual;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    public static function cambiarEstado($id, $estado, $validarPerfil = true)
    {
        $db = Conexion::conectar();
        $db->beginTransaction();
        try {
            $pedido = self::leer($db, $id);
            if ($validarPerfil) self::autorizar($pedido);
            self::actualizar($db, $id, ['estado' => self::estado($estado)]);
            $db->commit();
            return 'ok';
        } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
    }

    /** Las dos conexiones se mantienen en transacción durante la actualización. */
    private static function vincular($db, $wp, $pedido, $idOrden)
    {
        $orden = self::leer($wp, $idOrden, 'ordenes');
        if ((int)$orden['id_empresa'] !== (int)$pedido['id_empresa']) throw new InvalidArgumentException('La orden y el pedido deben pertenecer a la misma empresa.');
        if (!empty($orden['id_pedido']) && (int)$orden['id_pedido'] !== (int)$pedido['id']) throw new RuntimeException('La orden ya tiene otro pedido asignado.');
        $stmt = $db->prepare('SELECT id FROM pedidos WHERE id_orden = :orden AND id <> :pedido');
        $stmt->execute(['orden' => $idOrden, 'pedido' => $pedido['id']]);
        if ($stmt->fetch()) throw new RuntimeException('La orden ya tiene otro pedido asignado.');
        $wp->prepare('UPDATE ordenes SET id_pedido = 0 WHERE id_pedido = :pedido')->execute(['pedido' => $pedido['id']]);
        $wp->prepare('UPDATE ordenes SET id_pedido = :pedido WHERE id = :orden')->execute(['pedido' => $pedido['id'], 'orden' => $idOrden]);
        self::actualizar($db, $pedido['id'], ['id_orden' => (int)$idOrden]);
    }

    public static function asignar($idPedido, $idOrden)
    {
        $db = Conexion::conectar(); $wp = ConexionWP::conectarWP();
        $db->beginTransaction();
        try {
            if ($wp !== $db) $wp->beginTransaction();
            $pedido = self::leer($db, $idPedido);
            self::autorizar($pedido);
            self::vincular($db, $wp, $pedido, $idOrden);
            if ($wp !== $db) $wp->commit();
            $db->commit();
            return 'ok';
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($wp->inTransaction()) $wp->rollBack();
            throw $e;
        }
    }

    public static function crear($entrada)
    {
        self::autorizar(['id_empresa' => $entrada['empresa']]);
        if ((int)$entrada['cliente'] <= 0 || (int)$entrada['asesor'] <= 0) throw new InvalidArgumentException('Selecciona cliente y asesor.');
        $productos = self::productos($entrada['productos']);
        if (!$productos) throw new InvalidArgumentException('Agrega al menos un producto.');
        $pagos = self::pagos($entrada['pago']);
        $total = array_sum(array_column($productos, 'precio'));
        $estado = self::estado($entrada['estado']);
        $datos = ['id_empresa' => (int)$entrada['empresa'], 'id_cliente' => (int)$entrada['cliente'], 'id_Asesor' => (int)$entrada['asesor'], 'productos' => json_encode($productos, JSON_UNESCAPED_UNICODE), 'pagos' => json_encode($pagos), 'observaciones' => '[]', 'estado' => $estado, 'total' => $total, 'adeudo' => max(0, round($total - array_sum(array_column($pagos, 'pago')), 2)), 'id_orden' => 0];
        $db = Conexion::conectar(); $wp = null;
        $db->beginTransaction();
        try {
            $db->prepare('INSERT INTO pedidos (id_empresa, id_cliente, id_Asesor, productos, pagos, observaciones, estado, total, adeudo, id_orden, fechaDePedido) VALUES (:id_empresa, :id_cliente, :id_Asesor, :productos, :pagos, :observaciones, :estado, :total, :adeudo, :id_orden, CURRENT_TIMESTAMP)')->execute($datos);
            $id = $db->lastInsertId();
            if (strpos($estado, 'Entregado') === 0) self::actualizar($db, $id, ['estado' => $estado]);
            if ((int)$entrada['id_orden'] > 0) {
                $wp = ConexionWP::conectarWP();
                if ($wp !== $db) $wp->beginTransaction();
                self::vincular($db, $wp, self::leer($db, $id), (int)$entrada['id_orden']);
                if ($wp !== $db) $wp->commit();
            }
            $db->commit();
            return 'ok';
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($wp && $wp->inTransaction()) $wp->rollBack();
            throw $e;
        }
    }
}
