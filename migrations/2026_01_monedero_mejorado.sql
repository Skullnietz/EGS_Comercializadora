-- ============================================================
-- MIGRACIÓN: Sistema de Monedero Mejorado
-- Fecha: 2026
-- Objetivo: Normalizar trazabilidad y persistir importes completos
-- ============================================================

-- ============================================================
-- 1. MEJORAR TABLA DINERO_ELECTRONICO_MOVIMIENTOS
-- ============================================================

-- Agregar campos para referencia fuerte (evitar depender de descripcion)
ALTER TABLE `dinero_electronico_movimientos` 
ADD COLUMN IF NOT EXISTS `referencia_tipo` ENUM('orden','venta','pedido','reversion') NULL DEFAULT NULL AFTER `id_orden`,
ADD COLUMN IF NOT EXISTS `referencia_id` INT(11) NULL DEFAULT NULL AFTER `referencia_tipo`,
ADD COLUMN IF NOT EXISTS `id_empresa` INT(11) NULL DEFAULT NULL AFTER `referencia_id`,
ADD COLUMN IF NOT EXISTS `id_usuario_aplico` INT(11) NULL DEFAULT NULL AFTER `id_empresa`,
ADD COLUMN IF NOT EXISTS `origen_total_bruto` DECIMAL(10,2) NULL DEFAULT NULL AFTER `id_usuario_aplico`,
ADD COLUMN IF NOT EXISTS `origen_total_neto` DECIMAL(10,2) NULL DEFAULT NULL AFTER `origen_total_bruto`,
ADD COLUMN IF NOT EXISTS `monto_aplicado` DECIMAL(10,2) NULL DEFAULT NULL AFTER `origen_total_neto`,
ADD INDEX `idx_referencia` (`referencia_tipo`, `referencia_id`),
ADD INDEX `idx_empresa` (`id_empresa`),
ADD INDEX `idx_usuario_aplico` (`id_usuario_aplico`);

-- ============================================================
-- 2. ACTUALIZAR REGISTROS EXISTENTES (migración de datos)
-- ============================================================

-- Actualizar registros de ordenes (basado en descripcion)
UPDATE `dinero_electronico_movimientos` 
SET `referencia_tipo` = 'orden',
    `referencia_id` = `id_orden`
WHERE `tipo` = 'canje' 
  AND `descripcion` LIKE '%Orden%'
  AND (`referencia_tipo` IS NULL OR `referencia_tipo` = '');

-- Actualizar registros de ventas (basado en descripcion)
UPDATE `dinero_electronico_movimientos` 
SET `referencia_tipo` = 'venta',
    `referencia_id` = `id_orden`
WHERE `tipo` = 'canje' 
  AND `descripcion` LIKE '%Venta%'
  AND (`referencia_tipo` IS NULL OR `referencia_tipo` = '');

-- Actualizar registros de pedidos (basado en descripcion)
UPDATE `dinero_electronico_movimientos` 
SET `referencia_tipo` = 'pedido',
    `referencia_id` = `id_orden`
WHERE `tipo` = 'canje' 
  AND `descripcion` LIKE '%Pedido%'
  AND (`referencia_tipo` IS NULL OR `referencia_tipo` = '');

-- ============================================================
-- 3. AGREGAR COLUMNAS A TABLA ORDENES
-- ============================================================

ALTER TABLE `ordenes`
ADD COLUMN IF NOT EXISTS `total_bruto_monedero` DECIMAL(10,2) NULL DEFAULT NULL AFTER `total`,
ADD COLUMN IF NOT EXISTS `monto_monedero_aplicado` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `total_bruto_monedero`,
ADD COLUMN IF NOT EXISTS `total_pagado_cliente` DECIMAL(10,2) NULL DEFAULT NULL AFTER `monto_monedero_aplicado`,
ADD COLUMN IF NOT EXISTS `fecha_canje_monedero` DATETIME NULL DEFAULT NULL AFTER `total_pagado_cliente`;

-- ============================================================
-- 4. AGREGAR COLUMNAS A TABLA COMPRAS (Ventas)
-- ============================================================

ALTER TABLE `compras`
ADD COLUMN IF NOT EXISTS `total_antes_monedero` DECIMAL(10,2) NULL DEFAULT NULL AFTER `pago`,
ADD COLUMN IF NOT EXISTS `monto_monedero_aplicado` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `total_antes_monedero`;

-- ============================================================
-- 5. CREAR ÍNDICES ADICIONALES PARA PERFORMANCE
-- ============================================================

-- Índice único para evitar duplicados de canje
ALTER TABLE `dinero_electronico_movimientos`
ADD UNIQUE KEY `uk_referencia_canje` (`referencia_tipo`, `referencia_id`, `tipo`);

-- Nota: El índice único solo aplica para tipos 'canje'. 
-- Para tipos 'acumulacion' pueden existir múltiples registros por referencia.
-- Si MySQL no permite índices parciales, manejar la unicidad en código.

-- ============================================================
-- 6. VERIFICACIÓN (ejecutar después de la migración)
-- ============================================================

-- Verificar estructura actualizada
-- DESCRIBE `dinero_electronico_movimientos`;
-- DESCRIBE `ordenes`;
-- DESCRIBE `compras`;

-- Verificar datos migrados
-- SELECT COUNT(*) as total_registros,
--        SUM(CASE WHEN referencia_tipo IS NOT NULL THEN 1 ELSE 0 END) as con_referencia,
--        SUM(CASE WHEN referencia_tipo IS NULL THEN 1 ELSE 0 END) as sin_referencia
-- FROM `dinero_electronico_movimientos`;

-- ============================================================
-- NOTAS:
-- ============================================================
-- - Ejecutar este script en la base de datos WordPress (egsequip_wp17)
-- - Hacer backup antes de ejecutar
-- - La columna `id_orden` se mantiene por compatibilidad temporal
-- - En futura migración se puede eliminar `id_orden` y usar solo `referencia_id`
-- ============================================================
