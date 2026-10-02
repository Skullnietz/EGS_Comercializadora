# Guardado de pedidos

Se reforzó `PedidosPersistencia`, que ya reunía transacciones PDO, bloqueo de filas, versión del detalle y conservación de datos antiguos. El alta, el detalle, las observaciones, los estados, las asignaciones y la eliminación pasan por esa capa. Las antiguas escrituras que reenviaban listas completas quedan rechazadas; la ruta `AgregarPedido` abre el alta actual.

## Comportamiento

- **Verificación de todos los campos escritos.** Antes del commit se vuelve a leer el registro y se compara cada valor: productos, pagos, observaciones, total, adeudo, estado, empresa, cliente, asesor, orden y fechas, según la operación. Una diferencia revierte la transacción. Guardar sin cambios sigue siendo válido; un ID inexistente falla.
- **Recibos persistentes.** El navegador identifica cada intento con `solicitud`. `pedidos_guardados` conserva una huella del contenido y el resultado en la misma transacción que el pedido. Si se pierde la respuesta, el mismo intento recupera su resultado aunque la versión del pedido haya cambiado después. Reutilizar el identificador con otro contenido se rechaza. También se registran las operaciones de observaciones con su `ref`.
- **Reintentos acotados.** Timeout de 20 segundos por petición y hasta dos reintentos automáticos para errores transitorios o respuestas sin confirmar. Siempre se repite el mismo contenido. Los errores de validación, permisos, sesión y conflictos no se reintentan automáticamente. Si el resultado sigue siendo incierto, se permite volver a pulsar Guardar para consultar el mismo intento y se bloquean cambios a su captura.
- **Recuperación de captura.** Alta y detalle mantienen borradores en `sessionStorage`, separados por usuario, empresa, pedido y pestaña. Se recuperan al recargar esa pestaña. Solo se limpian al confirmar éxito o descartar expresamente. Se puede descargar una copia JSON de la captura. Un borrador de una versión anterior conserva esa versión: requiere recuperar la captura y recapturar sobre los datos actuales; no se aplica automáticamente sobre los cambios de otro usuario.
- **Guardar Pedido es una operación conjunta.** El detalle guarda productos editados, estado, total, abonos nuevos y el texto de observación pendiente dentro de una transacción. Agregar una observación por su botón continúa siendo una operación independiente.
- **Vínculo completo.** Pedido y orden se actualizan con una sola conexión PDO y un único commit, usando el nombre calificado de la tabla de órdenes. Se comprueban ambos extremos y se limpia el vínculo anterior al reasignar o eliminar. Se rechazan órdenes ocupadas o de otra empresa.
- **Compatibilidad de datos.** El detalle conserva los productos que no se editaron y los pagos históricos. El adeudo incluye pago inicial, abonos antiguos y JSON de pagos. Las fechas nuevas se validan como fechas reales `YYYY-MM-DD`. El JSON sigue escapando Unicode para no cortar emojis con las conexiones históricas `utf8`.

## Despliegue requerido

1. Respaldar las tablas afectadas y reservar una ventana de mantenimiento: `ALTER TABLE` puede bloquear escrituras. Aplicar `migrations/20261002_guardado_pedidos.sql` en la base **ECOMMERCE**, que es la conexión utilizada por los modelos locales de pedidos. Esta migración convierte `pedidos` a InnoDB, amplía sus listas a MEDIUMTEXT y crea `pedidos_guardados`.
2. Revisar el motor de `ordenes` en la base **WORDPRESS**. Debe ser InnoDB. Si es distinto, convertirlo en mantenimiento. Los modelos comprueban el motor antes de empezar; nunca convierten tablas ni crean esquema durante el guardado.
3. Para asignar o eliminar pedidos, ambas bases deben estar en el mismo servidor MySQL. La cuenta de ECOMMERCE necesita **SELECT y UPDATE** sobre la tabla `ordenes` del esquema principal. No se requieren permisos generales de administración. Si faltan permisos o las bases están en servidores diferentes, la operación falla antes de escribir y conserva los datos anteriores.
4. Verificar que los tipos de `total` y `adeudo` admitan los importes y centavos utilizados. La comparación detecta columnas que redondean, truncan o cambian esos valores. No se convierten columnas monetarias sin revisar los datos históricos y el esquema real.
5. Desplegar backend, vistas y JavaScript juntos, y recargar las pestañas antiguas. Los archivos nuevos usan `filemtime` para invalidar caché. No borrar recibos para recuperar espacio sin acordar primero el plazo de retención: su eliminación pierde la protección contra reintentos antiguos.

La condición InnoDB importa: PDO puede aceptar `beginTransaction` sobre una tabla MyISAM aunque no permita rollback. Además, el DDL de MySQL puede confirmar transacciones implícitamente. Por eso el esquema se instala aparte y se verifica el motor antes de escribir. Referencias: [transacciones PDO](https://www.php.net/manual/en/pdo.transactions.php), [lecturas con bloqueo de InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking-reads.html).

## Pruebas reproducibles

Desde la raíz del proyecto:

```powershell
php tests/pedidos.persistencia.php
npm ci --prefix tests --ignore-scripts --no-audit --no-fund
npm --prefix tests test
npm --prefix tests run test:ajax
```

La suite PHP ejecuta los modelos reales contra SQLite en memoria, inyectado en el pool PDO, sin conexiones externas. Incluye creación y edición repetidas, cambios de otro usuario, recibos fallidos, falta de migración, comprobación de campos alterados mediante triggers, rollback, fechas, centavos, emojis, datos históricos, permisos, asignación y eliminación.

La suite JavaScript ejecuta los scripts reales de transporte, detalle y alta con jQuery en jsdom; para agregar filas utiliza el manejador real de `gestorOrdenes.js`. Simula respuestas perdidas y rechazadas, recargas con borrador, doble envío, sesión expirada, conservación del identificador y confirmación de éxito. No sustituye una prueba de navegador ni del servidor MySQL.

La suite del endpoint ejecuta `ajax/pedidos.ajax.php` real en procesos PHP independientes, con sesiones de prueba y SQLite temporal compartido. Comprueba las respuestas JSON, códigos HTTP, guardado completo, duplicados, conflictos, sesión expirada, permisos y pestañas antiguas.

## Validación del entorno desplegado

En una copia de la base real, probar crear, guardar, recargar y volver a leer cada flujo con los perfiles autorizados. Incluir dos usuarios editando el mismo pedido, interrumpir una respuesta después del commit y recuperar el intento, centavos, emojis, textos largos y reasignación de orden. Revisar motores, tipos de columna, triggers, permisos, límites HTTP y la configuración efectiva de ambas conexiones.

Las comprobaciones locales no prueban el comportamiento del esquema, bloqueos ni commits de MySQL real. No se accedió a producción ni se ejecutó la migración en una base externa.

Los borradores dependen del almacenamiento de la pestaña: no son una copia del servidor ni garantizan recuperación si se cierra la pestaña, se borra el almacenamiento o el navegador lo bloquea. Si falla la copia local, la pantalla lo indica y permite descargar la captura. Ante un fallo de red, el sistema conserva y permite recuperar el intento; no afirma que un cambio quedó guardado sin confirmación.
