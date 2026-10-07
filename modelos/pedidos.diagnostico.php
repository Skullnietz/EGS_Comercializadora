<?php

/** Diagnóstico de permisos: exclusivamente SELECT y EXPLAIN; nunca escribe ni concede permisos. */
class PedidosDiagnostico
{
    private static function identificador($nombre)
    {
        return '`' . str_replace('`', '``', $nombre) . '`';
    }

    public static function ejecutar($ecommerce, $wordpress)
    {
        $identidad = 'SELECT DATABASE() AS esquema, CURRENT_USER() AS cuenta, @@hostname AS servidor, @@port AS puerto, @@server_id AS id_servidor';
        $ec = $ecommerce->query($identidad)->fetch(PDO::FETCH_ASSOC);
        $wp = $wordpress->query($identidad)->fetch(PDO::FETCH_ASSOC);
        $pos = strrpos($ec['cuenta'], '@');
        if ($pos === false || $pos === 0) throw new RuntimeException('No se pudo identificar una cuenta de base de datos válida para pedidos.');
        $cuenta = $ecommerce->quote(substr($ec['cuenta'], 0, $pos)) . '@' . $ecommerce->quote(substr($ec['cuenta'], $pos + 1));
        $pedidos = self::identificador($ec['esquema']) . '.`pedidos`';
        $recibos = self::identificador($ec['esquema']) . '.`pedidos_guardados`';
        $ordenes = self::identificador($wp['esquema']) . '.`ordenes`';
        $mismoServidor = true;
        foreach (['servidor', 'puerto', 'id_servidor'] as $campo) {
            if ((string)$ec[$campo] !== (string)$wp[$campo]) $mismoServidor = false;
        }
        $pruebas = [
            ['Leer pedidos', "SELECT * FROM $pedidos WHERE id = 0", "SELECT ON $pedidos"],
            ['Crear pedido', "INSERT INTO $pedidos (id_empresa,id_cliente,id_Asesor,productos,pagos,observaciones,estado,total,adeudo,id_orden,fechaDePedido,fechaEntrega) VALUES (0,0,0,'[]','[]','[]','Entregado/Pagado',0,0,0,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)", "INSERT ON $pedidos"],
            ['Actualizar pedido', "UPDATE $pedidos SET estado=estado,total=total,adeudo=adeudo,productos=productos,pagos=pagos,observaciones=observaciones,fechaEntrega=fechaEntrega,id_orden=id_orden WHERE id=0", "SELECT, UPDATE ON $pedidos"],
            ['Eliminar pedido', "DELETE FROM $pedidos WHERE id=0", "SELECT, DELETE ON $pedidos"],
            ['Crear o recuperar confirmación', "INSERT INTO $recibos (clave,huella,resultado) VALUES ('diagnostico','diagnostico',NULL) ON DUPLICATE KEY UPDATE resultado=resultado", "SELECT, INSERT, UPDATE ON $recibos"],
            ['Leer y bloquear confirmación', "SELECT huella,resultado FROM $recibos WHERE clave='diagnostico' FOR UPDATE", "SELECT, UPDATE ON $recibos"],
            ['Confirmar guardado', "UPDATE $recibos SET resultado=resultado WHERE clave='diagnostico'", "SELECT, UPDATE ON $recibos"]
        ];
        if ($mismoServidor) {
            $pruebas[] = ['Leer orden vinculada', "SELECT * FROM $ordenes WHERE id=0 FOR UPDATE", "SELECT, UPDATE ON $ordenes"];
            $pruebas[] = ['Vincular orden con la cuenta de pedidos', "UPDATE $ordenes SET id_pedido=0 WHERE id=0", "SELECT, UPDATE ON $ordenes"];
        }
        $resultado = ['cuenta_pedidos' => $ec['cuenta'], 'base_pedidos' => $ec['esquema'], 'base_ordenes' => $wp['esquema'],
            'mismo_servidor' => $mismoServidor, 'pruebas' => [], 'sql_para_administrador_bd' => [], 'correcto' => $mismoServidor];
        foreach ($pruebas as [$operacion, $sql, $privilegios]) {
            try {
                // EXPLAIN necesita los permisos de la sentencia; no usar EXPLAIN ANALYZE.
                $stmt = $ecommerce->query('EXPLAIN ' . $sql);
                $stmt->closeCursor();
                $resultado['pruebas'][] = ['operacion' => $operacion, 'correcto' => true];
            } catch (PDOException $e) {
                $codigo = (int)($e->errorInfo[1] ?? $e->getCode());
                $resultado['pruebas'][] = ['operacion' => $operacion, 'correcto' => false, 'codigo' => $codigo];
                $resultado['correcto'] = false;
                if (in_array($codigo, [1044, 1142, 1143, 1227], true)) {
                    $resultado['sql_para_administrador_bd'][] = "GRANT $privilegios TO $cuenta;";
                }
            }
        }
        $resultado['sql_para_administrador_bd'] = array_values(array_unique($resultado['sql_para_administrador_bd']));
        if (!$mismoServidor) $resultado['indicacion'] = 'Las bases están en servidores distintos. Se requiere una conexión compartida para vincular en una sola transacción.';
        return $resultado;
    }
}
