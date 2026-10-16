<?php
/** Prueba real de exportar/cifrar/restaurar a esquemas y rutas diferentes.
 * EGS_TEST_MYSQL_DSN=mysql:host=127.0.0.1;port=43316 EGS_TEST_MYSQL_PASSWORD=... php tests/hosting_migration.php
 * Crea/elimina únicamente esquemas egs_fixture_<token>_* en una base de pruebas.
 */
require_once dirname(__DIR__).'/ops/migration/Migration.php';
function assertMigration($ok,$message){if(!$ok)throw new RuntimeException($message);}
function rejectsMigration($callback,$message){try{$callback();}catch(Throwable $e){return;}throw new RuntimeException($message);}
$base=sys_get_temp_dir().'/egs-hosting-test-'.bin2hex(random_bytes(5));mkdir($base,0700,true);$schemas=array();$pdo=null;
try{
    $config=json_decode(file_get_contents(dirname(__DIR__).'/ops/migration/hosting.example.json'),true);
    foreach(array('backend','wordpress','drive','backup','work','mail','mail_config') as $key){$config['paths'][$key]=$base.'/source/'.$key;mkdir($config['paths'][$key],0700,true);}
    mkdir($config['paths']['backend'].'/controladores',0700,true);file_put_contents($config['paths']['backend'].'/index.php','<?php echo "fixture";');
    copy(dirname(__DIR__).'/controladores/ordenes.controlador.php',$config['paths']['backend'].'/controladores/ordenes.controlador.php');
    // Simular el controlador antiguo: el configurador aplica sólo el bloque de correo.
    $controller=file_get_contents($config['paths']['backend'].'/controladores/ordenes.controlador.php');
    $controller=str_replace("require_once dirname(__DIR__).'/config/Mail.php';\n            EgsMail::configure(\$mail);", "\$mail->SMTPSecure = 'tls';\n\$mail->isMail();\n\$mail->setFrom('noreplay.ordenes@backend.comercializadoraegs.com');\n\$mail->addReplyTo('noreplay.ordenes@backend.comercializadoraegs.com');",$controller);file_put_contents($config['paths']['backend'].'/controladores/ordenes.controlador.php',$controller);
    file_put_contents($config['paths']['wordpress'].'/wp-config.php',"<?php\ndefine('DB_NAME','old');\ndefine('DB_USER','old');\ndefine('DB_PASSWORD','old');\ndefine('DB_HOST','localhost');\n");
    file_put_contents($config['paths']['wordpress'].'/index.php','<?php echo "wordpress-fixture";');
    file_put_contents($config['paths']['wordpress'].'/.htaccess',"\n# BEGIN EGS Drive Media\nRewriteEngine On\nRewriteCond /home/egsequip/backend.comercializadoraegs.com/.drive-media-enabled -f\nRewriteRule ^wp-content/uploads/.+ egs-drive-media.php [END,NC]\n# END EGS Drive Media\n# BEGIN cPanel-generated php ini directives, do not edit\n<IfModule php7_module>\nphp_value session.save_path \"/var/cpanel/php/sessions/ea-php73\"\n</IfModule>\n# END cPanel-generated php ini directives, do not edit\n");
    file_put_contents($config['paths']['backend'].'/.htaccess',file_get_contents(dirname(__DIR__).'/.htaccess'));
    mkdir($config['paths']['backend'].'/.git');file_put_contents($config['paths']['backend'].'/.git/omit','omit');
    file_put_contents($config['paths']['backend'].'/error_log','omit');file_put_contents($config['paths']['backend'].'/file with spaces.txt','original');
    symlink('/etc/passwd',$config['paths']['backend'].'/unsafe-link');
    mkdir($config['paths']['backend'].'/unzipper-master');file_put_contents($config['paths']['backend'].'/unzipper-master/'."nombre\r\nespecial",'obsolete');
    $sqlite=new PDO('sqlite:'.$config['paths']['drive'].'/media.sqlite');$sqlite->exec("CREATE TABLE media(path TEXT); INSERT INTO media VALUES('original-url');");$sqlite=null;
    foreach(array('client.json','tokens.json','library.json','enabled') as $name)file_put_contents($config['paths']['drive'].'/'.$name,'fixture');
    file_put_contents($config['paths']['backup'].'/recovery.key','fixture-old-key');file_put_contents($config['paths']['mail'].'/message','private-email');
    $config['runtime_root']=dirname(__DIR__);$config['environment']['MAIL_PASSWORD']='fixture-secret';$config['databases']=array();
    $dsn=getenv('EGS_TEST_MYSQL_DSN');if(!$dsn)throw new RuntimeException('Define EGS_TEST_MYSQL_DSN para una base de pruebas aislada.');
    $user=getenv('EGS_TEST_MYSQL_USER') ?: 'root';$password=getenv('EGS_TEST_MYSQL_PASSWORD') ?: ''; $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    preg_match('/host=([^;]+)/',$dsn,$host);preg_match('/port=(\d+)/',$dsn,$port);$prefix='egs_fixture_'.bin2hex(random_bytes(4));
    foreach(array('ecommerce','wordpress','legacy','archive') as $key){$name=$prefix.'_'.$key;$schemas[]=$name;$pdo->exec('CREATE DATABASE `'.$name.'`');
        if($key!=='archive'){$pdo->exec('CREATE TABLE `'.$name.'`.items (id INT PRIMARY KEY, value TEXT) ENGINE=MyISAM');$pdo->exec("INSERT INTO `".$name."`.items VALUES(1,'imagen áéí y orden 123')");}
        $config['databases'][]=array('key'=>$key,'host'=>$host[1],'port'=>(int)$port[1],'name'=>$name,'user'=>$user,'password'=>$password);}
    $alias=$config['databases'][1];$alias['key']='site';$config['databases'][]=$alias;
    file_put_contents($base.'/source.json',json_encode($config));$migration=new EgsMigration($base.'/source.json');$lock=$migration->lock();$other=new EgsMigration($base.'/source.json');rejectsMigration(function()use($other){$other->lock();},'Se permitió una migración concurrente');flock($lock,LOCK_UN);fclose($lock);
    file_put_contents($base.'/migration.key',bin2hex(random_bytes(32)));$package=$config['paths']['work'].'/package.gpg';
    rejectsMigration(function()use($migration,$package,$base){$migration->export($package,$base.'/migration.key',true);},'Se permitió declarar final sin congelamiento');
    $receipt=$migration->export($package,$base.'/migration.key');assertMigration(is_file($package)&&$receipt['bytes']>0,'Falta paquete cifrado');
    $manifest=$migration->unpack($package,$base.'/migration.key',$base.'/unpacked');assertMigration(count($manifest['databases'])===4&&$manifest['drive_alias_count']===1,'Manifiesto incompleto');
    assertMigration($manifest['archives']['backend']['skipped_symlinks']===array('unsafe-link'),'No se documentó el enlace excluido');
    foreach(array('backend','wordpress','drive','backup','work','mail','mail_config') as $key)$config['paths'][$key]=$base.'/destination/'.$key;
    $created=array();foreach($config['databases'] as &$d){$d['name'].='_new';if(!isset($created[$d['name']])){$schemas[]=$d['name'];$pdo->exec('CREATE DATABASE `'.$d['name'].'`');$created[$d['name']]=true;}}unset($d);
    file_put_contents($base.'/destination.json',json_encode($config));$destination=new EgsMigration($base.'/destination.json');
    $destination->restoreFiles($base.'/unpacked',false);assertMigration(!is_dir($config['paths']['backend']),'La vista previa restauró archivos');
    $destination->restoreFiles($base.'/unpacked',true);assertMigration(!file_exists($config['paths']['backend'].'/.git'),'Se copió Git');assertMigration(!file_exists($config['paths']['backend'].'/unzipper-master'),'Se copió la antigua herramienta de despliegue');assertMigration(!file_exists($config['paths']['backend'].'/unsafe-link'),'Se copió un enlace externo');
    assertMigration(file_get_contents($config['paths']['backend'].'/file with spaces.txt')==='original','Los archivos no coinciden');
    rejectsMigration(function()use($destination,$base){$destination->restoreFiles($base.'/unpacked',true);},'Se sobrescribió un destino existente');
    $broken=$config;foreach($broken['databases'] as &$d)if($d['key']==='site')$d['name'].='_split';unset($d);file_put_contents($base.'/split.json',json_encode($broken));$split=new EgsMigration($base.'/split.json');rejectsMigration(function()use($split,$base){$split->restoreDatabases($base.'/unpacked',false);},'Se separó WordPress de la base compartida');
    $destination->restoreDatabases($base.'/unpacked',false);$destination->restoreDatabases($base.'/unpacked',true);
    foreach($config['databases'] as $d)if($d['key']!=='archive')assertMigration($pdo->query('SELECT value FROM `'.$d['name'].'`.items')->fetchColumn()==='imagen áéí y orden 123','Se alteraron datos al renombrar la base');
    rejectsMigration(function()use($destination,$base){$destination->restoreDatabases($base.'/unpacked',true);},'Se importó sobre una base con tablas');
    $preview=$destination->configure($base.'/unpacked',false);assertMigration(!is_file($config['paths']['work'].'/hosting.env'),'La vista previa escribió configuración');
    $destination->configure($base.'/unpacked',true);$again=$destination->configure($base.'/unpacked',false);assertMigration(count($again['changed_files'])===0,'La configuración no es idempotente');
    $backend=$config['paths']['backend'];assertMigration(strpos(file_get_contents($backend.'/.htaccess'),'/home/egsequip')===false,'Quedó ruta fija del origen');
    assertMigration(strpos(file_get_contents($backend.'/.htaccess'),'ea-php71')===false,'Quedó handler PHP anterior');
    $wpHt=file_get_contents($config['paths']['wordpress'].'/.htaccess');assertMigration(strpos($wpHt,'/home/egsequip')===false&&strpos($wpHt,'/var/cpanel')===false,'Quedaron rutas del hosting anterior en WordPress');
    assertMigration(strpos(file_get_contents($backend.'/controladores/ordenes.controlador.php'),'EgsMail::configure($mail)')!==false,'No se adaptó el SMTP legado');
    assertMigration((fileperms($config['paths']['work'].'/hosting.env')&0777)===0600,'Entorno no privado');assertMigration((fileperms($config['paths']['wordpress'].'/wp-config.php')&0777)===0600,'wp-config no privado');
    assertMigration(strpos(file_get_contents($config['paths']['wordpress'].'/wp-config.php'),"egs-hosting.php")!==false,'No se instaló el puente WP');
    require $backend.'/config/DriveBackup.php';$backup=new EgsDriveBackup();$dumpMethod=new ReflectionMethod('EgsDriveBackup','dumpDatabases');$dumpMethod->setAccessible(true);$dumpWork=$config['paths']['backup'].'/fixture-dumps';mkdir($dumpWork,0700);$dumps=$dumpMethod->invoke($backup,$dumpWork);assertMigration(count($dumps)===4,'Los respaldos no conservaron las cuatro bases después de renombrarlas');
    $logrotate=$destination->binary('logrotate');$destination->command(array($logrotate,'-d',$config['paths']['work'].'/logrotate.conf'));
    file_put_contents($base.'/unpacked/db-archive.sql.gz','corrupt');rejectsMigration(function()use($destination,$base){$destination->verify($base.'/unpacked');},'Se aceptó un paquete modificado');
    // Un TAR con enlaces es rechazado antes de extraer.
    mkdir($base.'/malicious');symlink('/etc/passwd',$base.'/malicious/link');$destination->command(array($destination->binary('tar'),'--format=gnu','-czf',$base.'/malicious.tar.gz','-C',$base.'/malicious','.'));
    rejectsMigration(function()use($destination,$base){$destination->validateTar($base.'/malicious.tar.gz');},'Se aceptó un enlace TAR');
    echo "PASS: exportación real MyISAM/UTF-8, 4 bases y alias compartido, cifrado/descifrado, checksums, rutas/esquemas distintos, exclusiones, restauración protegida, SMTP legado, configuración idempotente y logrotate.\n";
}finally{
    if($pdo)foreach($schemas as $schema)if(preg_match('/^egs_fixture_[a-f0-9]+_/', $schema))$pdo->exec('DROP DATABASE IF EXISTS `'.$schema.'`');
    if(getenv('EGS_TEST_KEEP_FILES')==='1'){echo 'Fixture conservada: '.$base.PHP_EOL;}elseif(is_dir($base)){$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);foreach($it as $f){if($f->isDir()&&!$f->isLink())rmdir($f->getPathname());else unlink($f->getPathname());}rmdir($base);}
}
