<?php
/** Plugin Name: EGS Google Drive Media
 * Keeps existing WordPress attachment URLs and offloads originals and generated sizes.
 */
if(!defined('ABSPATH'))exit;
if(!class_exists('EgsDriveStorage',false)){
    $egsStorage=dirname(rtrim(ABSPATH,'/')).'/backend.comercializadoraegs.com/config/DriveStorage.php';
    if(!is_file($egsStorage))return;
    require_once $egsStorage;
}

function egs_drive_wp_key($absolute)
{
    $uploads=wp_upload_dir();$base=rtrim($uploads['basedir'],'/').'/';
    if(strpos($absolute,$base)!==0)return null;
    return EgsDriveStorage::normalize('wp-content/uploads/'.substr($absolute,strlen($base)),'wordpress');
}
function egs_drive_wp_offload($metadata,$attachmentId)
{
    if(!EgsDriveStorage::enabled() || !is_array($metadata))return $metadata;
    $uploads=wp_upload_dir();$base=rtrim($uploads['basedir'],'/');
    if(!empty($metadata['file']))$relative=$metadata['file'];
    else{$original=get_attached_file($attachmentId,true);if(!$original)return $metadata;$originalKey=egs_drive_wp_key($original);if(!$originalKey)return $metadata;$relative=substr($originalKey,strlen('wordpress/wp-content/uploads/'));}
    if(!EgsDriveStorage::mediaPath($relative))return $metadata;
    $names=array($relative);$dir=dirname($relative);
    $prefix=$dir==='.'?'':$dir.'/';
    if(!empty($metadata['original_image']))$names[]=$prefix.$metadata['original_image'];
    if(!empty($metadata['sizes']))foreach($metadata['sizes'] as $size)if(!empty($size['file']))$names[]=$prefix.$size['file'];
    foreach($names as $name){$backup=$name.'.orig';if(is_file($base.'/'.$backup)||EgsDriveStorage::lookup('wordpress/wp-content/uploads/'.$backup))$names[]=$backup;}
    $verified=array();
    try{
        foreach(array_unique($names) as $name){
            $key=EgsDriveStorage::normalize('wp-content/uploads/'.$name,'wordpress');$file=$base.'/'.$name;
            if(!is_file($file)){if(!EgsDriveStorage::lookup($key))throw new RuntimeException('Falta una variante de la imagen.');continue;}
            $canonical=realpath($file);if(!$canonical||strpos($canonical,realpath($base).'/')!==0||is_link($file))throw new RuntimeException('Ruta de imagen inválida.');
            $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);if(!EgsDriveStorage::mediaMime($mime))continue;
            $data=file_get_contents($file);if($data===false)throw new RuntimeException('No se pudo leer la imagen.');
            $folder='Sitio_publico/'.($dir==='.'?'':$dir.'/').'Adjunto_'.intval($attachmentId);$context=array('drive_folder'=>$folder,'attachment_id'=>intval($attachmentId),'wordpress_title'=>get_the_title($attachmentId),'ruta_original'=>$name);
            if(substr($name,-5)==='.orig'){$context['drive_folder'].='/Respaldos_originales';$context['respaldo_de']=substr($name,0,-5);}
            $row=EgsDriveStorage::put($key,$data,$mime,$context);
            if(EgsDriveStorage::bytes($row)!==$data)throw new RuntimeException('La comprobación de la variante falló.');
            $verified[]=array('path'=>$file,'md5'=>md5($data),'bytes'=>strlen($data));
        }
        // All variants must be verified before any local image is removed.
        foreach($verified as $v){clearstatcache(true,$v['path']);if(is_file($v['path'])&&filesize($v['path'])===$v['bytes']&&md5_file($v['path'])===$v['md5'])unlink($v['path']);}
        delete_post_meta($attachmentId,'_egs_drive_pending');
    }catch(Throwable $e){
        update_post_meta($attachmentId,'_egs_drive_pending',1);
        if(!wp_next_scheduled('egs_drive_retry_attachment',array(intval($attachmentId))))wp_schedule_single_event(time()+3600,'egs_drive_retry_attachment',array(intval($attachmentId)));
    }
    return $metadata;
}
add_filter('wp_update_attachment_metadata','egs_drive_wp_offload',30,2);
add_action('egs_drive_retry_attachment',function($id){$meta=wp_get_attachment_metadata($id);egs_drive_wp_offload(is_array($meta)?$meta:array(),$id);});
add_action('add_attachment',function($id){if(!EgsDriveStorage::enabled())return;$mime=get_post_mime_type($id);if(!EgsDriveStorage::mediaMime($mime)||strpos($mime,'image/')===0)return;if(!wp_next_scheduled('egs_drive_retry_attachment',array(intval($id))))wp_schedule_single_event(time()+60,'egs_drive_retry_attachment',array(intval($id)));});

// Editors and thumbnail generation need a physical original. Materialize it only
// during the request, then remove it if its bytes have not changed.
function egs_drive_wp_materialize($file)
{
    if(is_file($file))return;
    $key=egs_drive_wp_key($file);if(!$key)return $file;
    try{
        $row=EgsDriveStorage::lookup($key);if(!$row)return $file;
        $uploads=wp_upload_dir();$base=realpath($uploads['basedir']);if(!$base)return $file;
        $parent=dirname($file);if(!is_dir($parent)&&!wp_mkdir_p($parent))return $file;
        $actualParent=realpath($parent);if(!$actualParent||($actualParent!==$base&&strpos($actualParent,$base.'/')!==0))return $file;
        $content=EgsDriveStorage::bytes($row);$tmp=tempnam($parent,'egs-drive-');
        if(file_put_contents($tmp,$content)===false){unlink($tmp);return $file;}
        if(is_file($file)){unlink($tmp);return $file;}
        if(!rename($tmp,$file)){unlink($tmp);return $file;}
        chmod($file,0644);$md5=$row['md5'];
        register_shutdown_function(function()use($file,$md5){clearstatcache(true,$file);if(is_file($file)&&md5_file($file)===$md5)unlink($file);});
    }catch(Throwable $e){return $file;}
}
add_filter('get_attached_file',function($file,$id){
    if(!EgsDriveStorage::enabled()||!is_string($file)||$file==='')return $file;
    egs_drive_wp_materialize($file);egs_drive_wp_materialize($file.'.orig');return $file;
},20,2);

// Drive copies are retained when a WordPress record is deleted; shared images
// and rollback copies are reconciled independently of attachment deletion.
