# Consumo de dinero electrónico al entregar órdenes

Revisión y corrección del 2 de octubre de 2026. Regla confirmada por el responsable: **el cliente solicita el importe y solo el administrador, desde su propia sesión, autoriza su aplicación durante la entrega**.

## Fallas encontradas y corregidas

| Hallazgo | Efecto anterior | Corrección |
| --- | --- | --- |
| `infoOrden.php` enviaba `montoCanjeMonederoOrden`, pero el controlador dinámico leía `montoCanjeElectronico`. | La petición del cliente podía ignorarse. | El guardado activo lee el campo real. Conserva el nombre antiguo para solicitudes anteriores, con las mismas validaciones. |
| Se descontaba por separado del guardado de la orden; se ignoraban resultados y excepciones. | Entrega sin consumo o consumo sin entrega confirmada. | Una conexión PDO y una transacción abarcan movimiento, orden, fecha y desglose. Un fallo revierte todo y muestra error. |
| `ctrEditarInversiones()` se ejecutaba después del guardado principal y volvía a escribir el estado. | Podía marcar entregada una orden después de rechazar el canje y mostrar otro mensaje de éxito. | El formulario completo guarda las inversiones y observaciones dentro del guardado principal. El modelo independiente de inversiones ya no cambia el estado. |
| El camino dinámico no guardaba el desglose de monedero. | El ticket podía mostrar cobro bruto aunque existiera consumo. | Persistencia de `total_bruto_monedero`, `monto_monedero_aplicado`, `total_pagado_cliente` y `fecha_canje_monedero`. |
| Canjes sin autorización comprobada en servidor; endpoint AJAX sin sesión. | Era posible intentar consumos fuera de la entrega y sin administrador. | Solo administrador puede entregar/aplicar; token de sesión para canjes; validación de empresa y cliente real. Los endpoints de consumo independiente se rechazan antes de consultar la base. |
| Solo se consultaba el saldo y luego se insertaba, sin bloqueo; reenvíos retornaban fallo o podían duplicarse según la migración. | Dos operaciones podían usar el mismo saldo. | Bloqueo del monedero por cliente y de la orden, validación posterior al bloqueo y referencia única. Un reenvío con los mismos importes confirma el consumo existente sin volver a descontar. Las ventas usan el mismo bloqueo. |
| `saldo_anterior` y `saldo_nuevo` se insertaban en cero; faltaban administrador, empresa y origen de importes en el flujo activo. | Registro insuficiente para comprobar qué se consumió y quién lo autorizó. | Movimiento con saldos reales, importe negativo, monto aplicado positivo, administrador, empresa, cliente, orden, bruto y neto. Se vuelve a leer para comprobarlo antes del commit. |
| El botón Todo usaba el saldo completo aunque superara el servicio; el navegador calculaba con un bruto oculto cuando el real era cero. | Desglose que podía cobrar cero y consumir más de lo debido. | Máximo = menor entre saldo y total actual. Una captura inválida detiene el envío, sin cambiar silenciosamente lo solicitado. El servidor valida otra vez. |
| Precios y total se vinculaban como `PDO::PARAM_INT`. | Pérdida de centavos. | Importes enviados como decimales, validados y calculados en centavos para el canje. |
| El saldo e historial acumulaban sobre el bruto, mientras el ticket usaba el importe pagado después del monedero. | Recompensa distinta entre ticket y saldo. | Ambos cálculos de órdenes usan el neto cuando hubo monedero; el saldo se devuelve con dos decimales. |
| Las tablas creadas por el modelo no incluían las columnas que usaban sus INSERT, ni el tipo reversión. | SQL fallido que el flujo de entrega ocultaba. | Esquema inicial alineado y migración para instalaciones existentes. No se ejecuta DDL dentro de un guardado. |

## Flujo que queda implementado

1. El administrador abre la orden del cliente, elige **Entregado (Ent)** y captura el importe que este solicita. Cero o campo ausente significa no aplicar monedero.
2. El servidor comprueba sesión, perfil, empresa, cliente, importe, token de autorización y total de la orden. Los totales ocultos de monedero no determinan el descuento ni el cobro: el desglose se calcula a partir del total que se guarda en la orden.
3. En una transacción bloquea el monedero y la orden. Calcula el saldo disponible con las reglas existentes **antes de entregar**, para que la recompensa nueva no financie esa misma entrega.
4. Inserta el consumo y guarda la orden con su desglose y fecha. Comprueba lo escrito; únicamente después confirma.
5. El ticket existente lee el bruto, descuento y neto guardados. El saldo e historial consideran el consumo y la recompensa del neto de la entrega nueva.

