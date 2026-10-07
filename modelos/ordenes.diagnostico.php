<?php

/** Diagnóstico de entrega desde la cuenta WORDPRESS: únicamente SELECT y EXPLAIN. */
class OrdenesDiagnostico
{
    public static function ejecutar($db)
    {
        $identidad = $db->query('SELECT DATABASE() AS esquema, CURRENT_USER() AS cuenta')->fetch(PDO::FETCH_ASSOC);
        $r = ['cuenta_ordenes' => $identidad['cuenta'], 'base_ordenes' => $identidad['esquema'],
            'motores' => [], 'columnas_faltantes' => [], 'fecha_salida' => null,
            'pruebas' => [], 'sql_para_administrador_bd' => [], 'correcto' => true];
        $tablas = ['ordenes', 'dinero_electronico', 'dinero_electronico_movimientos'];
        foreach ($db->query("SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('ordenes','dinero_electronico','dinero_electronico_movimientos')")->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $r['motores'][$fila['TABLE_NAME']] = $fila['ENGINE'];
        }
        foreach ($tablas as $tabla) {
            if (!isset($r['motores'][$tabla])) $r['motores'][$tabla] = null;
            if (strtoupper((string)$r['motores'][$tabla]) !== 'INNODB') $r['correcto'] = false;
        }
        $requeridas = [
            'ordenes' => ['id','id_usuario','id_empresa','estado','total','fecha_Salida','total_bruto_monedero','monto_monedero_aplicado','total_pagado_cliente','fecha_canje_monedero'],
            'dinero_electronico' => ['id_cliente','saldo','token'],
            'dinero_electronico_movimientos' => ['id','id_cliente','id_orden','tipo','monto','saldo_anterior','saldo_nuevo','descripcion','referencia_tipo','referencia_id','id_empresa','id_usuario_aplico','origen_total_bruto','origen_total_neto','monto_aplicado']
        ];
        $columnas = [];
        foreach ($db->query("SELECT TABLE_NAME,COLUMN_NAME,DATA_TYPE,COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('ordenes','dinero_electronico','dinero_electronico_movimientos')")->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $columnas[$fila['TABLE_NAME']][] = $fila['COLUMN_NAME'];
            if ($fila['TABLE_NAME'] === 'ordenes' && $fila['COLUMN_NAME'] === 'fecha_Salida') $r['fecha_salida'] = $fila['COLUMN_TYPE'];
        }
        foreach ($requeridas as $tabla => $campos) {
            $faltantes = array_values(array_diff($campos, $columnas[$tabla] ?? []));
            if ($faltantes) { $r['columnas_faltantes'][$tabla] = $faltantes; $r['correcto'] = false; }
        }
        $pruebas = [
            ['Leer y bloquear orden', 'SELECT * FROM ordenes WHERE id=0 FOR UPDATE', 'SELECT, UPDATE ON ordenes'],
            ['Guardar estado y desglose de entrega', "UPDATE ordenes SET estado='Entregado (Ent)',total=total,fecha_Salida=CURRENT_TIMESTAMP,total_bruto_monedero=0,monto_monedero_aplicado=0,total_pagado_cliente=0,fecha_canje_monedero=NULL WHERE id=0", 'SELECT, UPDATE ON ordenes'],
            ['Crear o bloquear monedero', "INSERT INTO dinero_electronico (id_cliente,saldo,token) VALUES (0,0,'diagnostico') ON DUPLICATE KEY UPDATE id_cliente=id_cliente", 'SELECT, INSERT, UPDATE ON dinero_electronico'],
            ['Leer y bloquear monedero', 'SELECT * FROM dinero_electronico WHERE id_cliente=0 FOR UPDATE', 'SELECT, UPDATE ON dinero_electronico'],
            ['Consultar consumos anteriores', "SELECT * FROM dinero_electronico_movimientos WHERE tipo='canje' AND ((referencia_tipo='orden' AND referencia_id=0) OR (referencia_tipo IS NULL AND id_orden=0 AND descripcion LIKE '%Orden%'))", 'SELECT ON dinero_electronico_movimientos'],
            ['Registrar consumo autorizado', "INSERT INTO dinero_electronico_movimientos (id_cliente,id_orden,tipo,monto,saldo_anterior,saldo_nuevo,descripcion,referencia_tipo,referencia_id,id_empresa,id_usuario_aplico,origen_total_bruto,origen_total_neto,monto_aplicado) VALUES (0,0,'canje',0,0,0,'diagnostico','orden',0,0,0,0,0,0)", 'INSERT ON dinero_electronico_movimientos']
        ];
        $pos = strrpos($identidad['cuenta'], '@');
        if ($pos === false || $pos === 0) throw new RuntimeException('No se pudo identificar la cuenta de órdenes.');
        $cuenta = $db->quote(substr($identidad['cuenta'], 0, $pos)) . '@' . $db->quote(substr($identidad['cuenta'], $pos + 1));
        $base = '`' . str_replace('`', '``', $identidad['esquema']) . '`';
        foreach ($pruebas as [$operacion, $sql, $privilegio]) {
            try {
                // No usar EXPLAIN ANALYZE: el diagnóstico nunca ejecuta las escrituras.
                $db->query('EXPLAIN ' . $sql)->closeCursor();
                $r['pruebas'][] = ['operacion' => $operacion, 'correcto' => true];
            } catch (PDOException $e) {
                $codigo = (int)($e->errorInfo[1] ?? $e->getCode());
                $r['pruebas'][] = ['operacion' => $operacion, 'correcto' => false, 'codigo' => $codigo];
                $r['correcto'] = false;
                if (in_array($codigo, [1044,1142,1143,1227], true)) {
                    [$permisos,$tabla] = explode(' ON ', $privilegio);
                    $r['sql_para_administrador_bd'][] = "GRANT $permisos ON $base.`$tabla` TO $cuenta;";
                }
            }
        }
        $r['sql_para_administrador_bd'] = array_values(array_unique($r['sql_para_administrador_bd']));
        if ($r['columnas_faltantes'] || in_array(null, $r['motores'], true) || count(array_filter($r['motores'], function ($motor) { return strtoupper((string)$motor) !== 'INNODB'; }))) {
            $r['indicacion'] = 'Revisar y aplicar migrations/20261002_entrega_monedero.sql en esta base, con respaldo y mantenimiento. El diagnóstico no modifica el esquema.';
        }
        return $r;
    }
}
