-- =====================================================================
-- Reporte de estado del equipo (de cara al cliente)
-- Cada entrada es texto (descripcion) + varias fotos. Las imágenes se
-- guardan en disco (vistas/img/reporte-equipo/AAAA-MM/idOrden/); aquí
-- solo la ruta relativa. id_orden y fecha desnormalizados para limpiar
-- por orden o por antigüedad sin JOIN.
-- Ejecutar en la base de datos de e-commerce (la misma de observacionesOrdenes).
-- =====================================================================

CREATE TABLE IF NOT EXISTS reporteEstadoEquipo (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  id_orden    INT NOT NULL,
  id_creador  INT NOT NULL,
  descripcion VARCHAR(1000) NOT NULL,
  fecha       DATETIME NOT NULL,
  INDEX idx_orden (id_orden),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reporteEstadoEquipoFotos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  id_reporte INT NOT NULL,
  id_orden   INT NOT NULL,
  ruta       VARCHAR(255) NOT NULL,
  fecha      DATETIME NOT NULL,
  INDEX idx_reporte (id_reporte),
  INDEX idx_orden (id_orden),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
