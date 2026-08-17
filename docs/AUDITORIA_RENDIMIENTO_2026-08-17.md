# Auditoría de disponibilidad y rendimiento — 17 de agosto de 2026

## Resumen ejecutivo

La evidencia separa dos problemas que deben atenderse en este orden:

1. **Disponibilidad del origen:** durante una carga real, Cloudflare devolvió un 523 para una imagen estática. Esto demuestra que el servidor de origen fue inalcanzable de forma intermitente; optimizar JavaScript o imágenes no corrige esa causa.
2. **Carga del frontend WordPress:** cuando el origen responde, la portada ejecuta demasiados recursos y trabajo en el navegador. La demora principal del LCP es de renderizado, no de descarga de la imagen principal.

El error 520 fue reportado durante la incidencia, pero no se reprodujo en esta auditoría. Cloudflare lo clasifica como una respuesta inesperada, vacía o no interpretable del origen. Deben correlacionarse los 520 y 523 con los registros del hosting.

No se modificó el WordPress público porque su tema y plugins no existen en este repositorio. Aplicar aquí un cambio del frontend produciría una falsa sensación de protección. Las mitigaciones del panel administrativo ya están en el commit `912c3c5`.

## Evidencia capturada

### Incidencia 523 reproducida

- URL: `https://comercializadoraegs.com/wp-content/uploads/2021/05/bannner2.png`
- Momento: `2026-08-17 18:56:31 UTC` (`12:56:31`, hora del centro de México)
- Estado: `523 Origin is unreachable`
- Cloudflare Ray ID: `a2cae62eeb424557`
- Centro de datos observado: Atlanta (`ATL`)
- Respuesta: navegador operativo, Cloudflare operativo, host con error
- Reintento indicado por Cloudflare: 120 segundos

El proveedor debe buscar este momento y Ray ID en los registros del firewall, proxy, Apache/LiteSpeed, PHP-FPM, MySQL y límites de cuenta. La guía oficial de Cloudflare para 523 pide confirmar la IP real de origen y revisar el enrutamiento entre Cloudflare y el servidor: <https://developers.cloudflare.com/support/troubleshooting/http-status-codes/cloudflare-5xx-errors/error-523/>.

### Mediciones de laboratorio

Las dos mediciones usan perfiles distintos y no deben promediarse. DevTools se ejecutó sin limitación de CPU/red; Lighthouse simuló condiciones móviles.

| Métrica | DevTools, sin limitación | Lighthouse móvil | Objetivo inicial |
|---|---:|---:|---:|
| LCP | 3.96 s | 25.15 s | < 2.5 s |
| TTFB | 287 ms | 234 ms | < 800 ms |
| FCP | — | 8.8 s | < 1.8 s |
| TBT | — | 2.16 s | < 200 ms |
| CLS | 0.01 | 0.006 | < 0.10 |
| Solicitudes | — | 146 | < 80 como primer objetivo |
| Transferencia | — | 3.63 MB | < 2 MB como primer objetivo |

El LCP observado es el fondo del primer slide:

`/wp-content/uploads/2021/05/pexels-anthony-shkraba-4348395.jpg`

Desglose DevTools:

- TTFB: 287 ms
- Espera antes de cargar el recurso: 524 ms
- Descarga: 314 ms
- Espera de renderizado: 2.83 s, equivalente al 71.6 % del LCP

La meta oficial para un LCP bueno es 2.5 segundos o menos en al menos el percentil 75 de visitas: <https://web.dev/articles/optimize-lcp>.

## Hallazgos priorizados

### P0 — estabilizar el origen antes de optimizar la interfaz

1. Confirmar con el hosting la IP de origen vigente y compararla con los registros A y AAAA reales configurados en Cloudflare. Las respuestas DNS públicas observadas son las direcciones proxy de Cloudflare y no revelan la IP de origen.
2. Verificar si el servidor tiene IPv6 funcional. Si existe un AAAA de origen sin conectividad IPv6 real, corregirlo o retirarlo.
3. Confirmar que firewall, Imunify, CSF o reglas del proveedor permiten todos los rangos IP actuales de Cloudflare.
4. Correlacionar la ventana `18:50–19:05 UTC` con:
   - CPU, memoria, I/O y procesos simultáneos en cPanel;
   - límites CloudLinux: EP, NPROC, PMEM, IO e IOPS;
   - reinicios o saturación de Apache/LiteSpeed y PHP-FPM;
   - conexiones y consultas lentas de MySQL;
   - bloqueos o rate limits del firewall;
   - cambios de IP, ruta o mantenimiento del proveedor.
