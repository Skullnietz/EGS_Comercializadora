-- ═══════════════════════════════════════════════════
-- Tabla: dinero_electronico
-- BD: egsequip_respaldo (misma BD de órdenes)
-- Registro de monedero por cliente (token para acceso público)
-- El saldo se calcula DINÁMICAMENTE desde las órdenes entregadas
-- de los últimos 6 meses menos los canjes registrados.
-- ═══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `dinero_electronico` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` INT(11) NOT NULL,
  `saldo` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Campo legacy, el saldo real se calcula dinámicamente',
  `token` VARCHAR(64) NOT NULL COMMENT 'Token único para acceso público al monedero',
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cliente` (`id_cliente`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Monedero de dinero electrónico por cliente';

-- ═══════════════════════════════════════════════════
-- Tabla: dinero_electronico_movimientos
-- BD: egsequip_respaldo
-- Solo almacena CANJES (descuentos aplicados).
-- Las acumulaciones se calculan dinámicamente desde la tabla ordenes.
-- ═══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `dinero_electronico_movimientos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` INT(11) NOT NULL,
  `id_orden` INT(11) DEFAULT NULL,
  `tipo` ENUM('acumulacion','canje','expiracion') NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL COMMENT 'Negativo para canjes',
  `porcentaje_aplicado` DECIMAL(5,2) DEFAULT NULL,
  `saldo_anterior` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `saldo_nuevo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `fecha_expiracion` DATE DEFAULT NULL,
  `expirado` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_orden` (`id_orden`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_expiracion` (`fecha_expiracion`, `expirado`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Movimientos de dinero electrónico (canjes)';
