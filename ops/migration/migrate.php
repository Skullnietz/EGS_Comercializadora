<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
umask(0077);require_once __DIR__.'/Migration.php';
$command=isset($argv[1])?$argv[1]:'help';$o=array();
foreach(array_slice($argv,2) as $arg){if(strpos($arg,'--')!==0){fwrite(STDERR,"Usa opciones --nombre=valor.\n");exit(1);}$parts=explode('=',substr($arg,2),2);$o[$parts[0]]=isset($parts[1])?$parts[1]:true;}
if($command==='help'){echo "EGS migración (PHP >=7.1, Apache 2.4, SSH/cron)\n";
    echo "php migrate.php preflight --config=/privado/destino.json [--database]\nphp migrate.php export --config=/privado/origen.json --output=/privado/egs.tar.gz.gpg --key=/privado/migracion.key\nphp migrate.php unpack --config=/privado/destino.json --file=/privado/egs.tar.gz.gpg --key=/privado/migracion.key --destination=/privado/paquete\nphp migrate.php verify --config=/privado/destino.json --package=/privado/paquete\nphp migrate.php restore-files --config=/privado/destino.json --package=/privado/paquete [--apply]\nphp migrate.php restore-db --config=/privado/destino.json --package=/privado/paquete [--apply]\nphp migrate.php configure --config=/privado/destino.json --package=/privado/paquete [--apply]\n";exit;}
try{
    $need=function($name)use($o){if(!isset($o[$name])||!is_string($o[$name])||$o[$name]==='')throw new RuntimeException('Falta --'.$name.'=valor');return $o[$name];};
    $allowed=array('config','database','output','key','file','destination','package','apply','final','quiesced');foreach($o as $name=>$value)if(!in_array($name,$allowed,true))throw new RuntimeException('Opción desconocida: --'.$name);
    foreach(array('apply','database','final','quiesced') as $flag)if(isset($o[$flag])&&$o[$flag]!==true)throw new RuntimeException('--'.$flag.' se usa sin valor.');
    $migration=new EgsMigration($need('config'));$apply=isset($o['apply']);
    if(!in_array($command,array('preflight','verify'),true))$lock=$migration->lock();
    switch($command){
        case 'preflight':$result=$migration->preflight(isset($o['database']));break;
        case 'export':$result=$migration->export($need('output'),$need('key'),isset($o['final']),isset($o['quiesced']));break;
        case 'unpack':$result=$migration->unpack($need('file'),$need('key'),$need('destination'));break;
        case 'verify':$result=$migration->verify($need('package'));break;
        case 'restore-files':$result=$migration->restoreFiles($need('package'),$apply);break;
        case 'restore-db':$result=$migration->restoreDatabases($need('package'),$apply);break;
        case 'configure':$result=$migration->configure($need('package'),$apply);break;
        default:throw new RuntimeException('Comando desconocido. Usa help.');
    }
    echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
    if($command==='preflight'&&!$result['ok'])exit(2);
}catch(Throwable $e){fwrite(STDERR,'ERROR: '.$e->getMessage()."\n");exit(1);}
