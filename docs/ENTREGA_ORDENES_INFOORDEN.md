# Entrega que no cambia de estado en infoOrden

Revisión del 7 de octubre de 2026. El problema reportado ocurre en `infoOrden`: el proceso parece terminar, pero la orden conserva su estado y no se ve un error.

Se reprodujo que, al rechazar la entrega, la vista seguía ejecutando los guardados de tipo de reparación y partidas del segundo técnico. Sus mensajes de éxito reemplazaban el aviso de error y redirigían a la lista. Ahora el controlador devuelve un fallo explícito, la vista detiene esos guardados y deja el motivo tanto en una alerta HTML como en SweetAlert. Los rechazos SQL incluyen su código; los detalles completos permanecen en `error_log` bajo `Entrega/monedero orden:`.

También se reprodujo una incompatibilidad con `fecha_Salida` de tipo DATE: el guardado enviaba fecha y hora y luego exigía leer exactamente ese texto. Una columna DATE conserva el día, por lo que la comprobación revertía toda la entrega. Ahora se consulta el tipo existente y se escribe y verifica el formato apropiado, sin cambiar el esquema ni relajar la comprobación de estado e importes. La fecha del consumo mantiene su hora en `fecha_canje_monedero`. Véase la [conversión de tipos temporales de MySQL](https://dev.mysql.com/doc/refman/8.4/en/date-and-time-type-conversion.html).

Estas fallas se comprobaron localmente. Sin el registro de error o el diagnóstico de entrega del hosting, no se puede afirmar cuál rechazo originó el caso concreto reportado.

## Publicación y diagnóstico del hosting

Publicar juntos `controladores/ordenes.controlador.php`, `modelos/monedero.persistencia.php`, `vistas/modulos/infoOrden.php` y el nuevo `vistas/modulos/infoOrden.guardado.php`. Para el diagnóstico, publicar además `modelos/ordenes.diagnostico.php` y `scripts/diagnosticar_entrega_ordenes.php`.

Desde la terminal de cPanel:

```sh
cd /home/egsequip/backend.comercializadoraegs.com
php scripts/diagnosticar_entrega_ordenes.php
```

El comando solo consulta metadatos y planes EXPLAIN; no entrega órdenes, consume dinero, cambia tablas ni concede permisos. No imprime contraseñas. Devuelve la cuenta efectiva WORDPRESS, motores, columnas faltantes, tipo de fecha y permisos de las operaciones de entrega y consumo. Un GRANT sugerido se dirige a esa cuenta y a la tabla correspondiente, y debe ejecutarlo el administrador de la base. El diagnóstico de pedidos anterior usa la cuenta ECOMMERCE; su resultado correcto no comprueba este flujo.

La entrega actual usa las tablas de monedero aun con importe cero, para bloquear al cliente y comprobar consumos previos. Si faltan columnas o tablas, o un motor no es InnoDB, revisar y aplicar `migrations/20261002_entrega_monedero.sql` en la base indicada por el diagnóstico, con respaldo y mantenimiento. No ejecutar cambios de esquema durante el guardado ni saltarse la validación para forzar un estado Entregado.

Después, desde la sesión del administrador, comprobar una entrega sin monedero y una entrega con el importe solicitado por el cliente; revisar estado al abrir de nuevo, fecha, ticket, cobro y movimiento. La entrega con consumo continúa siendo una sola transacción y los reenvíos no descuentan dos veces.

## Verificación local

```sh
php tests/monedero.entrega.php
php tests/ordenes.diagnostico.php
node tests/monedero.frontend.cjs
```

Las pruebas usan SQLite temporal, rechazos controlados y jsdom. Reproducen el error ocultado por los guardados secundarios, la ausencia de redirección tras un fallo y la compatibilidad de fecha DATE con y sin consumo, además de las pruebas existentes de autorización, centavos, saldo, reenvíos y concurrencia. No certifican motores, permisos ni escrituras de MySQL en producción.
