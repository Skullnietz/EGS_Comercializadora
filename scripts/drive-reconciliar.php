<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once dirname(__DIR__).'/config/DriveStorage.php';
if(!EgsDriveStorage::enabled())exit;
$lock=fopen(EgsDriveStorage::stateDir().'/reconcile.lock','c');if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))exit;
try{echo json_encode(array('ordenes_organizadas'=>EgsDriveStorage::reconcileOrders(25)))."\n";}
catch(Throwable $e){fwrite(STDERR,'No se pudo completar la organización de imágenes en Drive. Se conserva la cola para reintentar.'."\n");exit(1);}
finally{flock($lock,LOCK_UN);fclose($lock);}
