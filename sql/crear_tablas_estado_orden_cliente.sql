-- =====================================================================
-- Vista pública de estado de orden para el cliente (acceso por QR)
--
-- Dos tablas en la BD de e-commerce (la misma de observacionesOrdenes y
-- reporteEstadoEquipo). Referencian id_orden por valor; no hay FK cruzada
-- porque la tabla `ordenes` vive en otra BD (egsequip_respaldo).
--
--   1) comentariosClienteOrden : comentarios que escribe el cliente desde
--      su página pública. También se muestran al personal en infoOrden.php.
--   2) resenaOrden             : reseña + calificación (1..5). Envío único
--      por orden (UNIQUE en id_orden).
--
-- Ejecutar en la base de datos de e-commerce.
-- =====================================================================

CREATE TABLE IF NOT EXISTS comentariosClienteOrden (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  id_orden   INT NOT NULL,
  comentario VARCHAR(1000) NOT NULL,
  fecha      DATETIME NOT NULL,
  INDEX idx_orden (id_orden),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS resenaOrden (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  id_orden     INT NOT NULL,
  calificacion TINYINT NOT NULL,            -- 1..5
  comentario   VARCHAR(1000) NULL,
  fecha        DATETIME NOT NULL,
  UNIQUE INDEX uq_orden (id_orden),         -- una reseña por orden (envío único)
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
