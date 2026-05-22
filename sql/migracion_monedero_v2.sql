-- ═══════════════════════════════════════════════════════════════════════
-- MIGRACIÓN: Sistema de Monedero Electrónico v2
-- Fecha: 2026-05-04
--
-- INSTRUCCIONES PARA PHPMYADMIN:
--   Opción A (recomendada si ambas BDs están en el mismo servidor):
--     Selecciona cualquier BD y ejecuta todo el script de una vez.
--     Los USE egsequip_respaldo / USE egsequip_ecomerce cambiarán el
--     contexto automáticamente.
--
--   Opción B (si las BDs están separadas):
--     Ejecuta la SECCIÓN A seleccionando egsequip_respaldo.
--     Ejecuta la SECCIÓN B seleccionando egsequip_ecomerce.
--
--   Si ya corriste parte del script y ves error "Duplicate column name",
--   ese ALTER ya se aplicó antes. Puedes ignorar ese error y continuar.
-- ═══════════════════════════════════════════════════════════════════════


-- ══════════════════════════════════════════════════════════════════
--  SECCIÓN A — BD: egsequip_respaldo  (órdenes + monedero)
-- ══════════════════════════════════════════════════════════════════

USE `egsequip_respaldo`;

-- ─────────────────────────────────────────────────────────────────
-- 1. dinero_electronico_movimientos
--    - Ampliar ENUM tipo para soportar reversiones
--    - Agregar columnas de referencia estructurada (reemplaza lógica
--      basada en descripcion LIKE '%Orden%')
--    - Agregar columnas de trazabilidad de importes
-- ─────────────────────────────────────────────────────────────────

-- 1a) Ampliar el ENUM para incluir 'reversion'
ALTER TABLE `dinero_electronico_movimientos`
    MODIFY COLUMN `tipo`
        ENUM('acumulacion','canje','expiracion','reversion')
        NOT NULL
        COMMENT 'Tipo de movimiento';

-- 1b) Agregar columnas de referencia y trazabilidad
ALTER TABLE `dinero_electronico_movimientos`
    ADD COLUMN `referencia_tipo` ENUM('orden','venta','pedido','reversion') DEFAULT NULL
        COMMENT 'Tipo de entidad origen del movimiento'
        AFTER `id_orden`,

    ADD COLUMN `referencia_id` INT(11) DEFAULT NULL
        COMMENT 'ID de la orden, venta o pedido relacionado'
        AFTER `referencia_tipo`,

    ADD COLUMN `id_empresa` INT(11) DEFAULT NULL
        COMMENT 'Empresa donde se realizó la transacción'
        AFTER `referencia_id`,

    ADD COLUMN `id_usuario_aplico` INT(11) DEFAULT NULL
        COMMENT 'ID del usuario (admin/vendedor) que aplicó el movimiento'
        AFTER `id_empresa`,

    ADD COLUMN `origen_total_bruto` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Total de la venta/orden antes de descontar monedero'
        AFTER `id_usuario_aplico`,

    ADD COLUMN `origen_total_neto` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Total cobrado al cliente después de aplicar monedero'
        AFTER `origen_total_bruto`,

    ADD COLUMN `monto_aplicado` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Monto de monedero efectivamente descontado'
        AFTER `origen_total_neto`;

-- 1c) Índices para búsquedas por referencia (evita LIKE '%Orden%')
ALTER TABLE `dinero_electronico_movimientos`
    ADD INDEX `idx_referencia` (`referencia_tipo`, `referencia_id`),
    ADD INDEX `idx_empresa`    (`id_empresa`);

-- 1d) Migrar datos históricos: poblar referencia_tipo y referencia_id
--     a partir de descripcion + id_orden que ya existen
UPDATE `dinero_electronico_movimientos`
SET
    `referencia_tipo` = CASE
        WHEN `descripcion` LIKE '%Venta%'  THEN 'venta'
        WHEN `descripcion` LIKE '%Pedido%' THEN 'pedido'
        WHEN `descripcion` LIKE '%Orden%'  THEN 'orden'
        ELSE NULL
    END,
    `referencia_id` = `id_orden`
WHERE `tipo` = 'canje'
  AND `referencia_tipo` IS NULL
  AND `id_orden` IS NOT NULL;


-- ─────────────────────────────────────────────────────────────────
-- 2. ordenes
--    - Agregar campos para guardar el desglose de cobro cuando se
--      aplica monedero al momento de entrega
-- ─────────────────────────────────────────────────────────────────

ALTER TABLE `ordenes`
    ADD COLUMN `total_bruto_monedero` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Total del servicio antes de descontar monedero'
        AFTER `total`,

    ADD COLUMN `monto_monedero_aplicado` DECIMAL(10,2) NOT NULL DEFAULT 0.00
        COMMENT 'Monto de monedero descontado en esta orden'
        AFTER `total_bruto_monedero`,

    ADD COLUMN `total_pagado_cliente` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Importe efectivamente cobrado al cliente (total - monedero)'
        AFTER `monto_monedero_aplicado`,

    ADD COLUMN `fecha_canje_monedero` DATETIME DEFAULT NULL
        COMMENT 'Fecha y hora en que se aplicó el monedero'
        AFTER `total_pagado_cliente`;


-- ══════════════════════════════════════════════════════════════════
--  SECCIÓN B — BD: egsequip_ecomerce  (ventas rápidas / compras)
-- ══════════════════════════════════════════════════════════════════

USE `egsequip_ecomerce`;

-- ─────────────────────────────────────────────────────────────────
-- 3. compras
--    - Agregar campos para auditoría del descuento por monedero
--    - El campo `pago` existente se conserva como total FINAL cobrado
--      para no romper reportes actuales
-- ─────────────────────────────────────────────────────────────────

ALTER TABLE `compras`
    ADD COLUMN `total_antes_monedero` DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Importe bruto antes de descontar monedero'
        AFTER `pago`,

    ADD COLUMN `monto_monedero_aplicado` DECIMAL(10,2) NOT NULL DEFAULT 0.00
        COMMENT 'Monto de monedero descontado en esta venta'
        AFTER `total_antes_monedero`;

-- ─────────────────────────────────────────────────────────────────
-- FIN DE MIGRACIÓN
-- Verifica con:
--   SHOW COLUMNS FROM egsequip_respaldo.dinero_electronico_movimientos;
--   SHOW COLUMNS FROM egsequip_respaldo.ordenes;
--   SHOW COLUMNS FROM egsequip_ecomerce.compras;
-- ─────────────────────────────────────────────────────────────────
