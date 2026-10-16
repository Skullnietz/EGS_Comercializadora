## Desde Windows: transferencia si Upload no admite el paquete

Ejecuta en PowerShell. Sustituye la ruta de tu entrega, USUARIO, HOST_NUEVO y PUERTO_SSH con los datos del proveedor. La carpeta privada del destino debe existir. Estos comandos no se ejecutan en la Terminal Linux de cPanel.

```powershell
$EgsBase = 'C:\RUTA\A\TU\ENTREGA'
scp -P PUERTO_SSH "$EgsBase\kit-migracion-egs.tar.gz" "USUARIO@HOST_NUEVO:/home/USUARIO/.egs-migration/"
scp -P PUERTO_SSH "$EgsBase\paquete\egs-preliminar-20261016.tar.gz.gpg" "USUARIO@HOST_NUEVO:/home/USUARIO/.egs-migration/"
scp -P PUERTO_SSH "$EgsBase\paquete\egs-preliminar-20261016.tar.gz.gpg.json" "USUARIO@HOST_NUEVO:/home/USUARIO/.egs-migration/"
```

Transfiere la llave por separado desde su ubicación privada. No la incluyas en el kit ni la publiques. La autenticación SSH del proveedor es distinta de la llave que descifra el respaldo. Si el proveedor nuevo no tiene SFTP, consulta si requiere el modo antiguo `scp -O`; esa limitación estaba presente en el origen y no se debe asumir para el destino.

## Desde Windows: verificar la descarga del paquete

Usa la misma ruta de entrega del bloque anterior. El resultado debe ser True. Si sale False, no uses esa copia hasta comprobar la descarga y el recibo.

```powershell
$EgsArchivo = Join-Path $EgsBase 'paquete\egs-preliminar-20261016.tar.gz.gpg'
$EgsEsperado = (Get-Content "$EgsArchivo.json" -Raw | ConvertFrom-Json).sha256
$EgsObtenido = (Get-FileHash -LiteralPath $EgsArchivo -Algorithm SHA256).Hash
$EgsObtenido -ieq $EgsEsperado
```

## Paso 17: comprobar una imagen en el origen nuevo

Copia la URL original de una imagen desde una orden que puedas consultar en el sistema actual: clic derecho sobre la imagen → Copiar dirección de imagen. Usa una URL de `https://backend.comercializadoraegs.com/...`, no el enlace de un archivo de Drive. Sustituye la IP y la URL de muestra antes de ejecutar.

```powershell
$EgsIp = 'IP_NUEVA_REAL'
$EgsFotoUrl = 'PEGA_AQUI_LA_URL_ORIGINAL_DE_LA_IMAGEN'
$EgsFotoLocal = Join-Path $env:TEMP 'egs-foto-prueba.jpg'
curl.exe --resolve "backend.comercializadoraegs.com:443:$EgsIp" --fail --show-error --output "$EgsFotoLocal" "$EgsFotoUrl"
if ($LASTEXITCODE -eq 0) {
    Get-FileHash -LiteralPath $EgsFotoLocal -Algorithm MD5
}
```

La imagen histórica concreta de la orden 12323 que ya se verificó tiene MD5 `5feb2838718142ec846247d70740fb97`. Si seleccionas otra foto, compara con esa misma imagen original; su huella será distinta. Confirma también que el archivo no existe en el directorio de multimedia del nuevo hosting para distinguir la lectura de Drive de un original local.

## Nota de sesión

Las variables `$EgsBase`, `$EgsArchivo` y `$EgsIp` son de PowerShell. Las variables `EGS_PHP`, `EGS_TOOL`, `EGS_CONFIG`, `EGS_ARCHIVE`, `EGS_KEY` y `EGS_PACKAGE` de la guía son de la Terminal Linux y deben definirse de nuevo si abres otra sesión.
