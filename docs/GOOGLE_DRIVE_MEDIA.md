# Imágenes del sitio en Google Drive

## Estado al 15 de octubre de 2026

La integración está instalada y activa en `backend.comercializadoraegs.com` y en el WordPress de `comercializadoraegs.com`, autorizada por la cuenta propietaria. Las nuevas cargas de imágenes utilizan Drive. La migración del inventario existente terminó: **66,244 archivos copiados, descargados para verificación y retirados del disco local**, con **4,933,727,398 bytes** de contenido liberados, sin pendientes ni casos para revisión en la comprobación final. Los tres procesos de migración siguen programados y consultan su cola. La pantalla de conexión está en:

https://backend.comercializadoraegs.com/drive-conectar.php

Permite una sesión del panel con perfil **Super-Administrador** o un acceso temporal del propietario generado por SSH. La base actual no tiene cuentas Super-Administrador, por lo que se preparó el segundo acceso sin cambiar roles. El cliente OAuth ya está configurado y la cuenta está autorizada. Para renovar el acceso del propietario, ejecutar por SSH `/opt/cpanel/ea-php71/root/usr/bin/php /home/egsequip/backend.comercializadoraegs.com/scripts/drive-owner-link.php`.

La carga, descarga íntegra y presentación por HTTP se verificaron con la cuenta real en ambos sitios. También pasó el optimizador real del backend y el flujo de adjuntos y recuperación temporal del editor de WordPress. El primer lote de 20 imágenes existentes pasó la copia y descarga completas. La biblioteca es https://drive.google.com/drive/folders/1rA7m97bSWZ1y1qhaKaWFKYZ0JP_ZjrZL .

La aplicación OAuth se confirmó en Producción y la autorización se renovó después de esa confirmación. El código exige ese orden antes de retirar originales. Las renovaciones futuras se realizan desde la pantalla del propietario; cada retirada sigue exigiendo una copia descargada y verificada y conserva los archivos en conflicto para revisión.

## Configuración del propietario

1. Crear o seleccionar un proyecto en Google Cloud y habilitar **Google Drive API**.
2. Configurar Google Auth Platform con el correo del propietario. Usar el permiso `https://www.googleapis.com/auth/drive.file` y publicar la aplicación en modo **Producción** para evitar que la autorización de prueba expire a los siete días.
3. Crear un cliente OAuth de tipo **Aplicación web**. Registrar exactamente esta URI de redirección:

   `https://backend.comercializadoraegs.com/drive-conectar.php`

4. Descargar el JSON de ese cliente. Abrir el enlace temporal del propietario y pulsar **Continuar como propietario del servidor**. Seleccionar el JSON y guardarlo. No enviar ese archivo ni sus claves a chats, repositorios o carpetas compartidas.
5. Pulsar **Conectar con Google** y autorizar la cuenta que almacenará las imágenes. La integración crea su propia carpeta **EGS Multimedia - comercializadoraegs.com**. La pantalla muestra la cuenta y el enlace de esa biblioteca.
6. Ejecutar **Probar carga y lectura**. Se crea una imagen de un píxel en `Pruebas`; se compara su contenido al volver a leerla. Esto todavía no activa las cargas del sitio.
7. Verificar una carga y lectura completas en cada sitio. Solo después establecer `backend_ready` y `wordpress_ready` en la configuración privada y activar las cargas.

El permiso `drive.file` permite administrar los archivos creados por esta integración. Los archivos existentes en otras carpetas de Drive no se incorporan automáticamente. La migración de imágenes se ejecutará con las credenciales del propio sitio para que pueda leer las copias resultantes.

## Comportamiento activo

