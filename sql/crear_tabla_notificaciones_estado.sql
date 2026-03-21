-- ═══════════════════════════════════════════════════
-- Tabla: notificaciones_estado
-- BD: egsequip_respaldo (misma BD de órdenes)
-- Almacena notificaciones de cambios de estado y traspasos
-- ═══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `notificaciones_estado` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_orden` INT(11) NOT NULL,
  `estado_anterior` VARCHAR(100) NOT NULL COMMENT 'Estado anterior o nombre técnico anterior (traspaso)',
  `estado_nuevo` VARCHAR(100) NOT NULL COMMENT 'Estado nuevo o nombre técnico nuevo (traspaso)',
  `id_usuario_accion` INT(11) DEFAULT NULL COMMENT 'Quién hizo el cambio',
  `nombre_usuario` VARCHAR(150) DEFAULT NULL,
  `titulo_orden` VARCHAR(255) DEFAULT NULL,
  `id_empresa` INT(11) DEFAULT NULL,
  `id_asesor` INT(11) DEFAULT NULL,
  `id_tecnico` INT(11) DEFAULT NULL,
  `tipo` VARCHAR(20) NOT NULL DEFAULT 'estado' COMMENT 'estado | traspaso',
  `leido_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `leido_vendedor` TINYINT(1) NOT NULL DEFAULT 0,
  `leido_tecnico` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_leido` (`id_empresa`, `leido_admin`, `fecha`),
  KEY `idx_asesor_leido` (`id_asesor`, `leido_vendedor`, `fecha`),
  KEY `idx_tecnico_leido` (`id_tecnico`, `leido_tecnico`, `fecha`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Notificaciones de cambios de estado y traspasos en órdenes';
