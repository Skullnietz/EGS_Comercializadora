# Asistente de migración EGS

Leer [el procedimiento completo](../../docs/MIGRACION_HOSTING.md).

Copiar `hosting.example.json` fuera del directorio web, rellenar conexiones/rutas y mantener `mode: staging`. La configuración real y las llaves son privadas.

```bash
php migrate.php help
php migrate.php preflight --config=/privado/destino.json --database
```

`restore-files`, `restore-db` y `configure` muestran la propuesta hasta añadir `--apply`. `export` crea un paquete cifrado; `unpack` lo descifra en un directorio privado nuevo. Ningún comando cambia DNS o instala cron. El corte final requiere suspender escrituras y la confirmación explícita `--final --quiesced`.
