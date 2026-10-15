<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once dirname(__DIR__).'/config/DriveStorage.php';
echo EgsDriveStorage::ownerLink()."\n";
