<?php
require_once 'lib/functions.php';
require_once 'lib/db.inc.php';
require_once 'api/rateEverythingAPI.class.php';

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new RateEverythingAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo stripcslashes(trim($API->processAPI(),'"'));
} catch (Exception $e) {
    echo json_encode(Array('status'=>'Error','response' => $e->getMessage()));
}
$Db->close();
