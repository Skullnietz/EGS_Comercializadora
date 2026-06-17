-- Registro digital de aceptación del aviso de privacidad por cliente.
-- Reemplaza la firma física que antes se imprimía en el ticket.
-- Una sola decisión vigente por cliente (uk_cliente) — al re-aceptar se actualiza.

CREATE TABLE IF NOT EXISTS aceptaciones_privacidad (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  aceptado TINYINT(1) NOT NULL,
  fecha DATETIME NOT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  UNIQUE KEY uk_cliente (id_cliente),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