5. Entregar al hosting la URL, hora, código y Ray ID anteriores. Solicitar también un MTR/traceroute desde el origen hacia una IP de Cloudflare usada antes de la incidencia.
6. En Cloudflare Analytics, filtrar `Edge status code` y `Origin status code` por 520/523. Configurar una alerta de tasa de errores del origen. Cloudflare documenta que 521, 522 y 523 cuentan como errores de origen: <https://developers.cloudflare.com/notifications/reference/traffic-alerts/>.

**Criterio de salida:** 24 horas sin 520/523 durante carga normal y una prueba controlada, con CPU/EP/NPROC debajo de los límites del plan.

### P1 — eliminar la cadena WooCommerce de 4.87 segundos

La portada ejecutó:

`POST /?wc-ajax=get_refreshed_fragments`

La petición tardó aproximadamente 4.87 s, no fue cacheable y devolvió fragmentos de un carrito vacío. El tema debe comprobar si realmente muestra un minicart que necesite actualización en la portada.

Orden seguro:

1. Clonar producción a staging.
2. Desactivar `wc-cart-fragments` únicamente en páginas que no tengan minicart, carrito ni acciones de agregar al carrito.
3. Probar usuario anónimo y autenticado: agregar producto, contador, carrito, checkout y retorno a la portada.
4. Si alguna función falla, revertir el dequeue y evaluar el Mini-Cart Block o carga condicional.

WooCommerce reconoce que Cart Fragments puede causar problemas de rendimiento cuando la tienda no usa esa funcionalidad y eliminó su carga global en versiones posteriores: <https://developer.woocommerce.com/2023/05/24/woocommerce-7-8-beta-1-released/>.

### P1 — consolidar analítica y terceros

La portada contiene 91 etiquetas `script` y 12 cargas de Google Analytics/Tag Manager. Se observaron un contenedor GTM, varias propiedades GA4, propiedades Universal Analytics y dos cargas de `analytics.js`. En la traza, Google Tag Manager transfirió cerca de 1.5 MB y ocupó aproximadamente 480 ms del hilo principal.

Acción:

1. Inventariar propietario y propósito de cada ID desde WordPress, GTM y plugins de analítica.
2. Mantener un solo bootstrap de GTM o `gtag.js`.
3. Configurar dentro de ese contenedor únicamente las propiedades vigentes.
4. Retirar Universal Analytics y plugins que inyecten etiquetas duplicadas.
5. Verificar eventos de compra, formulario y consentimiento antes y después con Tag Assistant.
6. Publicar primero en staging y conservar una versión exportada del contenedor para rollback.

**Criterio de salida:** una sola carga base de GTM/gtag y ningún evento de compra duplicado.

### P1 — corregir fuentes que ocultan texto

DevTools estimó hasta 3 segundos de mejora potencial de FCP. `star.woff` de WooCommerce y fuentes de Font Awesome, Roboto, Poppins y Astra usan comportamiento de bloqueo.

Acción:

1. Añadir `font-display: swap` u `optional` en las declaraciones `@font-face` controladas.
2. Eliminar familias y pesos no usados; alojar localmente solo los necesarios.
3. Sustituir icon fonts por SVG donde resulte práctico.
4. Confirmar que no cambie el diseño al intercambiar la fuente.

Chrome documenta que `font-display` evita ocultar el texto mientras llega la fuente: <https://developer.chrome.com/docs/lighthouse/performance/font-display>.

### P1 — reducir CSS y plugins por página

Lighthouse encontró un paquete CSS combinado de aproximadamente 259 KB transferidos, 1.59 MB descomprimidos y cerca de 97 % sin uso en la portada. También se cargan recursos de Elementor, Smart Slider, Ultimate Member, WooCommerce, WPForms, Dokan, Perfect Survey, TranslatePress y A3 Lazy Load.

Acción:

1. Crear inventario de qué plugin aporta contenido visible en la portada.
2. Descargar condicionalmente CSS/JS de plugins sin uso en esa ruta.
3. Generar CSS crítico específico de la portada; no insertar todo el paquete combinado como crítico.
4. Vaciar y regenerar Fast Velocity Minify después de cada lote de cambios.
5. Medir después de cada lote; no combinar actualización masiva de plugins con optimización de activos.

### P2 — mejorar LCP, caché e imágenes

