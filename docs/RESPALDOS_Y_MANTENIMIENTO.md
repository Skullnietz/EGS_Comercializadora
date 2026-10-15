# Respaldos y mantenimiento de EGS

Configuración aplicada el 15 de octubre de 2026. El código funciona con PHP 7.1 del hosting; las pruebas locales utilizan PHP 8.3. Las rutas de `ops/` corresponden a la cuenta actual `/home/egsequip` y deben adaptarse al cambiar de servidor.

## Archivos versionados y estado privado

| Componente | Fuente en Git | Estado fuera de Git |
| --- | --- | --- |
| Multimedia y OAuth | `config/DriveStorage.php`, `config/DriveMigration.php`, lectores, controladores y scripts `drive-*` | `.egs-drive`: cliente OAuth, tokens, SQLite, manifiestos y marcadores |
| WordPress | `integraciones/wordpress/`: lector, mu-plugins y bloque de Apache | WordPress instalado, base de datos y adjuntos |
| Respaldos cifrados | `config/DriveBackup.php`, `scripts/backup-drive.php` | `.egs-backup`: configuración, llave, estado, agente GPG y trabajo temporal |
| Tareas automáticas | `ops/crontab.egs.example` | Crontab del usuario, combinado con sus otras tareas |
| Rotación de logs | `ops/logs/logrotate.conf`, `ops/logs/aplicar-mantenimiento.php` | `.egs-log-maintenance`: muestras, estado, configuración instalada y respaldo del tema |
| Información legal | `integraciones/wordpress/legales/`, `publicar-legales.php`, mu-plugin de enlaces | Páginas publicadas en WordPress |

Los directorios privados utilizan modo 0700 y los archivos sensibles, 0600. `.gitignore` excluye llaves, tokens, marcadores de activación, bases SQLite y respaldos. No copiar credenciales reales a plantillas ni sustituir los índices privados al desplegar Git: las imágenes migradas necesitan el índice existente y la autorización de su biblioteca.

## Política de respaldos

La carpeta privada de Drive se llama `Respaldos_EGS` y contiene bases diarias, bases semanales y capturas de los sitios. Los identificadores reales se consultan mediante `backup-drive.php --status`, con acceso al servidor; no se requieren identificadores incrustados en el repositorio.

- Bases de datos: conservar **7 fechas diarias** y **4 semanales**. La ejecución corresponde a las **03:00 de America/Mexico_City**, y la copia semanal al **domingo**. El cron consulta cada 15 minutos; la aplicación decide si corresponde ejecutar y no repite una fecha completa.
- Se incluyen las cuatro bases de la cuenta: `egsequip_ecomerce`, `egsequip_respaldo`, `egsequip_wp54331` y `egsequip_sisanweb`. El nombre `respaldo` no implica que sea descartable: contiene datos activos.
- Sitios: capturas manuales de WordPress, administración y estado privado de la integración. No se generan capturas completas nuevas cada día. Se excluyen Git, cachés y respaldos antiguos para evitar acumulación.
- Cifrado: OpenPGP/AES256 con MDC. La llave `recovery.key` permanece fuera de Drive y debe conservarse también fuera del hosting. Git no contiene esa llave.
- Verificación: tamaño y MD5 en Drive, descarga completa de cada archivo cifrado, descifrado y SHA256 del original, integridad de gzip/tar y de las copias SQLite. Para SQL se comprueban tablas y finalización del volcado. No se ha realizado una restauración completa de las bases en un servidor independiente.
- Retención: retirar únicamente carpetas fechadas propias y completas, después de verificar el nuevo respaldo. Los volcados y temporales locales se retiran al terminar, también ante errores.

Los sitios guardan el **índice** de las imágenes de Drive. Estas capturas no duplican de forma independiente toda la biblioteca multimedia: esa biblioteca y sus permisos deben conservarse para restaurar el servicio.

## Operación por CLI

Desde la raíz del backend y con el PHP del hosting:

```bash
PHP_EGS=/opt/cpanel/ea-php71/root/usr/bin/php
$PHP_EGS scripts/backup-drive.php --status
$PHP_EGS scripts/backup-drive.php --databases
$PHP_EGS scripts/backup-drive.php --sites
```

En una instalación nueva, `--init` crea la estructura y una llave si no existe. Requiere una integración de Drive ya conectada y no debe emplearse para sustituir una llave de recuperación existente. `--databases --seed-weekly` permite generar la primera copia semanal junto con la diaria. Para las tareas periódicas se usa `--scheduled`.

