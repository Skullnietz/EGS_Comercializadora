# EGS Comercializadora

Panel de administracion interno para la operacion comercial de EGS Equipo de Computo. El proyecto centraliza procesos de ordenes de servicio, ventas, pedidos, cotizaciones, clientes, inventario, comisiones, metas, tickets y administracion general.

## Resumen

- Arquitectura MVC en PHP sin framework.
- Frontend basado en AdminLTE 2, Bootstrap 3 y jQuery.
- Ruteo por `mod_rewrite` de Apache y lista blanca en `config/routes.php`.
- Integracion con 3 bases de datos MySQL.
- Soporte para PDFs, correo SMTP y webhook/servicios auxiliares.

## Alcance funcional

Los modulos principales del sistema incluyen:

- Ordenes de servicio y seguimiento.
- Ventas, cortes y reportes.
- Pedidos y control comercial.
- Cotizaciones e impresion.
- Clientes y CRM.
- Productos, categorias, stock y almacenes.
- Tecnicos, asesores, comisiones y metas.
- Tickets internos.
- Reportes administrativos.
- Configuracion de WhatsApp y componentes auxiliares.

## Stack del proyecto

| Capa | Tecnologia |
| --- | --- |
| Backend | PHP 7.1+ |
| Base de datos | MySQL |
| Acceso a datos | mysqli + PDO |
| Frontend | AdminLTE 2 + Bootstrap 3 |
| JavaScript | jQuery + DataTables |
| Correo | PHPMailer |
| PDF | TCPDF |
| Servidor | Apache con `mod_rewrite` |
| Dependencias PHP | Composer |

## Estructura principal

```text
EGS_Comercializadora/
|-- ajax/                 Endpoints AJAX
|-- config/               Configuracion, entorno, router y rutas
|-- controladores/        Logica del sistema
|-- extensiones/          Librerias de terceros
|-- modelos/              Acceso a datos
|-- ServerSide/           Soporte para DataTables server-side
|-- sql/                  Scripts SQL y migraciones puntuales
|-- vistas/               Plantillas, modulos, JS, CSS e imagenes
|-- webhook/              Componentes auxiliares de integracion
|-- .env.example          Plantilla de variables de entorno
|-- .htaccess             Reglas de Apache
|-- composer.json         Configuracion de Composer
|-- index.php             Punto de entrada
`-- documentacion_proyecto_egs.md
```

## Inventario rapido del codigo

Conteo actual del repo:

- `controladores/`: 37 archivos
- `modelos/`: 41 archivos
- `ajax/`: 51 archivos
- `vistas/modulos/`: 122 archivos
- `vistas/js/`: 33 archivos
- `config/routes.php`: 72 rutas permitidas

## Flujo general

1. Apache redirige la solicitud a `index.php`.
2. `index.php` carga entorno, controladores, modelos y extensiones necesarias.
3. `controladores/plantilla.controlador.php` delega la vista principal.
4. `vistas/plantilla.php` resuelve la ruta solicitada.
5. `config/routes.php` limita los slugs permitidos.
6. Los modulos interactuan con modelos y endpoints AJAX segun la operacion.

## Configuracion de entorno

El proyecto usa un archivo `.env` cargado desde `config/env.php`. La plantilla disponible es `.env.example`.

Variables relevantes:

- `APP_ENV`
- `APP_NAME`
- `DB_SISTEMA_*`
- `DB_ECOMMERCE_*`
- `DB_WORDPRESS_*`
- `MAIL_*`
- `NODE_WHATSAPP_*`

## Bases de datos

El sistema trabaja con tres conexiones:

| Conexion | Archivo principal | Driver | Uso |
| --- | --- | --- | --- |
| Sistema | `config/Conexion.php` | mysqli | Operacion del sistema de ventas |
| E-commerce | `modelos/conexion.php` | PDO | Catalogo y datos comerciales relacionados |
| Principal | `modelos/conexionWordpress.php` | PDO | Ordenes, pedidos, comisiones y procesos centrales |

La configuracion central tambien se apoya en `config/Database.php`.

## Instalacion local

1. Colocar el proyecto en un servidor Apache con PHP.
2. Crear el archivo `.env` a partir de `.env.example`.
3. Configurar las tres conexiones MySQL.
4. Ejecutar `composer install`.
5. Verificar que `mod_rewrite` este habilitado.
6. Apuntar el DocumentRoot al proyecto o abrirlo como subdirectorio.

Ejemplo:

```bash
composer install
cp .env.example .env
```

## Archivos clave

- `index.php`: bootstrap principal del sistema.
- `config/env.php`: carga de variables de entorno.
- `config/Database.php`: configuracion unificada de base de datos.
- `config/routes.php`: rutas permitidas del panel.
- `config/Router.php`: soporte del sistema de ruteo.
- `vistas/plantilla.php`: layout principal y resolucion de modulos.
- `ServerSide/`: procesamiento de tablas con carga dinamica.

## Integraciones y dependencias incluidas

- `extensiones/PHPMailer/` para envio de correos.
- `extensiones/tcpdf/` para generacion de PDFs.
- `extensiones/vendor/` para dependencias instaladas por Composer.
- `webhook/` para integraciones auxiliares.
- Variables `NODE_WHATSAPP_*` para comunicar eventos a un servicio externo.

## Rutas y modulos

Las rutas autorizadas viven en `config/routes.php`. Algunas areas del sistema son:

- `inicio`
- `ordenes`
- `ventas`
- `pedidos`
- `cotizacion`
- `clientes`
- `crm`
- `tecnicos`
- `comisiones`
- `metas`
- `almacenes`
- `tickets`
- `reportes`
- `config-whatsapp`

Para agregar una nueva vista publica dentro del panel:

1. Crear el archivo correspondiente en `vistas/modulos/`.
2. Registrar el slug en `config/routes.php`.
3. Conectar la vista con su controlador, modelo o AJAX segun corresponda.

## Despliegue

Consideraciones practicas para produccion:

- No versionar el archivo `.env`.
- Revisar permisos de escritura para cargas de archivos y generacion de PDFs.
- Confirmar acceso a las tres bases de datos antes de publicar cambios.
- Mantener sincronizadas las reglas de `.htaccess` con el entorno Apache/cPanel.

## Documentacion adicional

El archivo `documentacion_proyecto_egs.md` conserva una documentacion mas extensa del sistema. Este `README.md` funciona como referencia rapida de onboarding y operacion tecnica.

## Notas de mantenimiento

- El proyecto sigue dependiendo de PHP 7.1+, por lo que cualquier actualizacion de entorno debe validarse con cuidado.
- Existen componentes legacy y modulos grandes; conviene probar flujos criticos despues de cambios en ordenes, ventas, pedidos y cotizaciones.
- Antes de tocar rutas o bootstrap, revisar `index.php`, `config/routes.php` y `vistas/plantilla.php` en conjunto.
