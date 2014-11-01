<?php
require_once('ConfigCls.php');

class LoginCls
{
	private $_db, $log;


	public function __construct($loggerFile=null)
	{
   	// Fetch a logger, it will inherit settings from the root logger
		if ($loggerFile)
	   	$this->log = new KLogger ( $loggerFile , KLogger::DEBUG );

		session_start();

		$dbConfig = ConfigCls::getDbConfig();
		//                 'hostname', 'username' ,'password', 'database'
		$this->_db = new mysqli($dbConfig['hostname'], $dbConfig['username'] ,$dbConfig['password'], $dbConfig['database']);

		if ($this->_db->connect_error) {
			die('Connection Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		}

		$this->_db->set_charset('utf8');
		//		$this->_db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		return $this->_db;
	}

	public static function httpsUsed()
	{
		if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" )
			return true;
		else
			return false;
	}

	public function login($user, $password)
	{
		$this->log->LogInfo("user:".$user." password:".$password);

		$_SESSION['user'] = null;

		$query = 'SELECT login,password FROM nurses WHERE login<>"" AND UPPER(login)=UPPER("'.$user.'") AND activen=true';

		$results = array();

		$this->log->LogInfo("User : ".$query);
		$_result = $this->_db->query($query);

		if ($_result)
		{
			if ($_result->num_rows > 0)
			{
				//There are data corresponding to the given data, so we return the data
				$row = $_result->fetch_assoc();
				if ($row)
				{
					//Check user/Password
					if ($password == $row['password'])
					{
						$_SESSION['user'] = $user;
						$this->log->LogInfo("user logged:".$user." SID:".htmlspecialchars(session_id()));
					}
					else
					{
						$this->log->LogWarn("Invalid password:".$user);
					   $errorText = 'Login failed';
					}
				}
			}
			else
			{
				$this->log->LogWarn("Invalid user:".$user);
				$errorText = 'Login failed';
			}
		}
		else
		{
			$this->log->LogError('Connect Error (' . $_db->connect_errno . ') ' . $_db->connect_error);
			$errorText = 'System error';
		}

		if (isset($errorText))
		{
			$results['success']=false;
			$results['errors']=array(
					'update' => $errorText
			);
		}
		else
			$results['success']=true;

		$this->log->LogInfo(var_export($results,true));

		return $results;
	}

	/*
	 * This function will invalidate the current session
	 */
	public function logoff()
	{
		$_SESSION['user'] = null;
		$results = array();
		$results['success']=true;
		return $results;
	}

	public static function checkHttps()
	{
		//Check if we need HTTPS, if not return true (means ok)
		if (!ConfigCls::getNeedHttps())
			return true;

		//Else, return true only if HTTP is set
		if (!self::httpsUsed())
			die('HTTPS connection required');
	}

	/*
	 * This function will return the login of the user logged or it will die if there
	 * is no active session
	 */
	public function checkUser()
	{
		if (isset($_SESSION['user']) && ($_SESSION['user']!=null))
		{
			$_SESSION['lasttime'] = date('d/m/Y h:i:s a', time());
			return $_SESSION['user'];
		}
		else
			die('Not logged');
	}

	public function __destruct()
	{
		$this->_db->close();

		return $this;
	}
}