Agregar las entradas de `ops/crontab.egs.example` al crontab conservando las tareas existentes y evitando duplicados. No reemplazar el crontab completo por este fragmento. Los tres trabajadores de la migración pueden retirarse del cron cuando no se necesiten más lotes; la reconciliación por orden sigue siendo necesaria.

## Restauración

1. Preparar un entorno separado y conservar una copia de su estado previo.
2. Recuperar de forma privada la llave, descargar los archivos y descifrarlos:

   ```bash
   gpg --batch --passphrase-file recovery.key --output archivo.tar.gz --decrypt archivo.tar.gz.gpg
   gzip -t archivo.tar.gz
   tar -tzf archivo.tar.gz
   ```

3. Restaurar archivos y SQL en el entorno separado, adaptar rutas, dominios y credenciales, y verificar la aplicación antes de cambiar tráfico. Los archivos SQL están comprimidos; descifrarlos, comprobar gzip y descomprimirlos antes de importar.
4. Recuperar `.egs-drive`, especialmente `media.sqlite`, `library.json` y la autorización. Conservar la biblioteca remota y volver a autorizar a su propietario si fuera necesario.
5. Conservar o recuperar `.egs-backup`, su configuración y la llave. Reinstalar las tareas y comprobar nuevas cargas, lectura de medios y un respaldo completo.

## Logs y cachés

Se vaciaron los grandes logs PHP conservando muestras privadas y los inodos y permisos de los archivos activos. WordPress tenía un registro a `dcms_insertar_js`, función inexistente; se protegió mediante `is_callable()`. El cambio está en `integraciones/wordpress/patches/astra-callback.patch` y también en el instalador de mantenimiento.

`ops/logs/logrotate.conf` revisa cuatro logs PHP: backend, WordPress principal y los dos `error_log` históricos de WordPress. Usa `copytruncate`, umbral de 10 MiB y una copia comprimida por archivo en directorios privados separados. El cron revisa cada 15 minutos, por lo que el umbral no constituye un límite estricto. El propio log del mantenimiento tiene umbral de 100 KiB y una copia.

Para instalar o reaplicar en este hosting:

```bash
/opt/cpanel/ea-php71/root/usr/bin/php ops/logs/aplicar-mantenimiento.php --apply
```

El comando comprueba rotación y retención en archivos temporales, respalda y protege el callback de Astra, conserva una muestra y vacía los tres logs de WordPress mayores de 1 MiB; después instala su propia sección de cron preservando las otras tareas. Sin `--apply` solo muestra ayuda. Esta operación no es parte de un despliegue normal ni debe ejecutarse automáticamente con cada pull. La actualización del tema puede sustituir el ajuste de Astra.

La limpieza de caché de WordPress se realizó con las funciones de sus plugins. El caché se vuelve a generar con el tráfico. Se preservaron navegadores de Puppeteer, versiones de Node, correo y estadísticas históricas del hosting.

## Información legal y reglas de WordPress

Las tres páginas publicadas y su contenido están en `integraciones/wordpress/legales/`. `publicar-legales.php --apply` las publica o actualiza por slug, configura la página de privacidad e instala el mu-plugin de enlaces, conservando antes una copia privada. Sin `--apply` muestra ayuda. Las páginas son contenido ya publicado; cualquier cambio de condiciones requiere revisión independiente antes de volver a publicar.

Copiar el lector `integraciones/wordpress/egs-drive-media.php` a la raíz de WordPress y los mu-plugins a `wp-content/mu-plugins`. Añadir el bloque de `htaccess-drive.conf` antes de las reglas generales de WordPress, conservando los bloques de caché y seguridad existentes. El marcador de lectura se activa después de verificar el índice privado; no se versiona ni se activa por descargar Git.

El dominio se verificó y la autorización se confirmó desde Google. Los cambios manuales de Google Cloud y sus credenciales no se reproducen mediante Git; el procedimiento de configuración está en [GOOGLE_DRIVE_MEDIA.md](GOOGLE_DRIVE_MEDIA.md).

## Validación

```bash
php tests/drive_storage.php
php tests/drive_migration_queue.php
php scripts/backup-drive.php --test
```

Estas pruebas usan respuestas simuladas de Google y directorios temporales. La cola incluye comprobaciones de conflicto, integridad y retirada local. También se verificaron en el hosting PHP 7.1, cargas y descargas reales, copias cifradas y la configuración de logs. En la comprobación del 15 de octubre WordPress, el acceso del panel y una imagen de la orden 12323 respondieron correctamente; la imagen mantuvo su MD5 al retirarse la copia local.
