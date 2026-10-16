# Migración de EGS a otro hosting

Para seguir los pasos en un panel, consulta la [guía visual de cPanel con capturas](guias/migracion-cpanel/GUIA_PASO_A_PASO.md). Sus imágenes son de referencia; el proveedor del destino aún debe confirmarse.

Preparado el 16 de octubre de 2026. El servidor actual sigue atendiendo el negocio. Esta preparación no cambia DNS, no interrumpe pedidos y no activa tareas en un segundo servidor.

## Qué se entrega

El asistente `ops/migration/migrate.php` permite exportar, descifrar, verificar, restaurar y configurar. `hosting.example.json` es la única plantilla que necesita adaptar el proveedor: rutas, conexiones, URLs, binarios y SMTP. El JSON real contiene contraseñas y debe vivir fuera de cualquier carpeta pública, con permiso 0600.

El paquete cifrado contiene:

- Archivos efectivos de administración y WordPress, incluidos plugins y páginas legales.
- Las cuatro bases actuales como SQL comprimido, con conteo de tablas.
- Índices SQLite, autorización OAuth y configuración de la biblioteca de Drive.
- Configuración de los respaldos y su llave anterior, necesaria para recuperar las copias existentes.
- Correo Maildir y configuración de las cuentas para entregar al proveedor.
- Adaptaciones portables del sistema, manifiesto SHA-256 y copia del crontab anterior.

No incluye `.git`, `node_modules`, cachés, logs de operación ni la antigua herramienta `unzipper-master`, bloqueada en el origen y con archivos de nombres anómalos. Los enlaces simbólicos se excluyen y se enumeran en el manifiesto; el proveedor debe recrear los alias de buzones. El paquete conserva las referencias a Drive; no contiene otra copia de los 66,244 archivos de la biblioteca. No es un respaldo completo de cuenta cPanel: SSL, DNS, filtros/redirecciones de correo, usuarios y cuotas deben revisarse en el panel.

La exportación preliminar ocurre con el sistema funcionando. Puede tener diferencias de tiempo entre archivos, bases y correo. Antes del corte se necesita una exportación final con escrituras suspendidas.

## Hosting que hay que contratar

Para evaluar un VPS: **2 vCPU, 4 GB RAM y 40 GB de disco** como perfil inicial de prueba. No es un mínimo certificado. No hay mediciones que justifiquen exigir 4 vCPU u 8 GB. Para hosting administrado, comenzar evaluando 15–20 GB de cuota y al menos 300,000 inodos. El origen tenía aproximadamente 8 GB incluyendo correo y archivos que no necesitan desplegarse; el destino requiere espacio temporal adicional para restaurar y validar.

Requisitos funcionales:

- Dos sitios HTTPS con Apache 2.4 y `mod_rewrite`, `.htaccess`, `AllowOverride` y acceso a sus directorios. Nginx/LiteSpeed requieren comprobar o traducir las reglas; no hay plantilla Nginx certificada.
- PHP CLI y PHP-FPM del mismo entorno, con curl, gd, mbstring, openssl, pdo_mysql, mysqli, pdo_sqlite, dom/xml, zip, fileinfo y zlib. PDO SQLite sobre disco local con bloqueos `flock` funcionales.
- SSH, cron y permiso para ejecutar PHP, mysqldump/mariadb-dump, mysql/mariadb, GNU tar, gzip, GnuPG, flock y logrotate. `proc_open` y `exec` disponibles para respaldos.
- Acceso HTTPS saliente a Google Drive/OAuth. Las imágenes conservan sus URLs originales; el backend debe poder leer el estado privado de Drive y WordPress debe ejecutar bajo un usuario que pueda acceder a él.
- MySQL/MariaDB capaz de importar los SQL y las collations existentes; hay tablas InnoDB y MyISAM. El usuario de respaldo necesita SELECT, SHOW VIEW, TRIGGER, LOCK TABLES y acceso a rutinas/eventos. La aplicación también realiza CREATE/ALTER en algunos flujos.
- Capacidad para importar cinco buzones (incluye `egs.compras` y `egs_compras`, que son distintos), SMTP autenticado y manejo de DKIM/SPF/DMARC. El correo puede mantenerse temporalmente en el proveedor anterior.

