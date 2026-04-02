-- Ejecutar en la BD egsequip_ecomerce (donde está la tabla citas)
-- Agrega columna para ocultar citas sin eliminarlas
ALTER TABLE citas ADD COLUMN oculto TINYINT(1) NOT NULL DEFAULT 0;
