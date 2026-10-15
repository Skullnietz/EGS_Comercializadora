<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$options=getopt('',array('output:','wordpress-root:'));
if(empty($options['output']))throw new RuntimeException('Indica --output con la ruta privada del manifiesto CSV.');
$wpRoot=isset($options['wordpress-root'])?realpath($options['wordpress-root']):dirname(dirname(__DIR__)).'/public_html';
if(!$wpRoot||!is_file($wpRoot.'/wp-load.php'))throw new RuntimeException('La raíz de WordPress no es válida.');
define('SHORTINIT',true);require $wpRoot.'/wp-load.php';
global $wpdb;$wpdb->query('SET SESSION TRANSACTION READ ONLY');$wpdb->query('START TRANSACTION');
$attachments=$wpdb->get_results("SELECT ID,post_title,post_mime_type FROM {$wpdb->posts} WHERE post_type='attachment'",ARRAY_A);
$metadata=$wpdb->get_results("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_wp_attached_file','_wp_attachment_metadata')",ARRAY_A);
$owners=$wpdb->get_results("SELECT pm.post_id,pm.meta_key,pm.meta_value,p.post_title,p.post_type FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID=pm.post_id WHERE pm.meta_key IN ('_thumbnail_id','_product_image_gallery')",ARRAY_A);
$wpdb->query('ROLLBACK');
$titles=array();foreach($attachments as $a)$titles[$a['ID']]=$a['post_title'];
$posts=array();foreach($owners as $r){foreach(explode(',',$r['meta_value']) as $id){$id=trim($id);if(ctype_digit($id))$posts[$id][$r['post_id']]=$r['post_type'].':'.$r['post_title'];}}
$byId=array();foreach($metadata as $m)$byId[$m['post_id']][$m['meta_key']]=$m['meta_value'];
$refs=array();
foreach($byId as $id=>$m){
    $names=array();if(!empty($m['_wp_attached_file']))$names[]=$m['_wp_attached_file'];
    $meta=isset($m['_wp_attachment_metadata'])?@unserialize($m['_wp_attachment_metadata'],array('allowed_classes'=>false)):null;
    if(is_array($meta)&&!empty($meta['file'])){
        $names[]=$meta['file'];$dir=dirname($meta['file']);$prefix=$dir==='.'?'':$dir.'/';
        if(!empty($meta['original_image']))$names[]=$prefix.$meta['original_image'];
        if(!empty($meta['sizes'])&&is_array($meta['sizes']))foreach($meta['sizes'] as $size)if(is_array($size)&&!empty($size['file']))$names[]=$prefix.$size['file'];
    }
    foreach(array_unique($names)as $name){if(!is_string($name)||strpos($name,'..')!==false||strpos($name,'://')!==false||substr($name,0,1)==='/')continue;$refs['wp-content/uploads/'.$name][$id]=true;}
}
$root=$wpRoot.'/wp-content/uploads';$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS));$finfo=new finfo(FILEINFO_MIME_TYPE);
$header=array('archivo','bytes','tipo','estado','ordenes_confirmadas','ordenes_probables','titulo_orden','fuentes_bd','destino_drive_propuesto','wordpress_attachment_ids','wordpress_post_ids','titulo_registro');
$out=fopen($options['output'],'w');if(!$out)throw new RuntimeException('No se pudo escribir el inventario.');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,$header);$images=0;$linked=0;$unlinked=0;
function egs_wp_csv_safe($v){return is_string($v)&&preg_match('/^[=+\-@\t\r]/',$v)?"'".$v:$v;}
foreach($it as $f){
    if(!$f->isFile()||$f->isLink()||!in_array(strtolower($f->getExtension()),array('jpg','jpeg','png','gif','webp','avif','bmp','ico','svg','tif','tiff','pdf','mp3','m4a','wav','ogg','flac','mp4','mov','webm','mpeg','mpg','avi'),true))continue;
    $mime=$finfo->file($f->getPathname());if(strpos($mime,'image/')!==0&&strpos($mime,'audio/')!==0&&strpos($mime,'video/')!==0&&$mime!=='application/pdf')continue;
    $alias=substr($f->getPathname(),strlen($wpRoot)+1);$ids=isset($refs[$alias])?array_keys($refs[$alias]):array();$postIds=array();$recordTitles=array();
    foreach($ids as $id){if(isset($titles[$id]))$recordTitles[]=$titles[$id];if(isset($posts[$id]))foreach($posts[$id]as $pid=>$title)$postIds[$pid]=$title;}
    $date=dirname(substr($alias,strlen('wp-content/uploads/')));$date=$date==='.'?'Sin_fecha':$date;
    if(count($ids)===1){$state='adjunto_wordpress_confirmado';$dest='Sitio_publico/'.$date.'/Adjunto_'.$ids[0].'/'.$f->getFilename();$linked++;}
    elseif(count($ids)>1){$state='compartida_entre_adjuntos';$dest='Compartidas_sitio_publico/'.$date.'/'.$f->getFilename();$linked++;}
    else{$state='sin_vinculo_wordpress_identificado';$dest='Por_revisar_sitio_publico/'.substr($alias,strlen('wp-content/uploads/'));$unlinked++;}
    $row=array($alias,$f->getSize(),strtolower($f->getExtension()),$state,'','',implode(' | ',$recordTitles),DB_NAME.'.'.$wpdb->posts.';'.DB_NAME.'.'.$wpdb->postmeta,$dest,implode(';',$ids),implode(';',array_keys($postIds)),implode(' | ',array_merge($recordTitles,array_values($postIds))));
    fputcsv($out,array_map('egs_wp_csv_safe',$row));$images++;
}
fclose($out);echo json_encode(array('adjuntos_bd'=>count($attachments),'imagenes'=>$images,'con_adjunto'=>$linked,'sin_vinculo'=>$unlinked))."\n";
