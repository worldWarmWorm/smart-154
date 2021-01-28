<?php
if(preg_replace('/^([^?]+)\?(.*)$/', '$1', $_SERVER['REQUEST_URI']) == '/index.php') {
	header("HTTP/1.1 301 Moved Permanently");
	header('Location: /');
	exit;
}
ob_start();

header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING); // ~E_WARNING - Fix PHP 7.0 deprecateds.
// $debugMode=true;
if(!isset($debugMode)) {
    error_reporting(0);
}
ini_set('default_charset', 'utf8');
ini_set('display_errors', empty($debugMode)?'off':'on');
ini_set('short_open_tag', 'on');

date_default_timezone_set('Asia/Novosibirsk');

require_once(dirname(__FILE__).'/protected/components/helpers/HKontur.php');
HKontur::robots();

mb_internal_encoding('utf-8');

define('DS', DIRECTORY_SEPARATOR);

$yii = '../yii/framework/yiilite.php';

if (!is_file($yii))
    $yii = dirname(__FILE__).'/../yii/yiilite.php';

if(is_file(dirname(__FILE__) . DS . 'local.index.php')) {
	include('local.index.php'); 
}

if (!is_file($yii))
    die('Framework not found!');

defined('D_MODE_LOCAL') or define('D_MODE_LOCAL', (strpos($_SERVER['SERVER_NAME'], 'local') !== false));

if(!empty($debugMode)) defined('YII_DEBUG') or define('YII_DEBUG',true);

defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

$config = dirname(__FILE__).'/protected/config/main.php';

require_once($yii);

Yii::createWebApplication($config)->run();

ob_end_flush();
