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
    /** Nunca iniciar una transacción sobre tablas que no permitan rollback. */
    private static function iniciarTransaccion($db, array $tablas = ['pedidos'])
    {
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            foreach ($tablas as $tabla) {
                $partes = explode('.', str_replace('`', '', $tabla));
                $nombre = array_pop($partes);
                $esquema = $partes ? $partes[0] : $db->query('SELECT DATABASE()')->fetchColumn();
                $stmt = $db->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = :esquema AND TABLE_NAME = :tabla');
                $stmt->execute(['esquema' => $esquema, 'tabla' => $nombre]);
                if (strtoupper((string)$stmt->fetchColumn()) !== 'INNODB') {
                    throw new RuntimeException('El guardado seguro requiere tablas InnoDB. Pide a soporte aplicar la migración 20261002_guardado_pedidos.sql.');
                }
            }
        }
        $db->beginTransaction();
    }

    /** Un recibo y el pedido se confirman en la misma transacción; nunca se crea esquema aquí. */
    private static function iniciarSolicitud($db, $ref, $accion, $entrada)
    {
        if ($ref === null) return [null, null]; // Compatibilidad con llamadas internas.
        if (!self::ref($ref)) throw new InvalidArgumentException('Falta el identificador de guardado. Recarga la página.');
        $autor = [(string)($_SESSION['id'] ?? $_SESSION['nombre'] ?? ''), (string)($_SESSION['empresa'] ?? '')];
        $clave = hash('sha256', self::json($autor) . ':' . $ref);
        $huella = hash('sha256', self::json([$accion, $entrada]));
        $sql = 'INSERT INTO pedidos_guardados (clave, huella, resultado) VALUES (:clave, :huella, NULL)';
        $sql .= $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql'
            ? ' ON DUPLICATE KEY UPDATE clave = clave' : ' ON CONFLICT(clave) DO NOTHING';
        $db->prepare($sql)->execute(['clave' => $clave, 'huella' => $huella]);
        $lock = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $stmt = $db->prepare('SELECT huella, resultado FROM pedidos_guardados WHERE clave = :clave' . $lock);
        $stmt->execute(['clave' => $clave]);
        $recibo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$recibo || !hash_equals($huella, $recibo['huella'])) throw new InvalidArgumentException('Ese intento de guardado tiene otros datos. Recupera su resultado antes de enviar cambios nuevos.');
        $resultado = $recibo['resultado'] === null ? null : json_decode($recibo['resultado'], true);
        if ($recibo['resultado'] !== null && (json_last_error() !== JSON_ERROR_NONE || !is_array($resultado) || !$resultado)) {
            throw new RuntimeException('El recibo de ese intento está dañado. Soporte debe revisarlo antes de reintentar; no se hicieron cambios nuevos.');
        }
        return [$clave, $resultado];
    }

    private static function terminarSolicitud($db, $clave, $resultado)
    {
        if ($clave === null) return;
        $json = self::json($resultado);
        $db->prepare('UPDATE pedidos_guardados SET resultado = :resultado WHERE clave = :clave')->execute(['resultado' => $json, 'clave' => $clave]);
        $stmt = $db->prepare('SELECT resultado FROM pedidos_guardados WHERE clave = :clave');
        $stmt->execute(['clave' => $clave]);
        if ($stmt->fetchColumn() !== $json) throw new RuntimeException('No se pudo confirmar el recibo del guardado. No se aplicaron cambios.');
    }

    /** Comprueba cada campo escrito, incluidos importes, IDs, estado y fechas. */
    private static function comprobarCampos($actual, $datos)
    {
        $importes = ['total', 'adeudo'];
        $ids = ['id_empresa', 'id_cliente', 'id_Asesor', 'id_orden', 'id_pedido'];
        foreach ($datos as $campo => $valor) {
            if (!array_key_exists($campo, $actual)) throw new RuntimeException('No se pudo verificar el campo ' . $campo . '. No se aplicaron cambios.');
            $guardado = $actual[$campo];
            $igual = in_array($campo, $importes, true)
                ? is_numeric($guardado) && abs((float)$guardado - (float)$valor) < 0.000001
                : (in_array($campo, $ids, true) ? is_numeric($guardado) && (float)$guardado === (float)(int)$valor : (string)$guardado === (string)$valor);
            if (!$igual) throw new RuntimeException('La base de datos alteró o recortó el campo ' . $campo . '. No se guardó ningún cambio. Avisa a soporte.');
        }
    }

    /** Pedidos y órdenes deben escribirse con una sola conexión y un solo commit. */
    private static function tablaOrdenes($db)
    {
        $wp = ConexionWP::conectarWP();
        if ($wp === $db) return 'ordenes';
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'mysql' || $wp->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'mysql') {
            throw new RuntimeException('La asignación requiere una conexión compartida para pedidos y órdenes.');
        }
        $servidor = 'SELECT @@hostname AS host, @@port AS puerto, @@server_id AS servidor';
        if ($db->query($servidor)->fetch(PDO::FETCH_ASSOC) !== $wp->query($servidor)->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException('Pedidos y órdenes están en servidores distintos. Soporte debe configurar una conexión compartida antes de asignar.');
        }
        $esquema = $wp->query('SELECT DATABASE()')->fetchColumn();
        $tabla = '`' . str_replace('`', '``', $esquema) . '`.`ordenes`';
        // Comprueba acceso antes de modificar cualquier registro.
        $db->query('SELECT id FROM ' . $tabla . ' WHERE 1 = 0');
        return $tabla;
    }

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
        $numero = round((float)$valor, $decimales);
        if (!is_finite($numero)) throw new InvalidArgumentException('El importe o cantidad excede el límite permitido.');
        return $numero;
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
            if ($descripcion === '') throw new InvalidArgumentException('Cada producto necesita una descripción.');
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
            if ($monto <= 0 && $fecha !== '') throw new InvalidArgumentException('La fecha de pago necesita un monto mayor a cero.');
            if ($monto > 0) {
                self::fechaPago($fecha);
                $resultado[] = ['pago' => $monto, 'fecha' => $fecha];
            }
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
            self::fechaPago($fecha);
            $pago = ['pago' => $monto, 'fecha' => $fecha];
            if ($ref = self::ref($fila['ref'] ?? null)) $pago['ref'] = $ref;
            $resultado[] = $pago;
        }
        return $resultado;
    }

    private static function fechaPago($fecha)
    {
        $valor = DateTime::createFromFormat('!Y-m-d', $fecha);
        if (!$valor || $valor->format('Y-m-d') !== $fecha) throw new InvalidArgumentException('La fecha del abono no es válida.');
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
        if (filter_var($id, FILTER_VALIDATE_INT) === false || (int)$id <= 0) throw new InvalidArgumentException('Selecciona un registro válido.');
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
        $fechaEntrega = (string)($anterior['fechaEntrega'] ?? '');
        if (isset($datos['estado']) && strpos($datos['estado'], 'Entregado') === 0
            && (strpos((string)$anterior['estado'], 'Entregado') !== 0 || $fechaEntrega === '' || strpos($fechaEntrega, '0000') === 0)) {
            $datos['fechaEntrega'] = $db->query('SELECT CURRENT_TIMESTAMP')->fetchColumn();
            $sets[] = 'fechaEntrega = :fechaEntrega';
        }
        $id = ['id' => (int)$anterior['id']];
        $db->prepare('UPDATE pedidos SET ' . implode(', ', $sets) . ' WHERE id = :id')->execute($datos + $id);
        $actual = self::leer($db, $anterior['id']);
        self::comprobarCampos($actual, $datos);
        return $actual;
    }

    /** Guarda estado, total, productos editados y abonos nuevos (solo administradores). */
    public static function guardar($id, $entrada, $version, $solicitud = null)
    {
        $db = Conexion::conectar();
        self::iniciarTransaccion($db, $solicitud === null ? ['pedidos'] : ['pedidos', 'pedidos_guardados']);
        try {
            list($clave, $resultado) = self::iniciarSolicitud($db, $solicitud, 'guardar', [$id, $entrada, $version]);
            $pedido = self::leer($db, $id);
            self::autorizar($pedido, true);
            if ($resultado !== null) { $db->commit(); return $resultado; }
            self::comprobarVersion($pedido, $version);
            $datos = [];
            $estado = trim((string)($entrada['estado'] ?? ''));
            if ($estado !== '' && $estado !== (string)$pedido['estado']) $datos['estado'] = self::estado($estado);
            $editados = self::lista($entrada['productos'] ?? '');
            if ($editados) $datos['productos'] = self::json(self::productosEditados($editados, self::guardada($pedido['productos'], 'productos')));
            $pagos = self::guardada($pedido['pagos'], 'pagos');
            // Un reintento tras perder la respuesta no vuelve a agregar los mismos abonos.
            $refs = array_column(array_filter($pagos, 'is_array'), 'ref');
            $nuevos = [];
            foreach (self::pagosNuevos($entrada['pagos'] ?? '') as $pago) {
                if (isset($pago['ref']) && in_array($pago['ref'], $refs, true)) continue;
                $nuevos[] = $pago;
                if (isset($pago['ref'])) $refs[] = $pago['ref'];
            }
            if ($nuevos) $datos['pagos'] = self::json(array_merge($pagos, $nuevos));
            $total = trim((string)($entrada['total'] ?? '')) === '' ? self::importe($pedido['total']) : self::numero($entrada['total']);
            $datos['total'] = $total;
            $datos['adeudo'] = round(max(0, $total - self::pagado($pedido, array_merge($pagos, $nuevos))), 2);
            $texto = trim(str_replace("\r\n", "\n", (string)($entrada['observacion'] ?? '')));
            if ($texto !== '') {
                if (strlen($texto) > 5000) throw new InvalidArgumentException('La observación es demasiado larga.');
                $lista = self::observacionesGuardadas($pedido['observaciones']);
                array_unshift($lista, ['observacion' => $texto, 'creador' => (string)($_SESSION['nombre'] ?? 'Usuario'), 'fecha' => date('n/j/Y')]);
                $datos['observaciones'] = self::json($lista);
            }
            $actual = self::actualizar($db, $pedido, $datos);
            self::terminarSolicitud($db, $clave, $actual);
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
        self::iniciarTransaccion($db, $ref === null ? ['pedidos'] : ['pedidos', 'pedidos_guardados']);
        try {
            list($clave, $resultado) = self::iniciarSolicitud($db, $ref, 'agregarObservacion', [$id, $texto]);
            $pedido = self::leer($db, $id);
            self::autorizar($pedido);
            if ($resultado !== null) { $db->commit(); return $resultado; }
            $lista = self::observacionesGuardadas($pedido['observaciones']);
            $ref = self::ref($ref);
            foreach ($lista as $obs) {
                if ($ref !== null && ($obs['ref'] ?? null) === $ref) {
                    if (($obs['observacion'] ?? '') !== $texto) throw new InvalidArgumentException('Ese intento de observación contiene otro texto.');
                    self::terminarSolicitud($db, $clave, $obs);
                    $db->commit();
                    return $obs;
                }
            }
            $observacion = ['observacion' => $texto, 'creador' => (string)($_SESSION['nombre'] ?? 'Usuario'), 'fecha' => date('n/j/Y')];
            if ($ref !== null) $observacion['ref'] = $ref;
            array_unshift($lista, $observacion);
            self::actualizar($db, $pedido, ['observaciones' => self::json($lista)]);
            self::terminarSolicitud($db, $clave, $observacion);
            $db->commit();
            return $observacion;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /** Quita una observación propia recién agregada, identificada por su contenido exacto. */
    public static function quitarObservacion($id, $observacion, $ref = null)
    {
        if (!is_array($observacion) || !isset($observacion['observacion'], $observacion['creador'], $observacion['fecha'])) throw new InvalidArgumentException('La observación no es válida.');
        if ($observacion['creador'] !== (string)($_SESSION['nombre'] ?? '')) throw new RuntimeException('Solo puedes quitar tus propias observaciones.');
        $db = Conexion::conectar();
        self::iniciarTransaccion($db, $ref === null ? ['pedidos'] : ['pedidos', 'pedidos_guardados']);
        try {
            list($clave, $resultado) = self::iniciarSolicitud($db, $ref, 'quitarObservacion', [$id, $observacion]);
            $pedido = self::leer($db, $id);
            self::autorizar($pedido);
            if ($resultado !== null) { $db->commit(); return; }
            $lista = self::observacionesGuardadas($pedido['observaciones']);
            foreach ($lista as $i => $obs) {
                if ((!isset($observacion['ref']) || ($obs['ref'] ?? null) === $observacion['ref']) && ($obs['observacion'] ?? null) === $observacion['observacion'] && ($obs['creador'] ?? null) === $observacion['creador'] && ($obs['fecha'] ?? null) === $observacion['fecha']) {
                    array_splice($lista, $i, 1);
                    self::actualizar($db, $pedido, ['observaciones' => self::json($lista)]);
                    self::terminarSolicitud($db, $clave, ['quitada' => true]);
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
        self::iniciarTransaccion($db);
        try {
            $pedido = self::leer($db, $id);
            if ($validarPerfil) self::autorizar($pedido);
            if (!$validarPerfil && ($_SESSION['perfil'] ?? '') !== 'Super-Administrador' && (int)$pedido['id_empresa'] !== (int)($_SESSION['empresa'] ?? 0)) {
                throw new RuntimeException('El pedido pertenece a otra empresa.');
            }
            $estado = self::estado($estado);
            if ($estado !== (string)$pedido['estado']) self::actualizar($db, $pedido, ['estado' => $estado]);
            $db->commit();
            return 'ok';
        } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
    }

    /** Ambos extremos del vínculo se escriben y verifican en la misma transacción. */
    private static function vincular($db, $tablaOrdenes, $pedido, $idOrden)
    {
        $orden = self::leer($db, $idOrden, $tablaOrdenes);
        if ((int)$orden['id_empresa'] !== (int)$pedido['id_empresa']) throw new InvalidArgumentException('La orden y el pedido deben pertenecer a la misma empresa.');
        if (!empty($orden['id_pedido']) && (int)$orden['id_pedido'] !== (int)$pedido['id']) throw new RuntimeException('La orden ya tiene otro pedido asignado.');
        $stmt = $db->prepare('SELECT id FROM pedidos WHERE id_orden = :orden AND id <> :pedido');
        $stmt->execute(['orden' => $idOrden, 'pedido' => $pedido['id']]);
        if ($stmt->fetch()) throw new RuntimeException('La orden ya tiene otro pedido asignado.');
        $lock = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $stmt = $db->prepare("SELECT id FROM $tablaOrdenes WHERE id_pedido = :pedido" . $lock);
        $stmt->execute(['pedido' => $pedido['id']]);
        $anteriores = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $db->prepare("UPDATE $tablaOrdenes SET id_pedido = 0 WHERE id_pedido = :pedido")->execute(['pedido' => $pedido['id']]);
        $db->prepare("UPDATE $tablaOrdenes SET id_pedido = :pedido WHERE id = :orden")->execute(['pedido' => $pedido['id'], 'orden' => $idOrden]);
        self::comprobarCampos(self::leer($db, $idOrden, $tablaOrdenes), ['id_pedido' => $pedido['id']]);
        foreach ($anteriores as $anterior) {
            if ((int)$anterior !== (int)$idOrden) self::comprobarCampos(self::leer($db, $anterior, $tablaOrdenes), ['id_pedido' => 0]);
        }
        self::actualizar($db, $pedido, ['id_orden' => (int)$idOrden]);
    }

    public static function asignar($idPedido, $idOrden)
    {
        $db = Conexion::conectar();
        $tablaOrdenes = self::tablaOrdenes($db);
        self::iniciarTransaccion($db, ['pedidos', $tablaOrdenes]);
        try {
            $pedido = self::leer($db, $idPedido);
            self::autorizar($pedido);
            self::vincular($db, $tablaOrdenes, $pedido, $idOrden);
            $db->commit();
            return 'ok';
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    public static function crear($entrada)
    {
        self::crearPedido($entrada, $entrada['solicitud'] ?? null);
        return 'ok';
    }

    public static function crearPedido($entrada, $solicitud = null)
    {
        foreach (['empresa', 'cliente', 'asesor', 'id_orden'] as $campo) {
            $valor = $entrada[$campo] ?? ($campo === 'id_orden' ? 0 : null);
            if (filter_var($valor, FILTER_VALIDATE_INT) === false || (int)$valor < ($campo === 'id_orden' ? 0 : 1)) throw new InvalidArgumentException('Selecciona un valor válido para ' . $campo . '.');
        }
        self::autorizar(['id_empresa' => $entrada['empresa']]);
        if ((int)$entrada['cliente'] <= 0 || (int)$entrada['asesor'] <= 0) throw new InvalidArgumentException('Selecciona cliente y asesor.');
        $productos = self::productos($entrada['productos']);
        if (!$productos) throw new InvalidArgumentException('Agrega al menos un producto.');
        $pagos = self::pagos($entrada['pago']);
        $total = self::numero(array_sum(array_column($productos, 'precio')));
        $estado = self::estado($entrada['estado']);
        $datos = ['id_empresa' => (int)$entrada['empresa'], 'id_cliente' => (int)$entrada['cliente'], 'id_Asesor' => (int)$entrada['asesor'], 'productos' => self::json($productos), 'pagos' => self::json($pagos), 'observaciones' => '[]', 'estado' => $estado, 'total' => $total, 'adeudo' => max(0, round($total - array_sum(array_column($pagos, 'pago')), 2)), 'id_orden' => 0];
        // La fecha de entrega solo se escribe si nace entregado; si no, conserva el valor por omisión de la columna.
        $db = Conexion::conectar();
        $tablaOrdenes = (int)($entrada['id_orden'] ?? 0) > 0 ? self::tablaOrdenes($db) : null;
        $tablas = ['pedidos'];
        if ($solicitud !== null) $tablas[] = 'pedidos_guardados';
        if ($tablaOrdenes !== null) $tablas[] = $tablaOrdenes;
        self::iniciarTransaccion($db, $tablas);
        try {
            list($clave, $resultado) = self::iniciarSolicitud($db, $solicitud, 'crear', $entrada);
            if ($resultado !== null) {
                self::autorizar(self::leer($db, $resultado['id']));
                $db->commit();
                return $resultado;
            }
            $datos['fechaDePedido'] = $db->query('SELECT CURRENT_TIMESTAMP')->fetchColumn();
            if (strpos($estado, 'Entregado') === 0) $datos['fechaEntrega'] = $datos['fechaDePedido'];
            $campos = array_keys($datos);
            $db->prepare('INSERT INTO pedidos (' . implode(', ', $campos) . ') VALUES (:' . implode(', :', $campos) . ')')->execute($datos);
            $id = $db->lastInsertId();
            self::comprobarCampos(self::leer($db, $id), $datos);
            if ($tablaOrdenes !== null) self::vincular($db, $tablaOrdenes, self::leer($db, $id), (int)$entrada['id_orden']);
            $actual = self::leer($db, $id);
            self::terminarSolicitud($db, $clave, $actual);
            $db->commit();
            return $actual;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    public static function eliminar($id)
    {
        $db = Conexion::conectar();
        $tablaOrdenes = self::tablaOrdenes($db);
        self::iniciarTransaccion($db, ['pedidos', $tablaOrdenes]);
        try {
            $pedido = self::leer($db, $id);
            self::autorizar($pedido, true);
            $db->prepare("UPDATE $tablaOrdenes SET id_pedido = 0 WHERE id_pedido = :id")->execute(['id' => $pedido['id']]);
            $stmt = $db->prepare("SELECT id FROM $tablaOrdenes WHERE id_pedido = :id");
            $stmt->execute(['id' => $pedido['id']]);
            if ($stmt->fetch()) throw new RuntimeException('No se pudo quitar el vínculo de la orden. No se eliminó el pedido.');
            $db->prepare('DELETE FROM pedidos WHERE id = :id')->execute(['id' => $pedido['id']]);
            $stmt = $db->prepare('SELECT id FROM pedidos WHERE id = :id');
            $stmt->execute(['id' => $pedido['id']]);
            if ($stmt->fetch()) throw new RuntimeException('No se pudo verificar la eliminación del pedido.');
            $db->commit();
            return 'ok';
        } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
    }
}
