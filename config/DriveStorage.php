<?php
/** Google Drive storage for EGS. Compatible with PHP 7.1; secrets and metadata live outside the web root. */
class EgsDriveStorage
{
    const REDIRECT_URI = 'https://backend.comercializadoraegs.com/drive-conectar.php';
    const SCOPE = 'https://www.googleapis.com/auth/drive.file';
    private static $db;
    private static $transport;
    private static $curl;
    public static function root() { return dirname(__DIR__); }
    public static function stateDir() { return getenv('EGS_DRIVE_STATE_DIR') ?: dirname(self::root()).'/.egs-drive'; }
    public static function enabled() { return is_file(self::stateDir().'/enabled'); }
    public static function setTransportForTests($callback) { self::$transport=$callback; }
    public static function init()
    {
        $dir=self::stateDir();
        if (!is_dir($dir) && !mkdir($dir,0700,true)) throw new RuntimeException('No se pudo preparar el almacenamiento de configuración.');
        chmod($dir,0700);
    }
    public static function readJson($name)
    {
        $p=self::stateDir().'/'.$name.'.json';
        return is_file($p) ? (json_decode(file_get_contents($p),true) ?: array()) : array();
    }
    public static function saveJson($name,$value)
    {
        self::init();$path=self::stateDir().'/'.$name.'.json';$tmp=tempnam(self::stateDir(),'save-');chmod($tmp,0600);
        try {
            $json=json_encode($value,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            if ($json===false || file_put_contents($tmp,$json)===false || !rename($tmp,$path)) throw new RuntimeException('No se pudo guardar la configuración.');
            chmod($path,0600);
        } finally { if (is_file($tmp)) unlink($tmp); }
    }
    public static function saveClient($input)
    {
        $v=json_decode($input,true);$v=isset($v['web'])?$v['web']:$v;
        if (!is_array($v) || empty($v['client_id']) || empty($v['client_secret']) || !preg_match('/^[A-Za-z0-9._-]+\.apps\.googleusercontent\.com$/',$v['client_id'])) throw new InvalidArgumentException('Se necesita un cliente OAuth de tipo Aplicación web.');
        if (isset($v['redirect_uris']) && !in_array(self::REDIRECT_URI,$v['redirect_uris'],true)) throw new InvalidArgumentException('El cliente OAuth debe incluir la URL de retorno indicada en esta página.');
        $existing=self::readJson('client');
        if (!empty(self::readJson('tokens')) && (!isset($existing['client_id']) || $existing['client_id']!==$v['client_id'])) throw new RuntimeException('No se puede cambiar el cliente de una biblioteca conectada.');
        self::saveJson('client',array('client_id'=>$v['client_id'],'client_secret'=>$v['client_secret']));
    }
    public static function ownerLink()
    {
        if(PHP_SAPI!=='cli')throw new RuntimeException('El acceso del propietario solo se puede generar desde la terminal del servidor.');
        $token=bin2hex(random_bytes(32));self::saveJson('owner_setup',array('sha256'=>hash('sha256',$token),'expires_at'=>time()+86400));
        return self::REDIRECT_URI.'?owner_setup='.$token;
    }
    public static function ownerTokenValid($token)
    {
        if(!is_string($token)||!preg_match('/^[a-f0-9]{64}$/',$token))return false;
        $v=self::readJson('owner_setup');return !empty($v['sha256'])&&isset($v['expires_at'])&&$v['expires_at']>=time()&&hash_equals($v['sha256'],hash('sha256',$token));
    }
    public static function claimOwnerToken($token)
    {
        self::init();$lock=fopen(self::stateDir().'/owner-setup.lock','c');chmod(self::stateDir().'/owner-setup.lock',0600);
        if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo validar el acceso del propietario.');
        try{if(!self::ownerTokenValid($token))throw new RuntimeException('Este acceso venció o ya fue utilizado.');self::saveJson('owner_setup',array('consumed_at'=>time()));}
        finally{flock($lock,LOCK_UN);fclose($lock);}
    }
    public static function authorizationUrl($state)
    {
        $client=self::readJson('client');if(empty($client['client_id']))throw new RuntimeException('Primero configura el cliente OAuth.');
        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query(array('client_id'=>$client['client_id'],'redirect_uri'=>self::REDIRECT_URI,'response_type'=>'code','scope'=>self::SCOPE,'access_type'=>'offline','prompt'=>'consent','state'=>$state), '', '&', PHP_QUERY_RFC3986);
    }
    public static function rawRequest($method,$url,$headers=array(),$body=null)
    {
        $parts=parse_url($url);
        if (!isset($parts['scheme'],$parts['host']) || $parts['scheme']!=='https' || !in_array($parts['host'],array('www.googleapis.com','oauth2.googleapis.com'),true) || isset($parts['user']) || isset($parts['pass'])) throw new RuntimeException('Destino de API inválido.');
        if (self::$transport) return call_user_func(self::$transport,$method,$url,$headers,$body);
        if(!self::$curl)self::$curl=curl_init();
        for ($attempt=0;$attempt<3;$attempt++) {
            $responseHeaders=array();$ch=self::$curl;curl_reset($ch);
            curl_setopt_array($ch,array(CURLOPT_URL=>$url,CURLOPT_MAXCONNECTS=>5,CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>$headers,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>90,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,CURLOPT_HEADERFUNCTION=>function($ch,$line)use(&$responseHeaders){$pair=explode(':',$line,2);if(count($pair)===2)$responseHeaders[strtolower(trim($pair[0]))]=trim($pair[1]);return strlen($line);}));
            if ($body!==null) curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            $result=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
            if ($result!==false && $status!==429 && $status<500) return array('status'=>$status,'headers'=>$responseHeaders,'body'=>$result);
            if ($attempt<2) usleep((250000*(1<<$attempt)));
        }
        throw new RuntimeException('Google Drive no respondió; el archivo original se conserva.');
    }
    private static function checkedJson($r)
    {
        if($r['status']<200 || $r['status']>=300) throw new RuntimeException('Google Drive rechazó la operación (HTTP '.(int)$r['status'].').');
        $v=json_decode($r['body'],true);if(!is_array($v))throw new RuntimeException('Respuesta inválida de Google Drive.');return $v;
    }
    private static function tokenRequest($params)
    {
        $c=self::readJson('client');if(empty($c['client_id'])||empty($c['client_secret']))throw new RuntimeException('Google Drive aún no está configurado.');
        return self::checkedJson(self::rawRequest('POST','https://oauth2.googleapis.com/token',array('Content-Type: application/x-www-form-urlencoded'),http_build_query(array_merge($c,$params),'','&')));
    }
    public static function exchangeCode($code)
    {
        $v=self::tokenRequest(array('grant_type'=>'authorization_code','code'=>$code,'redirect_uri'=>self::REDIRECT_URI));
        if(empty($v['refresh_token']) || empty($v['access_token']))throw new RuntimeException('Google no entregó autorización permanente. Vuelve a conectar con consentimiento.');
        $v['expires_at']=time()+(int)$v['expires_in'];$v['authorized_at']=time();self::saveJson('tokens',$v);
    }
    public static function productionReady()
    {
        $p=self::readJson('oauth_production');$tokens=self::readJson('tokens');
        return !empty($p['confirmed'])&&!empty($p['confirmed_at'])&&!empty($tokens['authorized_at'])&&$tokens['authorized_at']>=$p['confirmed_at'];
    }
    public static function token()
    {
        self::init();$lock=fopen(self::stateDir().'/token.lock','c');chmod(self::stateDir().'/token.lock',0600);
        if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo acceder a la conexión de Drive.');
        try {
            $v=self::readJson('tokens');
            if(!empty($v['access_token']) && isset($v['expires_at']) && $v['expires_at']>time()+120)return $v['access_token'];
            if(empty($v['refresh_token']))throw new RuntimeException('Conecta Google Drive antes de activar las cargas.');
            $fresh=self::tokenRequest(array('grant_type'=>'refresh_token','refresh_token'=>$v['refresh_token']));
            if(empty($fresh['access_token']))throw new RuntimeException('Es necesario reconectar Google Drive.');
            $v=array_merge($v,$fresh);$v['expires_at']=time()+(int)$fresh['expires_in'];self::saveJson('tokens',$v);return $v['access_token'];
        } finally {flock($lock,LOCK_UN);fclose($lock);}
    }
    public static function api($method,$path,$body=null)
    {
        $headers=array('Authorization: Bearer '.self::token());
        if($body!==null){$headers[]='Content-Type: application/json';$body=json_encode($body);}
        return self::checkedJson(self::rawRequest($method,'https://www.googleapis.com/drive/v3/'.$path,$headers,$body));
    }
    public static function database()
    {
        if(self::$db)return self::$db;self::init();
        $db=new PDO('sqlite:'.self::stateDir().'/media.sqlite');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        chmod(self::stateDir().'/media.sqlite',0600);$db->exec('PRAGMA busy_timeout=30000');
        $db->exec('CREATE TABLE IF NOT EXISTS media(path TEXT PRIMARY KEY,file_id TEXT NOT NULL,mime TEXT NOT NULL,bytes INTEGER NOT NULL,md5 TEXT NOT NULL,context TEXT NOT NULL,verified_at INTEGER NOT NULL); CREATE TABLE IF NOT EXISTS folders(path TEXT PRIMARY KEY,file_id TEXT NOT NULL); CREATE TABLE IF NOT EXISTS history(id INTEGER PRIMARY KEY AUTOINCREMENT,path TEXT,previous_file_id TEXT,event TEXT,created_at INTEGER); CREATE TABLE IF NOT EXISTS order_jobs(order_id INTEGER PRIMARY KEY,context TEXT NOT NULL,created_at INTEGER NOT NULL);');
        self::$db=$db;return $db;
    }
    public static function normalize($path,$site='backend')
    {
        if(!is_string($path) || preg_match('/[\x00-\x08\x0a-\x1f]/',$path) || strpos($path,'\\')!==false || strpos($path,'://')!==false)throw new InvalidArgumentException('Ruta de multimedia inválida.');
        $prefix=$site==='wordpress'?'wp-content/uploads/':'vistas/img/';
        if(strpos($path,$site.'/'.$prefix)===0)$path=substr($path,strlen($site)+1);
        if($site==='backend' && strpos($path,self::root().'/')===0)$path=substr($path,strlen(self::root())+1);
        // Legacy AJAX controllers write ../vistas/img while root controllers write vistas/img.
        if($site==='backend' && strpos($path,'../vistas/img/')===0)$path=substr($path,3);
        if(strpos($path,$prefix)!==0)throw new InvalidArgumentException('La ruta no pertenece a multimedia.');
        foreach(explode('/',$path) as $part)if($part==='..'||$part==='.'||$part==='')throw new InvalidArgumentException('La ruta contiene segmentos inválidos.');
        // Four legacy folders contain a literal tab. Accept only an existing,
        // contained original or its registered alias, including after migration.
        if(strpos($path,"\t")!==false&&!self::lookup($site.'/'.$path)){
            $root=$site==='wordpress'?dirname(self::root()).'/public_html':self::root();$actual=realpath($root.'/'.$path);$allowed=realpath($root.'/'.rtrim($prefix,'/'));
            if(!$actual||!$allowed||!is_file($actual)||strpos($actual,$allowed.'/')!==0)throw new InvalidArgumentException('La ruta especial no corresponde a una imagen existente.');
        }
        return $site.'/'.$path;
    }
    public static function lookup($key)
    {
        $q=self::database()->prepare('SELECT * FROM media WHERE path=?');$q->execute(array($key));return $q->fetch(PDO::FETCH_ASSOC)?:null;
    }
    public static function folder($path)
    {
        self::init();$lock=fopen(self::stateDir().'/folders.lock','c');chmod(self::stateDir().'/folders.lock',0600);
        if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo organizar la carpeta de Drive.');
        try{return self::folderUnlocked($path);}finally{flock($lock,LOCK_UN);fclose($lock);}
    }
    private static function folderUnlocked($path)
    {
        $db=self::database();$q=$db->prepare('SELECT file_id FROM folders WHERE path=?');$q->execute(array($path));$found=$q->fetchColumn();if($found)return $found;
        $config=self::readJson('library');if(empty($config['root_id']))throw new RuntimeException('Falta preparar la biblioteca de Drive.');
        $parent=$config['root_id'];$name=$path;
        if(strpos($path,'/')!==false){$parent=self::folderUnlocked(dirname($path));$name=basename($path);}
        $idKey=hash('sha256','folder:'.$path);$query="trashed=false and '".$parent."' in parents and appProperties has { key='egs_key' and value='".$idKey."' }";
        $r=self::api('GET','files?'.http_build_query(array('q'=>$query,'fields'=>'files(id)','pageSize'=>10),'','&',PHP_QUERY_RFC3986));
        $id=empty($r['files'])?self::api('POST','files',array('name'=>$name,'mimeType'=>'application/vnd.google-apps.folder','parents'=>array($parent),'appProperties'=>array('egs_key'=>$idKey)))['id']:$r['files'][0]['id'];
        $q=$db->prepare('INSERT OR IGNORE INTO folders(path,file_id)VALUES(?,?)');$q->execute(array($path,$id));return $id;
    }
    public static function prepareLibrary()
    {
        $about=self::api('GET','about?fields=user(emailAddress),storageQuota');
        $library=self::readJson('library');
        if(!empty($library['owner_email']) && $library['owner_email']!==$about['user']['emailAddress'])throw new RuntimeException('La cuenta conectada no coincide con la biblioteca existente.');
        if(empty($library['root_id'])) {
            $root=self::api('POST','files',array('name'=>'EGS Multimedia - comercializadoraegs.com','mimeType'=>'application/vnd.google-apps.folder','description'=>'Imágenes del backend y del sitio público. Los IDs se relacionan con sus rutas y registros en el índice de EGS.'));
            $library=array('root_id'=>$root['id'],'owner_email'=>$about['user']['emailAddress']);self::saveJson('library',$library);
        }
        return array('library'=>$library,'quota'=>$about['storageQuota']);
    }
    public static function verify($meta,$bytes,$md5)
    {
        if(empty($meta['id'])||!isset($meta['size'],$meta['md5Checksum']) || (int)$meta['size']!==$bytes || !hash_equals($md5,strtolower($meta['md5Checksum'])))throw new RuntimeException('La copia de Drive no coincide con el original; no se puede retirar del hosting.');
    }
    public static function mediaMime($mime)
    {
        return strpos($mime,'image/')===0||strpos($mime,'audio/')===0||strpos($mime,'video/')===0||$mime==='application/pdf';
    }
    public static function mediaPath($path)
    {
        return preg_match('/\.(?:jpe?g|jpe|png|gif|webp|avif|bmp|ico|svg|tiff?|heic|heif|jp2|jpx|j2k|psd|orig|pdf|mp3|mp2|m4a|m4b|aac|aif|aiff|ra|ram|wav|ogg|oga|flac|opus|mp4|m4v|mov|qt|webm|mpeg|mpg|mpe|avi|wmv|flv|asf|divx|ogv|3gp|3g2|mkv)$/i',$path)===1;
    }
    public static function put($key,$content,$mime,$context=array())
    {
        $key=self::normalize($key,strpos($key,'wordpress/')===0?'wordpress':'backend');self::init();
        $name='/upload-'.substr(hash('sha256',$key),0,2).'.lock';$lock=fopen(self::stateDir().$name,'c');chmod(self::stateDir().$name,0600);
        if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo confirmar la carga de la imagen.');
        try{return self::putUnlocked($key,$content,$mime,$context);}finally{flock($lock,LOCK_UN);fclose($lock);}
    }
    private static function putUnlocked($key,$content,$mime,$context)
    {
        $site=strpos($key,'wordpress/')===0?'wordpress':'backend';$key=self::normalize($key,$site);
        if(!self::mediaMime($mime))throw new InvalidArgumentException('Solo se permiten imágenes, PDF, audio y video en este almacenamiento.');
        if(!self::mediaPath($key))throw new InvalidArgumentException('La extensión no está habilitada para mostrarse desde Drive.');
        $bytes=strlen($content);if($bytes===0)throw new InvalidArgumentException('La imagen está vacía.');$md5=md5($content);$old=self::lookup($key);
        if(!$context && $old)$context=json_decode($old['context'],true)?:array();
        if($old && $old['md5']===$md5 && (int)$old['bytes']===$bytes){self::verify(self::api('GET','files/'.$old['file_id'].'?fields=id,size,md5Checksum'),$bytes,$md5);return $old;}
        $dir=isset($context['drive_folder'])?$context['drive_folder']:($site==='wordpress'?'Sitio_publico/'.dirname(substr($key,strlen('wordpress/wp-content/uploads/'))):'Backend/'.dirname(substr($key,strlen('backend/vistas/img/'))));
        if(substr($dir,-2)==='/.')$dir=substr($dir,0,-2);
        $parent=self::folder($dir);$name=isset($context['drive_name'])?$context['drive_name']:basename($key);
        if($old)$name=pathinfo($name,PATHINFO_FILENAME).'__version_'.date('Ymd_His').'_'.substr($md5,0,8).'.'.pathinfo($name,PATHINFO_EXTENSION);
        $metadata=array('name'=>$name,'mimeType'=>$mime,'parents'=>array($parent),'description'=>json_encode(array('ruta_original'=>$key,'relacion'=>$context),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),'appProperties'=>array('egs_path'=>hash('sha256',$key),'egs_md5'=>$md5));
        // Recover a verified object when a previous request uploaded it but lost its response.
        $query="trashed=false and appProperties has { key='egs_path' and value='".hash('sha256',$key)."' } and appProperties has { key='egs_md5' and value='".$md5."' }";
        $existing=self::api('GET','files?'.http_build_query(array('q'=>$query,'fields'=>'files(id,size,md5Checksum,mimeType)','pageSize'=>100),'','&',PHP_QUERY_RFC3986));
        foreach($existing['files'] as $candidate){if(isset($candidate['size'],$candidate['md5Checksum'])&&(int)$candidate['size']===$bytes&&hash_equals($md5,$candidate['md5Checksum'])){self::verify($candidate,$bytes,$md5);$meta=$candidate;break;}}
        if(!isset($meta)){
        $headers=array('Authorization: Bearer '.self::token(),'Content-Type: application/json','X-Upload-Content-Type: '.$mime,'X-Upload-Content-Length: '.$bytes);
        $r=self::rawRequest('POST','https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable&fields=id,size,md5Checksum,mimeType',$headers,json_encode($metadata));
        if($r['status']!==200 || empty($r['headers']['location']))throw new RuntimeException('No se pudo iniciar la carga a Drive.');
        $url=$r['headers']['location'];$offset=0;$meta=null;
        while($offset<$bytes){$chunk=substr($content,$offset,2*1024*1024);$end=$offset+strlen($chunk)-1;
            $r=self::rawRequest('PUT',$url,array('Authorization: Bearer '.self::token(),'Content-Type: '.$mime,'Content-Range: bytes '.$offset.'-'.$end.'/'.$bytes),$chunk);
            if($r['status']===308){if(empty($r['headers']['range'])||!preg_match('/^bytes=0-(\d+)$/',$r['headers']['range'],$m))throw new RuntimeException('No se pudo confirmar el avance de la carga.');$next=(int)$m[1]+1;if($next<=$offset||$next>$end+1)throw new RuntimeException('Avance de carga inválido.');$offset=$next;}
            else{$meta=self::checkedJson($r);break;}
        }
        if(!$meta)throw new RuntimeException('La carga a Drive quedó incompleta.');
        }
        self::verify($meta,$bytes,$md5);
        // A readback verifies the stored object before the local mapping is changed.
        self::verify(self::api('GET','files/'.$meta['id'].'?fields=id,size,md5Checksum'),$bytes,$md5);
        $db=self::database();$db->beginTransaction();
        try{
            if($old){$q=$db->prepare('INSERT INTO history(path,previous_file_id,event,created_at)VALUES(?,?,?,?)');$q->execute(array($key,$old['file_id'],'replaced',time()));}
            $q=$db->prepare('INSERT OR REPLACE INTO media(path,file_id,mime,bytes,md5,context,verified_at)VALUES(?,?,?,?,?,?,?)');$q->execute(array($key,$meta['id'],$mime,$bytes,$md5,json_encode($context),time()));$db->commit();
        }catch(Throwable $e){$db->rollBack();throw $e;}
        return self::lookup($key);
    }
    public static function bytes($row)
    {
        $r=self::rawRequest('GET','https://www.googleapis.com/drive/v3/files/'.rawurlencode($row['file_id']).'?alt=media',array('Authorization: Bearer '.self::token()));
        if($r['status']!==200 || strlen($r['body'])!==(int)$row['bytes'] || !hash_equals($row['md5'],md5($r['body'])))throw new RuntimeException('No se pudo leer una imagen íntegra desde Drive.');return $r['body'];
    }
    /** Serialize migration against live uploads of the same path. */
    public static function migrateFile($key,$context=array(),$releaseLocal=false)
    {
        $site=strpos($key,'wordpress/')===0?'wordpress':'backend';$key=self::normalize($key,$site);self::init();
        $root=$site==='wordpress'?dirname(self::root()).'/public_html':self::root();
        $file=$root.'/'.substr($key,strlen($site)+1);
        $lock=fopen(self::stateDir().'/upload-'.substr(hash('sha256',$key),0,2).'.lock','c');
        if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo bloquear la imagen para trasladarla.');
        chmod(self::stateDir().'/upload-'.substr(hash('sha256',$key),0,2).'.lock',0600);
        try{
            clearstatcache(true,$file);$prior=self::lookup($key);
            if(!is_file($file)){
                if(!$prior)throw new RuntimeException('El original desapareció y no existe una copia registrada en Drive.');
                self::bytes($prior);return array('status'=>'already_remote','bytes'=>(int)$prior['bytes'],'file_id'=>$prior['file_id']);
            }
            $allowed=realpath($root.($site==='wordpress'?'/wp-content/uploads':'/vistas/img'));$actual=realpath($file);
            if(is_link($file)||!$actual||!$allowed||strpos($actual,$allowed.'/')!==0)throw new DomainException('Ruta de origen inválida para trasladar.');
            $data=file_get_contents($file);if($data===false)throw new RuntimeException('No se pudo leer el original.');
            $bytes=strlen($data);$md5=md5($data);$mime=(new finfo(FILEINFO_MIME_TYPE))->buffer($data);
            if(!self::mediaMime($mime))throw new DomainException('El archivo no es multimedia admitida; se conserva en el servidor.');
            if($prior&&($prior['md5']!==$md5||(int)$prior['bytes']!==$bytes))throw new DomainException('El alias ya apunta a una versión distinta en Drive; se conserva el original para revisión.');
            $row=self::putUnlocked($key,$data,$mime,$context);
            if(self::bytes($row)!==$data)throw new RuntimeException('La descarga completa no coincide; se conserva el original.');
            $status='verified';
            if($releaseLocal){
                clearstatcache(true,$file);
                if(!is_file($file)||filesize($file)!==$bytes||md5_file($file)!==$md5)throw new DomainException('El original cambió durante el traslado; se conserva para revisión.');
                if(!unlink($file))throw new RuntimeException('No se pudo retirar la copia local verificada.');$status='released';
            }
            return array('status'=>$status,'bytes'=>$bytes,'file_id'=>$row['file_id']);
        }finally{flock($lock,LOCK_UN);fclose($lock);}
    }
    public static function encode($image,$path,$kind,$quality)
    {
        if(!self::enabled())return $kind==='jpeg'?imagejpeg($image,$path,$quality):imagepng($image,$path,$quality);
        $key=self::normalize($path);ob_start();
        try{$ok=$kind==='jpeg'?imagejpeg($image,null,$quality):imagepng($image,null,$quality);$data=ob_get_contents();}finally{ob_end_clean();}
        if(!$ok || $data==='')throw new RuntimeException('No se pudo codificar la imagen.');
        self::put($key,$data,$kind==='jpeg'?'image/jpeg':'image/png');return true;
    }
    public static function jpeg($image,$path,$quality=-1){return self::encode($image,$path,'jpeg',$quality);}
    public static function png($image,$path,$quality=-1){return self::encode($image,$path,'png',$quality);}
    public static function uploadedImage($upload,$path)
    {
        if(empty($upload['tmp_name']) || !isset($upload['error']) || $upload['error']!==UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name']))throw new RuntimeException('La imagen no se recibió correctamente.');
        $info=getimagesize($upload['tmp_name']);if(!$info || !isset($info['mime']) || strpos($info['mime'],'image/')!==0)throw new RuntimeException('El archivo no es una imagen.');
        if(!self::enabled())return move_uploaded_file($upload['tmp_name'],$path);
        $content=file_get_contents($upload['tmp_name']);if($content===false)throw new RuntimeException('No se pudo leer la imagen.');
        self::put(self::normalize($path),$content,$info['mime']);return true;
    }
    public static function exists($path)
    {
        if(!self::enabled())return file_exists($path);
        try{$key=self::normalize(rtrim($path,'/'));return self::lookup($key)!==null || strpos(basename($key),'.')===false || file_exists($path);}catch(InvalidArgumentException $e){return file_exists($path);}
    }
    public static function makeDirectory($path,$mode=0755,$recursive=false){if(!self::enabled())return mkdir($path,$mode,$recursive);self::normalize(rtrim($path,'/').'/placeholder.jpg');return true;}
    public static function removeDirectory($path){if(!self::enabled())return rmdir($path);self::normalize(rtrim($path,'/').'/placeholder.jpg');return true;}
    public static function remove($path)
    {
        if(!self::enabled())return unlink($path);$key=self::normalize($path);
        // Retain Drive originals: a file can be referenced by more than one record.
        $q=self::database()->prepare('INSERT INTO history(path,previous_file_id,event,created_at)VALUES(?,?,?,?)');$row=self::lookup($key);$q->execute(array($key,$row?$row['file_id']:null,'deletion_requested',time()));return true;
    }
    public static function matching($pattern)
    {
        if(!self::enabled())return glob($pattern);
        if(substr($pattern,-2)!=='/*')throw new InvalidArgumentException('Patrón de multimedia inválido.');
        $key=self::normalize(substr($pattern,0,-1).'placeholder.jpg');$prefix=dirname($key).'/';$q=self::database()->prepare('SELECT path FROM media WHERE substr(path,1,?)=?');$q->execute(array(strlen($prefix),$prefix));$paths=glob($pattern)?:array();
        foreach($q->fetchAll(PDO::FETCH_COLUMN) as $p)$paths[]=substr($p,strlen('backend/'));return array_values(array_unique($paths));
    }
    public static function queueOrder($id,$data)
    {
        if(!self::enabled())return;
        if(empty($data['fechaIngreso'])&&class_exists('ConexionWP',false)){
            $parent=ConexionWP::conectarWP()->prepare('SELECT titulo,fecha_ingreso FROM ordenes WHERE id=:id');$parent->execute(array(':id'=>(int)$id));$order=$parent->fetch(PDO::FETCH_ASSOC);
            if($order){$data['fechaIngreso']=$order['fecha_ingreso'];if(empty($data['titulo']))$data['titulo']=$order['titulo'];}
        }
        $paths=array();
        foreach(array('multimedia','imgFotoPrincipal','imgPortada','portada') as $field){
            if(!isset($data[$field]))continue;$value=$data[$field];$decoded=is_string($value)?json_decode($value,true):$value;
            if(is_array($decoded))array_walk_recursive($decoded,function($v)use(&$paths){if(is_string($v)&&strpos($v,'vistas/img/')!==false)$paths[]=$v;});
            elseif(is_string($value)&&strpos($value,'vistas/img/')!==false)$paths[]=$value;
        }
        $lock=fopen(self::stateDir().'/order-queue.lock','c');if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se pudo preparar la organización por orden.');chmod(self::stateDir().'/order-queue.lock',0600);
        try{
            $q=self::database()->prepare('SELECT context FROM order_jobs WHERE order_id=?');$q->execute(array((int)$id));$previous=json_decode($q->fetchColumn()?:'{}',true)?:array();
            if(!empty($previous['paths']))$paths=array_merge($previous['paths'],$paths);
            $context=array('order_id'=>(int)$id,'titulo'=>isset($data['titulo'])?$data['titulo']:(isset($previous['titulo'])?$previous['titulo']:''),'fecha_ingreso'=>isset($data['fechaIngreso'])?$data['fechaIngreso']:(isset($previous['fecha_ingreso'])?$previous['fecha_ingreso']:date('Y-m-d')),'paths'=>array_values(array_unique($paths)));
            $q=self::database()->prepare('INSERT OR REPLACE INTO order_jobs(order_id,context,created_at)VALUES(?,?,?)');$q->execute(array((int)$id,json_encode($context),(int)(microtime(true)*1000000)));
        }finally{flock($lock,LOCK_UN);fclose($lock);}
    }
    public static function queueOrderPhoto($id,$path)
    {
        if(!self::enabled())return;
        if(!class_exists('ConexionWP',false))require_once self::root().'/modelos/conexionWordpress.php';
        $q=ConexionWP::conectarWP()->prepare('SELECT titulo,fecha_ingreso FROM ordenes WHERE id=:id');$q->execute(array(':id'=>(int)$id));$order=$q->fetch(PDO::FETCH_ASSOC);
        if(!$order)throw new RuntimeException('No se encontró la orden relacionada con la imagen.');
        self::queueOrder($id,array('titulo'=>$order['titulo'],'fechaIngreso'=>$order['fecha_ingreso'],'multimedia'=>array(array('foto'=>$path))));
    }
    public static function reconcileOrders($limit=25)
    {
        $jobs=self::database()->query('SELECT * FROM order_jobs ORDER BY created_at LIMIT '.max(1,(int)$limit))->fetchAll(PDO::FETCH_ASSOC);$done=0;
        foreach($jobs as $job){$context=json_decode($job['context'],true);
            foreach($context['paths'] as $path){$key=self::normalize($path);if(strpos($key,'backend/vistas/img/default/')===0||strpos($key,'backend/vistas/img/productos/default/')===0)continue;$row=self::lookup($key);if(!$row)continue;
                $category=explode('/',$key)[3];$fileContext=$context;unset($fileContext['paths']);
                $previousContext=json_decode($row['context'],true)?:array();$knownIds=array($context['order_id']);
                if(!empty($previousContext['order_id']))$knownIds[]=(int)$previousContext['order_id'];
                if(!empty($previousContext['order_ids']))$knownIds=array_merge($knownIds,$previousContext['order_ids']);
                if(!empty($previousContext['ordenes_confirmadas']))$knownIds=array_merge($knownIds,array_map('intval',explode(';',$previousContext['ordenes_confirmadas'])));
                $knownIds=array_values(array_unique(array_filter($knownIds)));$fileContext['order_ids']=$knownIds;
                $folder=count($knownIds)>1?'Compartidas/'.$category:'Ordenes/'.substr($context['fecha_ingreso'],0,4).'/Orden_'.$context['order_id'].'/'.$category;
                $parent=self::folder($folder);$old=self::api('GET','files/'.$row['file_id'].'?fields=id,parents');
                $params=array('fields'=>'id,parents');if(!in_array($parent,$old['parents'],true)){$params['addParents']=$parent;$params['removeParents']=implode(',',$old['parents']);}
                $query=http_build_query($params,'','&',PHP_QUERY_RFC3986);
                self::api('PATCH','files/'.$row['file_id'].'?'.$query,array('description'=>json_encode(array('ruta_original'=>$key,'relacion'=>$fileContext),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));
                $q=self::database()->prepare('UPDATE media SET context=? WHERE path=?');$q->execute(array(json_encode($fileContext),$key));
            }
            $q=self::database()->prepare('DELETE FROM order_jobs WHERE order_id=? AND created_at=?');$q->execute(array($job['order_id'],$job['created_at']));$done++;
        }
        return $done;
    }
}
