<?php
// Publicar la copia versionada de las páginas legales en el WordPress de EGS.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$options=getopt('',array('apply'));
if(!isset($options['apply'])){echo "Uso: php integraciones/wordpress/publicar-legales.php --apply\nPublica las tres páginas y el pie legal; conserva una copia privada del estado anterior.\n";exit;}
define('WP_USE_THEMES', false);
require '/home/egsequip/public_html/wp-load.php';
if (untrailingslashit(home_url('/')) !== 'https://comercializadoraegs.com') { throw new RuntimeException('Sitio inesperado.'); }
$pages = json_decode(file_get_contents(__DIR__.'/legales/pages.json'), true);
if (!is_array($pages)) { throw new RuntimeException('Manifiesto legal inválido.'); }
foreach ($pages as &$page) {
    $file=__DIR__.'/legales/'.basename($page['content_file']);
    $page['content']=file_get_contents($file);
    if($page['content']===false){throw new RuntimeException('Falta contenido legal.');}
}
unset($page);
$plugin = file_get_contents(__DIR__.'/wp-content/mu-plugins/egs-legal-links.php');
if (!is_array($pages) || count($pages)!==3 || strpos($plugin, 'EGS - Información legal pública')===false) { throw new RuntimeException('Contenido inválido.'); }
$backupDir='/home/egsequip/.egs-legal-backups/'.gmdate('Ymd_His').'_'.bin2hex(random_bytes(3));
if (!mkdir($backupDir,0700,true)) { throw new RuntimeException('No se pudo preparar el respaldo.'); }
$pluginPath=WPMU_PLUGIN_DIR.'/egs-legal-links.php';
$backup=array('at'=>gmdate('c'),'privacy_page_id'=>get_option('wp_page_for_privacy_policy'),'pages'=>array(),'mu_plugin_existed'=>is_file($pluginPath));
foreach ($pages as $page) {
    $existing=get_page_by_path($page['slug'],OBJECT,'page');
    $backup['pages'][$page['slug']]=$existing?(array)$existing:null;
}
if (is_file($pluginPath)) { copy($pluginPath,$backupDir.'/egs-legal-links.php'); chmod($backupDir.'/egs-legal-links.php',0600); }
$backupFile=$backupDir.'/before.json';
if (file_put_contents($backupFile,json_encode($backup,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))===false) { throw new RuntimeException('Fallo al respaldar.'); }
chmod($backupFile,0600);
$admins=get_users(array('role'=>'administrator','number'=>1,'fields'=>'ID'));
$author=$admins?(int)$admins[0]:0;
if (!$author) { throw new RuntimeException('No existe un administrador de WordPress.'); }
$result=array('at'=>gmdate('c'),'backup'=>$backupDir,'pages'=>array());
foreach ($pages as $page) {
    $prior=$backup['pages'][$page['slug']];
    $data=array('post_title'=>$page['title'],'post_name'=>$page['slug'],'post_type'=>'page','post_status'=>'publish','post_content'=>$page['content'],'comment_status'=>'closed','ping_status'=>'closed');
    if ($prior) { $data['ID']=$prior['ID']; } else { $data['post_author']=$author; }
    $id=wp_insert_post(wp_slash($data),true);
    if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
    if ($page['slug']==='politica-de-privacidad') { update_option('wp_page_for_privacy_policy',(int)$id); }
    $saved=get_post($id);
    if (!$saved || $saved->post_status!=='publish' || $saved->post_content!==$page['content']) { throw new RuntimeException('La página no coincide después de publicar: '.$page['slug']); }
    $result['pages'][]=array('id'=>$id,'slug'=>$page['slug'],'url'=>get_permalink($id),'sha256_content'=>hash('sha256',$saved->post_content));
}
if (!is_dir(WPMU_PLUGIN_DIR) && !mkdir(WPMU_PLUGIN_DIR,0755,true)) { throw new RuntimeException('No se pudo crear mu-plugins.'); }
$tmp=tempnam(WPMU_PLUGIN_DIR,'egs-legal-');
if ($tmp===false || file_put_contents($tmp,$plugin)===false) { throw new RuntimeException('No se pudo preparar el pie legal.'); }
chmod($tmp,0644);
if (!rename($tmp,$pluginPath)) { throw new RuntimeException('No se pudo publicar el pie legal.'); }
if (function_exists('wp_cache_flush')) { wp_cache_flush(); }
if (class_exists('LiteSpeed\\Purge')) { do_action('litespeed_purge_all'); }
if (function_exists('rocket_clean_domain')) { rocket_clean_domain(); }
if (function_exists('wp_cache_clear_cache')) { wp_cache_clear_cache(); }
$result['footer_plugin']=$pluginPath;
file_put_contents($backupDir.'/after.json',json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
chmod($backupDir.'/after.json',0600);
echo json_encode($result,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