El origen usa PHP 7.1.33, MySQL 5.7.44 y WordPress 5.7.20 con plugins antiguos. PHP 7.1 está fuera de soporte: [tabla oficial de PHP](https://www.php.net/eol.php). El kit mantiene sintaxis compatible con 7.1 para exportar, pero **no certifica todo el negocio en PHP 8 ni la importación en una versión nueva de MySQL**. Hacer esas pruebas en el destino; no elegir un proveedor que obligue a cambiar todas las versiones sin un entorno de pruebas. WhatsApp Node no es un requisito activo del despliegue auditado.

## 1. Conservar configuración y llaves privadas

Copiar `hosting.example.json` a `/home/USUARIO/.egs-migration/destino.json`, con permiso 0600. Cambiar:

| Campo | Uso |
|---|---|
| `paths.backend` / `paths.wordpress` | Directorios públicos nuevos, separados |
| `paths.drive` / `paths.backup` | Directorios privados nuevos, fuera de ambos sitios |
| `paths.work` | Directorio privado del asistente, configuración y temporales |
| `databases[].key` | Identidad lógica; conservar ecommerce, wordpress, site, legacy y archive |
| `databases[].name/user/password/host/port` | Nuevos esquemas vacíos y credenciales del proveedor |
| `binaries` | Rutas absolutas si las herramientas no aparecen en PATH |
| `environment` | SMTP y ajustes adicionales de `.env.example` |
| `mode` | Dejar `staging` durante las pruebas |

`wordpress` y `site` apuntan ambos a **egsequip_respaldo** en el origen: órdenes y WordPress comparten físicamente ese esquema y deben seguir compartiéndolo en el destino. El exportador lo guarda una sola vez y valida sus alias al restaurar. `legacy` conserva egsequip_wp54331 (tres tablas) y `archive` egsequip_sisanweb (vacía al auditarla). La conexión histórica `sistema` no funcionaba en el origen; no inventar un esquema ni activarla sin comprobar su uso. Puede añadirse con otra entrada si se recupera.

Mantener los dominios `comercializadoraegs.com` y `backend.comercializadoraegs.com`. Así se conservan enlaces, URLs multimedia y retorno OAuth. No realizar reemplazos masivos SQL de URLs: WordPress contiene valores serializados. Véase [migración oficial de WordPress](https://developer.wordpress.org/advanced-administration/upgrade/migrating/).

La llave del paquete de migración es diferente a la de los respaldos diarios. Guardarla en otro lugar seguro y transferirla por separado. No pegar ninguna de las dos en tickets públicos o Git. El origen todavía conserva credenciales antiguas en código; el configurador las retira del destino y genera un entorno privado. Rotar usuarios/contraseñas al migrar; retirarlas del último commit no las borra del historial anterior de Git.

## 2. Generar una copia preliminar en el origen

Usar el kit revisado fuera de `public_html`; no desplegar `main` entero sobre el servidor para exportar. La copia mantiene el código efectivo del origen. El HEAD del servidor y `main` difieren; se instala únicamente la lista explícita de `runtime-files.json` y el pequeño bloque de correo del controlador. No se sustituyen las pantallas de pedidos por otra revisión. Antes de desplegar estos cambios mediante Git, instalar un entorno privado válido: las conexiones nuevas ya no incluyen contraseñas de respaldo en código.

`capture-source.php` puede extraer las conexiones antiguas sin imprimirlas ni cargar WordPress:

```bash
php capture-source.php --backend=/home/ORIGEN/backend.comercializadoraegs.com --wordpress=/home/ORIGEN/public_html --drive=/home/ORIGEN/.egs-drive --backup=/home/ORIGEN/.egs-backup --work=/home/ORIGEN/.egs-migration --output=/home/ORIGEN/.egs-migration/origen.json --mail=/home/ORIGEN/mail --mail-config=/home/ORIGEN/etc --extra-schema=egsequip_sisanweb --legacy-schema=egsequip_wp54331 --runtime-root=/ruta/kit
php migrate.php preflight --config=/home/ORIGEN/.egs-migration/origen.json --database
php migrate.php export --config=/home/ORIGEN/.egs-migration/origen.json --output=/privado/egs-preliminar.tar.gz.gpg --key=/privado/migracion.key
```

Crear la llave antes: `umask 077` y `openssl rand -hex 32 > /privado/migracion.key`. El destino de exportación debe ser nuevo y privado. Tener espacio libre para archivos temporales, cifrado y descifrado de comprobación. Cada SQL usa bloqueos de tablas de su base, incluidos MyISAM; los bloqueos pueden demorar escrituras mientras se genera el dump. No hay garantía de consistencia entre las cuatro bases mientras el negocio sigue activo. [Referencia de mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html).

El exportador comprueba dumps, SQLite, compresión y descifrado antes de dar éxito. El recibo `.json` permite cotejar tamaño y SHA-256 al transferir. Transferir con SSH/SCP, no mediante enlaces públicos. El origen actual necesita `scp -O` porque SFTP está desactivado. El kit no instala paquetes del sistema ni crea usuarios o bases por sí mismo.

## 3. Restaurar en el destino

Crear los cuatro esquemas vacíos y sus usuarios desde el panel. No reutilizar las bases de otro proyecto. Subir kit, paquete cifrado y llave al área privada del destino. El asistente se ejecuta como el usuario dueño de los sitios, sin root.

```bash
php migrate.php unpack --config=/privado/destino.json --file=/privado/egs-preliminar.tar.gz.gpg --key=/privado/migracion.key --destination=/privado/paquete
php migrate.php verify --config=/privado/destino.json --package=/privado/paquete
php migrate.php restore-files --config=/privado/destino.json --package=/privado/paquete
php migrate.php restore-files --config=/privado/destino.json --package=/privado/paquete --apply
php migrate.php restore-db --config=/privado/destino.json --package=/privado/paquete
php migrate.php restore-db --config=/privado/destino.json --package=/privado/paquete --apply
php migrate.php configure --config=/privado/destino.json --package=/privado/paquete
php migrate.php configure --config=/privado/destino.json --package=/privado/paquete --apply
php migrate.php preflight --config=/privado/destino.json --database
```

Sin `--apply`, restauración y configuración muestran la propuesta. `unpack` sí escribe el paquete descifrado en una carpeta privada nueva. Los directorios de restauración deben ser nuevos y las bases deben estar vacías. La importación SQL no es atómica; si falla, recrear únicamente los esquemas nuevos antes de reintentar. No usar `--force` para ignorar errores SQL.

El configurador genera el entorno privado y los puentes PHP, adapta las conexiones y conserva los alias de la base compartida, habilita el lector de multimedia con las rutas nuevas, configura SMTP, quita handlers cPanel y rutas de sesiones anteriores, y genera `crontab.pending` y logrotate. Guarda originales de los archivos modificados en `before-configure-*`. No instala cron ni selecciona la versión de PHP en el panel. El SMTP del JSON configura las notificaciones de órdenes; revisar también el usuario y la contraseña del plugin SMTP de WordPress si el proveedor cambia esas credenciales. Configurar allí límites de memoria, subida, ejecución y sesiones; no copiar las rutas `/var/cpanel/php/sessions/...` del origen.

La importación mantiene tipos/collations. Al cambiar usuarios, revisar los `DEFINER` de vistas/rutinas/triggers y sus permisos; al cambiar nombres, revisar referencias cruzadas a esquemas antiguos. El asistente no sustituye esas expresiones SQL automáticamente. Tampoco actualiza WordPress, plugins o PHP.

El correo se entrega como `mail.tar.gz` y `mail_config.tar.gz` dentro del paquete privado. Pedir al proveedor importación de Maildir, buzones, contraseñas y alias. No extraer `mail_config.tar.gz` en `/etc` del sistema operativo. Los filtros, redirecciones y cuotas se verifican contra el panel anterior.

## 4. Pruebas antes de DNS

Probar con los dominios reales mediante un archivo `hosts` local apuntando a la IP nueva y SSL válido. Si Cloudflare sigue apuntando al servidor viejo, usar acceso directo al origen/`curl --resolve` con certificados válidos. No es suficiente abrir el dominio normal y asumir que se está viendo el destino.

`staging` bloquea POST mediante Apache, correos del flujo de órdenes, escrituras de la integración Drive y sus tareas; WordPress no ejecuta WP-Cron y bloquea sus correos/HTTP salientes. Permite inspección de páginas e imágenes. No constituye aislamiento total de cualquier plugin: mantener el entorno privado y restringir red o usar credenciales de pruebas para ensayos transaccionales. Para probar login, nuevas órdenes y pagos, levantar temporalmente las restricciones en una copia de pruebas con servicios simulados y otra biblioteca Drive, o hacer una prueba controlada durante la ventana de corte.

Comprobar:

- Acceso a administración, consulta de una orden existente y reportes/PDF sin errores PHP.
- Imagen de la orden 12323 a través de su URL anterior y con el archivo ausente localmente; miniaturas, editor y PDF de WordPress.
- Integridad SQLite y mismo número de alias del manifiesto. No crear una biblioteca nueva ni inicializar Drive otra vez.
- Tienda, productos, carrito, login y plugins; pagos únicamente en modo de pruebas.
- Políticas y condiciones públicas; retorno OAuth con URL exacta si cambiara el dominio.
- SMTP con cuenta nueva y prueba de envío expresamente autorizada; recepción de los cinco buzones.
- Backups con la misma biblioteca privada y retención de **7 diarios y 4 semanales**. Probar recuperación, no sólo subida.
- Logs, 404, recursos y crecimiento de almacenamiento. Medir durante actividad real antes de ampliar recursos.

## 5. Corte y reversión

1. Acordar ventana de corte y responsable. Guardar DNS actuales y comprobar certificado de ambos sitios en el destino. Mantener el origen disponible para reversión.
2. Bloquear nuevas escrituras en ambos sitios del origen, incluyendo API, pagos/webhooks y WP-Cron. Detener sus tareas de organización y backup; esperar que terminen procesos activos. Guardar el crontab antes de editarlo. Pausar recepción de correo o acordar una sincronización delta con el proveedor.
3. Generar paquete final nuevo con las escrituras suspendidas:

   ```bash
   php migrate.php export --config=/privado/origen.json --output=/privado/egs-final.tar.gz.gpg --key=/privado/migracion.key --final --quiesced
   ```

   `--quiesced` es una confirmación del operador; el programa no congela servicios ni puede verificar todos los productores externos. En este modo cualquier archivo que cambie durante `tar` hace fallar la exportación. No declarar final la copia preliminar.
4. Restaurar el final en directorios nuevos y esquemas vacíos. Repetir configuración y verificaciones. Evitar escribir sobre el ensayo anterior sin conservarlo.
5. Cambiar `mode` a `production`, ejecutar `configure --apply` y hacer una prueba de orden/comentario/imagen controlada. Verificar Drive y correo.
6. Cambiar los registros de origen en Cloudflare para raíz y backend. No transferir el registro del dominio. Si se mueve correo, cambiar también `mail`, MX, DKIM/SPF/DMARC y confirmar entrega; evitar copiar una política SPF incompatible.
7. Instalar el contenido de `crontab.pending` **añadiéndolo al crontab existente del destino**. Conservar tareas ajenas y verificar que no esté activo en el origen. No copiar los workers de migración ya terminada ni el borrado anual de multimedia del crontab antiguo.
8. Abrir escrituras en el destino, verificar pedidos, imágenes y respaldo. Conservar el origen sin escrituras durante el periodo acordado.

Si hay un fallo antes de aceptar operaciones nuevas, devolver DNS al origen, restaurar su crontab y retirar mantenimiento. Si el destino ya recibió órdenes o pagos, primero reconciliar esos datos y el índice Drive: volver directamente a la base antigua perdería operaciones. No permitir escrituras simultáneas en dos índices Drive independientes.

## Pruebas del kit

`tests/hosting_migration.php` usa un servidor MariaDB aislado indicado por `EGS_TEST_MYSQL_DSN`. Crea y elimina exclusivamente esquemas `egs_fixture_*`: comprueba exportación MyISAM/UTF-8, cuatro bases físicas (una vacía y otra compartida por dos conexiones), cifrado, recuperación con otros nombres y directorios, permisos, SMTP legado, idempotencia, bloqueo de sobrescrituras, detección de corrupción y rechazo de enlaces TAR. No ejecutarlo apuntando a una base productiva. Los tests de Drive verifican rutas antiguas, cargas, fallos, cola y WordPress con un transporte simulado.


## Verificación de la copia real preparada

El 16 de octubre de 2026 se verificó un paquete cifrado de 600,955,622 bytes. Se restauraron sus 185 tablas en cuatro esquemas nuevos de MariaDB 10.6 en un entorno aislado, manteniendo los alias de la base compartida. Se restauraron los archivos, se aplicó la configuración y una segunda ejecución no propuso cambios. La imagen histórica de la orden 12323 se leyó desde Drive con MD5 `5feb2838718142ec846247d70740fb97`, desde las rutas nuevas y sin archivo local.

También se probaron reglas Apache con PHP 7.1: páginas e imágenes accesibles, POST bloqueado en staging y configuración privada denegada por HTTP. Esta evidencia valida el paquete y esos recorridos; los flujos comerciales completos y las versiones del nuevo proveedor se prueban antes del corte. WordPress cambió durante la copia en vivo, por lo que es obligatorio repetirla con escrituras suspendidas para obtener el respaldo final.
