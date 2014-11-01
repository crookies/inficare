<?php
require('config.php');
require_once('classes/ConfigCls.php');
require_once("classes/LogginCls.php");

// Insert the path where you unpacked log4php
//require_once(dirname(__FILE__).'/log4php/Logger.php');
// Tell log4php to use our configuration file.
//Logger::configure(dirname(__FILE__).'/log_config.xml');
require_once (dirname(__FILE__).'/KLogger/KLogger.php');

LoginCls::checkHttps();

$login = new LoginCls(ConfigCls::loggerFileName());


if (isset($_GET['logoff']) && $_GET['logoff'])
	$result = $login->logoff();
else
	$result = $login->login($_GET['user'], $_GET['password']);

header('Content-Type: text/javascript');
echo json_encode($result);
