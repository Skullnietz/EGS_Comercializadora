<?php
/** Captura credenciales del hosting anterior en JSON PRIVADO. No las imprime. */
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
umask(0077);
$o=getopt('',array('backend:','wordpress:','drive:','backup:','work:','output:','mail:','mail-config:','extra-schema:','legacy-schema:','runtime-root:'));
try{
    foreach(array('backend','wordpress','drive','backup','work','output') as $key)if(empty($o[$key]))throw new RuntimeException('Falta --'.$key);
    require_once $o['backend'].'/config/Database.php';$method=new ReflectionMethod('Database','config');$method->setAccessible(true);$databases=array();
    foreach(array('ecommerce','wordpress') as $key){$db=$method->invoke(null,$key);$databases[]=array('key'=>$key,'host'=>$db['host'],'port'=>isset($db['port'])?(int)$db['port']:3306,'name'=>$db['dbname'],'user'=>$db['user'],'password'=>$db['pass']);}
    $text=file_get_contents($o['wordpress'].'/wp-config.php');$wp=array();
    foreach(array('DB_HOST','DB_NAME','DB_USER','DB_PASSWORD') as $key){
        $pattern='~define\s*\(\s*([\'"])'.preg_quote($key,'~').'\1\s*,\s*(\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*")\s*\)~';
        if(!preg_match($pattern,$text,$m))throw new RuntimeException('Revisar wp-config.php: '.$key);
        $literal=$m[2];$value=substr($literal,1,-1);$wp[$key]=$literal[0]==="'"?str_replace(array("\\'","\\\\"),array("'","\\"),$value):stripcslashes($value);
    }
    $host=$wp['DB_HOST'];$port=3306;if(preg_match('/^([^:]+):(\d+)$/',$host,$m)){$host=$m[1];$port=(int)$m[2];}
    $databases[]=array('key'=>'site','host'=>$host,'port'=>$port,'name'=>$wp['DB_NAME'],'user'=>$wp['DB_USER'],'password'=>$wp['DB_PASSWORD']);
    if(!empty($o['extra-schema'])){$d=$databases[1];$d['key']='archive';$d['name']=$o['extra-schema'];$databases[]=$d;}
    if(!empty($o['legacy-schema'])){$d=$databases[1];$d['key']='legacy';$d['name']=$o['legacy-schema'];$databases[]=$d;}
    $c=array('mode'=>'staging','paths'=>array_intersect_key($o,array_flip(array('backend','wordpress','drive','backup','work','mail'))),'urls'=>array('backend'=>'https://backend.comercializadoraegs.com','wordpress'=>'https://comercializadoraegs.com'),'binaries'=>array('php'=>PHP_BINARY),'databases'=>$databases,'environment'=>array());
    if(!empty($o['mail-config']))$c['paths']['mail_config']=$o['mail-config'];if(!empty($o['runtime-root']))$c['runtime_root']=$o['runtime-root'];
    require_once __DIR__.'/Migration.php';$validation=tempnam(sys_get_temp_dir(),'egs-config-');chmod($validation,0600);file_put_contents($validation,json_encode($c));try{new EgsMigration($validation);}finally{unlink($validation);}
    if(!is_dir($o['work']))mkdir($o['work'],0700,true);chmod($o['work'],0700);
    if(realpath(dirname($o['output']))!==realpath($o['work']))throw new RuntimeException('El JSON debe guardarse directamente en --work, fuera del sitio.');
    if(file_exists($o['output']))throw new RuntimeException('El archivo privado ya existe.');
    $temporary=tempnam($o['work'],'capture-');file_put_contents($temporary,json_encode($c,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n");chmod($temporary,0600);
    rename($temporary,$o['output']);echo "Configuración privada capturada. No publicar ni agregar a Git.\n";
}catch(Throwable $e){fwrite(STDERR,'No se pudo capturar la configuración. Revisa rutas, permisos y formato de wp-config.php; no se imprimen credenciales.'."\n");exit(1);}
