<?php

require_once __DIR__ . "/../config/backend-session-guard.php";

header("Content-Type: application/json; charset=utf-8");
echo json_encode(array("data" => array()));
