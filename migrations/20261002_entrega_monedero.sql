-- Ejecutar en la base WordPress que contiene ordenes. Respaldar antes y usar ventana de mantenimiento.
-- No modifica importes históricos ni elimina movimientos. Requiere permiso CREATE ROUTINE/ALTER.
CREATE TABLE IF NOT EXISTS dinero_electronico (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL, saldo DECIMAL(10,2) NOT NULL DEFAULT 0,
  token VARCHAR(64) NOT NULL,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_cliente (id_cliente), UNIQUE KEY uk_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS dinero_electronico_movimientos (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL, id_orden INT NULL,
  tipo ENUM('acumulacion','canje','expiracion','reversion') NOT NULL,
  monto DECIMAL(10,2) NOT NULL, porcentaje_aplicado DECIMAL(5,2) NULL,
  saldo_anterior DECIMAL(10,2) NOT NULL DEFAULT 0, saldo_nuevo DECIMAL(10,2) NOT NULL DEFAULT 0,
  fecha_expiracion DATE NULL, expirado TINYINT NOT NULL DEFAULT 0,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, descripcion VARCHAR(255) NULL,
  KEY idx_cliente (id_cliente), KEY idx_orden (id_orden), KEY idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP PROCEDURE IF EXISTS egs_monedero_columna;
DELIMITER $$
CREATE PROCEDURE egs_monedero_columna(IN tabla VARCHAR(64), IN columna VARCHAR(64), IN definicion VARCHAR(255))
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=tabla AND COLUMN_NAME=columna) THEN
    SET @egs_monedero_ddl = CONCAT('ALTER TABLE `',tabla,'` ADD COLUMN `',columna,'` ',definicion);
    PREPARE egs_stmt FROM @egs_monedero_ddl;
    EXECUTE egs_stmt;
    DEALLOCATE PREPARE egs_stmt;
  END IF;
END$$
DELIMITER ;
CALL egs_monedero_columna('dinero_electronico_movimientos','referencia_tipo','ENUM(''orden'',''venta'',''pedido'',''reversion'') NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','referencia_id','INT NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','id_empresa','INT NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','id_usuario_aplico','INT NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','origen_total_bruto','DECIMAL(10,2) NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','origen_total_neto','DECIMAL(10,2) NULL');
CALL egs_monedero_columna('dinero_electronico_movimientos','monto_aplicado','DECIMAL(10,2) NULL');
CALL egs_monedero_columna('ordenes','total_bruto_monedero','DECIMAL(10,2) NULL');
CALL egs_monedero_columna('ordenes','monto_monedero_aplicado','DECIMAL(10,2) NOT NULL DEFAULT 0');
CALL egs_monedero_columna('ordenes','total_pagado_cliente','DECIMAL(10,2) NULL');
CALL egs_monedero_columna('ordenes','fecha_canje_monedero','DATETIME NULL');
DROP PROCEDURE egs_monedero_columna;

ALTER TABLE dinero_electronico ENGINE=InnoDB;
ALTER TABLE dinero_electronico_movimientos ENGINE=InnoDB,
  MODIFY tipo ENUM('acumulacion','canje','expiracion','reversion') NOT NULL;
ALTER TABLE ordenes ENGINE=InnoDB;

-- Solo referencias identificables; no se inventan administradores ni saldos pasados.
UPDATE dinero_electronico_movimientos
SET referencia_tipo = CASE WHEN descripcion LIKE '%Venta%' THEN 'venta'
  WHEN descripcion LIKE '%Pedido%' THEN 'pedido' WHEN descripcion LIKE '%Orden%' THEN 'orden' ELSE NULL END,
  referencia_id = id_orden
WHERE referencia_tipo IS NULL AND id_orden IS NOT NULL AND tipo IN ('canje','reversion');

DROP PROCEDURE IF EXISTS egs_monedero_indice;
DELIMITER $$
CREATE PROCEDURE egs_monedero_indice()
BEGIN
  IF EXISTS (SELECT 1 FROM dinero_electronico_movimientos
    WHERE referencia_tipo IS NOT NULL AND referencia_id IS NOT NULL
    GROUP BY referencia_tipo, referencia_id, tipo HAVING COUNT(*) > 1) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Hay referencias duplicadas. Conciliar movimientos antes de continuar; no se eliminaron importes.';
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE()
    AND TABLE_NAME='dinero_electronico_movimientos' AND INDEX_NAME='uq_monedero_referencia') THEN
    ALTER TABLE dinero_electronico_movimientos ADD UNIQUE KEY uq_monedero_referencia (referencia_tipo,referencia_id,tipo);
  END IF;
END$$
DELIMITER ;
CALL egs_monedero_indice();
DROP PROCEDURE egs_monedero_indice;

-- Confirmación de motores y esquema; correr también sql/auditoria_consumos_monedero.sql.
SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE()
  AND TABLE_NAME IN ('ordenes','dinero_electronico','dinero_electronico_movimientos');
