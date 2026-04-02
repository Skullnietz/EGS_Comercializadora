-- Agregar columna id_cliente a tabla compras para vincular ventas rápidas con el sistema de recompensas
-- Ejecutar una sola vez en la base de datos egsequip_ecomerce

ALTER TABLE `compras` ADD COLUMN `id_cliente` INT(11) DEFAULT NULL AFTER `id_empresa`;
ALTER TABLE `compras` ADD INDEX `idx_id_cliente` (`id_cliente`);
