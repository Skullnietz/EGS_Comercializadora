<?php
define('EGS_DRIVE_WORDPRESS',true);
$bridge=__DIR__.'/egs-hosting.php';
if(is_file($bridge))require_once $bridge;
$backend=defined('EGS_BACKEND_ROOT')?EGS_BACKEND_ROOT:(getenv('EGS_BACKEND_ROOT') ?: dirname(__DIR__).'/backend.comercializadoraegs.com');
require $backend.'/drive-media.php';