Ejemplo probado: saldo previo **$100.00**, servicio **$200.75**, canje **$60.25**. El movimiento registra saldo anterior **$100.00** y saldo posterior al consumo **$39.75**; la orden y el ticket cobran **$140.50**. La recompensa nueva de **$1.41** deja el saldo disponible en **$41.16**. El saldo del movimiento describe el descuento; la recompensa se obtiene dinámicamente, como en el sistema original.

Una orden con consumo ya registrado no permite cambiar el cliente, cambiar el total, reabrirla ni agregar otro importe de monedero. Inconsistencias antiguas o consumos duplicados exigen conciliación. No se inventan saldos ni autorizaciones históricas.

La estrategia sigue el patrón transaccional ya utilizado por `PedidosPersistencia`. El bloqueo dentro de la transacción está respaldado por la [documentación de lecturas con bloqueo de MySQL](https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html); el esquema se prepara por separado porque [CREATE/ALTER pueden confirmar implícitamente una transacción](https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html).

## Instalación y comprobación en servidor

1. Respaldar la base WordPress que contiene `ordenes`, suspender escrituras durante la migración y ejecutar `migrations/20261002_entrega_monedero.sql` en esa base. No hay un `USE` fijo para evitar seleccionar una base diferente de la configuración instalada.
2. La migración acepta columnas existentes, convierte las tres tablas a InnoDB y añade una referencia única. Si encuentra referencias duplicadas, detiene el alta del índice sin borrar movimientos. Conciliar los duplicados y repetir. Los cambios DDL ya completados no se revierten automáticamente.
3. Ejecutar `sql/auditoria_consumos_monedero.sql`. Revisar descuentos sin consumo, consumos sin descuento/orden/administrador, clientes distintos, saldos incoherentes y duplicados. Una orden antigua sin descuento ni movimiento no permite saber si el cliente lo solicitó: hay que revisar comprobantes y evidencia de la entrega.
4. Publicar los PHP y ambos JavaScript modificados, incluyendo `vistas/js/ordenes.monedero.js`. Recargar las sesiones y formularios abiertos.
5. En un ambiente de prueba MySQL realizar una entrega parcial con administrador, comprobar ticket/historial/saldo, reenviar y probar dos operaciones sobre el mismo cliente. Verificar que los otros perfiles no pueden consumir, ni alterando el POST.

## Pruebas realizadas y límites de la revisión

`npm --prefix tests run test:monedero` ejecuta 66 comprobaciones de persistencia/controlador y 19 de interfaz, endpoint y procesos concurrentes. Incluye los modelos de ambos editores y el controlador activo, el nombre real del formulario, centavos, token, empresa, cliente, perfiles, saldo insuficiente, reintentos, fallos SQL/esquema, alteraciones mediante triggers, el segundo guardado de inversiones y dos entregas concurrentes. También se verifican sintaxis PHP y las 117 comprobaciones existentes de pedidos.

Las pruebas usan SQLite temporal y jsdom, con el pool PDO inyectado: no se conectaron a producción ni se consumió dinero de clientes reales. No hay controlador PDO MySQL ni servidor MySQL local disponible; el SQL de la migración, los motores y los bloqueos InnoDB deben verificarse en el servidor de prueba antes de publicar. Esta revisión del código no certifica que los registros históricos de producción ya estén conciliados.

Quedan estos asuntos fuera de la corrección de entrega, para una revisión del monedero completo:

- El saldo sigue calculándose sobre una ventana móvil de seis meses para recompensas **y consumos**. No existe una asignación de cada canje a los créditos que gastó; su vencimiento puede afectar créditos posteriores. Para una contabilidad por vencimiento se necesita definir una regla de aplicación por antigüedad y migrar los movimientos, sin asumir una política nueva aquí.
- Las ventas comparten ahora el bloqueo del monedero, pero los controladores de ventas todavía guardan la compra y el descuento en operaciones separadas y ocultan fallos del canje. La atomicidad compra-consumo requiere una corrección específica del flujo de ventas y de sus dos bases.
- Existe un método de reversión sin un flujo operativo conectado para anular entregas. La entrega con consumo se bloquea al intentar reabrirla; una reversión formal debe coordinar saldo, estado, ticket e historial. El historial público existente muestra canjes y acumulaciones, pero no incluye las reversiones.
- El porcentaje histórico anterior al 7 de abril se calcula según el número actual de transacciones, sin congelar la tasa original. No se cambiaron esas reglas comerciales ni se reconstruyeron saldos antiguos.
