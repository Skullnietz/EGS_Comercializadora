<?php
require_once __DIR__.'/config/DriveStorage.php';
ini_set('zlib.output_compression','0');
while(ob_get_level()>0){$buffer=ob_get_status();if(empty($buffer['flags'])||!($buffer['flags']&PHP_OUTPUT_HANDLER_REMOVABLE))break;ob_end_clean();}
if(function_exists('apache_setenv'))apache_setenv('no-gzip','1');
$site=defined('EGS_DRIVE_WORDPRESS')?'wordpress':'backend';
$uri=isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['REQUEST_URI'])?parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH):'');
$path=rawurldecode(ltrim((string)$uri,'/'));
if(!EgsDriveStorage::mediaPath($path)){http_response_code(404);exit;}
try {
    $key=EgsDriveStorage::normalize($path,$site);$row=EgsDriveStorage::lookup($key);
    if($row){$etag='"'.$row['md5'].'"';$mime=$row['mime'];}
    else {
        $root=$site==='wordpress'?dirname(EgsDriveStorage::root()).'/public_html':EgsDriveStorage::root();
        $file=realpath($root.'/'.$path);$allowed=realpath($root.($site==='wordpress'?'/wp-content/uploads':'/vistas/img'));
        if(!$file||!$allowed||strpos($file,$allowed.'/')!==0||!is_file($file)){http_response_code(404);exit;}
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);
        if(!EgsDriveStorage::mediaMime($mime)){http_response_code(404);exit;}
        $etag='"'.md5_file($file).'"';
    }
    header('Content-Type: '.$mime);header('X-Content-Type-Options: nosniff');header('Cache-Control: public,max-age=300,no-transform');header('ETag: '.$etag);
    if($mime==='image/svg+xml')header("Content-Security-Policy: sandbox; default-src 'none'; style-src 'unsafe-inline'");
    if(isset($_SERVER['HTTP_IF_NONE_MATCH'])&&$_SERVER['HTTP_IF_NONE_MATCH']===$etag){http_response_code(304);exit;}
    header('Accept-Ranges: bytes');
    if($row)$content=EgsDriveStorage::bytes($row);else{$content=file_get_contents($file);if($content===false)throw new RuntimeException('No se pudo leer el archivo.');}
    $total=strlen($content);$start=0;$end=$total-1;
    if(isset($_SERVER['HTTP_RANGE'])&&(!isset($_SERVER['HTTP_IF_RANGE'])||$_SERVER['HTTP_IF_RANGE']===$etag)){
        if(!preg_match('/^bytes=(\d*)-(\d*)$/',trim($_SERVER['HTTP_RANGE']),$range)||($range[1]===''&&$range[2]==='')){http_response_code(416);header('Content-Range: bytes */'.$total);exit;}
        if($range[1]===''){$length=(int)$range[2];if($length<=0){http_response_code(416);header('Content-Range: bytes */'.$total);exit;}$start=max(0,$total-$length);}
        else{$start=(int)$range[1];if($range[2]!=='')$end=min($end,(int)$range[2]);}
        if($start>$end||$start>=$total){http_response_code(416);header('Content-Range: bytes */'.$total);exit;}
        http_response_code(206);header('Content-Range: bytes '.$start.'-'.$end.'/'.$total);
    }
    header('Content-Length: '.($end-$start+1));if($_SERVER['REQUEST_METHOD']!=='HEAD')echo $start===0&&$end===$total-1?$content:substr($content,$start,$end-$start+1);
}catch(InvalidArgumentException $e){http_response_code(404);}
catch(Throwable $e){http_response_code(503);header('Retry-After: 60');header('Cache-Control: no-store');}
