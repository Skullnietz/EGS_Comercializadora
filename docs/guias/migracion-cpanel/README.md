# Guía visual de migración con cPanel

[Leer la guía paso a paso con capturas](GUIA_PASO_A_PASO.md).

La referencia es cPanel Jupiter. El panel y proveedor del destino aún no se confirmaron. Las capturas son de la demostración pública de cPanel, documentación oficial de Cloudflare y visores locales de archivos reales del kit. No son pantallas de una cuenta nueva ni prueban que se hayan realizado acciones en el destino.

La guía distingue preparación, ensayo y corte coordinado. Contiene pasos para las raíces separadas, la base compartida entre WordPress y órdenes, directorios nuevos, importación, reconfiguración de PHP, SSL previo a DNS, pruebas por hosts/resolve, correo y activación de cron.

`contenido.json` es la fuente editorial. `capturas/` contiene los PNG originales y `fuentes_capturas.json` registra su origen y SHA-256. No contienen credenciales ni la llave del respaldo. `APENDICE_WINDOWS.md` conserva los bloques de transferencia y pruebas desde PowerShell.

Para regenerar el HTML autónomo, Markdown y archivos de apoyo:

```bash
python3 render.py --output /ruta/a/la/entrega
```

Abre el HTML y usa Imprimir / PDF para guardar en A4 con gráficos de fondo. La versión HTML incluye índice, progreso manual, copia de comandos y ampliación de capturas. Los secretos reales se rellenan en el JSON privado del servidor, no en este documento.

Consulta también [el procedimiento técnico de migración](../../MIGRACION_HOSTING.md).