- Las rutas guardadas en las bases se conservan. Un índice privado traduce cada ruta original a su ID de Drive; las reglas de Apache permiten mostrarla desde la URL que ya utiliza el sitio.
- Las nuevas imágenes del backend se codifican con su procesamiento actual, se suben a Drive y se verifican por tamaño y MD5. No se guarda una copia permanente en `vistas/img` cuando Drive está activo. Si la carga falla, se rechaza antes de registrar un alias inexistente.
- Las imágenes, variantes, respaldos `.orig`, PDF y formatos comunes de audio y video de WordPress se suben conservando metadatos y URLs. Solo se retiran las copias locales después de verificar todos los archivos de la familia. Durante generación de miniaturas, restauración de respaldos y edición pueden existir originales temporales. Ante fallos se conservan y se marca `_egs_drive_pending` para reintentar. Los formatos que el lector no puede servir se conservan en el servidor.
- Un cron cada cinco minutos organiza las imágenes de órdenes nuevas o editadas, observaciones y reportes por ID y fecha de ingreso de la orden. La cola conserva todas las fotos pendientes de una orden. Las imágenes compartidas mantienen todas sus relaciones. Los recursos predeterminados se conservan separados de las evidencias de órdenes.
- Las sustituciones conservan la versión anterior de Drive. Las solicitudes de eliminación se registran y no borran automáticamente un archivo que otra orden pudiera compartir.
- Las imágenes permanecen privadas en Drive. El sitio conserva el acceso que ya tenían sus URLs originales; esta integración no añade autenticación a URLs antes públicas.
- Drive almacena los archivos; el hosting sigue sirviendo la aplicación, las bases y las solicitudes de lectura de imágenes. El índice y las credenciales ocupan espacio pequeño en el hosting.

## Inventarios

El inventario del backend está en `output/auditoria-imagenes/`: 31,412 imágenes y 12 archivos de otro tipo. Se confirmaron 27,500 imágenes asociadas a 8,090 órdenes, además de referencias a productos, cabeceras, perfiles y otros registros. Los candidatos por nombre y las relaciones ambiguas están separados de las confirmadas.

El inventario de WordPress está en `output/integracion-drive/wordpress_imagenes_por_registro.csv`: 23,787 imágenes, 21,785 asociadas a sus adjuntos y 2,002 sin vínculo identificado. También documenta productos y entradas que usan el adjunto como portada o en galerías. Se revisaron 2,258 registros de adjuntos.

La auditoría inicial inventarió **55,199 imágenes**. Al comenzar el traslado se encontraron **55,200 imágenes actuales**: 31,413 del backend y 23,787 de WordPress, con 4,615,060,702 bytes de contenido. Los archivos nuevos que no están en el inventario anterior se conservan en `Por_revisar/Sin_inventario`. Cuatro rutas antiguas contienen un tabulador; se preservan sus alias y se limpian los nombres de carpeta correspondientes en Drive.

Se amplió el plan a **66,244 archivos y 4,933,727,398 bytes**: 55,200 imágenes del inventario inicial del traslado, 11,037 respaldos originales de imágenes y 7 PDF. Los PDF están relacionados con sus adjuntos de WordPress. Los respaldos mantienen la referencia a su imagen base y se organizan en `Respaldos_originales`. Un `.orig` vacío no se considera una imagen y permanece en el servidor.

Los archivos sin vínculo no se consideran descartables. El código, los CSS, fuentes, registros y otros archivos de infraestructura permanecen en el hosting. Los ZIP de respaldo de cabeceras se conservaron en respaldos cifrados verificados en Drive antes de retirar las copias locales. Las 743 rutas ausentes de las carpetas actuales que coinciden con entradas del índice del ZIP son información histórica; no forman parte de los 66,244 originales retirados por la migración.

## Migración automática y consulta del progreso

El plan y los estados de cada archivo están en el índice privado `migration.sqlite`. Tres procesos con bloqueos por proceso y por imagen ejecutan lotes de hasta 50 segundos cada minuto. Los estados permiten recuperar interrupciones, conservar conflictos y reintentar errores transitorios. La integración reutiliza conexiones HTTPS entre operaciones de Drive.

El archivo **Progreso de migración EGS.txt** de la biblioteca de Drive se actualiza aproximadamente cada diez minutos y al terminar. Muestra copias verificadas, archivos retirados, bytes liberados, pendientes y casos para revisar.

Por SSH, consultar el estado o ejecutar un lote manual:

```bash
/opt/cpanel/ea-php71/root/usr/bin/php scripts/drive-migration-worker.php --status

/opt/cpanel/ea-php71/root/usr/bin/php scripts/drive-migration-worker.php \
  --run --site=backend --slot=1 --limit=20 --seconds=50 --release-local
```

`--release-local` solo permite retirar originales cuando Producción está confirmada y `tokens.authorized_at` es posterior o igual a `oauth_production.confirmed_at`. La confirmación y renovación se realizan desde el formulario del propietario. Cada retirada exige descarga completa desde Drive, tamaño y MD5 correctos, y un original local sin modificaciones. El bloqueo compartido con las cargas del sitio impide reemplazar una versión nueva con una copia anterior. Los archivos no verificados permanecen en el servidor. `scripts/drive-migrar.php` conserva el modo manual anterior con las mismas protecciones de versión e integridad.

