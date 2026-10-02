-- Ejecutar en la BD de e-commerce antes de desplegar el código, en mantenimiento y con respaldo.
-- Las escrituras seguras comprueban InnoDB; no ejecutan DDL durante una petición.
ALTER TABLE pedidos ENGINE = InnoDB;
ALTER TABLE pedidos
  MODIFY productos MEDIUMTEXT NULL,
  MODIFY pagos MEDIUMTEXT NULL,
  MODIFY observaciones MEDIUMTEXT NULL;

CREATE TABLE IF NOT EXISTS pedidos_guardados (
  clave CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  huella CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  resultado MEDIUMTEXT CHARACTER SET ascii COLLATE ascii_bin NULL,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (clave),
  KEY idx_pedidos_guardados_creado (creado)
) ENGINE = InnoDB;

-- En la BD principal, verificar que ordenes sea InnoDB; convertirla durante mantenimiento si no lo es.
-- La cuenta PDO de e-commerce necesita SELECT y UPDATE sobre <BD_PRINCIPAL>.ordenes.
-- Ambos esquemas deben estar en el mismo servidor MySQL para un único commit.
-- Conservar los recibos: borrarlos elimina la protección ante reintentos antiguos.
