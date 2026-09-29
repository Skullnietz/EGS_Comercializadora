<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/conexionWordpress.php';

/**
 * Escrituras de pedidos con validación, bloqueo de fila y comprobación de lo guardado.
 * Abonos y observaciones solo se agregan: lo ya guardado nunca se reenvía desde el navegador,
 * así los datos antiguos (fechas, importes con formato, textos) no pasan por inputs que los alteren.
 */
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

    /** Importes y cantidades capturados en pantalla. */
    public static function numero($valor, $decimales = 2)
    {
        if (!is_numeric($valor) || !is_finite((float)$valor) || (float)$valor < 0) {
            throw new InvalidArgumentException('Los importes y cantidades deben ser números positivos o cero, sin comas ni signos.');
        }
        return round((float)$valor, $decimales);
    }

    /** Importes guardados por versiones anteriores ("1,500", "$800.00", ""): se leen sin rechazarlos. */
    public static function importe($valor)
    {
        if (is_int($valor) || is_float($valor)) return is_finite($valor) ? (float)$valor : 0.0;
        $limpio = preg_replace('/[^0-9.\-]/', '', (string)$valor);
        return is_numeric($limpio) ? (float)$limpio : 0.0;
    }

    /** JSON en ASCII (\uXXXX): la conexión utf8 de 3 bytes rechaza o corta los emojis. */
    public static function json($valor)
    {
        $json = json_encode($valor);
        if ($json === false) throw new InvalidArgumentException('El texto contiene caracteres no válidos.');
        return $json;
    }

    /** Lista JSON enviada por el navegador. */
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

    /** Lista JSON guardada en el pedido; vacía si nunca se capturó. */
    public static function guardada($json, $nombre)
    {
        if ($json === null || trim($json) === '' || trim($json) === 'null') return [];
        $lista = json_decode($json, true);
        if (!is_array($lista)) throw new RuntimeException("Los $nombre guardados en este pedido están dañados y no se modificaron. Avisa a soporte.");
        return array_values($lista);
    }

    /** Si el JSON quedó cortado (versiones anteriores con emojis), su texto se conserva como una observación. */
    public static function observacionesGuardadas($json)
    {
        try {
            $lista = self::guardada($json, 'observaciones');
        } catch (RuntimeException $e) {
            $lista = ['Texto recuperado de observaciones anteriores: ' . mb_convert_encoding($json, 'UTF-8', 'UTF-8')];
        }
        foreach ($lista as $i => $obs) {
            if (!is_array($obs)) $lista[$i] = ['observacion' => is_scalar($obs) ? (string)$obs : '', 'creador' => 'Sistema', 'fecha' => ''];
        }
        return $lista;
    }

    /** Identificador que genera el navegador para no duplicar abonos u observaciones al reintentar. */
    private static function ref($ref)
    {
        return is_string($ref) && preg_match('/^[a-z0-9]{8,40}$/', $ref) ? $ref : null;
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

    /** Solo las filas que el usuario editó; las demás se guardan tal como estaban. */
    private static function productosEditados(array $editados, array $productos)
    {
        foreach ($editados as $fila) {
            $i = isset($fila['indice']) ? filter_var($fila['indice'], FILTER_VALIDATE_INT) : false;
            if ($i === false || !isset($productos[$i]) || !is_array($productos[$i])) throw new InvalidArgumentException('Uno de los productos ya no existe. Recarga el pedido.');
            $cantidad = self::numero($fila['cantidad'] ?? '', 6);
            if ($cantidad <= 0) throw new InvalidArgumentException('Cada producto necesita una cantidad mayor a cero.');
            $unitario = self::numero($fila['precioUnitario'] ?? '', 6);
            $productos[$i] = array_merge($productos[$i], ['Descripcion' => trim((string)($fila['Descripcion'] ?? '')), 'cantidad' => $cantidad, 'precioUnitario' => $unitario, 'precio' => round($unitario * $cantidad, 2)]);
        }
        return $productos;
    }

    /** Pagos del alta de pedidos: se ignoran filas vacías. */
    public static function pagos($json)
    {
        $resultado = [];
        foreach (self::lista($json) as $fila) {
            $monto = $fila['pago'] ?? '';
            $fecha = trim((string)($fila['fecha'] ?? ''));
            if (($monto === '' || $monto === null || (is_numeric($monto) && (float)$monto == 0)) && $fecha === '') continue;
            $monto = self::numero($monto);
            if ($monto > 0) $resultado[] = ['pago' => $monto, 'fecha' => $fecha];
        }
        return $resultado;
    }

    /** Abonos nuevos capturados en el detalle. */
    public static function pagosNuevos($json)
    {
        $resultado = [];
        foreach (self::lista($json) as $fila) {
            $monto = self::numero($fila['pago'] ?? '');
            $fecha = trim((string)($fila['fecha'] ?? ''));
            if ($monto <= 0 || $fecha === '') throw new InvalidArgumentException('Cada abono necesita un monto mayor a cero y una fecha.');
            $pago = ['pago' => $monto, 'fecha' => substr($fecha, 0, 20)];
            if ($ref = self::ref($fila['ref'] ?? null)) $pago['ref'] = $ref;
            $resultado[] = $pago;
        }
        return $resultado;
    }

    public static function pagosAnteriores($pedido)
    {
        $pagos = [];
        foreach (['pagoPedido' => null, 'abonoUno' => 'fechaAbonoUno', 'abonoDos' => 'fechaAbonoDos', 'abonoTres' => 'fechaAbonoTres', 'abonoCuatro' => 'fechaAbonoCuatro', 'abonoCinco' => 'fechaAbonoCinco'] as $campo => $fecha) {
            $monto = self::importe($pedido[$campo] ?? 0);
            if ($monto > 0) $pagos[] = ['pago' => $monto, 'fecha' => $fecha ? (string)($pedido[$fecha] ?? '') : '', 'campo' => $campo];
        }
        return $pagos;
    }

    /** Lo pagado: columnas antiguas más los abonos en JSON. */
    public static function pagado($pedido, array $pagos)
    {
        $total = array_sum(array_column(self::pagosAnteriores($pedido), 'pago'));
        foreach ($pagos as $pago) $total += is_array($pago) ? self::importe($pago['pago'] ?? 0) : 0;
        return round($total, 2);
    }

    /** Campos que el detalle reescribe; abonos y observaciones se agregan sin conflicto. */
    public static function version($pedido)
    {
        return hash('sha256', implode("\x1F", [(string)($pedido['estado'] ?? ''), (string)($pedido['productos'] ?? ''), (string)($pedido['total'] ?? '')]));
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

    private static function comprobarVersion($pedido, $version)
    {
        if (!is_string($version) || !hash_equals(self::version($pedido), $version)) {
            throw new RuntimeException('Alguien más modificó este pedido mientras lo tenías abierto. Recarga la página y vuelve a capturar tus cambios.');
        }
    }

    /** $anterior es la fila leída con bloqueo: define la fecha de entrega y permite restaurarla. */
    private static function actualizar($db, $anterior, $datos)
    {
        $sets = [];
        foreach ($datos as $campo => $valor) $sets[] = "$campo = :$campo";
        $restaurar = implode(', ', $sets);
        $fechaEntrega = (string)($anterior['fechaEntrega'] ?? '');
        if (isset($datos['estado']) && strpos($datos['estado'], 'Entregado') === 0
            && (strpos((string)$anterior['estado'], 'Entregado') !== 0 || $fechaEntrega === '' || strpos($fechaEntrega, '0000') === 0)) {
            $sets[] = 'fechaEntrega = NOW()';
        }
        $id = ['id' => (int)$anterior['id']];
        $db->prepare('UPDATE pedidos SET ' . implode(', ', $sets) . ' WHERE id = :id')->execute($datos + $id);
        // Sin modo estricto, MySQL recorta los textos largos sin avisar: se compara lo guardado y, si difiere, se restaura.
        $actual = self::leer($db, $anterior['id']);
        foreach (array_intersect_key($datos, array_flip(['productos', 'pagos', 'observaciones'])) as $campo => $valor) {
            if ((string)$actual[$campo] !== $valor) {
                $db->prepare("UPDATE pedidos SET $restaurar WHERE id = :id")->execute(array_intersect_key($anterior, $datos) + $id);
                throw new RuntimeException('La base de datos no aceptó el texto completo, así que no se guardó ningún cambio. Avisa a soporte.');
            }
        }
        return $actual;
    }

    /** Guarda estado, total, productos editados y abonos nuevos (solo administradores). */
    public static function guardar($id, $entrada, $version)
    {
        $db = Conexion::conectar();
        $db->beginTransaction();
        try {
            $pedido = self::leer($db, $id);
            self::autorizar($pedido, true);
            self::comprobarVersion($pedido, $version);
            $datos = [];
            $estado = trim((string)($entrada['estado'] ?? ''));
            if ($estado !== '' && $estado !== (string)$pedido['estado']) $datos['estado'] = self::estado($estado);
            $editados = self::lista($entrada['productos'] ?? '');
            if ($editados) $datos['productos'] = self::json(self::productosEditados($editados, self::guardada($pedido['productos'], 'productos')));
            $pagos = self::guardada($pedido['pagos'], 'pagos');
            // Un reintento tras perder la respuesta no vuelve a agregar los mismos abonos.
            $refs = array_column(array_filter($pagos, 'is_array'), 'ref');
            $nuevos = array_values(array_filter(self::pagosNuevos($entrada['pagos'] ?? ''), function ($pago) use ($refs) {
                return !isset($pago['ref']) || !in_array($pago['ref'], $refs, true);
            }));
            if ($nuevos) $datos['pagos'] = self::json(array_merge($pagos, $nuevos));
            $total = trim((string)($entrada['total'] ?? '')) === '' ? self::importe($pedido['total']) : self::numero($entrada['total']);
            $datos['total'] = $total;
            $datos['adeudo'] = round(max(0, $total - self::pagado($pedido, array_merge($pagos, $nuevos))), 2);
            $actual = self::actualizar($db, $pedido, $datos);
            $db->commit();
            return $actual;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /** Agrega una observación al inicio (más reciente primero) con el usuario de la sesión. */
    public static function agregarObservacion($id, $texto, $ref = null)
    {
        $texto = trim(str_replace("\r\n", "\n", (string)$texto));
        if ($texto === '') throw new InvalidArgumentException('Escribe la observación antes de agregarla.');
        if (strlen($texto) > 5000) throw new InvalidArgumentException('La observación es demasiado larga.');
        $db = Conexion::conectar();
        $db->beginTransaction();
        try {
            $pedido = self::leer($db, $id);
            self::autorizar($pedido);
            $lista = self::observacionesGuardadas($pedido['observaciones']);
            $ref = self::ref($ref);
            foreach ($lista as $obs) {
                if ($ref !== null && ($obs['ref'] ?? null) === $ref) { $db->commit(); return $obs; }
            }
            $observacion = ['observacion' => $texto, 'creador' => (string)($_SESSION['nombre'] ?? 'Usuario'), 'fecha' => date('n/j/Y')];
            if ($ref !== null) $observacion['ref'] = $ref;
            array_unshift($lista, $observacion);
            self::actualizar($db, $pedido, ['observaciones' => self::json($lista)]);
            $db->commit();
            return $observacion;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /** Quita una observación propia recién agregada, identificada por su contenido exacto. */
    public static function quitarObservacion($id, $observacion)
    {
        if (!is_array($observacion) || !isset($observacion['observacion'], $observacion['creador'], $observacion['fecha'])) throw new InvalidArgumentException('La observación no es válida.');
        if ($observacion['creador'] !== (string)($_SESSION['nombre'] ?? '')) throw new RuntimeException('Solo puedes quitar tus propias observaciones.');
        $db = Conexion::conectar();
        $db->beginTransaction();
        try {
            $pedido = self::leer($db, $id);
            self::autorizar($pedido);
            $lista = self::observacionesGuardadas($pedido['observaciones']);
            foreach ($lista as $i => $obs) {
                if (($obs['observacion'] ?? null) === $observacion['observacion'] && ($obs['creador'] ?? null) === $observacion['creador'] && ($obs['fecha'] ?? null) === $observacion['fecha']) {
                    array_splice($lista, $i, 1);
                    self::actualizar($db, $pedido, ['observaciones' => self::json($lista)]);
                    $db->commit();
                    return;
                }
            }
            throw new RuntimeException('La observación ya no existe. Recarga el pedido.');
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
            $estado = self::estado($estado);
            if ($estado !== (string)$pedido['estado']) self::actualizar($db, $pedido, ['estado' => $estado]);
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
        self::actualizar($db, $pedido, ['id_orden' => (int)$idOrden]);
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
        $datos = ['id_empresa' => (int)$entrada['empresa'], 'id_cliente' => (int)$entrada['cliente'], 'id_Asesor' => (int)$entrada['asesor'], 'productos' => self::json($productos), 'pagos' => self::json($pagos), 'observaciones' => '[]', 'estado' => $estado, 'total' => $total, 'adeudo' => max(0, round($total - array_sum(array_column($pagos, 'pago')), 2)), 'id_orden' => 0];
        // La fecha de entrega solo se escribe si nace entregado; si no, conserva el valor por omisión de la columna.
        $entrega = strpos($estado, 'Entregado') === 0;
        $db = Conexion::conectar(); $wp = null;
        $db->beginTransaction();
        try {
            $db->prepare('INSERT INTO pedidos (id_empresa, id_cliente, id_Asesor, productos, pagos, observaciones, estado, total, adeudo, id_orden, fechaDePedido' . ($entrega ? ', fechaEntrega' : '') . ') VALUES (:id_empresa, :id_cliente, :id_Asesor, :productos, :pagos, :observaciones, :estado, :total, :adeudo, :id_orden, CURRENT_TIMESTAMP' . ($entrega ? ', NOW()' : '') . ')')->execute($datos);
            $id = $db->lastInsertId();
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
