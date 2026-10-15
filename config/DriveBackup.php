<?php
/** Private, encrypted EGS backups. PHP 7.1. No application media are deleted here. */
require_once __DIR__.'/DriveStorage.php';
require_once __DIR__.'/Database.php';
class EgsDriveBackup
{
    const OWNER = 'egs-private-backup-v1';
    private $dir;
    private $config;
    public function __construct($dir=null)
    {
        $this->dir=$dir?:dirname(EgsDriveStorage::root()).'/.egs-backup';
        umask(0077);
        if(!is_dir($this->dir)&&!mkdir($this->dir,0700,true))throw new RuntimeException('No se pudo crear el directorio privado de respaldos.');
        chmod($this->dir,0700);
        $this->config=$this->read('config');
    }
    public function directory(){return $this->dir;}
    private function read($name){$p=$this->dir.'/'.$name.'.json';return is_file($p)?(json_decode(file_get_contents($p),true)?:array()):array();}
    private function save($name,$data)
    {
        $tmp=tempnam($this->dir,'state-');chmod($tmp,0600);
        if(file_put_contents($tmp,json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE))===false||!rename($tmp,$this->dir.'/'.$name.'.json'))throw new RuntimeException('No se pudo guardar el estado de respaldo.');
    }
    public static function log($event,$data=array()){echo json_encode(array_merge(array('at'=>gmdate('c'),'event'=>$event),$data),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";flush();}
    public static function due($now,$lastDaily,$lastWeekly)
    {
        $local=(new DateTimeImmutable('@'.$now))->setTimezone(new DateTimeZone('America/Mexico_City'));
        $day=$local->format('Y-m-d');$after=(int)$local->format('H')>=3;
        return array('day'=>$day,'daily'=>$after&&$lastDaily!==$day,'weekly'=>$after&&$local->format('N')==='7'&&$lastWeekly!==$day);
    }
    public static function retentionCandidates($folders,$keep,$protectedId)
    {
        $safe=array();
        foreach($folders as $f){$a=isset($f['appProperties'])?$f['appProperties']:array();
            if(empty($a['egs_backup_owner'])||$a['egs_backup_owner']!==self::OWNER||empty($a['egs_backup_complete'])||$a['egs_backup_complete']!=='yes'||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$f['name'])||$f['name']>gmdate('Y-m-d',time()+86400))continue;
            $safe[]=$f;
        }
        usort($safe,function($a,$b){return strcmp($b['name'],$a['name']);});
        $out=array();foreach(array_slice($safe,max(1,(int)$keep)) as $f)if($f['id']!==$protectedId)$out[]=$f;
        return $out;
    }
    private function request($method,$path,$body=null)
    {
        $headers=array('Authorization: Bearer '.EgsDriveStorage::token());
        if($body!==null){$headers[]='Content-Type: application/json';$body=json_encode($body);}
        $r=EgsDriveStorage::rawRequest($method,'https://www.googleapis.com/drive/v3/'.$path,$headers,$body);
        if($r['status']<200||$r['status']>=300)throw new RuntimeException('Drive rechazó la operación de respaldo (HTTP '.$r['status'].').');
        if($r['status']===204||$r['body']==='')return array();
        $v=json_decode($r['body'],true);if(!is_array($v))throw new RuntimeException('Respuesta de Drive inválida.');return $v;
    }
    private function listFiles($query)
    {
        $out=array();$page=null;
        do{$p=array('q'=>$query,'pageSize'=>1000,'fields'=>'nextPageToken,files(id,name,mimeType,size,md5Checksum,appProperties,parents,createdTime)');if($page)$p['pageToken']=$page;
            $r=$this->request('GET','files?'.http_build_query($p,'','&',PHP_QUERY_RFC3986));$out=array_merge($out,isset($r['files'])?$r['files']:array());$page=isset($r['nextPageToken'])?$r['nextPageToken']:null;
        }while($page);return $out;
    }
    private function folder($parent,$name,$kind)
    {
        $q="trashed=false and '".$parent."' in parents and name='".str_replace("'","\\'",$name)."' and appProperties has { key='egs_backup_owner' and value='".self::OWNER."' }";
        $found=$this->listFiles($q);if($found)return $found[0]['id'];
        $r=$this->request('POST','files?fields=id',array('name'=>$name,'mimeType'=>'application/vnd.google-apps.folder','parents'=>array($parent),'appProperties'=>array('egs_backup_owner'=>self::OWNER,'egs_backup_kind'=>$kind)));
        return $r['id'];
    }
    public function initialize()
    {
        $root=EgsDriveStorage::folder('Respaldos_EGS');
        $this->config=array_merge($this->config,array('owner'=>self::OWNER,'root_id'=>$root,'daily_id'=>$this->folder($root,'Bases_de_datos_diarios','daily_root'),'weekly_id'=>$this->folder($root,'Bases_de_datos_semanales','weekly_root'),'sites_id'=>$this->folder($root,'Sitios_actualizados','sites_root'),'daily_keep'=>7,'weekly_keep'=>4,'timezone'=>'America/Mexico_City','daily_hour'=>3,'weekly_day'=>'Sunday'));
        if(!is_file($this->dir.'/recovery.key')){file_put_contents($this->dir.'/recovery.key',bin2hex(random_bytes(32))."\n");chmod($this->dir.'/recovery.key',0600);}
        if(!is_dir($this->dir.'/gnupg'))mkdir($this->dir.'/gnupg',0700);
        $this->save('config',$this->config);self::log('initialized',array('folders'=>$this->config,'recovery_key_created'=>true));return $this->config;
    }
    private function requireReady(){if(empty($this->config['root_id'])||!is_file($this->dir.'/recovery.key'))throw new RuntimeException('Primero inicializa los respaldos.');}
    private function command($args,$stdout=null)
    {
        $error=tempnam($this->dir,'command-error-');$out=$stdout?:'/dev/null';
        $spec=array(0=>array('file','/dev/null','r'),1=>array('file',$out,'w'),2=>array('file',$error,'w'));
        $proc=proc_open(implode(' ',array_map('escapeshellarg',$args)),$spec,$pipes);
        if(!is_resource($proc))throw new RuntimeException('No se pudo ejecutar la herramienta de respaldo.');
        $code=proc_close($proc);$details=file_get_contents($error);unlink($error);
        if($code!==0)throw new RuntimeException(basename($args[0]).' terminó con código '.$code.'. '.substr(preg_replace('/password[^\s]*/i','credencial',$details),0,350));
    }
    private function work($kind)
    {
        $p=$this->dir.'/work-'.$kind.'-'.gmdate('Ymd_His').'-'.bin2hex(random_bytes(3));
        if(!mkdir($p,0700))throw new RuntimeException('No se pudo preparar el respaldo.');return $p;
    }
    private function encrypt($file)
    {
        $this->ensureAgent();
        $target=$file.'.gpg';
        $this->command(array('/usr/bin/gpg','--no-options','--homedir',$this->dir.'/gnupg','--batch','--yes','--passphrase-file',$this->dir.'/recovery.key','--symmetric','--cipher-algo','AES256','--compress-algo','none','--force-mdc','--s2k-digest-algo','SHA256','--output',$target,$file));
        $check=$file.'.decrypt-check';
        $this->command(array('/usr/bin/gpg','--no-options','--homedir',$this->dir.'/gnupg','--batch','--yes','--passphrase-file',$this->dir.'/recovery.key','--output',$check,'--decrypt',$target));
        if(hash_file('sha256',$check)!==hash_file('sha256',$file))throw new RuntimeException('La comprobación de cifrado no coincide.');
        unlink($check);return $target;
    }
    private function ensureAgent()
    {
        $check=array('/usr/bin/timeout','10','/usr/bin/gpg-connect-agent','--homedir',$this->dir.'/gnupg','/bye');
        try{$this->command($check);return;}catch(Throwable $e){}
        // File-backed descriptors let the daemon detach without keeping a PHP pipe open.
        $this->command(array('/usr/bin/timeout','20','/usr/bin/gpg-agent','--homedir',$this->dir.'/gnupg','--use-standard-socket','--daemon'));
        $this->command($check);
    }
    private function upload($path,$parent,$kind)
    {
        $size=filesize($path);$md5=md5_file($path);$sha=hash_file('sha256',$path);$name=basename($path);
        if($size<=0)throw new RuntimeException('El respaldo está vacío.');
        $q="trashed=false and '".$parent."' in parents and appProperties has { key='egs_backup_sha256' and value='".$sha."' }";
        $found=$this->listFiles($q);$meta=null;
        foreach($found as $f)if(isset($f['size'],$f['md5Checksum'])&&(int)$f['size']===$size&&$f['md5Checksum']===$md5){$meta=$f;break;}
        if(!$meta){
            $body=array('name'=>$name,'parents'=>array($parent),'mimeType'=>'application/octet-stream','description'=>substr($name,-4)==='.gpg'?'Respaldo privado cifrado de EGS. Llave de recuperación requerida.':'Metadatos de respaldo EGS; no contienen contraseñas.','appProperties'=>array('egs_backup_owner'=>self::OWNER,'egs_backup_kind'=>$kind,'egs_backup_sha256'=>$sha));
            $r=EgsDriveStorage::rawRequest('POST','https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable&fields=id,name,size,md5Checksum',array('Authorization: Bearer '.EgsDriveStorage::token(),'Content-Type: application/json','X-Upload-Content-Type: application/octet-stream','X-Upload-Content-Length: '.$size),json_encode($body));
            if($r['status']!==200||empty($r['headers']['location']))throw new RuntimeException('No se pudo iniciar la subida del respaldo.');
            $url=$r['headers']['location'];$fh=fopen($path,'rb');$offset=0;$lastLog=0;
            try{while($offset<$size){
                fseek($fh,$offset);$chunk=fread($fh,4*1024*1024);$end=$offset+strlen($chunk)-1;$response=null;
                for($attempt=0;$attempt<4;$attempt++){
                    try{$response=EgsDriveStorage::rawRequest('PUT',$url,array('Authorization: Bearer '.EgsDriveStorage::token(),'Content-Type: application/octet-stream','Content-Range: bytes '.$offset.'-'.$end.'/'.$size),$chunk);}
                    catch(Throwable $e){$response=null;}
                    if($response&&($response['status']===308||($response['status']>=200&&$response['status']<300)))break;
                    $status=EgsDriveStorage::rawRequest('PUT',$url,array('Authorization: Bearer '.EgsDriveStorage::token(),'Content-Length: 0','Content-Range: bytes */'.$size),'');
                    if($status['status']>=200&&$status['status']<300){$response=$status;break;}
                    if($status['status']===308&&isset($status['headers']['range'])&&preg_match('/bytes=0-(\d+)/',$status['headers']['range'],$m)){if((int)$m[1]+1!==$offset){$response=$status;break;}}
                    usleep(500000*(1+$attempt));
                }
                if(!$response)throw new RuntimeException('Se interrumpió la subida del respaldo.');
                if($response['status']===308){if(empty($response['headers']['range'])||!preg_match('/bytes=0-(\d+)/',$response['headers']['range'],$m))throw new RuntimeException('Drive no confirmó el avance.');$next=(int)$m[1]+1;if($next<=$offset||$next>$size)throw new RuntimeException('Avance de subida inválido.');$offset=$next;}
                elseif($response['status']>=200&&$response['status']<300){$meta=json_decode($response['body'],true);$offset=$size;}
                else throw new RuntimeException('Falló la subida del respaldo.');
                if($offset-$lastLog>=32*1024*1024||$offset===$size){self::log('upload_progress',array('file'=>$name,'uploaded'=>$offset,'total'=>$size));$lastLog=$offset;}
            }}finally{fclose($fh);}
        }
        if(!$meta||empty($meta['id']))throw new RuntimeException('Drive no devolvió el respaldo.');
        $meta=$this->request('GET','files/'.$meta['id'].'?fields=id,name,size,md5Checksum');
        if((int)$meta['size']!==$size||!hash_equals($md5,$meta['md5Checksum']))throw new RuntimeException('La copia de Drive no coincide.');
        $this->verifyRemote($meta);
        $meta['sha256']=$sha;$meta['verified_at']=gmdate('c');self::log('uploaded_verified',array('file'=>$name,'id'=>$meta['id'],'bytes'=>$size));return $meta;
    }
    private function verifyRemote($meta)
    {
        $tmp=tempnam($this->dir,'readback-');chmod($tmp,0600);
        try{
            for($i=0;$i<3;$i++){
                $fh=fopen($tmp,'wb');$ch=curl_init('https://www.googleapis.com/drive/v3/files/'.rawurlencode($meta['id']).'?alt=media');
                curl_setopt_array($ch,array(CURLOPT_HTTPHEADER=>array('Authorization: Bearer '.EgsDriveStorage::token()),CURLOPT_FILE=>$fh,CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>3600,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS));
                $ok=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);fclose($fh);clearstatcache(true,$tmp);
                if($ok&&$status===200&&filesize($tmp)===(int)$meta['size']&&hash_equals($meta['md5Checksum'],md5_file($tmp)))return;
                usleep(500000*(1+$i));
            }
            throw new RuntimeException('La descarga de comprobación de Drive falló.');
        }finally{if(is_file($tmp))unlink($tmp);}
    }
    private function dumpDatabases($work)
    {
        $configs=array();$method=new ReflectionMethod('Database','config');$method->setAccessible(true);
        foreach(array(Database::ECOMMERCE,Database::WORDPRESS) as $key){$c=$method->invoke(null,$key);$configs[$key]=$c;}
        $schemas=array('egsequip_ecomerce'=>Database::ECOMMERCE,'egsequip_respaldo'=>Database::WORDPRESS,'egsequip_wp54331'=>Database::WORDPRESS,'egsequip_sisanweb'=>Database::WORDPRESS);
        $out=array();
        foreach($schemas as $schema=>$key){
            self::log('database_dump_start',array('database'=>$schema));$c=$configs[$key];$cnf=$work.'/mysql-'.$key.'.cnf';
            $quote=function($v){return '"'.str_replace(array('\\','"',"\n","\r"),array('\\\\','\\"','\\n','\\r'),$v).'"';};
            file_put_contents($cnf,"[client]\nhost=".$quote($c['host'])."\nuser=".$quote($c['user'])."\npassword=".$quote($c['pass'])."\n");chmod($cnf,0600);
            $sql=$work.'/'.$schema.'.sql';
            try{$this->command(array('/usr/bin/mysqldump','--defaults-extra-file='.$cnf,'--lock-tables','--quick','--hex-blob','--routines','--events','--triggers','--set-gtid-purged=OFF','--default-character-set=utf8mb4','--databases',$schema),$sql);}finally{if(is_file($cnf))unlink($cnf);}
            $pdo=Database::conectar($key);$countQuery=$pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_TYPE=\'BASE TABLE\'');$countQuery->execute(array($schema));$expected=(int)$countQuery->fetchColumn();
            $fh=fopen($sql,'rb');$actual=0;$completed=false;while(($line=fgets($fh))!==false){if(strpos($line,'CREATE TABLE ')===0)$actual++;if(strpos($line,'-- Dump completed on ')===0)$completed=true;}fclose($fh);
            if(!$completed||$actual!==$expected)throw new RuntimeException('El dump no está completo: '.$schema.' (tablas '.$actual.'/'.$expected.').');
            $this->command(array('/usr/bin/gzip','-n','-6',$sql));$gz=$sql.'.gz';$this->command(array('/usr/bin/gzip','-t',$gz));
            $encrypted=$this->encrypt($gz);$out[]=array('schema'=>$schema,'tables'=>$expected,'path'=>$encrypted,'plain_sha256'=>hash_file('sha256',$gz));unlink($gz);
        }return $out;
    }
    private function uploadManifest($folder,$work,$manifest,$kind)
    {
        $p=$work.'/manifest.json';file_put_contents($p,json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        // Manifest contains only names, counts, checksums and Drive IDs, no credentials.
        return $this->upload($p,$folder,$kind.'_manifest');
    }
    private function complete($folder,$kind)
    {
        $this->request('PATCH','files/'.$folder.'?fields=id',array('appProperties'=>array('egs_backup_owner'=>self::OWNER,'egs_backup_kind'=>$kind,'egs_backup_complete'=>'yes','egs_backup_verified_at'=>gmdate('c'))));
    }
    private function removeTree($path)
    {
        if(strpos($path,$this->dir.'/work-')!==0||realpath(dirname($path))!==realpath($this->dir))throw new RuntimeException('Ruta temporal inesperada.');
        if(!is_dir($path))return;
        $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
        foreach($it as $f){if($f->isDir()&&!$f->isLink())rmdir($f->getPathname());else unlink($f->getPathname());}rmdir($path);
    }
    private function prune($parent,$keep,$protected)
    {
        $folders=$this->listFiles("trashed=false and '".$parent."' in parents and mimeType='application/vnd.google-apps.folder'");$removed=array();
        foreach(self::retentionCandidates($folders,$keep,$protected) as $f){
            // Only this backup tool's dated folders under its private retention root.
            if($this->ownedTree($f['id'])){$this->request('DELETE','files/'.$f['id']);$removed[]=array('id'=>$f['id'],'day'=>$f['name']);}
        }
        // A failed job must not leave unbounded partial copies in Drive.
        foreach($folders as $f){$a=isset($f['appProperties'])?$f['appProperties']:array();
            if($f['id']===$protected||!isset($a['egs_backup_owner'])||$a['egs_backup_owner']!==self::OWNER||isset($a['egs_backup_complete'])||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$f['name'])||$f['name']>=gmdate('Y-m-d',time()-2*86400))continue;
            if($this->ownedTree($f['id'])){$this->request('DELETE','files/'.$f['id']);$removed[]=array('id'=>$f['id'],'day'=>$f['name'],'incomplete'=>true);}
        }
        return $removed;
    }
    private function ownedTree($folder)
    {
        foreach($this->listFiles("trashed=false and '".$folder."' in parents") as $f){
            if(empty($f['appProperties']['egs_backup_owner'])||$f['appProperties']['egs_backup_owner']!==self::OWNER)return false;
            if($f['mimeType']==='application/vnd.google-apps.folder'&&!$this->ownedTree($f['id']))return false;
        }return true;
    }
    private function prunePartialFiles($folder,$keepIds)
    {
        foreach($this->listFiles("trashed=false and '".$folder."' in parents") as $f){
            if(in_array($f['id'],$keepIds,true)||empty($f['appProperties']['egs_backup_owner'])||$f['appProperties']['egs_backup_owner']!==self::OWNER||$f['mimeType']==='application/vnd.google-apps.folder')continue;
            $this->request('DELETE','files/'.$f['id']);
        }
    }
    private function weeklyCopy($daily,$day,$work)
    {
        $folder=$this->folder($this->config['weekly_id'],$day,'weekly');$copies=array();
        foreach($daily['databases'] as $entry){$id=$entry['drive']['id'];$name=$entry['drive']['name'];
            $found=$this->listFiles("trashed=false and '".$folder."' in parents and name='".$name."'");$copy=null;
            foreach($found as $candidate)if(isset($candidate['md5Checksum'])&&$candidate['md5Checksum']===$entry['drive']['md5Checksum']){$copy=$candidate;break;}
            if(!$copy)$copy=$this->request('POST','files/'.$id.'/copy?fields=id,name,size,md5Checksum',array('name'=>$name,'parents'=>array($folder),'appProperties'=>array('egs_backup_owner'=>self::OWNER,'egs_backup_kind'=>'weekly_database')));
            if((int)$copy['size']!==(int)$entry['drive']['size']||$copy['md5Checksum']!==$entry['drive']['md5Checksum'])throw new RuntimeException('La copia semanal no coincide.');
            $this->verifyRemote($copy);$entry['drive']=$copy;$copies[]=$entry;
        }
        $manifest=array('version'=>1,'kind'=>'weekly','day'=>$day,'created_utc'=>gmdate('c'),'encryption'=>'OpenPGP AES256 with MDC','databases'=>$copies);
        $manifestFile=$this->uploadManifest($folder,$work,$manifest,'weekly');$this->complete($folder,'weekly');
        $keepIds=array($manifestFile['id']);foreach($copies as $entry)$keepIds[]=$entry['drive']['id'];$this->prunePartialFiles($folder,$keepIds);
        $removed=$this->prune($this->config['weekly_id'],$this->config['weekly_keep'],$folder);
        $state=$this->read('state');$state['last_weekly_day']=$day;$state['last_weekly_folder']=$folder;$this->save('state',$state);
        self::log('weekly_complete',array('day'=>$day,'folder_id'=>$folder,'pruned'=>$removed));return $folder;
    }
    public function backupDatabases($seedWeekly=false,$scheduled=false)
    {
        $this->requireReady();$state=$this->read('state');$due=self::due(time(),isset($state['last_daily_day'])?$state['last_daily_day']:null,isset($state['last_weekly_day'])?$state['last_weekly_day']:null);$day=$due['day'];
        if($scheduled&&!$due['daily']&&!$due['weekly'])return array('status'=>'not_due');
        $work=$this->work('database');
        try{
            if(isset($state['last_daily_day'])&&$state['last_daily_day']===$day&&!empty($state['last_daily_manifest'])){$manifest=$state['last_daily_manifest'];$folder=$state['last_daily_folder'];}
            else{
                $folder=$this->folder($this->config['daily_id'],$day,'daily');$dumps=$this->dumpDatabases($work);$entries=array();
                foreach($dumps as $dump){$remote=$this->upload($dump['path'],$folder,'daily_database');$entry=$dump;unset($entry['path']);$entry['drive']=$remote;$entries[]=$entry;}
                $manifest=array('version'=>1,'kind'=>'daily','day'=>$day,'created_utc'=>gmdate('c'),'encryption'=>'OpenPGP AES256 with MDC','consistency'=>'mysqldump --lock-tables for each database, includes MyISAM','databases'=>$entries);
                $manifestFile=$this->uploadManifest($folder,$work,$manifest,'daily');$this->complete($folder,'daily');
                $keepIds=array($manifestFile['id']);foreach($entries as $entry)$keepIds[]=$entry['drive']['id'];$this->prunePartialFiles($folder,$keepIds);
                $state=$this->read('state');$state['last_daily_day']=$day;$state['last_daily_folder']=$folder;$state['last_daily_manifest']=$manifest;$this->save('state',$state);
            }
            $removed=$this->prune($this->config['daily_id'],$this->config['daily_keep'],$folder);
            $weekly=null;if($seedWeekly||$due['weekly'])$weekly=$this->weeklyCopy($manifest,$day,$work);
            $result=array('daily_folder'=>$folder,'weekly_folder'=>$weekly,'day'=>$day,'databases'=>count($manifest['databases']),'pruned'=>$removed);self::log('databases_complete',$result);$this->removeTree($work);return $result;
        }catch(Throwable $e){$this->save('last-error',array('at'=>gmdate('c'),'kind'=>'database','message'=>$e->getMessage()));self::log('database_backup_failed',array('message'=>$e->getMessage()));$this->removeTree($work);throw $e;}
    }
    private function snapshotSqlite($source,$destination)
    {
        $pdo=new PDO('sqlite:'.$source);$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->exec('PRAGMA busy_timeout=30000');
        $mode=$pdo->query('PRAGMA journal_mode')->fetchColumn();if(strtolower($mode)!=='delete')throw new RuntimeException('Se requiere un respaldo SQLite compatible con su modo de diario.');
        $pdo->beginTransaction();try{$pdo->query('SELECT COUNT(*) FROM sqlite_master')->fetchColumn();if(!copy($source,$destination))throw new RuntimeException('No se pudo copiar el índice de Drive.');}finally{$pdo->rollBack();}
        $copy=new PDO('sqlite:'.$destination);$check=$copy->query('PRAGMA integrity_check')->fetchColumn();if($check!=='ok')throw new RuntimeException('El índice de Drive no supera integrity_check.');
    }
    private function archive($root,$items,$exclude,$target)
    {
        $args=array('/usr/bin/tar','--warning=no-file-changed','--ignore-failed-read','-czf',$target,'-C',$root);
        foreach($exclude as $pattern)$args[]='--exclude='.$pattern;foreach($items as $item)$args[]=$item;
        $this->command($args);$this->command(array('/usr/bin/gzip','-t',$target));
        $listing=$target.'.listing';$this->command(array('/usr/bin/tar','-tzf',$target),$listing);if(filesize($listing)===0)throw new RuntimeException('El respaldo de archivos está vacío.');
        return $listing;
    }
    public function snapshotSites()
    {
        $this->requireReady();$work=$this->work('sites');$stamp=(new DateTimeImmutable('now',new DateTimeZone('America/Mexico_City')))->format('Y-m-d_His');$folder=$this->folder($this->config['sites_id'],$stamp,'sites');$home=dirname(EgsDriveStorage::root());$mediaState=$work.'/drive-state';mkdir($mediaState,0700);
        $state=$this->read('state');
        $manifest=array('version'=>1,'kind'=>'sites','created_utc'=>gmdate('c'),'database_daily_folder'=>isset($state['last_daily_folder'])?$state['last_daily_folder']:null,'database_daily_manifest'=>isset($state['last_daily_manifest'])?$state['last_daily_manifest']:null,'files'=>array(),'media_storage'=>'Live media are in the EGS Drive library. This snapshot includes the path-to-Drive index; it does not make a second independent copy of all media.');
        $manifest['local_legacy_archives']=array();
        foreach(array('backend.comercializadoraegs.com/vistas/img/cabeceras/cabeceras.zip','backend_respaldo_antes_main/vistas/img/cabeceras.zip') as $legacy)if(is_file($home.'/'.$legacy))$manifest['local_legacy_archives'][]=array('path'=>$legacy,'bytes'=>filesize($home.'/'.$legacy),'sha256'=>hash_file('sha256',$home.'/'.$legacy));
        try{
            // Copy metadata under SQLite read locks before making the compressed state archive.
            foreach(array('media.sqlite','migration.sqlite') as $name)if(is_file(EgsDriveStorage::stateDir().'/'.$name))$this->snapshotSqlite(EgsDriveStorage::stateDir().'/'.$name,$mediaState.'/'.$name);
            foreach(array('library.json','client.json','tokens.json','oauth_production.json','enabled','integration_ready.json') as $name)if(is_file(EgsDriveStorage::stateDir().'/'.$name))copy(EgsDriveStorage::stateDir().'/'.$name,$mediaState.'/'.$name);
            $mapping=new PDO('sqlite:'.$mediaState.'/media.sqlite');$manifest['media_alias_count']=(int)$mapping->query('SELECT COUNT(*) FROM media')->fetchColumn();$manifest['media_library']=EgsDriveStorage::readJson('library');
            $jobs=array(
                array('name'=>'wordpress','root'=>$home,'items'=>array('public_html'),'exclude'=>array('public_html/.git','public_html/wp-content/ai1wm-backups','public_html/wp-content/cache','public_html/wp-content/wpo-cache','public_html/wp-content/upgrade','public_html/error_log','public_html/wp-content/debug.log')),
                array('name'=>'administracion','root'=>$home,'items'=>array('backend.comercializadoraegs.com'),'exclude'=>array('backend.comercializadoraegs.com/.git','backend.comercializadoraegs.com/storage/cache','backend.comercializadoraegs.com/error_log')),
                array('name'=>'integracion-drive','root'=>$work,'items'=>array('drive-state'),'exclude'=>array())
            );
            if(is_file($home.'/backend_respaldo_antes_main/vistas/img/cabeceras.zip'))$jobs[]=array('name'=>'legado-cabeceras','root'=>$home.'/backend_respaldo_antes_main','items'=>array('vistas/img/cabeceras.zip'),'exclude'=>array());
            foreach($jobs as $job){
                self::log('site_archive_start',array('site'=>$job['name']));$path=$work.'/'.$job['name'].'-'.$stamp.'.tar.gz';$listing=$this->archive($job['root'],$job['items'],$job['exclude'],$path);
                if($job['name']==='wordpress'&&strpos(file_get_contents($listing),'public_html/wp-config.php')===false)throw new RuntimeException('Falta wp-config.php en el respaldo.');
                if($job['name']==='administracion'&&strpos(file_get_contents($listing),'backend.comercializadoraegs.com/index.php')===false)throw new RuntimeException('Falta index.php en el respaldo.');
                $plainSha=hash_file('sha256',$path);$encrypted=$this->encrypt($path);$remote=$this->upload($encrypted,$folder,'site_archive');$manifest['files'][]=array('name'=>$job['name'],'plain_sha256'=>$plainSha,'drive'=>$remote);unlink($path);unlink($encrypted);unlink($listing);
            }
            $restore="RESTAURAR EGS\nEstos archivos están cifrados con OpenPGP/AES256. Conserva recovery.key fuera del hosting.\nDescifra: gpg --batch --passphrase-file recovery.key --output archivo.tar.gz --decrypt archivo.tar.gz.gpg\nComprueba: gzip -t archivo.tar.gz; tar -tzf archivo.tar.gz\nRestaura los archivos en un servidor separado, restaura los SQL en MySQL y adapta las credenciales y los dominios.\nEl índice drive-state/media.sqlite y library.json son necesarios para las imágenes alojadas en Drive. Conserva la biblioteca de multimedia: no es una copia independiente dentro de estos tar.\nLos archivos client.json y tokens.json contienen autorizaciones; deben permanecer privados. Puede ser necesario autorizar de nuevo Google.\nLos SQL diarios y semanales contienen las cuatro bases de la cuenta, cifrados individualmente. Descifra, descomprime e importa en un entorno aislado primero.\nLos respaldos de sitios excluyen Git, cachés y respaldos antiguos. El archivo de legado conserva el ZIP histórico de cabeceras antes de eliminar la copia local anterior.\n";
            file_put_contents($work.'/COMO_RESTAURAR.txt',$restore);$manifest['restore_readme']=$this->upload($work.'/COMO_RESTAURAR.txt',$folder,'restore_readme');
            $this->uploadManifest($folder,$work,$manifest,'sites');$this->complete($folder,'sites');
            $state=$this->read('state');$state['last_sites_folder']=$folder;$state['last_sites_at']=gmdate('c');$state['last_sites_manifest']=$manifest;$this->save('state',$state);
            $this->removeTree($work);self::log('sites_complete',array('folder_id'=>$folder,'files'=>count($manifest['files']),'media_alias_count'=>$manifest['media_alias_count']));return $manifest;
        }catch(Throwable $e){self::log('site_backup_failed',array('work_dir'=>$work,'message'=>$e->getMessage()));throw $e;}
    }
    public function status(){return array('config'=>$this->config,'state'=>$this->read('state'));}
    public function rotateLog()
    {
        $p=$this->dir.'/backup.log';if(is_file($p)&&filesize($p)>1024*1024){$f=fopen($p,'r+');flock($f,LOCK_EX);fseek($f,-524288,SEEK_END);$tail=stream_get_contents($f);rewind($f);ftruncate($f,0);fwrite($f,$tail);flock($f,LOCK_UN);fclose($f);}
    }
}
