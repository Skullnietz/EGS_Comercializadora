-- Permite resolver órdenes con dos técnicos asignando cada partida cobrada.
-- El JSON incluye la huella de las partidas para invalidar automáticamente
-- distribuciones que queden desactualizadas después de editar la orden.

ALTER TABLE `ordenes`
  ADD COLUMN `asignacionComisionTecnicos` LONGTEXT NULL AFTER `TotalTecnicoDos`;