1. Precargar la imagen de fondo LCP y marcar el preload con `fetchpriority="high"`; al ser un `background-image`, el atributo no puede agregarse directamente al `div`.
2. Mantener en alta prioridad solo el primer slide y reducir la prioridad de slides ocultos.
3. Convertir la imagen LCP a WebP/AVIF y ajustar dimensiones. El ahorro estimado fue 42.9 KB, por lo que esto es secundario frente al retraso de renderizado.
4. Aumentar TTL de archivos versionados de `/wp-content/uploads/`, `/plugins/` y `/themes/` de 30 minutos a 30 días o un año con `immutable`, según la estrategia de versionado.
5. No cachear indiscriminadamente HTML ni rutas/cookies dinámicas de WordPress y WooCommerce.

La traza estimó 715 KB evitables en visitas repetidas por TTL cortos, pero solo unos 50 ms de mejora LCP en la carga medida. La prioridad de carga ayuda especialmente a imágenes de fondo LCP cuando se combina con preload: <https://web.dev/articles/fetch-priority>.

### P2 — errores funcionales y accesibilidad

1. Corregir el enlace de menú `Mi cuenta`, actualmente resuelto como `http://mi-cuenta/`. Debe apuntar a la URL HTTPS/permalink real de la cuenta.
2. Traducir etiquetas expuestas en inglés: `Products search`, `Search`, `previous arrow`, `next arrow`, `Single Line Text` y `Comment or Message`.
3. Mantener el enlace de salto `Ir al contenido`, que sí está presente.
4. Asignar dimensiones a imágenes y placeholders del slider. El CLS actual es bueno; el objetivo es conservarlo.

## Reglas de caché: límites de seguridad

Antes de aumentar caché, excluir al menos:

- `/wp-admin/*` y `/wp-login.php`
- carrito, checkout y mi cuenta
- URLs con `wc-ajax`
- usuarios autenticados
- cookies de sesión de WordPress/WooCommerce

Aplicar primero la regla solo a extensiones estáticas y comprobar `CF-Cache-Status` con dos solicitudes consecutivas. Conservar una exportación o captura de las reglas actuales para volver atrás.

`Always Online` puede servir una versión estática durante una pérdida de conectividad, pero no reemplaza el origen ni protege carrito/checkout: <https://developers.cloudflare.com/cache/how-to/always-online/>.

## Plan de aplicación y verificación

### Fase 1 — hoy, sin cambios funcionales

- Abrir ticket con hosting usando la evidencia 523.
- Exportar métricas de cPanel y Cloudflare de la ventana de incidencia.
- Configurar alertas 5xx/origen.
- Hacer copia de seguridad de base de datos, `wp-content`, configuración de Cloudflare y contenedor GTM.
- Crear staging con caché y cron equivalentes a producción.

### Fase 2 — un cambio por lote en staging

1. Cart fragments condicional.
2. Consolidación de analítica.
3. `font-display` y reducción de fuentes.
4. Descarga condicional de plugins/CSS.
5. Preload LCP, formato moderno y TTL estático.

Después de cada lote:

- vaciar únicamente las cachés afectadas;
- ejecutar una carga fría y tres cargas calientes;
- probar cuenta, búsqueda, producto, carrito, checkout y formularios;
- comparar red, errores de consola, analítica y Core Web Vitals;
- revertir solo ese lote si aparece una regresión.

### Fase 3 — despliegue gradual

- Publicar en una ventana de bajo tráfico.
- Observar CPU, EP/NPROC, 5xx, tiempos PHP/MySQL y conversiones durante 60 minutos.
- Mantener disponibles la versión anterior del tema/plugin, exportación GTM y reglas Cloudflare.
- Repetir auditoría móvil y de escritorio a las 24 horas.

## Indicadores de éxito

- Cero 520/523 durante 24 horas y luego durante 7 días.
- LCP de laboratorio móvil por debajo de 4 s en la primera iteración y menor a 2.5 s como objetivo final.
- FCP menor a 1.8 s, TBT menor a 200 ms y CLS menor a 0.10.
- Menos de 80 solicitudes y menos de 2 MB transferidos en la portada como primer objetivo.
- Una sola carga base de analítica, sin eventos duplicados.
- Ninguna regresión en login, cuenta, carrito, checkout, búsqueda ni formularios.

## Alcance del repositorio

Este repositorio contiene el panel administrativo y un cliente de la API REST de WooCommerce, no el código del WordPress público. Para implementar P1/P2 se requiere el repositorio o copia de staging del tema hijo, mu-plugins y configuración de plugins del dominio `comercializadoraegs.com`.
