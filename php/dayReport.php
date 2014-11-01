<?php
require('config.php');
require_once('classes/ConfigCls.php');
require_once("classes/DayReportCls.php");
require_once("classes/LogginCls.php");
require_once(dirname(__FILE__).'/log4php/Logger.php');
require_once (dirname(__FILE__).'/KLogger/KLogger.php');

// Tell log4php to use our configuration file.
Logger::configure(dirname(__FILE__).'/log_config.xml');

header('Content-Type: text/plain');

LoginCls::checkHttps();

$login = new LoginCls(ConfigCls::loggerFileName());
$login->checkUser();

if (isset($_GET['email']))
	$email = ($_GET['email']=='true');
else
	$email = false;

if ($email)
{
	if (isset($_GET['mailMessage']))
		$mailMessage = $_GET['mailMessage'];
	else
		$mailMessage = "";
	
	if (isset($_GET['mailNurseList']))
		$mailNurseList = explode(",",$_GET['mailNurseList']);
	else
		$mailNurseList = array();
}
else
{
	//Not used
	$mailMessage = "";
	$mailNurseList = array();
}

$report = new DayReportCls($_GET['visitDate']);
$result = $report->output($email,$mailMessage,$mailNurseList);

if ($result != null)
{
	header('Content-Type: text/javascript');
	echo json_encode($result);
}