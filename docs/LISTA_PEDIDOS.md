# Lista, búsqueda y botones de pedidos

Revisión del 15 de octubre de 2026: la lista perdía su contexto al manipular DataTables o después de pasar tiempo buscando. Se revisaron la carga, búsqueda, paginación, ordenación, cantidad de filas y acciones de la lista.

## Hallazgos y cambios

- La vista hacía una petición previa sin utilizar su resultado y después cargaba DataTables. `gestor.pedidos.js` también intentaba inicializar la misma tabla. Ahora `pedidos.lista.js` realiza una sola carga y una sola inicialización.
- Empresa, usuario y perfil estaban en inputs ocultos dentro de `tbody`. Se reprodujo que DataTables los eliminaba al redibujar. Ahora se conservan fuera de la tabla.
- La búsqueda, la página, la cantidad de registros y el orden se conservan en sessionStorage, separados por usuario, empresa y perfil. Se guarda también el texto que todavía se está escribiendo. Volver desde el detalle, recargar o recibir una respuesta lenta conserva ese contexto.
- La búsqueda espera 300 ms después de la última entrada. Un número, `#1005`, `pedido 1005` o `folio 1005` busca parcialmente el folio real de pedido; evita coincidencias por numeración de filas, importes o atributos de botones. `orden 777` busca la orden vinculada. El texto libre busca los campos visibles y admite diferencias de mayúsculas y acentos.
- Las fechas se ordenan por día, mes, año y hora. Acciones y detalle quedan fuera de búsqueda y ordenación. Si una actualización reduce la cantidad de páginas, se muestra una página válida.
- Una carga fallida, respuesta incompleta o sesión vencida muestra un aviso y conserva las filas ya recibidas y el texto de búsqueda. Se ofrece reintentar la carga. Una lista vacía confirmada se distingue de un fallo. Las respuestas antiguas no reemplazan datos de una carga más reciente.
- Detalle e impresión usan la fila seleccionada y guardan el contexto de la lista. Se retiraron peticiones redundantes que no se utilizaban antes de navegar o imprimir. El ticket usa la ruta del mismo servidor y se abre en otra pestaña. Eliminar conserva la confirmación y evita duplicarla con dos clics; cancelar mantiene la fila y confirmar usa el folio seleccionado.
- `gestor.pedidos.js` y `gestorOrdenes.js` se cargan con la fecha de modificación del archivo para invalidar versiones antiguas en caché. La versión fija anterior podía conservar manejadores antiguos después de actualizar el código.

En el código actual del módulo no se encontró un temporizador que recargue la tabla por inactividad. El reporte del hosting requiere comprobarse después de publicar y recargar los archivos actualizados; las pruebas locales verifican que una pausa, respuesta tardía, redibujo o navegación no pierdan el contexto. La revisión de la búsqueda no ejecutó eliminaciones ni modificaciones en producción.

## Publicación y comprobación

Publicar juntos `vistas/js/pedidos.lista.js`, `vistas/js/gestor.pedidos.js`, `vistas/modulos/pedidos.php` y `vistas/plantilla.php`. Recargar una vez con Ctrl+F5 las pestañas que ya estaban abiertas. Esta corrección no requiere una migración de base de datos.

Buscar un folio y un cliente, cambiar cantidad y página, ordenar columnas, dejar una pausa y continuar. Abrir el detalle y regresar: se debe conservar el filtro y el contexto. Comprobar el ticket en otra pestaña y cancelar la confirmación de eliminación. Si se desea probar una eliminación real, usar un pedido de prueba.

## Pruebas

```sh
node tests/pedidos.lista.cjs
npm --prefix tests test
npm --prefix tests run test:ajax
php tests/pedidos.persistencia.php
```

La prueba de lista usa la vista real, renderiza sus inputs de sesión con valores de prueba y carga el DataTables 1.10.15 y Bootstrap incluidos en el proyecto. Simula respuestas lentas, fallidas, malformadas, fuera de orden y vacías; verifica filtros, navegación, orden de fechas, acciones y aislamiento entre empresas. Las acciones de navegación e impresión se capturan y la eliminación no se ejecuta contra un servidor. Las pruebas existentes de captura, endpoint y persistencia comprueban el guardado con bases temporales.