No restaurar las reglas de Apache anteriores después de retirar archivos locales sin recuperar también las imágenes o conservar el lector y el índice de Drive.

## Archivos y configuración

- `config/DriveStorage.php`: OAuth, subidas por partes, índices, verificación, adaptadores de escritura y organización por orden.
- `config/DriveMigration.php`: plan de archivos, asignación exclusiva de trabajos, reintentos y publicación del progreso.
- `scripts/drive-migration-worker.php`: procesos automáticos del traslado.
- `drive-conectar.php`: configuración de propietario y autorización.
- `drive-media.php`: lector de imágenes para las URLs actuales.
- `scripts/drive-migrar.php`: lotes verificados de copia y retirada local.
- `scripts/drive-wordpress-inventario.php`: cruce de archivos con adjuntos, productos y entradas de WordPress mediante consultas de solo lectura.
- `scripts/drive-reconciliar.php`: organización de imágenes de órdenes nuevas o editadas.
- `scripts/drive-owner-link.php`: genera, solo desde CLI, un enlace de acceso del propietario válido 24 horas y de un solo uso. La confirmación abre una sesión exclusiva de configuración de una hora; no inicia sesión en el panel ni modifica usuarios.
- `integraciones/wordpress/`: el lector y el mu-plugin instalados en el WordPress real.

En producción, configuración e índices viven en `/home/egsequip/.egs-drive`, fuera de los directorios públicos, con permisos 0700/0600. La variable `EGS_DRIVE_STATE_DIR` permite aislar pruebas. El archivo `.drive-media-enabled` activa las rutas de lectura; el marcador privado `enabled` activa las escrituras. Ambos están presentes. Los inventarios de relaciones están en `manifests/backend.csv` y `manifests/wordpress.csv` dentro de ese directorio privado. Los registros de cada proceso están en `migration-worker-N.log`.

Los respaldos locales anteriores de código se retiraron después de verificar los nuevos respaldos cifrados en Drive. La política de respaldos, restauración y mantenimiento está en [RESPALDOS_Y_MANTENIMIENTO.md](RESPALDOS_Y_MANTENIMIENTO.md). La configuración y las tareas del hosting se conservan en `ops/`; los tokens, las llaves, los índices y los datos reales permanecen privados fuera de Git.

## Validación realizada

Se ejecutó `php tests/drive_storage.php` localmente con PHP 8.3 y en una carpeta privada del servidor con PHP 7.1. La prueba usa respuestas simuladas de Google y verifica carga por partes, respuesta perdida, integridad, reintentos, caídas, el optimizador real, relaciones compartidas, renovación de tokens, variantes de WordPress, originales temporales del editor y el modo local desactivado. Las rutas HTTP de ambos sitios se probaron con imágenes originales y devolvieron el mismo MD5. El login continúa respondiendo y la pantalla de conexión rechaza usuarios sin la sesión requerida. El acceso temporal se verificó por HTTP: el GET no consume el enlace, la confirmación exige CSRF, el token se usa una vez y la sesión queda limitada a la configuración. HTTP redirige a HTTPS.

Además se ejecutó `php tests/drive_migration_queue.php` localmente y con PHP 7.1. Las pruebas usan un árbol temporal aislado y comprueban descarga corrupta sin pérdida del original, una versión más reciente en Drive, modificación concurrente del original, reanudación, alias con tabuladores, cola sin duplicados, autorización posterior a publicar y múltiples fotos de una orden con su fecha real. Las pruebas reales verificaron permisos, capacidad disponible, subidas, descargas, ambas rutas HTTP, originales, miniaturas, respaldos del editor, PDF y el lote inicial. El origen admite rangos 206 y rechaza rangos imposibles con 416. La configuración observada de Cloudflare entregaba el PDF completo con 200 al solicitar un rango; el contenido se verificó íntegro. El 15 de octubre se confirmó la cola completa y se verificó una imagen de la orden 12323 con el mismo MD5 después de retirar su copia local.

## Referencias de Google

- OAuth para aplicaciones web: https://developers.google.com/identity/protocols/oauth2/web-server
- Permiso por archivo: https://developers.google.com/workspace/drive/api/guides/api-specific-auth
- Cargas reanudables: https://developers.google.com/workspace/drive/api/guides/manage-uploads
