<?php
$current_script = $_SERVER["SCRIPT_NAME"];
$break = explode('/', $current_script);
$campaign = $break[1];
$current_page = $break[count($break) - 1];
$document_root = $_SERVER["DOCUMENT_ROOT"].'/';
$app_root = $document_root.'cts/';

/*
 * LIVE

$includes_path = $document_root.'cts/includes'; //live
$BASE_DIR = '/cts/'; // live
 */
/*
 * LOCAL
 */
$includes_path = $app_root.'includes'; // local
//$BASE_DIR = $BASE_DIR; //local

define('LOCAL_TIMEZONE', 'Africa/Johannesburg');

include('classes.inc.php');
?>