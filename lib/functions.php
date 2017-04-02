<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
date_default_timezone_set('Europe/Dublin');
define('SALT', 'SALT');
define('FILESTORAGE', 'db'); //db,file

if ($_SERVER['SERVER_NAME'] == 'rate-everything.app') {//localhost
    define('HOST_URL', 'http://'.$_SERVER['SERVER_NAME'].'/');
    define('DB_HOST', 'localhost');
    define('DB_PORT', 3306);
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'rate-everything');
    define('DATA_DIR', '');
} elseif ($_SERVER['SERVER_NAME'] == 'rateeverythingapi-aracnoweb.rhcloud.com') {//openshift
    define('HOST_URL', 'http://'.$_SERVER['SERVER_NAME'].'/');
    define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
    define('DB_PORT',getenv('OPENSHIFT_MYSQL_DB_PORT'));
    define('DB_USER',getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
    define('DB_PASSWORD',getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
    define('DB_NAME',getenv('OPENSHIFT_GEAR_NAME'))
    define('DATA_DIR', '');
}

include_once 'sessions/SessionHandler.php';

$session = new MySessionHandler($sessiondb);
session_set_save_handler($session, true);
session_start();
