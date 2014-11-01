<?php
require('config.php');
require_once('classes/ConfigCls.php');
require_once("classes/TarifReportCls.php");
require_once("classes/LogginCls.php");
require_once(dirname(__FILE__).'/log4php/Logger.php');
require_once (dirname(__FILE__).'/KLogger/KLogger.php');

// Tell log4php to use our configuration file.
Logger::configure(dirname(__FILE__).'/log_config.xml');

header('Content-Type: text/plain');

LoginCls::checkHttps();

/*
$login = new LoginCls(ConfigCls::loggerFileName());
$login->checkUser();
*/

$report = new TarifReportCls($_GET['visitDate']);
$result = $report->output();

if ($result != null)
{
	header('Content-Type: text/javascript');
	echo json_encode($result);
}