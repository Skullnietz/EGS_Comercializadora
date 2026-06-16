-- ═══════════════════════════════════════════════════
-- Tablas de tracking web — BD: egsequip_ecomerce
-- Ejecutar una vez en phpMyAdmin o: mysql ... < crear_tablas_visitas.sql
-- ═══════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `visitasPersonas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(45) NOT NULL,
  `pais` VARCHAR(80) NOT NULL DEFAULT 'Desconocido',
  `visitas` INT(11) NOT NULL DEFAULT 1,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_visitas_fecha` (`fecha`),
  KEY `idx_visitas_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Registro de visitantes por IP';

CREATE TABLE IF NOT EXISTS `visitasPaises` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pais` VARCHAR(80) NOT NULL,
  `cantidad` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visitas_pais` (`pais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agregado de visitas por país';
