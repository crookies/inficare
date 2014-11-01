<?php
require_once('ConfigCls.php');

class QueryPatientsDb
{
	private $_db;
	protected $_result;
	
	public function __construct()
	{
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('QueryPatientsDb');
//		$this->log->setLevel(LoggerLevel::getLevelOff());
		
		$dbConfig = ConfigCls::getDbConfig();
		//                 'hostname', 'username' ,'password', 'database'
		$this->_db = new mysqli($dbConfig['hostname'], $dbConfig['username'] ,$dbConfig['password'], $dbConfig['database']);
						
		if ($this->_db->connect_error) {
			die('Connection Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		}

		$this->_db->set_charset('utf8');

		return $this->_db;
			}
	
	public function getResults(stdClass $params)
	{
		
		$this->log->info(var_export($params,true)); 
		
		$query = "SELECT idp,activep,namep,address,tel,care,infop FROM patients";
		
		$this->log->info("Patients Query : ".$query);
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
	
	public function getPatientList()
	{
		$query = "SELECT idp,activep,namep,address,tel,care,infop FROM patients";
		
		$this->log->info("Patients Query : ".$query);
		$this->_result = $this->_db->query($query) or die('Connect Error (' . $_db->connect_errno . ') ' . $_db->connect_error);
		
		$results = array();
		//There are data corresponding to the given data, so we return the data
		while ($row = $this->_result->fetch_assoc())
		{
			$results[(int)($row['idp'])] = $row;  //Push the row using idp as index
		}
		
		return $results;
		
	}
	
	private function insertOneRecord($params)
	{
		if($stmt = $this->_db->prepare("INSERT INTO patients (activep, namep, address, tel, care, infop)".
				" VALUES           (?, ?, ?, ?, ?, ?)"))
		{
	
			$stmt->bind_param('isssss', $_activep, $_namep, $_address, $_tel, $_care, $_infop);
	
			$_activep = $params->activep;
			$_namep   = $params->namep;
			$_address = $params->address;
			$_tel     = $params->tel;
			$_care    = $params->care;
			$_infop   = $params->infop;
			
			if (!$stmt->execute())
			{
				$this->log->error("insert failed (execute is false) (". $stmt->error.")");   // Logged because WARN >= WARN
				die('insert Error 1');
			}
	
			if( $stmt->affected_rows == 0)
				die('insert Error 2');
	
			//We need to update the record id in the response during creation
			$id = $this->_db->insert_id;
			$this->log->info("Inserted Id ".(string)$id);
			$params->idp = $id;
	
			$stmt->close();
		}
		else
			die('insert Error 3');
	}
	
	public function createRecord(stdClass $params)
	{
		$this->log->info("createRecord");
		$this->log->info(var_export($params,true)); 

		if (gettype($params)=="array")
		{
			foreach ($params as $onerecord)
				$this->insertOneRecord($onerecord);
		}
		else
			$this->insertOneRecord($params);
		
		return $params;
	}
	
	private function updateOneRecord($params)
	{
		$query = "UPDATE patients SET activep=?, namep=?, address=?, tel=?, care=?, infop=? WHERE idp=?";
		if($stmt = $this->_db->prepare($query))
		{
	
			$stmt->bind_param('isssssi', $_activep, $_namep, $_address, $_tel, $_care, $_infop, $_idp);
	
			$_idp     = $params->idp;
			$_activep = $params->activep;
			$_namep   = $params->namep;
			$_address = $params->address;
			$_tel     = $params->tel;
			$_care    = $params->care;
			$_infop   = $params->infop;

			$this->log->info("Escaped Address:".$_address." Non escapted:".$params->address);
				
			if (!$stmt->execute())
			{
				$this->log->error("update failed (execute is false) (". $stmt->error.")");
				die('update Error 1');
			}
	
			if( $stmt->affected_rows == 0)
			{
				//Do not consider as an error as it may happen if the record is updated with identical values
			}
	
			$stmt->close();
		}
		else
		{
			$this->log->error("update failed 3 ('.$this->_db->error.')");
			die('update Error 3 ('.$this->_db->error.')');
		}
	
		return $params;
	}
	
	public function updateRecords(stdClass $params)
	{
		$this->log->info("updateRecord");
		$this->log->info(var_export($params,true));
		
		if (gettype($params)=="array")
		{
			foreach ($params as $onerecord)
				$this->updateOneRecord($onerecord);
		}
		else
			$this->updateOneRecord($params);
		
		return $params;
			}
	
	public function destroyRecord(stdClass $params)
	{
		$id = $params->idp;
		
		if(is_numeric($id)) {
			if($stmt = $this->_db->prepare("DELETE FROM patients WHERE idp = ? LIMIT 1")) 
			{
				$stmt->bind_param('i', $id);
				if (!$stmt->execute())
				{
					$this->log->error("delete failed (execute is false) (". $stmt->error.")");
					die('delete Error 1');
				}
							
				$stmt->close();
			}
			else
			{
				$this->log->error("delete failed 3 ('.$this->_db->error.')");
				die('delete Error 3 ('.$this->_db->error.')');
			}
		}
		else
		{
			$this->log->error("delete failed 4 ('.$this->_db->error.')");
			die('delete Error: id not numeric ('.$id.')');
		}
		
		return $this;
	}
	
	public function __destruct()
	{
		$_db = $this->__construct();
		$_db->close();
		
		return $this;
	}
}
