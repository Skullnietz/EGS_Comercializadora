-- Solicitudes de ayuda desde el portal del cliente.
-- El cliente envía el mensaje y el admin le da seguimiento internamente.

CREATE TABLE IF NOT EXISTS solicitudes_ayuda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  id_orden INT DEFAULT NULL,
  mensaje VARCHAR(2000) NOT NULL,
  estado ENUM('pendiente','en_proceso','resuelta') NOT NULL DEFAULT 'pendiente',
  fecha DATETIME NOT NULL,
  fecha_resolucion DATETIME DEFAULT NULL,
  notas_admin VARCHAR(2000) DEFAULT NULL,
  INDEX idx_cliente (id_cliente),
  INDEX idx_estado (estado),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
