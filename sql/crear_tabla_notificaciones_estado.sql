-- ═══════════════════════════════════════════════════
-- Tabla: notificaciones_estado
-- BD: egsequip_respaldo (misma BD de órdenes)
-- Almacena notificaciones de cambios de estado
-- ═══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `notificaciones_estado` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_orden` INT(11) NOT NULL,
  `estado_anterior` VARCHAR(100) NOT NULL,
  `estado_nuevo` VARCHAR(100) NOT NULL,
  `id_usuario_accion` INT(11) DEFAULT NULL COMMENT 'Quién hizo el cambio',
  `nombre_usuario` VARCHAR(150) DEFAULT NULL,
  `titulo_orden` VARCHAR(255) DEFAULT NULL,
  `id_empresa` INT(11) DEFAULT NULL,
  `id_asesor` INT(11) DEFAULT NULL,
  `id_tecnico` INT(11) DEFAULT NULL,
  `leido_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `leido_vendedor` TINYINT(1) NOT NULL DEFAULT 0,
  `leido_tecnico` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_leido` (`id_empresa`, `leido_admin`, `fecha`),
  KEY `idx_asesor_leido` (`id_asesor`, `leido_vendedor`, `fecha`),
  KEY `idx_tecnico_leido` (`id_tecnico`, `leido_tecnico`, `fecha`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Notificaciones de cambios de estado en órdenes';
