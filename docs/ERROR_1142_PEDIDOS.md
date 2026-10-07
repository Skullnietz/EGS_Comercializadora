# Error 1142 al guardar pedidos

El aviso reportado el 7 de octubre de 2026 muestra un rechazo de permisos de MySQL. El código **1142** significa que la cuenta que ejecutó la sentencia no tiene permiso para esa operación sobre una tabla; **1143** puede señalar una columna concreta. No es un rechazo por el importe, la fecha ni la descripción del producto.

La captura no permite saber cuál tabla o comando se rechazó. La línea `[pedidos]` del `error_log` del hosting contiene ese detalle. La conexión de pedidos (`ECOMMERCE`) y la de órdenes (`WORDPRESS`) tienen cuentas distintas en la configuración predeterminada. El nuevo guardado requiere:

| Cuenta que guarda pedidos | Permisos necesarios |
| --- | --- |
| Tabla `pedidos` de ECOMMERCE | SELECT, INSERT, UPDATE; DELETE para eliminar |
| Tabla `pedidos_guardados` de ECOMMERCE | SELECT, INSERT, UPDATE |
| Tabla `ordenes` de WORDPRESS, para vincular o eliminar un pedido | SELECT y UPDATE, desde **la cuenta de ECOMMERCE** |

La migración instala las tablas y el esquema, pero no cambia los permisos de las cuentas de MySQL. Son pasos separados. Una tabla creada con la cuenta de administración puede resultar inaccesible para la cuenta utilizada por PHP.

La lectura de la orden usa `SELECT ... FOR UPDATE`; por eso requiere permiso de bloqueo además de lectura. Se indican `SELECT, UPDATE` sobre esa tabla, como ya exigía el despliegue de guardado seguro. La aplicación solo escribe su vínculo `id_pedido` desde la cuenta de pedidos.

## Diagnóstico y solución del hosting

Ejecutar en la terminal del hosting, desde la raíz del proyecto, con la misma configuración de entorno de la aplicación:

```sh
php scripts/diagnosticar_guardado_pedidos.php
```

El comando devuelve la cuenta efectiva (`CURRENT_USER()`), los esquemas efectivos, las comprobaciones y, únicamente ante errores de permisos, `sql_para_administrador_bd`: sentencias GRANT dirigidas a esa cuenta y a esas tablas. Usa SELECT y EXPLAIN sin escribir pedidos, sin dar permisos ni imprimir contraseñas. La ejecución desde una instalación local diferente no diagnostica los permisos de producción.

El administrador de la base debe ejecutar los GRANT que correspondan. Ejemplo **solo para los nombres predeterminados**, sujeto a comprobar la cuenta efectiva y los esquemas del hosting:

```sql
GRANT SELECT, INSERT, UPDATE, DELETE ON `egsequip_ecomerce`.`pedidos`
  TO 'egsequip_bdecome'@'localhost';
GRANT SELECT, INSERT, UPDATE ON `egsequip_ecomerce`.`pedidos_guardados`
  TO 'egsequip_bdecome'@'localhost';
GRANT SELECT, UPDATE ON `egsequip_respaldo`.`ordenes`
  TO 'egsequip_bdecome'@'localhost';
```

Si el hosting solo permite gestionar permisos desde cPanel, el responsable debe dar a la cuenta efectiva acceso a las tablas indicadas; si su interfaz solo ofrece permisos para toda una base, solicitar los GRANT por tabla al proveedor. No hace falta crear una cuenta root, cambiar contraseñas ni conceder permisos a todas las bases.

Después, repetir el diagnóstico hasta que pasen las comprobaciones y probar crear un pedido sin orden y otro vinculado a una orden de la misma empresa. Confirmar el pedido en la lista y, para el vínculo, ambas referencias. El comando también revisa eliminación, por lo que un fallo exclusivamente en esa prueba no impide un alta. Un resultado **1146** indica tabla faltante y **1054** columna faltante: aplicar/revisar la migración antes de intentar GRANT. Si las bases están en servidores distintos, la vinculación necesita una conexión compartida; un GRANT no resuelve esa separación.

Si la terminal y PHP web tienen configuraciones de entorno distintas, comparar de forma privada sus cuentas/esquemas efectivos; no compartir contraseñas ni el archivo `.env`. La línea completa del rechazo en el `error_log` identifica la cuenta que utilizó la petición web.

## Corrección realizada en el código

- El backend marca los permisos denegados como `tipo: permisos_servidor`, `codigo: 1142` y `reintentar: false`, aun cuando la respuesta HTTP sea 500.
- El transporte deja de repetir automáticamente ese rechazo. Los fallos de conexión sin resultado confirmado conservan los reintentos con el mismo identificador.
- Alta y detalle muestran «Faltan permisos en el servidor», conservan productos, pagos y borrador, y permiten corregir la captura y volver a intentar después de reparar el servidor.
- La recuperación de confirmaciones usa un UPDATE sin cambios en `resultado`, la columna que ya se actualiza para confirmar. No requiere actualizar innecesariamente la clave del recibo.
- Las transacciones y los recibos siguen siendo obligatorios. No se guarda un pedido incompleto ni se muestra éxito para eludir un permiso denegado.

Se probaron rechazos en el INSERT de pedidos, INSERT y UPDATE de confirmaciones y UPDATE de órdenes, incluido un rechazo después de insertar el pedido. El endpoint devuelve un fallo definitivo y la transacción revierte lo escrito. Las pruebas de interfaz comprueban que no hay reintentos automáticos, que los campos se habilitan y que el borrador se recupera al recargar.

Las pruebas locales usan SQLite y rechazos MySQL simulados; no certifican los permisos del hosting. No se ejecutaron GRANT ni cambios de datos en producción. Hace falta el diagnóstico del servidor o la línea de rechazo para confirmar la tabla y el permiso exactos.

Referencias: [código 1142 de MySQL](https://dev.mysql.com/doc/mysql-errors/8.0/en/server-error-reference.html), [EXPLAIN y sus permisos](https://dev.mysql.com/doc/refman/8.4/en/explain.html), [permisos de SELECT FOR UPDATE](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking-reads.html).
