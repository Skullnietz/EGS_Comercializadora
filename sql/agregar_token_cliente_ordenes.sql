-- =====================================================================
-- token_cliente en la tabla `ordenes` (BD egsequip_respaldo)
--
-- Identificador opaco y no secuencial para el acceso público del cliente
-- por QR: ?ruta=estado-orden-cliente&token=<token_cliente>
--
-- El alta de órdenes nuevas genera el token desde PHP
-- (ModeloOrdenes::mdlIngresarOrden -> bin2hex(random_bytes(16))).
-- Este script agrega la columna y rellena las órdenes existentes.
--
-- Ejecutar en la base de datos de órdenes (egsequip_respaldo).
-- =====================================================================

ALTER TABLE ordenes
  ADD COLUMN token_cliente VARCHAR(64) NULL,
  ADD UNIQUE INDEX idx_token_cliente (token_cliente);

-- Backfill de órdenes existentes. UUID() + MD5 son ampliamente soportados;
-- el sufijo con el id garantiza unicidad incluso ante colisiones de MD5.
UPDATE ordenes
   SET token_cliente = LOWER(CONCAT(MD5(CONCAT(id, RAND(), UUID())), LPAD(HEX(id), 8, '0')))
 WHERE token_cliente IS NULL OR token_cliente = '';
