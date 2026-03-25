-- ─────────────────────────────────────────────────────────────────────────────
-- ml_webhook_tablas.sql
-- Tablas para el sistema de notificaciones webhook de MercadoLibre.
-- Base de datos: egsequip_dbsistema  (Database::SISTEMA)
--
-- Las tablas también se crean automáticamente la primera vez que llega
-- una notificación, pero puedes ejecutar este script para prepararlas
-- antes del primer uso.
-- ─────────────────────────────────────────────────────────────────────────────

-- ── 1. Log de notificaciones crudas ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ml_webhook_log` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `topic`       VARCHAR(50)      NOT NULL COMMENT 'orders_v2 | payments | ...',
    `resource`    VARCHAR(255)     NOT NULL COMMENT 'ej. /orders/12345678',
    `ml_user_id`  VARCHAR(50)      DEFAULT NULL,
    `payload`     TEXT             NOT NULL,
    `procesado`   TINYINT(1)       NOT NULL DEFAULT 0,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_topic`   (`topic`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log de notificaciones recibidas de ML';

-- ── 2. Caché de órdenes de compra ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ml_ordenes_cache` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `ml_order_id`      BIGINT UNSIGNED NOT NULL,
    `status`           VARCHAR(50)     DEFAULT NULL COMMENT 'paid | cancelled | ...',
    `total_amount`     DECIMAL(12,2)   DEFAULT NULL,
    `currency_id`      VARCHAR(10)     DEFAULT NULL,
    `date_created`     DATETIME        DEFAULT NULL,
    `date_closed`      DATETIME        DEFAULT NULL,
    `buyer_id`         BIGINT          DEFAULT NULL,
    `buyer_nickname`   VARCHAR(120)    DEFAULT NULL,
    `seller_id`        BIGINT          DEFAULT NULL,
    `seller_nickname`  VARCHAR(120)    DEFAULT NULL,
    `items_json`       TEXT            DEFAULT NULL COMMENT 'Array JSON de productos',
    `payload_json`     LONGTEXT        DEFAULT NULL COMMENT 'Respuesta completa de la API',
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                       ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ml_order_id` (`ml_order_id`),
    INDEX `idx_status`  (`status`),
    INDEX `idx_date`    (`date_created`),
    INDEX `idx_buyer`   (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Caché local de órdenes de MercadoLibre';
