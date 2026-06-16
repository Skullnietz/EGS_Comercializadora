-- =====================================================================
-- Tabla de fotos adjuntas a observaciones de órdenes
-- Las imágenes se guardan en disco (vistas/img/observaciones/AAAA-MM/idOrden/);
-- aquí solo se almacena la ruta relativa. id_orden y fecha se denormalizan
-- para poder borrar por orden o por antigüedad sin JOIN (limpieza por espacio).
-- Ejecutar en la base de datos de e-commerce (la misma de observacionesOrdenes).
-- =====================================================================

CREATE TABLE IF NOT EXISTS observacionesFotos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  id_observacion INT NOT NULL,
  id_orden       INT NOT NULL,
  ruta           VARCHAR(255) NOT NULL,
  fecha          DATETIME NOT NULL,
  INDEX idx_obs (id_observacion),
  INDEX idx_orden (id_orden),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
