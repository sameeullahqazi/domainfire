<?php

DEFINE('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

if (!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID >= 50300) {
	error_reporting(E_ALL); //  & ~E_NOTICE & ~E_DEPRECATED);
} else {
	error_reporting(E_ALL & ~E_NOTICE);
}

// DEFINE('REMOTE_IP', '115.167.101.64');
DEFINE('REMOTE_IP', '203.135.36.152');
DEFINE('LOCAL_IP', '192.168.15.20');


require_once 'includes/app_config.php';
//autoload all required classes from the /classes directory
function __autoload($class_name) {
	if(file_exists('classes/'.$class_name.'.class.php'))
    	require_once 'classes/'.$class_name.'.class.php';
	
	// error_log("class_name in autoload: $class_name");
}

/*

require_once 'classes/Database.class.php';
require_once 'classes/Session.class.php';
require_once 'classes/FrontController.class.php';
require_once 'classes/BasicDataModel.class.php';
require_once 'classes/Common.class.php';
require_once 'classes/Errors.class.php';

*/
// error_log("schoolmanagement index.php file: ");

$db = new Database();
try{
	$db->connect();
} catch(Exception $e)
{
	die ("The following error occurred: ".$e->getMessage());
}

//start the session
session_start();

FrontController::render();

?>
