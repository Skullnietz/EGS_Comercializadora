<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
umask(0077);require_once dirname(__DIR__).'/config/DriveBackup.php';
$options=getopt('',array('init','status','databases','seed-weekly','sites','scheduled','test'));
if(isset($options['test'])){
    $assert=function($ok,$message){if(!$ok)throw new RuntimeException($message);};
    $at=function($s){return (new DateTimeImmutable($s,new DateTimeZone('America/Mexico_City')))->getTimestamp();};
    $d=EgsDriveBackup::due($at('2026-10-18 02:59:00'),null,null);$assert(!$d['daily']&&!$d['weekly'],'No ejecutar antes de las 03:00.');
    $d=EgsDriveBackup::due($at('2026-10-18 03:00:00'),null,null);$assert($d['daily']&&$d['weekly'],'El domingo requiere ambas copias.');
    $d=EgsDriveBackup::due($at('2026-10-18 03:15:00'),'2026-10-18','2026-10-18');$assert(!$d['daily']&&!$d['weekly'],'No repetir un respaldo completo del día.');
    $d=EgsDriveBackup::due($at('2026-10-19 03:00:00'),'2026-10-18','2026-10-18');$assert($d['daily']&&!$d['weekly'],'El lunes solo requiere el diario.');
    $folders=array();foreach(range(1,10) as $n)$folders[]=array('id'=>'id'.$n,'name'=>'2026-10-'.str_pad($n,2,'0',STR_PAD_LEFT),'appProperties'=>array('egs_backup_owner'=>EgsDriveBackup::OWNER,'egs_backup_complete'=>'yes'));
    $folders[]=array('id'=>'foreign','name'=>'2026-01-01','appProperties'=>array());
    $folders[]=array('id'=>'incomplete','name'=>'2026-01-02','appProperties'=>array('egs_backup_owner'=>EgsDriveBackup::OWNER));
    $drop=EgsDriveBackup::retentionCandidates($folders,7,'id10');$assert(array_column($drop,'id')===array('id3','id2','id1'),'Conservar los siete diarios más recientes sin tocar carpetas ajenas o incompletas.');
    $drop=EgsDriveBackup::retentionCandidates($folders,4,'id1');$assert(!in_array('id1',array_column($drop,'id'),true),'Nunca retirar el respaldo protegido.');
    echo "OK: horario CDMX, domingo, idempotencia y retención limitada.\n";exit;
}
$backup=new EgsDriveBackup();
$lock=fopen($backup->directory().'/run.lock','c');chmod($backup->directory().'/run.lock',0600);
if(!$lock||!flock($lock,LOCK_EX|LOCK_NB)){EgsDriveBackup::log('already_running');exit;}
try{
    $backup->rotateLog();
    if(isset($options['init']))$backup->initialize();
    elseif(isset($options['status']))echo json_encode($backup->status(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
    elseif(isset($options['databases']))$backup->backupDatabases(isset($options['seed-weekly']));
    elseif(isset($options['sites']))$backup->snapshotSites();
    elseif(isset($options['scheduled']))$backup->backupDatabases(false,true);
    else throw new RuntimeException('Indica --init, --status, --databases, --sites o --scheduled.');
}catch(Throwable $e){EgsDriveBackup::log('failed',array('message'=>$e->getMessage()));exit(1);}
finally{flock($lock,LOCK_UN);fclose($lock);}
