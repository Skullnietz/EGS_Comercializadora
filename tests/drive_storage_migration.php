<?php
$state=sys_get_temp_dir().'/egs-drive-test-'.bin2hex(random_bytes(5));putenv('EGS_DRIVE_STATE_DIR='.$state);
$sourceRoot=dirname(__DIR__);$testApp=$state.'/app';mkdir($testApp.'/config',0700,true);mkdir($testApp.'/controladores',0700,true);
copy($sourceRoot.'/config/DriveStorage.php',$testApp.'/config/DriveStorage.php');copy($sourceRoot.'/controladores/ordenes.controlador.php',$testApp.'/controladores/ordenes.controlador.php');
require $testApp.'/config/DriveStorage.php';
class ConexionWP { public static $testPdo; public static function conectarWP(){return self::$testPdo;} }
ConexionWP::$testPdo=new PDO('sqlite::memory:');ConexionWP::$testPdo->exec("CREATE TABLE ordenes(id INTEGER,titulo TEXT,fecha_ingreso TEXT); INSERT INTO ordenes VALUES(777,'Equipo de prueba','2020-06-15 12:00:00')");
function check($condition,$message){if(!$condition)throw new RuntimeException($message);}
function rejects($callback,$message){try{$callback();}catch(Throwable $e){return;}throw new RuntimeException($message);}
EgsDriveStorage::init();EgsDriveStorage::saveJson('tokens',array('access_token'=>'test-token','refresh_token'=>'test-refresh','expires_at'=>time()+3600));
EgsDriveStorage::saveJson('client',array('client_id'=>'test.apps.googleusercontent.com','client_secret'=>'test-secret'));
EgsDriveStorage::saveJson('library',array('root_id'=>'root-test','owner_email'=>'test@example.com'));
EgsDriveStorage::saveJson('oauth_production',array('confirmed'=>true,'confirmed_at'=>time()));
check(!EgsDriveStorage::productionReady(),'Se permitió retirar originales con una autorización anterior a publicar');
$authorized=EgsDriveStorage::readJson('tokens');$authorized['authorized_at']=time();EgsDriveStorage::saveJson('tokens',$authorized);check(EgsDriveStorage::productionReady(),'No se reconoció una autorización nueva después de publicar');EgsDriveStorage::saveJson('oauth_production',array());
$ownerLink=EgsDriveStorage::ownerLink();$ownerToken=substr($ownerLink,strrpos($ownerLink,'=')+1);
check(EgsDriveStorage::ownerTokenValid($ownerToken),'El acceso del propietario no se pudo validar');
rejects(function(){EgsDriveStorage::claimOwnerToken(str_repeat('0',64));},'Se aceptó un acceso del propietario inválido');
EgsDriveStorage::claimOwnerToken($ownerToken);check(!EgsDriveStorage::ownerTokenValid($ownerToken),'El acceso del propietario se pudo reutilizar');
rejects(function()use($ownerToken){EgsDriveStorage::claimOwnerToken($ownerToken);},'Se permitió reclamar dos veces el mismo acceso');
$objects=array();$uploads=array();$calls=array();$next=0;$corrupt=false;$down=false;$loseResponse=false;$corruptRead=false;$mutateSourceFile=null;
EgsDriveStorage::setTransportForTests(function($method,$url,$headers,$body)use(&$objects,&$uploads,&$calls,&$next,&$corrupt,&$down,&$loseResponse,&$corruptRead,&$mutateSourceFile){
    $calls[]=array($method,$url);if($down)throw new RuntimeException('Google no disponible');
    $response=function($v,$status=200,$h=array()){return array('status'=>$status,'headers'=>$h,'body'=>is_array($v)?json_encode($v):$v);};
    $p=parse_url($url);$query=array();if(isset($p['query']))parse_str($p['query'],$query);
    if($p['host']==='oauth2.googleapis.com')return $response(array('access_token'=>'refreshed-test','expires_in'=>3600));
    if($method==='GET' && isset($query['q'])){
        $found=array();if(strpos($query['q'],'egs_path')!==false)foreach($objects as $object)if(strpos($query['q'],$object['properties']['egs_path'])!==false&&strpos($query['q'],$object['properties']['egs_md5'])!==false)$found[]=$object['meta'];
        return $response(array('files'=>$found));
    }
    if($method==='POST' && strpos($p['path'],'/upload/')!==false){$id='session-'.(++$next);$uploads[$id]=array('metadata'=>json_decode($body,true),'bytes'=>'');return $response('',200,array('location'=>'https://www.googleapis.com/upload/drive/v3/files?upload_id='.$id));}
    if($method==='PUT'){
        $id=$query['upload_id'];$uploads[$id]['bytes'].=$body;$range='';foreach($headers as $h)if(strpos($h,'Content-Range:')===0)$range=$h;
        preg_match('/bytes (\d+)-(\d+)\/(\d+)/',$range,$m);check(count($m)===4,'Content-Range inválido');
        if((int)$m[2]+1<(int)$m[3])return $response('',308,array('range'=>'bytes=0-'.$m[2]));
        $content=$uploads[$id]['bytes'];$file='file-'.$id;$meta=array('id'=>$file,'size'=>strlen($content),'md5Checksum'=>$corrupt?str_repeat('0',32):md5($content),'parents'=>$uploads[$id]['metadata']['parents']);
        $objects[$file]=array('data'=>$content,'meta'=>$meta,'description'=>$uploads[$id]['metadata']['description'],'properties'=>$uploads[$id]['metadata']['appProperties']);
        if($loseResponse){$loseResponse=false;return $response(array('error'=>'respuesta perdida'),500);}return $response($meta);
    }
    if($method==='POST')return $response(array('id'=>'folder-'.(++$next)));
    preg_match('~/files/([^/?]+)~',$url,$m);$id=isset($m[1])?$m[1]:'';
    if($method==='GET' && isset($query['alt'])){
        if($mutateSourceFile){file_put_contents($mutateSourceFile,'new-local-version');$mutateSourceFile=null;}
        return $response($objects[$id]['data'].($corruptRead?'corrupt':''));
    }
    if($method==='GET' && isset($objects[$id]))return $response($objects[$id]['meta']);
    if($method==='PATCH'){
        check(!isset($query['addParents'],$query['removeParents'])||$query['addParents']!==$query['removeParents'],'La actualización intentó retirar la misma carpeta destino');
        $parents=isset($query['addParents'])?array($query['addParents']):$objects[$id]['meta']['parents'];$objects[$id]['meta']['parents']=$parents;
        $v=json_decode($body,true);if(isset($v['description'])){check(strpos($v['description'],'"paths"')===false,'La descripción repitió todas las rutas de la orden');$objects[$id]['description']=$v['description'];}
        return $response(array('id'=>$id,'parents'=>$parents));
    }
    throw new RuntimeException('Solicitud inesperada: '.$method.' '.$url);
});
check(EgsDriveStorage::normalize('../vistas/img/productos/a.jpg')==='backend/vistas/img/productos/a.jpg','Alias AJAX no normalizado');
check(EgsDriveStorage::normalize('wp-content/uploads/2026/01/a.jpg','wordpress')==='wordpress/wp-content/uploads/2026/01/a.jpg','Alias WordPress no normalizado');
foreach(array('vistas/img/../../config/env.php','../../vistas/img/a.jpg','https://evil.example/vistas/img/a.jpg',"vistas/img/a\0.jpg",'vistas/img/a\\b.jpg')as $bad)rejects(function()use($bad){EgsDriveStorage::normalize($bad);},'Se aceptó una ruta inválida');
rejects(function(){EgsDriveStorage::rawRequest('GET','https://evil.example/api');},'Destino ajeno a Google permitido');
$content=str_repeat('a',3*1024*1024+7);$key='backend/vistas/img/multimedia/orden-test/foto.jpg';
$row=EgsDriveStorage::put($key,$content,'image/jpeg',array('drive_folder'=>'Ordenes/2026/Orden_123/multimedia','orden_id'=>123));
check((int)$row['bytes']===strlen($content)&&EgsDriveStorage::bytes($row)===$content,'Carga y lectura no coinciden');
check(count(array_filter($calls,function($c){return $c[0]==='PUT';}))===2,'La carga por partes no se ejecutó');
$before=count($uploads);$again=EgsDriveStorage::put($key,$content,'image/jpeg');check($again['file_id']===$row['file_id']&&count($uploads)===$before,'Reintentar creó una copia adicional');
$lostKey='backend/vistas/img/multimedia/orden-test/lost-response.jpg';$loseResponse=true;
rejects(function()use($lostKey){EgsDriveStorage::put($lostKey,'lost-response-content','image/jpeg');},'Se aceptó una respuesta no confirmada');
$before=count($uploads);$recovered=EgsDriveStorage::put($lostKey,'lost-response-content','image/jpeg');check($recovered&&count($uploads)===$before,'No se recuperó la carga cuya respuesta se perdió');
$corrupt=true;rejects(function()use($key){EgsDriveStorage::put($key,'replacement','image/jpeg');},'Checksum incorrecto aceptado');
check(EgsDriveStorage::lookup($key)['file_id']===$row['file_id'],'Una copia corrupta reemplazó el mapa');$corrupt=false;
file_put_contents($state.'/enabled','drive');
$image=imagecreatetruecolor(20,10);$new='vistas/img/productos/drive-test-'.bin2hex(random_bytes(4)).'.jpg';
check(EgsDriveStorage::jpeg($image,$new,82),'La carga JPEG falló');check(!is_file(EgsDriveStorage::root().'/'.$new),'JPEG quedó almacenado localmente');
$jpeg=EgsDriveStorage::bytes(EgsDriveStorage::lookup(EgsDriveStorage::normalize($new)));check(getimagesizefromstring($jpeg)['mime']==='image/jpeg','No se conservó el formato');
$down=true;$failed='vistas/img/productos/fallo-drive.jpg';rejects(function()use($image,$failed){EgsDriveStorage::jpeg($image,$failed,82);},'Se informó éxito durante una caída');
check(EgsDriveStorage::lookup(EgsDriveStorage::normalize($failed))===null,'Se creó un alias sin archivo remoto');$down=false;
EgsDriveStorage::remove($new);check(EgsDriveStorage::lookup(EgsDriveStorage::normalize($new))!==null,'Se perdió una copia al solicitar eliminación');
require $testApp.'/controladores/ordenes.controlador.php';
$input=tempnam($state,'image-');imagepng($image,$input);$name='optimized-'.bin2hex(random_bytes(4));
$result=controladorOrdenes::ctrOptimizarImagenSubida(array('tmp_name'=>$input,'error'=>UPLOAD_ERR_OK,'size'=>filesize($input),'name'=>'equipo.png'),array('directorio'=>'../vistas/img/multimedia/test-order','nombre'=>$name,'ancho'=>10,'alto'=>10));
check($result['ok'],'El optimizador real rechazó la imagen');$optimized=EgsDriveStorage::lookup(EgsDriveStorage::normalize($result['ruta']));
check($optimized!==null,'El optimizador no dejó el vínculo en el mapa');$dimensions=getimagesizefromstring(EgsDriveStorage::bytes($optimized));check($dimensions[0]<=10&&$dimensions[1]<=10,'No se conservó el ajuste de tamaño');
check(!is_file(EgsDriveStorage::root().'/vistas/img/multimedia/test-order/'.$name.'.jpg'),'El optimizador escribió la imagen en el servidor');
EgsDriveStorage::queueOrder(123,array('titulo'=>'Prueba','multimedia'=>json_encode(array(array('foto'=>$result['ruta'])))));
check(EgsDriveStorage::reconcileOrders()===1,'La organización por ID de orden no terminó');check(EgsDriveStorage::database()->query('SELECT count(*) FROM order_jobs')->fetchColumn()==0,'Quedó una orden ya organizada en la cola');
EgsDriveStorage::queueOrder(123,array('titulo'=>'Prueba','multimedia'=>json_encode(array(array('foto'=>$result['ruta'])))));check(EgsDriveStorage::reconcileOrders()===1,'El reintento sobre la misma carpeta falló');
EgsDriveStorage::queueOrder(124,array('titulo'=>'Segunda orden','multimedia'=>json_encode(array(array('foto'=>$result['ruta'])))));EgsDriveStorage::reconcileOrders();$shared=json_decode(EgsDriveStorage::lookup(EgsDriveStorage::normalize($result['ruta']))['context'],true);check(count($shared['order_ids'])===2,'Se perdió la relación compartida entre órdenes');
EgsDriveStorage::saveJson('tokens',array('access_token'=>'expired','refresh_token'=>'test-refresh','expires_at'=>time()-100));check(EgsDriveStorage::token()==='refreshed-test','No se renovó la autorización');
check((fileperms($state.'/tokens.json')&0777)===0600,'Los tokens no tienen permisos privados');
// Exercise the actual WordPress plugin with a small hook/runtime harness.
$wpUploads=$state.'/wp-content/uploads';mkdir($wpUploads.'/2026/10',0700,true);$wpHooks=array();$wpMeta=array();
define('ABSPATH',$state.'/public_html/');
function wp_upload_dir(){global $wpUploads;return array('basedir'=>$wpUploads);}
function add_filter($name,$callback,$priority=10,$args=1){global $wpHooks;$wpHooks[$name]=$callback;}
function add_action($name,$callback){global $wpHooks;$wpHooks[$name]=$callback;}
function get_the_title($id){return 'Adjunto '.$id;}
function update_post_meta($id,$key,$value){global $wpMeta;$wpMeta[$id][$key]=$value;}
function delete_post_meta($id,$key){global $wpMeta;unset($wpMeta[$id][$key]);}
function wp_next_scheduled($name,$args){return false;}
function wp_schedule_single_event($at,$name,$args){return true;}
function wp_mkdir_p($path){return is_dir($path)||mkdir($path,0700,true);}
require dirname(__DIR__).'/integraciones/wordpress/wp-content/mu-plugins/egs-drive-media.php';
$wpOriginal=$wpUploads.'/2026/10/equipo.png';$wpThumb=$wpUploads.'/2026/10/equipo-10x10.png';$wpBackup=$wpOriginal.'.orig';imagepng($image,$wpOriginal);imagepng($image,$wpThumb);imagepng($image,$wpBackup);
$metadata=array('file'=>'2026/10/equipo.png','sizes'=>array('thumbnail'=>array('file'=>'equipo-10x10.png')));
$result=egs_drive_wp_offload($metadata,77);check($result===$metadata&&!is_file($wpOriginal)&&!is_file($wpThumb)&&!is_file($wpBackup),'WordPress no conservó metadatos o dejó las variantes y respaldos locales');
check(EgsDriveStorage::lookup('wordpress/wp-content/uploads/2026/10/equipo.png')!==null,'La imagen de WordPress no quedó relacionada');
$filter=$wpHooks['get_attached_file'];check($filter($wpOriginal,77)===$wpOriginal&&is_file($wpOriginal)&&is_file($wpBackup),'El editor no pudo obtener el original y su respaldo temporales');
$wpAttachments=array();function get_attached_file($id,$unfiltered=false){global $wpAttachments;return isset($wpAttachments[$id])?$wpAttachments[$id]:false;}
$pdf="%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF\n";$wpPdf=$wpUploads.'/2026/10/manual.pdf';file_put_contents($wpPdf,$pdf);$wpAttachments[79]=$wpPdf;
check(egs_drive_wp_offload(array(),79)===array()&&!is_file($wpPdf),'WordPress no trasladó un PDF con metadatos vacíos');check(EgsDriveStorage::bytes(EgsDriveStorage::lookup('wordpress/wp-content/uploads/2026/10/manual.pdf'))===$pdf,'El PDF cambió durante el traslado');
rejects(function(){EgsDriveStorage::put('wordpress/wp-content/uploads/peligroso.html','<script>bad()</script>','text/html');},'Se permitió publicar HTML como multimedia');
$unsupported=$wpUploads.'/2026/10/retener.formato-desconocido';imagepng($image,$unsupported);egs_drive_wp_offload(array('file'=>'2026/10/retener.formato-desconocido'),80);check(is_file($unsupported),'Se retiró un formato que el lector no puede servir');
$wpFail=$wpUploads.'/2026/10/fallo.png';imagepng($image,$wpFail);$down=true;
egs_drive_wp_offload(array('file'=>'2026/10/fallo.png'),78);check(is_file($wpFail)&&isset($wpMeta[78]['_egs_drive_pending']),'WordPress perdió el original durante una caída');$down=false;
unlink($state.'/enabled');$local=$state.'/local.jpg';check(EgsDriveStorage::jpeg($image,$local,82)&&is_file($local),'Cambió la carga local con Drive desactivado');
// Migration must preserve files on corrupted readback, a newer cloud alias, or a concurrent local edit.
$migrationDir=EgsDriveStorage::root().'/vistas/img/migration-tests-'.bin2hex(random_bytes(4));mkdir($migrationDir,0700,true);
$source=$migrationDir.'/source.jpg';imagejpeg($image,$source,82);$original=file_get_contents($source);
$migrationKey=EgsDriveStorage::normalize($source);
$copied=EgsDriveStorage::migrateFile($migrationKey,array('drive_folder'=>'Pruebas'),false);check($copied['status']==='verified'&&file_get_contents($source)===$original,'La copia de prueba retiró el original');
$corruptRead=true;rejects(function()use($migrationKey){EgsDriveStorage::migrateFile($migrationKey,array(),true);},'Se permitió borrar con descarga corrupta');check(is_file($source),'Se perdió el original ante descarga corrupta');$corruptRead=false;
$released=EgsDriveStorage::migrateFile($migrationKey,array(),true);check($released['status']==='released'&&!is_file($source),'No se retiró una copia íntegra verificada');check(EgsDriveStorage::bytes(EgsDriveStorage::lookup($migrationKey))===$original,'La copia migrada no coincide');
check(EgsDriveStorage::migrateFile($migrationKey,array(),true)['status']==='already_remote','El reintento de migración no fue idempotente');
$conflict=$migrationDir.'/conflict.jpg';imagejpeg($image,$conflict,82);$conflictKey=EgsDriveStorage::normalize($conflict);$newCloud=EgsDriveStorage::put($conflictKey,'new-cloud-image-version','image/jpeg');
rejects(function()use($conflictKey){EgsDriveStorage::migrateFile($conflictKey,array(),true);},'La migración reemplazó una versión nueva en Drive');check(is_file($conflict)&&EgsDriveStorage::lookup($conflictKey)['file_id']===$newCloud['file_id'],'No se preservaron ambas versiones en conflicto');
$changed=$migrationDir.'/changed.jpg';imagejpeg($image,$changed,82);$changedKey=EgsDriveStorage::normalize($changed);$mutateSourceFile=$changed;
rejects(function()use($changedKey){EgsDriveStorage::migrateFile($changedKey,array(),true);},'Se retiró un archivo modificado durante el traslado');check(file_get_contents($changed)==='new-local-version','No se conservó la modificación concurrente');
unlink($conflict);unlink($changed);
$tabSource=$migrationDir.'/legacy'."\t".'.jpg';imagejpeg($image,$tabSource,82);$tabKey=EgsDriveStorage::normalize($tabSource);EgsDriveStorage::migrateFile($tabKey,array('drive_folder'=>'Pruebas'),true);
check(!is_file($tabSource)&&EgsDriveStorage::normalize($tabKey)===$tabKey,'No se conservó el alias de la carpeta antigua con tabulador');
rejects(function(){EgsDriveStorage::normalize("vistas/img/inexistente\t.jpg");},'Se permitió una ruta especial sin original ni registro');rmdir($migrationDir);
// Multiple photos saved for one order must share a job and its actual entry year.
file_put_contents($state.'/enabled','drive');EgsDriveStorage::queueOrderPhoto(777,'vistas/img/observaciones/primera.jpg');EgsDriveStorage::queueOrderPhoto(777,'vistas/img/reportes/segunda.jpg');
$job=json_decode(EgsDriveStorage::database()->query('SELECT context FROM order_jobs WHERE order_id=777')->fetchColumn(),true);
check(count($job['paths'])===2&&substr($job['fecha_ingreso'],0,4)==='2020','La cola perdió una foto o el año de la orden');
rejects(function(){EgsDriveStorage::queueOrderPhoto(99999,'vistas/img/observaciones/inexistente.jpg');},'Una foto sin orden real recibió asociación confirmada');
unlink($state.'/enabled');imagedestroy($image);
echo "PASS: alias, rutas seguras, cargas por partes, respuesta perdida, checksum, caída de Drive, optimizador real, cola por orden, renovación, WordPress/variantes/editor y modo local.\n";
echo json_encode(array('temporal_editor'=>$wpOriginal))."\n";
