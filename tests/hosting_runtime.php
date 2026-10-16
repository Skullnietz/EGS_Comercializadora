<?php
$state=sys_get_temp_dir().'/egs-runtime-test-'.bin2hex(random_bytes(5));mkdir($state,0700,true);
function runtimeCheck($ok,$message){if(!$ok)throw new RuntimeException($message);}
function runtimeReject($callback,$message){try{$callback();}catch(Throwable $e){return;}throw new RuntimeException($message);}
try{
    $env=$state.'/hosting.env';file_put_contents($env,"BAD-NAME=value\nEGS_HOSTING_MODE=staging\nEGS_WORDPRESS_ROOT=\"/tmp/wp with spaces\"\nEGS_BACKEND_URL=https://backend.example.test\nDB_ECOMMERCE_NAME=fixture_ecommerce\nDB_ECOMMERCE_USER=fixture_user\nDB_ECOMMERCE_PASS=\" #=a'\\b\" \"\nDB_ECOMMERCE_PORT=43316\nAPP_NAME=from-file\n");
    putenv('EGS_ENV_FILE='.$env);putenv('APP_NAME=from-environment');putenv('EGS_DRIVE_STATE_DIR='.$state.'/drive');
    require dirname(__DIR__).'/config/DriveStorage.php';require dirname(__DIR__).'/config/DriveBackup.php';require dirname(__DIR__).'/config/Mail.php';
    runtimeCheck(getenv('BAD-NAME')===false,'Se cargó una variable de nombre inválido');runtimeCheck(getenv('APP_NAME')==='from-environment','Se sobreescribió el entorno del servidor');
    runtimeCheck(EgsHosting::wordpressRoot()==='/tmp/wp with spaces','No se cargó la ruta externa');runtimeCheck(EgsDriveStorage::redirectUri()==='https://backend.example.test/drive-conectar.php','Retorno OAuth no configurable');
    $method=new ReflectionMethod('Database','config');$method->setAccessible(true);$d=$method->invoke(null,'ecommerce');runtimeCheck($d['port']===43316&&$d['dbname']==='fixture_ecommerce','Conexión no configurable');
    runtimeReject(function()use($method){$method->invoke(null,'wordpress');},'Se usaron credenciales antiguas sin configurar');
    $called=false;EgsDriveStorage::setTransportForTests(function()use(&$called){$called=true;throw new RuntimeException('No debería llegar al transporte');});
    runtimeReject(function(){EgsDriveStorage::put('backend/vistas/img/test.jpg','image','image/jpeg');},'Staging permitió subir');
    runtimeReject(function(){EgsDriveStorage::api('DELETE','files/fixture');},'Staging permitió borrar en Drive');runtimeCheck(!$called,'Staging llegó a Google antes de rechazar la escritura');
    $backup=new EgsDriveBackup($state.'/backup');runtimeReject(function()use($backup){$backup->initialize();},'Staging permitió inicializar respaldos');
    $mail=new class { public $smtp=false; public $from; public $reply; public $Host; public $Port; public $SMTPAuth; public $Username; public $Password; public $SMTPSecure;
        function isSMTP(){$this->smtp=true;}function isMail(){$this->smtp=false;}function setFrom($from,$name=''){$this->from=$from;}function addReplyTo($reply){$this->reply=$reply;} };
    runtimeReject(function()use($mail){EgsMail::configure($mail);},'Staging permitió notificaciones de órdenes');
    putenv('EGS_HOSTING_MODE=production');putenv('MAIL_HOST=smtp.example.test');putenv('MAIL_PORT=465');putenv('MAIL_ENCRYPTION=ssl');putenv('MAIL_USERNAME=sender@example.test');putenv('MAIL_PASSWORD=fixture');putenv('MAIL_FROM_ADDRESS=sender@example.test');EgsMail::configure($mail);
    runtimeCheck($mail->smtp&&$mail->Port===465&&$mail->SMTPAuth&&$mail->from==='sender@example.test','No se configuró SMTP');
    file_put_contents($state.'/backup/recovery.key',bin2hex(random_bytes(32)));chmod($state.'/backup/recovery.key',0600);file_put_contents($state.'/plain','backup fixture');
    $encrypt=new ReflectionMethod('EgsDriveBackup','encrypt');$encrypt->setAccessible(true);$encrypted=$encrypt->invoke($backup,$state.'/plain');runtimeCheck(is_file($encrypted)&&!is_file($state.'/plain.decrypt-check'),'No se verificó el cifrado de respaldos');
    putenv('EGS_BACKUP_HOUR=5');$at=(new DateTimeImmutable('2026-10-18 04:30:00',new DateTimeZone('America/Mexico_City')))->getTimestamp();$due=EgsDriveBackup::due($at,null,null);runtimeCheck(!$due['daily'],'El horario de respaldo no es configurable');
    echo "PASS: entorno externo/prioridad, credenciales obligatorias, puerto, OAuth/rutas, staging bloquea Drive/backup/correo, SMTP y cifrado GnuPG real.\n";
}finally{
    if(is_dir($state)){$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($state,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);foreach($it as $f){if($f->isDir()&&!$f->isLink())rmdir($f->getPathname());else unlink($f->getPathname());}rmdir($state);}
}
