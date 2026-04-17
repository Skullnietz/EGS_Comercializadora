-- Migración: agregar columnas para el sistema dinámico de pedidos
-- Ejecutar una sola vez en la base de datos de producción

ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS pagos       TEXT    DEFAULT NULL COMMENT 'Historial de abonos en JSON',
  ADD COLUMN IF NOT EXISTS observaciones TEXT  DEFAULT NULL COMMENT 'Observaciones del pedido en JSON',
  ADD COLUMN IF NOT EXISTS productos   TEXT    DEFAULT NULL COMMENT 'Productos actualizados en JSON';
