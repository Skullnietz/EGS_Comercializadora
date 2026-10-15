<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$options = getopt('', array('apply', 'help'));
if (!isset($options['apply'])) {
    echo "Uso: php ops/logs/aplicar-mantenimiento.php --apply\nConfigura la rotación, protege el callback de Astra y vacía logs grandes conservando una muestra.\n";
    exit;
}
umask(0077);
$home = '/home/egsequip';
$dir = $home.'/.egs-log-maintenance';
$php = '/opt/cpanel/ea-php71/root/usr/bin/php';
function ensurePrivateDirectory($path) {
    if (is_link($path)) { throw new RuntimeException('Directorio enlazado: '.$path); }
    if (!is_dir($path) && !mkdir($path, 0700)) { throw new RuntimeException('No se pudo crear '.$path); }
    if (realpath($path) !== $path || !chmod($path, 0700)) { throw new RuntimeException('Directorio inesperado: '.$path); }
}
function runChecked($command) {
    $lines = array(); exec($command.' 2>&1', $lines, $status);
    if ($status !== 0) { throw new RuntimeException('Falló comando: '.implode("\n", $lines)); }
    return implode("\n", $lines);
}
function writePrivate($path, $content) {
    if (is_link($path)) { throw new RuntimeException('Archivo enlazado: '.$path); }
    if (file_put_contents($path, $content) !== strlen($content) || !chmod($path, 0600)) {
        throw new RuntimeException('No se pudo guardar '.$path);
    }
}
ensurePrivateDirectory($dir);
$lock = fopen($dir.'/install.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) { throw new RuntimeException('Mantenimiento ya en ejecución.'); }
$config = file_get_contents(__DIR__.'/logrotate.conf');
if ($config === false || strpos($config, $dir.'/wordpress-php') === false) { throw new RuntimeException('Falta configuración.'); }
foreach (array('wordpress-php', 'backend-php', 'wordpress-root', 'wordpress-admin') as $name) { ensurePrivateDirectory($dir.'/'.$name); }
writePrivate($dir.'/logrotate.conf', $config);

// Comprobar copytruncate y retención en un archivo aislado antes de tocar logs reales.
$test = $dir.'/fixture-'.bin2hex(random_bytes(4));
ensurePrivateDirectory($test);
ensurePrivateDirectory($test.'/old');
$testLog = $test.'/fixture.log';
$payload = str_repeat("EGS log rotation fixture\n", 100);
writePrivate($testLog, $payload);
$before = stat($testLog);
writePrivate($test.'/config', '"'.$testLog.'" {'."\n size 1k\n rotate 1\n compress\n copytruncate\n missingok\n notifempty\n olddir ".$test."/old\n}\n");
runChecked('/usr/sbin/logrotate -s '.escapeshellarg($test.'/state').' '.escapeshellarg($test.'/config'));
clearstatcache(true, $testLog);
$after = stat($testLog);
if ($after['size'] !== 0 || $before['ino'] !== $after['ino'] || $before['mode'] !== $after['mode'] || $before['uid'] !== $after['uid'] || gzdecode(file_get_contents($test.'/old/fixture.log.1.gz')) !== $payload) {
    throw new RuntimeException('Falló la verificación de rotación.');
}
writePrivate($testLog, $payload.'second run');
runChecked('/usr/sbin/logrotate -f -s '.escapeshellarg($test.'/state').' '.escapeshellarg($test.'/config'));
if (gzdecode(file_get_contents($test.'/old/fixture.log.1.gz')) !== $payload.'second run' || count(glob($test.'/old/fixture.log.*.gz')) !== 1) { throw new RuntimeException('Falló la retención.'); }
unlink($test.'/old/fixture.log.1.gz'); rmdir($test.'/old');
foreach (glob($test.'/*') as $path) { if (!is_file($path) || is_link($path) || !unlink($path)) { throw new RuntimeException('No se pudo limpiar fixture.'); } }
rmdir($test);

// El tema registraba un callback que no existe. Conservar registro solo si es invocable.
$theme = $home.'/public_html/wp-content/themes/astra/functions.php';
if (is_link($theme) || realpath($theme) !== $theme) { throw new RuntimeException('Ruta del tema inesperada.'); }
$source = file_get_contents($theme);
$needle = 'add_action("wp_enqueue_scripts", "dcms_insertar_js");';
$replacement = "// EGS: registrar el callback opcional solo si está disponible.\nif ( is_callable( 'dcms_insertar_js' ) ) {\n    ".$needle."\n}";
$themeChanged = false;
if (strpos($source, $replacement) === false) {
    if (substr_count($source, $needle) !== 1) { throw new RuntimeException('No coincide el registro esperado del callback.'); }
    $updated = str_replace($needle, $replacement, $source);
    writePrivate($dir.'/astra-functions-before.php.txt', $source);
    writePrivate($dir.'/astra-functions-check.php', $updated);
    runChecked(escapeshellarg($php).' -l '.escapeshellarg($dir.'/astra-functions-check.php'));
    $meta = stat($theme);
    $temp = dirname($theme).'/.egs-functions-'.bin2hex(random_bytes(4)).'.tmp';
    writePrivate($temp, $updated);
    chmod($temp, $meta['mode'] & 0777);
    if (hash_file('sha256', $theme) !== hash('sha256', $source)) { unlink($temp); throw new RuntimeException('Cambió el tema durante la revisión.'); }
    if (!rename($temp, $theme)) { unlink($temp); throw new RuntimeException('No se pudo actualizar el tema.'); }
    unlink($dir.'/astra-functions-check.php');
    $themeChanged = true;
}

// Guardar una muestra y vaciar los tres logs de WordPress, conservando sus inodos.
$cleaned = array();
foreach (array('logs/comercializadoraegs_com.php.error.log', 'public_html/error_log', 'public_html/wp-admin/error_log') as $relative) {
    $path = $home.'/'.$relative;
    if (is_link($path) || realpath($path) !== $path || !is_file($path)) { throw new RuntimeException('Ruta de log inesperada.'); }
    $before = stat($path);
    if ($before['size'] < 1048576) { continue; }
    $fh = fopen($path, 'r+');
    if (!$fh || !flock($fh, LOCK_EX)) { throw new RuntimeException('No se pudo abrir/bloquear log.'); }
    try {
        $opened = fstat($fh);
        if ($before['ino'] !== $opened['ino'] || $before['dev'] !== $opened['dev']) { throw new RuntimeException('Cambió el log.'); }
        $length = min(1048576, $opened['size']);
        if (fseek($fh, $opened['size'] - $length) !== 0) { throw new RuntimeException('No se pudo leer muestra.'); }
        $tail = stream_get_contents($fh, $length);
        if ($tail === false || strlen($tail) !== $length) { throw new RuntimeException('Muestra incompleta.'); }
        $sample = $dir.'/'.str_replace('/', '-', $relative).'-tail.log.gz';
        $compressed = gzencode($tail, 6);
        writePrivate($sample, $compressed);
        if (gzdecode(file_get_contents($sample)) !== $tail) { throw new RuntimeException('Muestra no verificada.'); }
        if (!ftruncate($fh, 0) || !fflush($fh)) { throw new RuntimeException('No se pudo vaciar log.'); }
        $after = fstat($fh);
        if ($before['ino'] !== $after['ino'] || $before['mode'] !== $after['mode'] || $before['uid'] !== $after['uid'] || $before['gid'] !== $after['gid']) { throw new RuntimeException('Cambió la identidad del log.'); }
        $cleaned[] = array('path'=>$relative, 'before_bytes'=>$before['size'], 'after_bytes'=>$after['size'], 'allocated_before'=>$before['blocks']*512, 'allocated_after'=>$after['blocks']*512, 'inode_preserved'=>true, 'permissions_and_owner_preserved'=>true, 'sample_compressed_bytes'=>strlen($compressed));
    } finally { flock($fh, LOCK_UN); fclose($fh); }
}

// La versión 3.8.6 da ENOENT en simulaciones de primeras rotaciones. Las rotaciones
// reales se probaron arriba; comprobar ahora las rutas reales, ya vaciadas.
writePrivate($dir.'/dry-run.txt', runChecked('/usr/sbin/logrotate -d -s '.escapeshellarg($dir.'/state').' '.escapeshellarg($dir.'/logrotate.conf')));

// Instalar una sección propia y preservar íntegramente los demás trabajos.
$cron = runChecked('/usr/bin/crontab -l')."\n";
if (!is_file($dir.'/crontab-before.txt')) { writePrivate($dir.'/crontab-before.txt', $cron); }
$pattern = '/^# BEGIN EGS LOG MAINTENANCE\n.*?^# END EGS LOG MAINTENANCE\n?/ms';
$base = preg_replace($pattern, '', $cron);
$block = "# BEGIN EGS LOG MAINTENANCE\n# Rotar logs PHP mayores de 10 MiB; una copia comprimida por log.\n*/15 * * * * /usr/bin/flock -n ".$dir.'/rotate.lock /usr/sbin/logrotate -s '.$dir.'/state '.$dir.'/logrotate.conf >> '.$dir."/rotation.log 2>&1\n# END EGS LOG MAINTENANCE\n";
$installed = rtrim($base)."\n".$block;
writePrivate($dir.'/crontab-installed.txt', $installed);
runChecked('/usr/bin/crontab '.escapeshellarg($dir.'/crontab-installed.txt'));
$actual = runChecked('/usr/bin/crontab -l')."\n";
if ($actual !== $installed || rtrim(preg_replace($pattern, '', $actual)) !== rtrim($base)) { throw new RuntimeException('No coincide cron.'); }
runChecked('/usr/bin/flock -n '.escapeshellarg($dir.'/rotate.lock').' /usr/sbin/logrotate -s '.escapeshellarg($dir.'/state').' '.escapeshellarg($dir.'/logrotate.conf'));
$result = array('at_utc'=>gmdate('c'), 'cleaned'=>$cleaned, 'freed_allocated_bytes'=>array_sum(array_column($cleaned, 'allocated_before'))-array_sum(array_column($cleaned, 'allocated_after')), 'theme_callback_guard_installed'=>true, 'theme_changed_now'=>$themeChanged, 'theme_sha256'=>hash_file('sha256', $theme), 'rotation'=>array('interval_minutes'=>15, 'threshold_bytes'=>10485760, 'retained_compressed_files_per_log'=>1, 'copytruncate_test_passed'=>true, 'retention_test_passed'=>true, 'dry_run_passed'=>true, 'cron_preserves_other_jobs'=>true));
writePrivate($dir.'/result.json', json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n");
echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
flock($lock, LOCK_UN); fclose($lock);
