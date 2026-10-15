<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once dirname(__DIR__).'/config/DriveStorage.php';
$options=getopt('',array('manifest:','limit:','release-local','inventory:','wordpress-root:'));
$limit=isset($options['limit'])?max(1,(int)$options['limit']):25;
$wordpress=isset($options['wordpress-root'])?realpath($options['wordpress-root']):null;
if(isset($options['wordpress-root']) && (!$wordpress || !is_file($wordpress.'/wp-load.php') || !is_dir($wordpress.'/wp-content/uploads')))throw new RuntimeException('La raíz de WordPress no es válida.');
$site=$wordpress?'wordpress':'backend';$root=$wordpress?:EgsDriveStorage::root();$media=$site==='wordpress'?'/wp-content/uploads':'/vistas/img';
$images=array('jpg','jpeg','png','gif','webp','avif','bmp','ico','svg','tif','tiff','orig','pdf','mp3','m4a','wav','ogg','flac','mp4','mov','webm','mpeg','mpg','avi');
$manifest=array();
if(isset($options['manifest'])){
    $fh=fopen($options['manifest'],'r');if(!$fh)throw new RuntimeException('No se pudo leer el manifiesto.');
    $header=fgetcsv($fh);$header[0]=preg_replace('/^\xEF\xBB\xBF/','',$header[0]);
    while(($r=fgetcsv($fh))!==false){$v=array_combine($header,$r);$manifest[$v['archivo']]=$v;}fclose($fh);
}
if(isset($options['inventory'])) {
    $out=fopen($options['inventory'],'w');fputcsv($out,array('sitio','archivo','bytes','md5','mime'));$count=0;
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.$media,FilesystemIterator::SKIP_DOTS));
    foreach($it as $f){if(!$f->isFile()||$f->isLink()||!in_array(strtolower($f->getExtension()),$images,true))continue;$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f->getPathname());if(!EgsDriveStorage::mediaMime($mime))continue;fputcsv($out,array($site,substr($f->getPathname(),strlen($root)+1),$f->getSize(),md5_file($f->getPathname()),$mime));$count++;}
    fclose($out);echo json_encode(array('imagenes'=>$count,'inventario'=>$options['inventory']))."\n";exit;
}
if(!EgsDriveStorage::enabled())throw new RuntimeException('Activa y verifica la conexión del sitio antes de migrar.');
$ready=EgsDriveStorage::readJson('integration_ready');if(empty($ready['backend_ready'])||empty($ready['wordpress_ready']))throw new RuntimeException('Falta verificar la integración de ambos sitios.');
if(isset($options['release-local'])&&!EgsDriveStorage::productionReady())throw new RuntimeException('Publica OAuth en Producción y renueva la autorización antes de retirar originales.');
EgsDriveStorage::init();$lock=fopen(EgsDriveStorage::stateDir().'/migration.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))throw new RuntimeException('Ya hay otra migración en ejecución.');
$count=0;$released=0;$failed=0;$skipped=0;
try{
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.$media,FilesystemIterator::SKIP_DOTS));
    foreach($it as $f){
        if(!$f->isFile()||$f->isLink()||!in_array(strtolower($f->getExtension()),$images,true))continue;
        $file=$f->getPathname();$alias=substr($file,strlen($root)+1);$key=EgsDriveStorage::normalize($alias,$site);
        $prior=EgsDriveStorage::lookup($key);
        if($prior && (int)$prior['bytes']===$f->getSize() && $prior['md5']===md5_file($file) && !isset($options['release-local'])){$skipped++;continue;}
        if($count+$failed>=$limit)break;
        try{
            $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);if(!EgsDriveStorage::mediaMime($mime)){$skipped++;continue;}
            $context=array('source_site'=>$site,'ruta_original'=>$alias,'migration'=>true);
            if(isset($manifest[$alias])){$m=$manifest[$alias];$context=array_merge($context,array('drive_folder'=>dirname($m['destino_drive_propuesto']),'drive_name'=>basename($m['destino_drive_propuesto']),'ordenes_confirmadas'=>$m['ordenes_confirmadas'],'ordenes_probables'=>$m['ordenes_probables'],'titulo_orden'=>$m['titulo_orden'],'fuentes_bd'=>$m['fuentes_bd'],'clasificacion'=>$m['estado']));}
            if(isset($manifest[$alias]['wordpress_attachment_ids'])){$m=$manifest[$alias];$context['wordpress_attachment_ids']=$m['wordpress_attachment_ids'];$context['wordpress_post_ids']=$m['wordpress_post_ids'];$context['titulo_registro']=$m['titulo_registro'];}
            $result=EgsDriveStorage::migrateFile($key,$context,isset($options['release-local']));
            if($result['status']==='released')$released++;
            $count++;
        }catch(Throwable $e){$failed++;fwrite(STDERR,json_encode(array('path_sha256'=>hash('sha256',$key),'error'=>$e->getMessage()))."\n");}
    }
}finally{flock($lock,LOCK_UN);fclose($lock);}
echo json_encode(array('sitio'=>$site,'copiados_verificados'=>$count,'retirados_del_hosting'=>$released,'errores'=>$failed,'omitidos'=>$skipped))."\n";
exit($failed?1:0);
