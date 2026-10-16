<?php
require_once __DIR__.'/DriveStorage.php';
class EgsDriveMigration
{
    private static $db;
    public static function database()
    {
        if(self::$db)return self::$db;EgsDriveStorage::init();
        $db=new PDO('sqlite:'.EgsDriveStorage::stateDir().'/migration.sqlite');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$db->exec('PRAGMA busy_timeout=30000');
        $db->exec("CREATE TABLE IF NOT EXISTS jobs(id INTEGER PRIMARY KEY AUTOINCREMENT,path TEXT UNIQUE NOT NULL,site TEXT NOT NULL,context TEXT NOT NULL,bytes INTEGER NOT NULL,status TEXT NOT NULL DEFAULT 'pending',attempts INTEGER NOT NULL DEFAULT 0,next_attempt INTEGER NOT NULL DEFAULT 0,lease_until INTEGER NOT NULL DEFAULT 0,updated_at INTEGER NOT NULL,last_error TEXT); CREATE INDEX IF NOT EXISTS jobs_ready ON jobs(status,next_attempt,id)");
        chmod(EgsDriveStorage::stateDir().'/migration.sqlite',0600);self::$db=$db;return $db;
    }
    public static function initialize($manifestDir)
    {
        $db=self::database();$insert=$db->prepare('INSERT OR IGNORE INTO jobs(path,site,context,bytes,updated_at)VALUES(?,?,?,?,?)');
        foreach(array('backend','wordpress') as $site){
            $manifest=array();$csv=$manifestDir.'/'.$site.'.csv';$fh=fopen($csv,'r');if(!$fh)throw new RuntimeException('Falta el inventario de relaciones: '.$site);
            $header=fgetcsv($fh);$header[0]=preg_replace('/^\xEF\xBB\xBF/','',$header[0]);
            while(($r=fgetcsv($fh))!==false){if(count($r)!==count($header))throw new RuntimeException('El inventario contiene una fila inválida.');$m=array_combine($header,$r);$manifest[$m['archivo']]=$m;}fclose($fh);
            $root=$site==='backend'?EgsDriveStorage::root():EgsHosting::wordpressRoot();$media=$site==='backend'?'/vistas/img':'/wp-content/uploads';
            $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.$media,FilesystemIterator::SKIP_DOTS));$count=0;$db->beginTransaction();
            try{foreach($it as $f){if(!$f->isFile()||$f->isLink()||!EgsDriveStorage::mediaPath($f->getFilename()))continue;
                if(!EgsDriveStorage::mediaMime((new finfo(FILEINFO_MIME_TYPE))->file($f->getPathname())))continue;
                $alias=substr($f->getPathname(),strlen($root)+1);$key=EgsDriveStorage::normalize($alias,$site);
                $context=array('source_site'=>$site,'ruta_original'=>$alias,'migration'=>true);
                $isBackup=strtolower($f->getExtension())==='orig';$reference=$isBackup?substr($alias,0,-5):$alias;
                if(isset($manifest[$reference])){
                    $m=$manifest[$reference];$context['drive_folder']=preg_replace('/[\x00-\x1f]/','_',dirname($m['destino_drive_propuesto']));$context['drive_name']=$isBackup?basename($alias):preg_replace('/[\x00-\x1f]/','_',basename($m['destino_drive_propuesto']));
                    foreach(array('ordenes_confirmadas','ordenes_probables','titulo_orden','fuentes_bd','wordpress_attachment_ids','wordpress_post_ids','titulo_registro') as $field)if(isset($m[$field]))$context[$field]=$m[$field];$context['clasificacion']=$m['estado'];
                }else{$context['drive_folder']='Por_revisar/Sin_inventario/'.$site.'/'.dirname($alias);$context['clasificacion']='sin_inventario_previo';}
                if($isBackup){$context['drive_folder'].='/Respaldos_originales';$context['respaldo_de']=$reference;}
                $insert->execute(array($key,$site,json_encode($context),$f->getSize(),time()));$count++;
            }$db->commit();}catch(Throwable $e){$db->rollBack();throw $e;}
        }
        EgsDriveStorage::saveJson('migration_plan',array('at'=>time(),'total'=>(int)$db->query('SELECT COUNT(*) FROM jobs')->fetchColumn(),'originals_released'=>false));return self::progress();
    }
    public static function claim($releaseLocal,$preferredSite=null)
    {
        $db=self::database();$now=time();$states=$releaseLocal?"'pending','retry','verified'":"'pending','retry'";
        $db->exec('BEGIN IMMEDIATE');
        try{
            $sql="SELECT * FROM jobs WHERE ((status IN ($states) AND next_attempt<=:now) OR (status='working' AND lease_until<:now)) AND attempts<5";
            if($preferredSite)$sql.=' ORDER BY CASE WHEN site=:site THEN 0 ELSE 1 END,id LIMIT 1';else $sql.=' ORDER BY id LIMIT 1';
            $q=$db->prepare($sql);$params=array(':now'=>$now);if($preferredSite)$params[':site']=$preferredSite;$q->execute($params);$job=$q->fetch(PDO::FETCH_ASSOC);
            if($job){$q=$db->prepare("UPDATE jobs SET status='working',lease_until=?,updated_at=? WHERE id=?");$q->execute(array($now+3600,$now,$job['id']));}
            $db->exec('COMMIT');return $job?:null;
        }catch(Throwable $e){$db->exec('ROLLBACK');throw $e;}
    }
    public static function finish($job,$result)
    {
        $q=self::database()->prepare('UPDATE jobs SET status=?,bytes=?,lease_until=0,next_attempt=0,updated_at=?,last_error=NULL WHERE id=?');$q->execute(array($result['status'],(int)$result['bytes'],time(),$job['id']));
    }
    public static function fail($job,$exception)
    {
        $attempt=(int)$job['attempts']+1;$review=$exception instanceof DomainException;$status=$review?'review':($attempt>=5?'failed':'retry');
        $q=self::database()->prepare('UPDATE jobs SET status=?,attempts=?,next_attempt=?,lease_until=0,updated_at=?,last_error=? WHERE id=?');$q->execute(array($status,$attempt,time()+min(3600,120*(1<<min($attempt,4))),time(),substr($exception->getMessage(),0,600),$job['id']));return $status;
    }
    public static function progress()
    {
        $rows=self::database()->query('SELECT site,status,COUNT(*) AS files,SUM(bytes) AS bytes FROM jobs GROUP BY site,status')->fetchAll(PDO::FETCH_ASSOC);
        $result=array('at'=>time(),'total'=>0,'copied_verified'=>0,'released'=>0,'released_bytes'=>0,'already_remote'=>0,'verified_remaining'=>0,'pending'=>0,'needs_review'=>0,'groups'=>$rows);
        foreach($rows as $r){$n=(int)$r['files'];$result['total']+=$n;if(in_array($r['status'],array('verified','released','already_remote'),true))$result['copied_verified']+=$n;
            if($r['status']==='released'){$result['released']+=$n;$result['released_bytes']+=(int)$r['bytes'];}
            elseif($r['status']==='already_remote')$result['already_remote']+=$n;
            elseif($r['status']==='verified')$result['verified_remaining']+=$n;
            elseif(in_array($r['status'],array('pending','retry','working'),true))$result['pending']+=$n;
            elseif(in_array($r['status'],array('review','failed'),true))$result['needs_review']+=$n;
        }
        $production=EgsDriveStorage::readJson('oauth_production');$result['production_confirmed']=!empty($production['confirmed']);
        $result['production_authorized']=EgsDriveStorage::productionReady();
        $result['phase']=$result['pending']?'copying':($result['needs_review']?'review_required':($result['released']+$result['already_remote']===$result['total']?'complete':($result['production_authorized']?'releasing_verified_originals':'copies_verified_awaiting_release')));
        EgsDriveStorage::saveJson('migration_progress',$result);return $result;
    }
    public static function publishProgress($progress,$force=false)
    {
        $lock=fopen(EgsDriveStorage::stateDir().'/progress-publish.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))return;chmod(EgsDriveStorage::stateDir().'/progress-publish.lock',0600);
        try{
            $report=EgsDriveStorage::readJson('migration_report');if(!$force&&!empty($report['at'])&&time()-$report['at']<600)return;
            $library=EgsDriveStorage::readJson('library');if(empty($library['root_id']))return;
            $text="MIGRACIÓN DE MULTIMEDIA EGS A GOOGLE DRIVE\nActualizado: ".gmdate('Y-m-d H:i:s').' UTC'."\n\n";
            $text.='Archivos de multimedia del inventario actual: '.$progress['total']."\nCopias completas verificadas: ".$progress['copied_verified']."\nOriginales retirados del hosting: ".$progress['released']."\nBytes liberados: ".$progress['released_bytes']."\nPendientes: ".$progress['pending']."\nPara revisión: ".$progress['needs_review']."\nEstado: ".$progress['phase']."\nOAuth en Producción confirmado: ".($progress['production_confirmed']?'Sí':'Pendiente')."\nAutorización renovada tras publicar: ".($progress['production_authorized']?'Sí':'Pendiente')."\n\n";
            $text.="Cada copia se descarga y compara antes de retirar el original. Las rutas del sitio y las relaciones con registros se conservan. Las imágenes sin asociación confirmada se conservan en Por_revisar; no se eliminan por estar desvinculadas.\n";
            if(empty($report['file_id'])){$file=EgsDriveStorage::api('POST','files?fields=id',array('name'=>'Progreso de migración EGS.txt','mimeType'=>'text/plain','parents'=>array($library['root_id'])));$report['file_id']=$file['id'];}
            $r=EgsDriveStorage::rawRequest('PATCH','https://www.googleapis.com/upload/drive/v3/files/'.rawurlencode($report['file_id']).'?uploadType=media&fields=id,size,md5Checksum',array('Authorization: Bearer '.EgsDriveStorage::token(),'Content-Type: text/plain; charset=utf-8'),$text);$meta=json_decode($r['body'],true);
            if($r['status']!==200||!is_array($meta))throw new RuntimeException('No se pudo publicar el progreso.');EgsDriveStorage::verify($meta,strlen($text),md5($text));
            $report['at']=time();EgsDriveStorage::saveJson('migration_report',$report);
        }finally{flock($lock,LOCK_UN);fclose($lock);}
    }
}
