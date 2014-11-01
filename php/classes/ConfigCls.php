<?php

class ConfigCls
{
	//In order to check if I am on the developper platform, I check where this file is stored.
	//This var is not currently used.
	const IS_DEV = false;

	public function __construct()
	{
	}

	public static function loggerFileName()
	{
		return dirname(__FILE__).'/../log/inficare.log';
	}

	public static function isDev()
	{
		//In order to check if I am on the developper platform, I check where this file is stored.
		return ((dirname(__FILE__) == '/opt/crookiesdev/github/inficare/php/classes') ||
				  (dirname(__FILE__) == '/opt/crookiesdev/github/inficare/build/inficare/production/php/classes'));
//	   return self::IS_DEV;
	}

	public static function getNeedHttps()
	{
		if (self::isDev())
			return false;
		else
			return true;
	}

	public static function getDbConfig()
	{
		$dbConfig = array();
		if (self::isDev())
		{
			$dbConfig['hostname'] = '127.0.0.1';
			$dbConfig['username'] = 'root';
			$dbConfig['password'] = '';
			$dbConfig['database'] = 'infidi';
		}
		else
		{
			$dbConfig['hostname'] = '';
			$dbConfig['username'] = '';
			$dbConfig['password'] = '';
			$dbConfig['database'] = '';
      }

      return $dbConfig;
	}

	public function __destruct()
	{
	}
}
