<?php
require_once('ConfigCls.php');

class QueryRoundDb
{
	private $_db, $log;
	protected $_result;
	
	public function __construct()
	{
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('QueryRoundDb');
		$this->log->setLevel(LoggerLevel::getLevelFatal());
				
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
	
	// Return the list of entries in the round for the given "ap"= 0/1 part of the day.
	public function getResults(stdClass $params)
	{
		$this->log->info(var_export($params,true)); 
		
		if (property_exists($params, 'ap'))
		   $apQuery = "WHERE (apr=".$params->ap.") AND (activer<>0)";
		else	
		   $apQuery = "WHERE (activer<>0)";
		$query = "SELECT idr,namer,apr FROM rounds ".$apQuery." ORDER BY namer";
		
		$this->log->info("Round Query : ".$query);
		$this->_result = $this->_db->query($query) or die('Connect Error (' . $_db->connect_errno . ') ' . $_db->connect_error);

		$results = array();
		if ($this->_result->num_rows > 0)
		{
			//There are data corresponding to the given data, so we return the data
			while ($row = $this->_result->fetch_assoc()) 
			{
				array_push($results, $row);
			}
		}
		
		$this->log->info(var_export($results,true));
		
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
