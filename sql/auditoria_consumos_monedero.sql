-- Solo lectura. Ejecutar en la base WordPress, después de la migración de monedero.
-- 1. Descuentos de órdenes sin consumo, duplicados, importes/clientes distintos o entrega faltante.
SELECT o.id AS id_orden, o.id_empresa, o.id_usuario AS cliente_orden, o.estado, o.fecha_Salida,
  o.total, o.total_bruto_monedero, o.monto_monedero_aplicado, o.total_pagado_cliente,
  COUNT(m.id) AS consumos, SUM(ABS(m.monto)) AS consumido, MIN(m.id_cliente) AS cliente_consumo
FROM ordenes o LEFT JOIN dinero_electronico_movimientos m
  ON m.tipo='canje' AND ((m.referencia_tipo='orden' AND m.referencia_id=o.id)
    OR (m.referencia_tipo IS NULL AND m.id_orden=o.id AND m.descripcion LIKE '%Orden%'))
GROUP BY o.id, o.id_empresa, o.id_usuario, o.estado, o.fecha_Salida, o.total,
  o.total_bruto_monedero, o.monto_monedero_aplicado, o.total_pagado_cliente
HAVING (o.monto_monedero_aplicado > 0 OR COUNT(m.id) > 0) AND (
  COUNT(m.id) <> 1 OR COALESCE(SUM(ABS(m.monto)),0) <> COALESCE(o.monto_monedero_aplicado,0)
  OR MIN(m.id_cliente) <> o.id_usuario OR MAX(m.id_cliente) <> o.id_usuario
  OR o.estado <> 'Entregado (Ent)' OR o.fecha_Salida IS NULL
  OR o.total_bruto_monedero IS NULL OR o.total_bruto_monedero <> o.total
  OR o.total_pagado_cliente IS NULL OR o.total_pagado_cliente <> o.total - o.monto_monedero_aplicado);

-- 2. Consumos sin administrador, sin desglose, con saldos incoherentes o signo incorrecto.
SELECT id, id_cliente, referencia_tipo, referencia_id, id_empresa, id_usuario_aplico,
  monto, saldo_anterior, saldo_nuevo, origen_total_bruto, origen_total_neto, monto_aplicado, fecha
FROM dinero_electronico_movimientos WHERE tipo='canje' AND (
  id_usuario_aplico IS NULL OR id_usuario_aplico=0 OR id_empresa IS NULL OR id_empresa=0
  OR referencia_tipo IS NULL OR referencia_id IS NULL OR monto >= 0
  OR origen_total_bruto IS NULL OR origen_total_neto IS NULL OR monto_aplicado IS NULL
  OR monto_aplicado <> ABS(monto) OR origen_total_bruto - monto_aplicado <> origen_total_neto
  OR saldo_anterior - ABS(monto) <> saldo_nuevo OR saldo_nuevo < 0);

-- 3. Consumos huérfanos de órdenes inexistentes.
SELECT m.* FROM dinero_electronico_movimientos m LEFT JOIN ordenes o ON o.id=m.referencia_id
WHERE m.tipo='canje' AND m.referencia_tipo='orden' AND o.id IS NULL;

-- 4. Duplicados históricos: conciliación manual, nunca borrarlos automáticamente.
SELECT referencia_tipo, referencia_id, tipo, COUNT(*) AS registros, SUM(monto) AS monto
FROM dinero_electronico_movimientos WHERE referencia_tipo IS NOT NULL AND referencia_id IS NOT NULL
GROUP BY referencia_tipo, referencia_id, tipo HAVING COUNT(*) > 1;
