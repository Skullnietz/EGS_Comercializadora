<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
umask(0077);require_once dirname(__DIR__).'/config/DriveMigration.php';
$options=getopt('',array('init','status','run','release-local','site:','slot:','limit:','seconds:','publish'));
if(isset($options['init'])){$result=EgsDriveMigration::initialize(EgsDriveStorage::stateDir().'/manifests');echo json_encode($result)."\n";exit;}
if(isset($options['status'])){$result=EgsDriveMigration::progress();if(isset($options['publish']))EgsDriveMigration::publishProgress($result,true);echo json_encode($result)."\n";exit;}
if(!isset($options['run']))throw new RuntimeException('Indica --init, --run o --status.');
if(is_file(EgsDriveStorage::stateDir().'/migration-pause'))exit;
$ready=EgsDriveStorage::readJson('integration_ready');if(!EgsDriveStorage::enabled()||!is_file(EgsDriveStorage::root().'/.drive-media-enabled')||empty($ready['backend_ready'])||empty($ready['wordpress_ready']))throw new RuntimeException('Los sitios deben estar activos y verificados antes de trasladar.');
$slot=isset($options['slot'])?max(1,min(3,(int)$options['slot'])):1;$lock=fopen(EgsDriveStorage::stateDir().'/migration-worker-'.$slot.'.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))exit;
$release=isset($options['release-local'])&&EgsDriveStorage::productionReady();
$limit=isset($options['limit'])?max(1,(int)$options['limit']):25;$seconds=isset($options['seconds'])?max(1,(int)$options['seconds']):50;$site=isset($options['site'])&&in_array($options['site'],array('backend','wordpress'),true)?$options['site']:null;
$started=microtime(true);$processed=0;$errors=0;
try{
    $library=EgsDriveStorage::readJson('library');if(empty($library['root_id']))throw new RuntimeException('Falta la biblioteca de Drive.');EgsDriveStorage::api('GET','files/'.rawurlencode($library['root_id']).'?fields=id');
    while($processed<$limit&&microtime(true)-$started<$seconds){$job=EgsDriveMigration::claim($release,$site);if(!$job)break;
        try{$result=EgsDriveStorage::migrateFile($job['path'],json_decode($job['context'],true)?:array(),$release);EgsDriveMigration::finish($job,$result);$errors=0;}
        catch(Throwable $e){$status=EgsDriveMigration::fail($job,$e);fwrite(STDERR,json_encode(array('job'=>$job['id'],'status'=>$status,'error'=>$e->getMessage()))."\n");if($status!=='review')$errors++;}
        $processed++;if($errors>=3)break;
    }
    $progress=EgsDriveMigration::progress();try{EgsDriveMigration::publishProgress($progress,$progress['pending']===0);}catch(Throwable $e){fwrite(STDERR,"El informe de progreso se publicará en el siguiente lote.\n");}
    echo json_encode(array('slot'=>$slot,'processed'=>$processed,'release_local'=>$release,'progress'=>$progress))."\n";
}finally{flock($lock,LOCK_UN);fclose($lock);}
