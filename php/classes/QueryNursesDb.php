<?php
require_once('ConfigCls.php');

class QueryNursesDb
{
	private $_db, $log;
	protected $_result;
	
	public function __construct()
	{
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('QueryNursesDb');
		$this->log->setLevel(LoggerLevel::getLevelOff());
		
		$dbConfig = ConfigCls::getDbConfig();
		//                 'hostname', 'username' ,'password', 'database'
		$this->_db = new mysqli($dbConfig['hostname'], $dbConfig['username'] ,$dbConfig['password'], $dbConfig['database']);
						
		if ($this->_db->connect_error) {
			die('Connection Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		}

		$this->_db->set_charset('utf8');

		return $this->_db;
	}
	
	// Return the list of entries in the round for the given "ap"= 0/1 part of the day.
	public function getResults(stdClass $params)
	{
		$this->log->info(var_export($params,true)); 
		
		$query = "SELECT idn,namen,email FROM nurses WHERE activen<>0 ORDER BY namen";
		
		$this->log->info("Nurse Query : ".$query);
		$this->_result = $this->_db->query($query) or die('Connect Error (' . $_db->connect_errno . ') ' . $_db->connect_error);

		$results = array();
		//There are data corresponding to the given data, so we return the data
		while ($row = $this->_result->fetch_assoc()) 
		{
			array_push($results, $row);
		}
		
		$this->log->info(var_export($results,true));
		
		return $results;
	}

	public function getNurseList()
	{
		$query = "SELECT idn,namen,email,shortname FROM nurses";
	
		$this->log->info("Nurse Query : ".$query);
		$this->_result = $this->_db->query($query) or die('Connect Error (' . $_db->connect_errno . ') ' . $_db->connect_error);
	
		$results = array();
		//There are data corresponding to the given data, so we return the data
		while ($row = $this->_result->fetch_assoc())
		{
			$results[(int)($row['idn'])] = $row;
		}
		return $results;
	}
	
	public function createRecord(stdClass $params)
	{
	}
	
	public function updateRecords(stdClass $params)
	{
	}
	
	public function destroyRecord(stdClass $params)
	{
	}
	
	public function __destruct()
	{
		$this->_db->close();
		
		return $this;
	}
}
