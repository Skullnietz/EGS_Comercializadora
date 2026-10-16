<?php
/** Herramientas de migración sin dependencias Composer. Sólo CLI. PHP >=7.1. */
class EgsMigration
{
    private $c;
    private $work;
    public function __construct($file)
    {
        if(!is_file($file)||!is_readable($file))throw new RuntimeException('No se puede leer la configuración privada.');
        $this->c=json_decode(file_get_contents($file),true);
        if(!is_array($this->c))throw new RuntimeException('Configuración JSON inválida.');
        foreach(array('backend','wordpress','drive','backup','work') as $key){
            if(empty($this->c['paths'][$key]))throw new RuntimeException('Falta paths.'.$key);
            $this->path($this->c['paths'][$key]);
        }
        $this->work=rtrim($this->c['paths']['work'],'/');
        $this->privatePath($this->work);
        foreach(array('drive','backup') as $key)$this->privatePath($this->c['paths'][$key]);
        if(!isset($this->c['databases'])||!is_array($this->c['databases']))throw new RuntimeException('Falta databases.');
        foreach($this->c['databases'] as $d){foreach(array('key','name','user','host') as $key)if(empty($d[$key]))throw new RuntimeException('Falta un campo de conexión.');
            if(!preg_match('/^[A-Za-z0-9_]+$/',$d['key'])||!preg_match('/^[A-Za-z0-9_]+$/',$d['name']))throw new RuntimeException('Identificador de base de datos inválido.');
        }
        $roots=array($this->canonical($this->c['paths']['backend']),$this->canonical($this->c['paths']['wordpress']));if($roots[0]===$roots[1]||strpos($roots[0],$roots[1].'/')===0||strpos($roots[1],$roots[0].'/')===0)throw new RuntimeException('Los sitios requieren directorios separados.');
        $roles=array('backend','wordpress','drive','backup','work');foreach($roles as $i=>$left)foreach(array_slice($roles,$i+1) as $right){$a=$this->canonical($this->c['paths'][$left]);$b=$this->canonical($this->c['paths'][$right]);if($a===$b||strpos($a,$b.'/')===0||strpos($b,$a.'/')===0)throw new RuntimeException('Las rutas '.$left.' y '.$right.' deben estar separadas.');}
        $keys=array_column($this->c['databases'],'key');if(count($keys)!==count(array_unique($keys)))throw new RuntimeException('Claves de bases de datos repetidas.');
    }
    private function path($p)
    {
        if(!is_string($p)||$p===''||$p[0]!=='/'||$p==='/'||preg_match('/[\x00-\x1f\x7f%]/',$p)||preg_match('~(?:^|/)\.\.?(/|$)~',$p))throw new RuntimeException('Se requiere una ruta absoluta sin segmentos especiales ni %.');
        return rtrim($p,'/');
    }
    private function canonical($p)
    {
        $suffix='';while(!file_exists($p)){ $suffix='/'.basename($p).$suffix;$p=dirname($p); }
        $real=realpath($p);if($real===false)throw new RuntimeException('No se pudo resolver una ruta.');return rtrim($real,'/').$suffix;
    }
    private function privatePath($p)
    {
        $p=$this->canonical($p);
        foreach(array('backend','wordpress') as $key){$root=$this->canonical($this->c['paths'][$key]);if($p===$root||strpos($p,$root.'/')===0||strpos($root,$p.'/')===0)throw new RuntimeException('Drive, respaldos y temporales deben estar fuera de ambos sitios públicos.');}
    }
    private function mkdirPrivate($p)
    {
        if(!is_dir($p)&&!mkdir($p,0700,true))throw new RuntimeException('No se pudo crear el directorio privado.');chmod($p,0700);
    }
    private function json($file,$v){$this->write($file,json_encode($v,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n");}
    private function write($file,$value)
    {
        $this->mkdirPrivate(dirname($file));$tmp=tempnam(dirname($file),'write-');chmod($tmp,0600);
        if(file_put_contents($tmp,$value)===false||!rename($tmp,$file))throw new RuntimeException('No se pudo guardar el archivo.');chmod($file,0600);
    }
    public function binary($key)
    {
        $p=isset($this->c['binaries'][$key])?$this->c['binaries'][$key]:null;
        if($p){if(!is_file($p)||!is_executable($p))throw new RuntimeException('Herramienta no ejecutable: '.$key);return $p;}
        foreach(explode(PATH_SEPARATOR,getenv('PATH') ?: '/usr/bin:/bin:/usr/sbin') as $dir){$p=rtrim($dir,'/').'/'.$key;if(is_file($p)&&is_executable($p))return $p;}
        if($key==='mysqldump')return $this->binary('mariadb-dump');
        if($key==='mysql')return $this->binary('mariadb');
        throw new RuntimeException('Falta '.$key.'; define binaries.'.$key);
    }
    public function command($args,$output=null,$input=null,$allowed=array(0))
    {
        $this->mkdirPrivate($this->work);$err=tempnam($this->work,'stderr-');$out=$output?:tempnam($this->work,'stdout-');
        $p=proc_open(implode(' ',array_map('escapeshellarg',$args)),array(0=>array('file',$input?:'/dev/null','r'),1=>array('file',$out,'w'),2=>array('file',$err,'w')),$pipes);
        if(!is_resource($p))throw new RuntimeException('No se pudo iniciar '.basename($args[0]));
        $code=proc_close($p);$result=$output?'':file_get_contents($out);unlink($err);if(!$output)unlink($out);
        if(!in_array($code,$allowed,true))throw new RuntimeException(basename($args[0]).' falló ('.$code.'). Revisa permisos, compatibilidad y configuración; no se imprimen credenciales.');
        return array('code'=>$code,'output'=>$result);
    }
    private function connection($d)
    {
        return new PDO('mysql:host='.$d['host'].';port='.(isset($d['port'])?(int)$d['port']:3306).';dbname='.$d['name'].';charset=utf8mb4',$d['user'],isset($d['password'])?$d['password']:'',array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    }
    private function clientConfig($d,$path)
    {
        $quote=function($v){return '"'.str_replace(array('\\','"',"\n","\r"),array('\\\\','\\"','\\n','\\r'),$v).'"';};
        $this->write($path,"[client]\nhost=".$quote($d['host'])."\nport=".(isset($d['port'])?(int)$d['port']:3306)."\nuser=".$quote($d['user'])."\npassword=".$quote(isset($d['password'])?$d['password']:'')."\n");
    }
    public function lock()
    {
        $this->mkdirPrivate($this->work);$file=$this->work.'/migration.lock';$lock=fopen($file,'c');if($lock)chmod($file,0600);
        if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))throw new RuntimeException('Ya hay otra operación de migración usando este directorio.');return $lock;
    }
    public function preflight($database=false)
    {
        $checks=array();$check=function($name,$ok,$detail='')use(&$checks){$checks[]=array('check'=>$name,'ok'=>(bool)$ok,'detail'=>$detail);};
        $check('php',version_compare(PHP_VERSION,'7.1','>='),PHP_VERSION);
        foreach(array('curl','gd','mbstring','openssl','pdo_mysql','pdo_sqlite','mysqli','dom','zip','fileinfo') as $extension)$check('php.'.$extension,extension_loaded($extension));
        foreach(array('proc_open','exec','flock') as $function)$check('php.'.$function,is_callable($function));
        foreach(array('php','tar','gzip','gpg','mysqldump','mysql','logrotate','flock') as $tool){try{$p=$this->binary($tool);$check('binary.'.$tool,true,$p);}catch(Throwable $e){$check('binary.'.$tool,false,$e->getMessage());}}
        foreach(array('backend'=>'index.php','wordpress'=>'wp-config.php','drive'=>'media.sqlite','backup'=>'recovery.key') as $key=>$file)$check('file.'.$key,is_file($this->c['paths'][$key].'/'.$file));
        if(is_file($this->c['paths']['drive'].'/media.sqlite')){try{$pdo=new PDO('sqlite:'.$this->c['paths']['drive'].'/media.sqlite');$check('drive.sqlite',$pdo->query('PRAGMA integrity_check')->fetchColumn()==='ok');$check('drive.aliases',(int)$pdo->query('SELECT COUNT(*) FROM media')->fetchColumn()>0);}catch(Throwable $e){$check('drive.sqlite',false);}}
        foreach(array('client.json','tokens.json','library.json','enabled') as $file)$check('drive.'.$file,is_file($this->c['paths']['drive'].'/'.$file));
        if($database)foreach($this->c['databases'] as $d){try{$pdo=$this->connection($d);$check('database.'.$d['key'],true,$pdo->getAttribute(PDO::ATTR_SERVER_VERSION));}catch(Throwable $e){$check('database.'.$d['key'],false,'No se pudo conectar.');}}
        $ok=!in_array(false,array_column($checks,'ok'),true);return array('ok'=>$ok,'checks'=>$checks,'limits'=>array('No certifica compatibilidad funcional, TLS, Apache, DNS, correo ni pagos.','Las pruebas de carga y el corte final se hacen en el destino.'));
    }
    private function symlinks($root)
    {
        $out=array();$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::SELF_FIRST);
        foreach($it as $f)if($f->isLink())$out[]=substr($f->getPathname(),strlen(rtrim($root,'/'))+1);return $out;
    }
    private function archive($root,$target,$exclude,$preliminary=false)
    {
        $links=$this->symlinks($root);$args=array($this->binary('tar'),'--format=gnu','--hard-dereference','-czf',$target,'-C',$root);
        foreach(array_merge($exclude,$links) as $item)$args[]='--exclude=./'.$item;
        $args[]='.';$r=$this->command($args,null,null,$preliminary?array(0,1):array(0));
        $this->command(array($this->binary('gzip'),'-t',$target));$this->validateTar($target);return array('skipped_symlinks'=>$links,'changed_during_copy'=>$r['code']===1);
    }
    private function snapshotSqlite($source,$target)
    {
        $pdo=new PDO('sqlite:'.$source);$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->exec('PRAGMA busy_timeout=30000');
        if(strtolower($pdo->query('PRAGMA journal_mode')->fetchColumn())!=='delete')throw new RuntimeException('El índice SQLite requiere snapshot compatible con su modo de diario.');
        $pdo->beginTransaction();try{$pdo->query('SELECT COUNT(*) FROM sqlite_master')->fetchColumn();if(!copy($source,$target))throw new RuntimeException('No se pudo copiar SQLite.');}finally{$pdo->rollBack();}
        $copy=new PDO('sqlite:'.$target);if($copy->query('PRAGMA integrity_check')->fetchColumn()!=='ok')throw new RuntimeException('SQLite no supera integrity_check.');chmod($target,0600);
    }
    private function gpgArgs($key)
    {
        if(!is_file($key)||!is_readable($key))throw new RuntimeException('Falta la llave privada.');$this->privatePath($key);
        $home=$this->work.'/gnupg';$this->mkdirPrivate($home);$gpg=$this->binary('gpg');$v=$this->command(array($gpg,'--version'));
        if(!preg_match('/(\d+\.\d+(?:\.\d+)?)/',$v['output'],$m))throw new RuntimeException('Versión de GnuPG desconocida.');
        $args=array($gpg,'--no-options','--homedir',$home,'--batch','--yes');if(version_compare($m[1],'2.1','>='))$args=array_merge($args,array('--pinentry-mode','loopback'));
        if(version_compare($m[1],'2.1','<')){
            $check=array($this->binary('timeout'),'10',$this->binary('gpg-connect-agent'),'--homedir',$home,'/bye');
            try{$this->command($check);}catch(Throwable $e){$this->command(array($this->binary('timeout'),'20',$this->binary('gpg-agent'),'--homedir',$home,'--use-standard-socket','--daemon'));$this->command($check);}
        }
        return array_merge($args,array('--passphrase-file',$key));
    }
    private function erase($root)
    {
        if(strpos($root,$this->work.'/job-')!==0||is_link($root))throw new RuntimeException('Ruta temporal inesperada.');
        $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
        foreach($it as $f){if($f->isDir()&&!$f->isLink())rmdir($f->getPathname());else unlink($f->getPathname());}rmdir($root);
    }
    public function export($output,$key,$final=false,$quiesced=false)
    {
        $this->path($output);$this->privatePath($output);$this->privatePath(dirname($output));if(file_exists($output))throw new RuntimeException('El paquete ya existe.');
        if($final&&!$quiesced)throw new RuntimeException('El corte exige --quiesced después de suspender escrituras, cron, webhooks y correo según el procedimiento.');
        $job=$this->work.'/job-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(3));$this->mkdirPrivate($job);
        $package=$job.'/package';$this->mkdirPrivate($package);$manifest=array('format'=>1,'created_utc'=>gmdate('c'),'kind'=>$final?'final_operator_confirmed_quiesced':'preliminary','files'=>array(),'databases'=>array(),'archives'=>array(),'limitations'=>array('Copia en vivo: repetir con escrituras, cron, webhooks y correo suspendidos para el corte.','Drive contiene los originales; este paquete conserva índices y autorización, no duplica toda la multimedia.','Correo: Maildir y configuración de cuenta para importación, no un restore automático de cPanel.','Se conserva el código efectivo del servidor; el overlay portable sólo cambia archivos indicados en runtime-files.json.'));
        try{
            foreach(array('backend','wordpress') as $site){$exclude=array('.git','unzipper-master','node_modules','error_log','*.log','wpdebug.txt','debug_log.txt','test_db_log.txt','storage/cache','wp-content/cache','wp-content/wpo-cache','wp-content/upgrade','wp-content/ai1wm-backups');
                $manifest['archives'][$site]=$this->archive($this->c['paths'][$site],$package.'/'.$site.'.tar.gz',$exclude,!$final);}
            foreach(array('mail','mail_config') as $site)if(!empty($this->c['paths'][$site])&&is_dir($this->c['paths'][$site]))$manifest['archives'][$site]=$this->archive($this->c['paths'][$site],$package.'/'.$site.'.tar.gz',array(),!$final);
            $state=$job.'/state';$this->mkdirPrivate($state);
            foreach(array('media.sqlite','migration.sqlite') as $name)if(is_file($this->c['paths']['drive'].'/'.$name))$this->snapshotSqlite($this->c['paths']['drive'].'/'.$name,$state.'/'.$name);
            foreach(array('client.json','tokens.json','library.json','oauth_production.json','integration_ready.json','enabled') as $name)if(is_file($this->c['paths']['drive'].'/'.$name))copy($this->c['paths']['drive'].'/'.$name,$state.'/'.$name);
            if(!is_file($state.'/media.sqlite')||!is_file($state.'/tokens.json'))throw new RuntimeException('Falta el índice o autorización de Drive.');
            $this->archive($state,$package.'/drive.tar.gz',array());$backup=$job.'/backup';$this->mkdirPrivate($backup);
            foreach(array('config.json','state.json','recovery.key') as $name)if(is_file($this->c['paths']['backup'].'/'.$name))copy($this->c['paths']['backup'].'/'.$name,$backup.'/'.$name);
            if(!is_file($backup.'/recovery.key'))throw new RuntimeException('Falta la llave de los respaldos anteriores.');$this->archive($backup,$package.'/backup.tar.gz',array());
            $seen=array();foreach($this->c['databases'] as $d){
                $identity=$d['host'].':'.(isset($d['port'])?(int)$d['port']:3306).'/'.$d['name'];
                if(isset($seen[$identity])){$manifest['databases'][$seen[$identity]]['keys'][]=$d['key'];continue;}$seen[$identity]=count($manifest['databases']);
                $pdo=$this->connection($d);$q=$pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_TYPE='BASE TABLE'");$q->execute(array($d['name']));$expected=(int)$q->fetchColumn();
                $cnf=$job.'/mysql.cnf';$this->clientConfig($d,$cnf);$sql=$package.'/db-'.$d['key'].'.sql';$dump=$this->binary('mysqldump');$v=$this->command(array($dump,'--version'));
                $args=array($dump,'--defaults-extra-file='.$cnf,'--lock-tables','--quick','--no-tablespaces','--hex-blob','--routines','--events','--triggers','--default-character-set=utf8mb4');
                if(stripos($v['output'],'mariadb')===false)$args[]='--set-gtid-purged=OFF';$args[]=$d['name'];
                try{$this->command($args,$sql);}finally{unlink($cnf);}
                $fh=fopen($sql,'rb');$tables=0;$completed=false;while(($line=fgets($fh))!==false){if(strpos($line,'CREATE TABLE ')===0)$tables++;if(strpos($line,'-- Dump completed on ')===0)$completed=true;}fclose($fh);
                if(!$completed||$tables!==$expected)throw new RuntimeException('Dump incompleto: '.$d['key']);
                $this->command(array($this->binary('gzip'),'-n','-6',$sql));$manifest['databases'][]=array('key'=>$d['key'],'keys'=>array($d['key']),'source_schema'=>$d['name'],'tables'=>$tables,'file'=>'db-'.$d['key'].'.sql.gz');
            }
            $runtime=$job.'/runtime';$this->mkdirPrivate($runtime);$list=json_decode(file_get_contents(__DIR__.'/runtime-files.json'),true);
            $runtimeRoot=isset($this->c['runtime_root'])?$this->c['runtime_root']:dirname(__DIR__,2);
            foreach($list as $name){$this->mkdirPrivate(dirname($runtime.'/'.$name));if(!copy($runtimeRoot.'/'.$name,$runtime.'/'.$name))throw new RuntimeException('Falta un archivo del overlay portable.');}
            $this->archive($runtime,$package.'/runtime.tar.gz',array());
            foreach(glob($package.'/*') as $file)$manifest['files'][basename($file)]=array('bytes'=>filesize($file),'sha256'=>hash_file('sha256',$file));
            $pdo=new PDO('sqlite:'.$state.'/media.sqlite');$manifest['drive_alias_count']=(int)$pdo->query('SELECT COUNT(*) FROM media')->fetchColumn();
            try{$this->command(array($this->binary('crontab'),'-l'),$package.'/source-crontab.txt');$manifest['files']['source-crontab.txt']=array('bytes'=>filesize($package.'/source-crontab.txt'),'sha256'=>hash_file('sha256',$package.'/source-crontab.txt'));}catch(Throwable $e){$manifest['limitations'][]='No se capturó el crontab del origen.';}
            $this->json($package.'/manifest.json',$manifest);$plain=$job.'/package.tar.gz';$this->archive($package,$plain,array());
            $this->mkdirPrivate(dirname($output));$args=$this->gpgArgs($key);$this->command(array_merge($args,array('--symmetric','--cipher-algo','AES256','--force-mdc','--compress-algo','none','--output',$output.'.partial',$plain)));
            $check=$job.'/decrypt-check.tar.gz';$this->command(array_merge($args,array('--output',$check,'--decrypt',$output.'.partial')));
            if(!hash_equals(hash_file('sha256',$plain),hash_file('sha256',$check)))throw new RuntimeException('Falló la verificación del cifrado.');
            if(!rename($output.'.partial',$output))throw new RuntimeException('No se pudo finalizar el paquete.');chmod($output,0600);
            $receipt=array('file'=>basename($output),'sha256'=>hash_file('sha256',$output),'bytes'=>filesize($output),'manifest'=>$manifest);$this->json($output.'.json',$receipt);
            return $receipt;
        }finally{if(is_dir($job))$this->erase($job);}
    }
    /** Comprueba miembros TAR antes de delegar extracción a tar. Rechaza links, dispositivos y traversal. */
    public function validateTar($file)
    {
        $f=gzopen($file,'rb');if(!$f)throw new RuntimeException('Archivo gzip inválido.');$long=null;$members=0;
        try{while(!gzeof($f)){$h=$this->readGzip($f,512);if($h===str_repeat("\0",512))break;if(strlen($h)!==512)throw new RuntimeException('Cabecera TAR truncada.');
                $sum=octdec(trim(substr($h,148,8)," \0"));$calculated=array_sum(unpack('C*',substr_replace($h,str_repeat(' ',8),148,8)));if($sum!==$calculated)throw new RuntimeException('Checksum TAR inválido.');
                $sizeField=trim(substr($h,124,12)," \0");if($sizeField!==''&&!preg_match('/^[0-7]+$/',$sizeField))throw new RuntimeException('Tamaño TAR no soportado.');$size=octdec($sizeField);$type=$h[156];
                $name=$long!==null?$long:rtrim(substr($h,0,100),"\0");$long=null;
                if($type==='L'){$long=rtrim($this->readGzip($f,$size),"\0");$this->readGzip($f,(512-$size%512)%512);continue;}
                // GNU tar usa el campo prefix para otros datos; sólo ustar POSIX lo interpreta como ruta.
                if(substr($h,257,6)==="ustar\0"){$prefix=rtrim(substr($h,345,155),"\0");if($prefix!=='')$name=$prefix.'/'.$name;}
                if($name===''||$name[0]==='/'||strpos($name,'\\')!==false||preg_match('~(?:^|/)\.\.(/|$)~',$name)||preg_match('/[\x00-\x08\x0a-\x1f]/',$name))throw new RuntimeException('Ruta TAR inválida.');
                if(!in_array($type,array("\0",'0','5'),true))throw new RuntimeException('TAR contiene enlaces o tipos especiales no permitidos.');
                for($left=$size;$left>0;$left-=$n){$n=min($left,1048576);$this->readGzip($f,$n);}$this->readGzip($f,(512-$size%512)%512);$members++;
            }}finally{gzclose($f);}if(!$members)throw new RuntimeException('Archivo TAR vacío.');return $members;
    }
    private function readGzip($f,$length)
    {
        $v='';while(strlen($v)<$length){$s=gzread($f,$length-strlen($v));if($s===false||$s==='')throw new RuntimeException('Contenido gzip truncado.');$v.=$s;}return $v;
    }
    private function extract($file,$destination)
    {
        $this->path($destination);if(file_exists($destination)||is_link($destination))throw new RuntimeException('La restauración exige un directorio nuevo: '.$destination);
        $this->validateTar($file);$this->mkdirPrivate($destination);$this->command(array($this->binary('tar'),'--no-same-owner','--no-same-permissions','-xzf',$file,'-C',$destination));
    }
    public function verify($package)
    {
        if(!is_file($package.'/manifest.json'))throw new RuntimeException('Falta manifest.json.');$m=json_decode(file_get_contents($package.'/manifest.json'),true);
        if(!is_array($m)||!isset($m['format'])||$m['format']!==1||empty($m['files']))throw new RuntimeException('Formato desconocido.');
        foreach($m['files'] as $name=>$expected){if(basename($name)!==$name)throw new RuntimeException('Nombre de archivo inválido.');$file=$package.'/'.$name;
            if(!is_file($file)||is_link($file)||filesize($file)!==$expected['bytes']||!hash_equals($expected['sha256'],hash_file('sha256',$file)))throw new RuntimeException('Checksum no coincide: '.$name);
            if(substr($name,-7)==='.tar.gz'){$this->command(array($this->binary('gzip'),'-t',$file));$this->validateTar($file);}elseif(substr($name,-3)==='.gz')$this->command(array($this->binary('gzip'),'-t',$file));
        }return $m;
    }
    public function unpack($file,$key,$destination)
    {
        $this->privatePath($destination);$this->path($destination);if(file_exists($destination))throw new RuntimeException('El destino del paquete debe ser nuevo.');
        $this->mkdirPrivate($this->work);$plain=tempnam($this->work,'unpack-');
        try{$this->command(array_merge($this->gpgArgs($key),array('--output',$plain,'--decrypt',$file)));$this->extract($plain,$destination);return $this->verify($destination);}finally{if(is_file($plain))unlink($plain);}
    }
    public function restoreFiles($package,$apply)
    {
        $m=$this->verify($package);$plan=array();foreach(array('backend','wordpress','drive','backup') as $key){if(file_exists($this->c['paths'][$key]))throw new RuntimeException('El directorio destino debe ser nuevo: '.$key);$plan[$key]=$this->c['paths'][$key];}
        if($apply){foreach($plan as $key=>$target)$this->extract($package.'/'.$key.'.tar.gz',$target);}
        return array('applied'=>$apply,'paths'=>$plan,'mail'=>'Importar mail.tar.gz y mail_config.tar.gz con el proveedor. No sobrescribir /etc de otro servidor.','next'=>'configure --package=... --apply');
    }
    public function restoreDatabases($package,$apply)
    {
        $m=$this->verify($package);$plan=array();$targets=array();$mapping=array();foreach($this->c['databases'] as $d)$mapping[$d['key']]=$d;
        foreach($m['databases'] as $entry){if(!isset($entry['file'])||basename($entry['file'])!==$entry['file']||!isset($m['files'][$entry['file']]))throw new RuntimeException('Archivo de base de datos inválido.');if(!isset($mapping[$entry['key']]))throw new RuntimeException('Falta destino para '.$entry['key']);$d=$mapping[$entry['key']];$identity=$d['host'].':'.(isset($d['port'])?(int)$d['port']:3306).'/'.$d['name'];
            if(isset($targets[$identity]))throw new RuntimeException('Dos bases del paquete apuntan al mismo destino.');$targets[$identity]=true;
            foreach(isset($entry['keys'])?$entry['keys']:array($entry['key']) as $alias){if(!isset($mapping[$alias]))throw new RuntimeException('Falta el alias de conexión '.$alias);$a=$mapping[$alias];if($a['host'].':'.(isset($a['port'])?(int)$a['port']:3306).'/'.$a['name']!==$identity)throw new RuntimeException('Conservar la base compartida entre '.implode(', ',$entry['keys']).'.');}
            $pdo=$this->connection($d);
            $q=$pdo->query('SHOW FULL TABLES');if($q->fetch()!==false)throw new RuntimeException('La base destino debe estar vacía: '.$d['key']);
            $plan[]=array('key'=>$d['key'],'target'=>$d['name'],'expected_tables'=>$entry['tables']);}
        if($apply){foreach($m['databases'] as $entry){$d=$mapping[$entry['key']];$cnf=$this->work.'/restore.cnf';$sql=$this->work.'/restore.sql';$this->clientConfig($d,$cnf);
                try{$this->command(array($this->binary('gzip'),'-dc',$package.'/'.$entry['file']),$sql);$this->command(array($this->binary('mysql'),'--defaults-extra-file='.$cnf,'--default-character-set=utf8mb4','--database='.$d['name']),null,$sql);
                    $pdo=$this->connection($d);$q=$pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_TYPE='BASE TABLE'");$q->execute(array($d['name']));if((int)$q->fetchColumn()!==$entry['tables'])throw new RuntimeException('El conteo restaurado no coincide.');
                }finally{if(is_file($cnf))unlink($cnf);if(is_file($sql))unlink($sql);}}
        }return array('applied'=>$apply,'databases'=>$plan,'note'=>'Importación no atómica: si falla, recrear las bases vacías antes de reintentar. Revisar DEFINER y referencias cruzadas al cambiar nombres.');
    }
    private function envValue($v)
    {
        $v=(string)$v;if(preg_match('/[\r\n\x00]/',$v))throw new RuntimeException('Las variables de entorno deben ocupar una línea.');return '"'.$v.'"';
    }
    private function patchWordpress($content,$db)
    {
        $values=array('DB_NAME'=>$db['name'],'DB_USER'=>$db['user'],'DB_PASSWORD'=>isset($db['password'])?$db['password']:'','DB_HOST'=>$db['host'].':'.(isset($db['port'])?(int)$db['port']:3306));
        foreach($values as $name=>$value){$pattern='~define\s*\(\s*([\'"])'.preg_quote($name,'~').'\1\s*,\s*(?:\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*")\s*\)\s*;~';
            $content=preg_replace_callback($pattern,function()use($name,$value){return "define('".$name."', ".var_export($value,true).");";},$content,-1,$count);
            if($count!==1)throw new RuntimeException('wp-config.php requiere revisión manual para '.$name.'; no se aplicó la configuración.');}
        if(strpos($content,"require_once __DIR__.'/egs-hosting.php';")===false)$content=preg_replace('/<\?php/',"<?php\nrequire_once __DIR__.'/egs-hosting.php';",$content,1);
        return $content;
    }
    private function validateMapping($manifest)
    {
        $mapping=array();foreach($this->c['databases'] as $d)$mapping[$d['key']]=$d;$targets=array();
        foreach($manifest['databases'] as $entry){$identity=null;
            foreach(isset($entry['keys'])?$entry['keys']:array($entry['key']) as $alias){
                if(!isset($mapping[$alias]))throw new RuntimeException('Falta la conexión '.$alias);$d=$mapping[$alias];$id=$d['host'].':'.(isset($d['port'])?(int)$d['port']:3306).'/'.$d['name'];
                if($identity!==null&&$identity!==$id)throw new RuntimeException('Conservar los alias de la base compartida.');$identity=$id;
            }
            if(isset($targets[$identity]))throw new RuntimeException('Dos bases del paquete apuntan al mismo destino.');$targets[$identity]=true;
        }
    }
    public function configure($package,$apply)
    {
        $m=$this->verify($package);$this->validateMapping($m);$mode=isset($this->c['mode'])?$this->c['mode']:'staging';
        if(!in_array($mode,array('staging','production'),true))throw new RuntimeException('mode debe ser staging o production.');
        $p=$this->c['paths'];foreach(array('backend','wordpress','drive','backup') as $key)if(!is_dir($p[$key]))throw new RuntimeException('Primero restaura los directorios: '.$key);
        foreach(array('backend','wordpress') as $key){$url=isset($this->c['urls'][$key])?$this->c['urls'][$key]:'';if(!filter_var($url,FILTER_VALIDATE_URL)||parse_url($url,PHP_URL_SCHEME)!=='https')throw new RuntimeException('Se requiere URL HTTPS para '.$key);}
        $job=$this->work.'/job-configure-'.bin2hex(random_bytes(4));$this->extract($package.'/runtime.tar.gz',$job);$files=array();
        try{
            $list=json_decode(file_get_contents(__DIR__.'/runtime-files.json'),true);foreach($list as $name){if(strpos($name,'integraciones/')===0)continue;$files[$p['backend'].'/'.$name]=file_get_contents($job.'/'.$name);}
            $dbs=array();$connections=array();$schemas=array();$env=array('EGS_HOSTING_MODE'=>$mode,'EGS_BACKEND_URL'=>$this->c['urls']['backend'],'EGS_WORDPRESS_URL'=>$this->c['urls']['wordpress'],'EGS_WORDPRESS_ROOT'=>$p['wordpress'],'EGS_DRIVE_STATE_DIR'=>$p['drive'],'EGS_BACKUP_STATE_DIR'=>$p['backup'],'EGS_BACKUP_TIMEZONE'=>'America/Mexico_City','EGS_BACKUP_HOUR'=>3,'EGS_BACKUP_WEEKDAY'=>7);
            foreach($this->c['databases'] as $d){$dbs[$d['key']]=$d;$schemas[$d['name']]=$d['key'];$connections[$d['key']]=array('host'=>$d['host'],'port'=>isset($d['port'])?(int)$d['port']:3306,'dbname'=>$d['name'],'user'=>$d['user'],'pass'=>isset($d['password'])?$d['password']:'');
                if(in_array($d['key'],array('sistema','ecommerce','wordpress'),true)){foreach(array('HOST'=>'host','PORT'=>'port','NAME'=>'name','USER'=>'user','PASS'=>'password') as $suffix=>$field)$env['DB_'.strtoupper($d['key']).'_'.$suffix]=isset($d[$field])?$d[$field]:($field==='port'?3306:'');}}
            foreach(array('ecommerce','wordpress','site') as $key)if(!isset($dbs[$key]))throw new RuntimeException('Falta la conexión '.$key);
            $env['EGS_BACKUP_DATABASES']=json_encode($schemas,JSON_UNESCAPED_SLASHES);$env['EGS_BACKUP_CONNECTIONS']=json_encode($connections,JSON_UNESCAPED_SLASHES);
            foreach(isset($this->c['environment'])?$this->c['environment']:array() as $key=>$v){if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$key))throw new RuntimeException('Nombre de variable inválido.');$env[$key]=$v;}
            // El modo de seguridad no puede quedar sobrescrito accidentalmente por environment.
            $env['EGS_HOSTING_MODE']=$mode;
            foreach(isset($this->c['binaries'])?$this->c['binaries']:array() as $name=>$path)if($path)$env['EGS_BIN_'.strtoupper(str_replace('-','_',$name))]=$path;
            $envFile=$this->work.'/hosting.env';$envText="# Configuración privada generada; no publicar ni versionar.\n";foreach($env as $key=>$value)$envText.=$key.'='.$this->envValue($value)."\n";
            $files[$envFile]=$envText;$files[$p['backend'].'/config/environment.php']="<?php\nreturn ".var_export($envFile,true).";\n";
            $bridge="<?php\n// EGS: ubicación del backend y entorno privado.\nif(!defined('EGS_BACKEND_ROOT'))define('EGS_BACKEND_ROOT',".var_export($p['backend'],true).");\nputenv('EGS_ENV_FILE='.".var_export($envFile,true).");\nrequire_once EGS_BACKEND_ROOT.'/config/env.php';\nif(!defined('WP_ENVIRONMENT_TYPE'))define('WP_ENVIRONMENT_TYPE',".var_export($mode==='production'?'production':'staging',true).");\n";
            if($mode==='staging')$bridge.="if(!defined('DISABLE_WP_CRON'))define('DISABLE_WP_CRON',true);\nif(!defined('WP_HTTP_BLOCK_EXTERNAL'))define('WP_HTTP_BLOCK_EXTERNAL',true);\n";
            $files[$p['wordpress'].'/egs-hosting.php']=$bridge;
            $files[$p['wordpress'].'/egs-drive-media.php']=file_get_contents($job.'/integraciones/wordpress/egs-drive-media.php');
            $files[$p['wordpress'].'/wp-content/mu-plugins/egs-drive-media.php']=file_get_contents($job.'/integraciones/wordpress/wp-content/mu-plugins/egs-drive-media.php');
            $files[$p['wordpress'].'/wp-content/mu-plugins/egs-migration-staging.php']="<?php\n// Evita correos y conexiones HTTP de WordPress durante pruebas de migración.\nif(!defined('ABSPATH'))exit;\nif(getenv('EGS_HOSTING_MODE')!=='production'){\n    add_filter('pre_wp_mail',function(){return false;});\n    add_filter('pre_http_request',function(){return new WP_Error('egs_staging','Conexiones externas desactivadas durante pruebas.');});\n}\n";
            $files[$p['wordpress'].'/wp-config.php']=$this->patchWordpress(file_get_contents($p['wordpress'].'/wp-config.php'),$dbs['site']);
            foreach(array('backend','wordpress') as $site){$ht=$p[$site].'/.htaccess';$text=is_file($ht)?file_get_contents($ht):'';
                $text=preg_replace('~# BEGIN EGS HOSTING.*?# END EGS HOSTING\s*~s','',$text);
                $text=preg_replace('~# BEGIN cPanel-generated php ini directives.*?# END cPanel-generated php ini directives, do not edit\s*~s','',$text);
                $text=preg_replace('~# php -- BEGIN cPanel-generated handler.*?# php -- END cPanel-generated handler, do not edit\s*~s','',$text);
                $text=preg_replace('~^\s*AddHandler\s+application/x-httpd-ea-php\d+.*$~m','',$text);
                // Los .user.ini del origen contienen handlers/rutas de sesiones que administra el nuevo proveedor.
                $files[$p[$site].'/.user.ini']="; EGS: seleccionar versión de PHP y sesiones en el panel del proveedor.\nlog_errors=On\ndisplay_errors=Off\n";
                $text=preg_replace('~RewriteCond\s+\S*/\.drive-media-enabled\s+-f~','RewriteCond %{DOCUMENT_ROOT}/.drive-media-enabled -f',$text);
                if($site==='wordpress'&&strpos($text,'# BEGIN EGS Drive Media')===false)$text="<IfModule mod_rewrite.c>\nRewriteEngine On\n# BEGIN EGS Drive Media\nRewriteCond %{DOCUMENT_ROOT}/.drive-media-enabled -f\nRewriteRule ^wp-content/uploads/.+ egs-drive-media.php [END,NC]\n# END EGS Drive Media\n</IfModule>\n".$text;
                $block="# BEGIN EGS HOSTING\nOptions -Indexes\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteRule ^(?:config|ops|scripts|tests|\\.git)(?:/|$) - [F,L,NC]\nRewriteRule ^(?:\\.env(?:\\..*)?|egs-hosting\\.php)$ - [F,L,NC]\n";
                if($mode==='staging')$block.="RewriteRule ^wp-cron\\.php$ - [R=503,L,NC]\nRewriteCond %{REQUEST_METHOD} !^(GET|HEAD|OPTIONS)$ [NC]\nRewriteRule ^ - [R=503,L]\n";
                $block.="</IfModule>\n# END EGS HOSTING\n";$files[$ht]=$block.ltrim($text,"\r\n");$files[$p[$site].'/.drive-media-enabled']="enabled\n";
            }
            $controller=$p['backend'].'/controladores/ordenes.controlador.php';$text=file_get_contents($controller);
            if(strpos($text,'EgsMail::configure($mail)')===false){$pattern="~\\\$mail->SMTPSecure\\s*=\\s*'tls';\\s*\\\$mail->isMail\\(\\);\\s*\\\$mail->setFrom\\('noreplay\\.ordenes@backend\\.comercializadoraegs\\.com'\\);\\s*\\\$mail->addReplyTo\\('noreplay\\.ordenes@backend\\.comercializadoraegs\\.com'\\);~";
                $text=preg_replace_callback($pattern,function(){return "require_once dirname(__DIR__).'/config/Mail.php';\n            EgsMail::configure(\$mail);";},$text,-1,$count);if($count!==1)throw new RuntimeException('Revisar adaptación SMTP del controlador; no se aplicaron cambios.');$files[$controller]=$text;}
            $php=$this->binary('php');$quote=function($v){return str_replace('%','\\%',escapeshellarg($v));};
            $cron="# EGS: activar únicamente después del corte y desactivar las tareas del origen.\n";
            $cron.="*/5 * * * * ".$quote($php).' '.$quote($p['backend'].'/scripts/drive-reconciliar.php').' >> '.$quote($p['drive'].'/reconcile.log')." 2>&1\n";
            $cron.="*/15 * * * * ".$quote($php).' '.$quote($p['backend'].'/scripts/backup-drive.php').' --scheduled >> '.$quote($p['backup'].'/backup.log')." 2>&1\n";
            $rotate=$this->work.'/logrotate.conf';$state=$this->work.'/logrotate.state';
            $cron.="*/15 * * * * ".$quote($this->binary('flock')).' -n '.$quote($this->work.'/rotate.lock').' '.$quote($this->binary('logrotate')).' -s '.$quote($state).' '.$quote($rotate)." >/dev/null 2>&1\n";
            $files[$this->work.'/crontab.pending']=$cron;
            $logs=array($p['backend'].'/error_log',$p['wordpress'].'/error_log',$p['wordpress'].'/wpdebug.txt',$p['wordpress'].'/wp-content/debug.log',$p['drive'].'/reconcile.log',$p['backup'].'/backup.log');$rotation='';
            foreach($logs as $log){if(strpos($log,'"')!==false||strpos($log,'\\')!==false)throw new RuntimeException('Ruta no compatible con logrotate.');$rotation.='"'.$log."\" {\n size 10M\n rotate 1\n compress\n missingok\n notifempty\n copytruncate\n}\n";}$files[$rotate]=$rotation;
            $changes=array();foreach(array_keys($files) as $target){if(is_link($target))throw new RuntimeException('Se rechazó un archivo de configuración enlazado.');$dir=dirname($target);while($dir!=='/'&&$dir!=='.'){if(is_link($dir))throw new RuntimeException('Se rechazó un directorio enlazado.');$dir=dirname($dir);}}
            foreach($files as $file=>$value)if(!is_file($file)||hash_file('sha256',$file)!==hash('sha256',$value))$changes[]=$file;
            if($apply){$backup=$this->work.'/before-configure-'.gmdate('YmdHis').'-'.bin2hex(random_bytes(3));$this->mkdirPrivate($backup);$saved=array();
                foreach($changes as $file){if(is_link($file))throw new RuntimeException('Se rechazó un archivo de configuración enlazado.');if(is_file($file)){$n=count($saved).'.original';copy($file,$backup.'/'.$n);$saved[$file]=$n;}$this->write($file,$files[$file]);}
                $this->json($backup.'/files.json',$saved);
                foreach(array('backend','wordpress') as $site){$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($p[$site],FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::SELF_FIRST);chmod($p[$site],0755);foreach($it as $f)if(!$f->isLink())chmod($f->getPathname(),$f->isDir()?0755:0644);}
                foreach(array($p['wordpress'].'/wp-config.php',$p['backend'].'/config/environment.php',$p['backend'].'/.env') as $secret)if(is_file($secret))chmod($secret,0600);
                $this->json($this->work.'/configuration.json',array('mode'=>$mode,'applied_utc'=>gmdate('c'),'changed_files'=>$changes));
            }
            return array('applied'=>$apply,'mode'=>$mode,'changed_files'=>$changes,'cron_installed'=>false,'cron_fragment'=>$this->work.'/crontab.pending','notice'=>'Conservar ambos dominios. Revisar SSL, SMTP, webhooks, WordPress/WooCommerce y prueba de orden antes de activar producción.');
        }finally{$this->erase($job);}
    }
}
