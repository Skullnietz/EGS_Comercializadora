# Analítica web — EGS Comercializadora

## 1. Tracking propio (backend)

Endpoint:

```
GET/POST https://backend.comercializadoraegs.com/extensiones/tracking/registrar-visita.php?pais=Mexico
```

Snippet para `comercializadoraegs.com` (footer):

```html
<script>
(function () {
  var u = 'https://backend.comercializadoraegs.com/extensiones/tracking/registrar-visita.php';
  try {
    fetch(u + '?pais=' + encodeURIComponent(navigator.language || ''), { mode: 'cors', credentials: 'omit' });
  } catch (e) {
    new Image().src = u + '&_=' + Date.now();
  }
})();
</script>
```

## 2. Google Analytics 4

1. Crear propiedad en [Google Analytics](https://analytics.google.com).
2. Instalar tag en el sitio público:

```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

3. En el backend, copiar `config/analytics.example.php` → `config/analytics.local.php` con `ga4_property_id` y ruta al JSON del service account.
4. El panel `?ruta=visitas` mostrará métricas GA4 cuando las credenciales sean válidas.

## 3. Microsoft Clarity (gratis)

1. Registrar proyecto en [clarity.microsoft.com](https://clarity.microsoft.com).
2. Pegar el script que Clarity proporciona en el `<head>` del sitio público.
3. Usar mapas de calor para validar recomendaciones de UX del panel.

## 4. Retirar SeeTheStats

Cuando GA4 y el tracking propio lleven al menos 7 días de datos, eliminar la pestaña legacy en el panel de visitas.
