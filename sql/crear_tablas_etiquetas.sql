-- Generador de etiquetas de contacto y garantía.
-- El modelo también ejecuta estas sentencias con IF NOT EXISTS al primer uso.

CREATE TABLE IF NOT EXISTS egs_etiquetas_config (
    id_empresa INT UNSIGNED NOT NULL,
    nombre_comercial VARCHAR(120) NOT NULL,
    lema VARCHAR(180) NOT NULL,
    direccion VARCHAR(300) NOT NULL,
    whatsapp VARCHAR(30) NOT NULL,
    telefono_1 VARCHAR(30) NOT NULL,
    telefono_2 VARCHAR(30) NOT NULL,
    telefono_3 VARCHAR(30) NOT NULL,
    sitio_web VARCHAR(180) NOT NULL,
    actualizado_por INT UNSIGNED DEFAULT NULL,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS egs_etiquetas_garantia (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_orden INT UNSIGNED NOT NULL,
    token CHAR(64) NOT NULL,
    fac_rem VARCHAR(80) NOT NULL DEFAULT '',
    tecnico VARCHAR(160) NOT NULL DEFAULT '',
    clave_cliente VARCHAR(100) NOT NULL DEFAULT '',
    nombre_cliente VARCHAR(180) NOT NULL DEFAULT '',
    equipo VARCHAR(220) NOT NULL DEFAULT '',
    numero_serie VARCHAR(160) NOT NULL DEFAULT '',
    fecha_entrega DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    proximo_servicio DATE DEFAULT NULL,
    creado_por INT UNSIGNED DEFAULT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_egs_etiqueta_orden (id_orden),
    UNIQUE KEY uq_egs_etiqueta_token (token),
    KEY idx_egs_etiqueta_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
