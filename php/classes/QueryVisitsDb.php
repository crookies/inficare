<?php
require_once('ConfigCls.php');
require_once('QueryRoundDb.php');

class QueryVisitsDb
{
	private $_db, $log;
	protected $_result;


	public function __construct()
	{
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('QueryVisitsDb');
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

	public function deleteWholeDay($visitDate)
	{
		$this->log->info("deleteWholeDay:".$visitDate);
		unset($errorText);
		$results = array();
		
		$query = "DELETE".
				" FROM visits WHERE datev='".$visitDate."'";
		$this->log->info("Query: ".$query);

		$_result = $this->_db->query($query);
		if ($_result)
		{
           $this->log->info(var_export($_result,true));
		}
		else
			$errorText =  'Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error;
		if (isset($errorText))
		{
			$results['success']=false;
			$results['errors']=array(
					'daycomment' => $errorText
			);
		}
		$this->log->info(var_export($results,true));
		
		return $results;
	}

	private function createVisit($parentidv,
			$roundid,
			$datev,
			$rectype,
			$treeindex,
			$namev,
			$ap,
			$nurseid,
			$patientinfo,
			$patientcare,
			$visitinfo)
	{
		$this->log->info("Create a Visit");
			
		if($stmt = $this->_db->prepare("INSERT INTO visits (parentidv, roundid, datev, rectype, treeindex, namev,  ap, nurseid, patientinfo, patientcare, visitinfo)".
				" VALUES           (?,?,?,?,?,?,?,?,?,?,?)"))
		{

			$stmt->bind_param('iisiisiisss', $_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientinfo, $_patientcare, $_visitinfo);

			$_parentidv = $parentidv;
			$_roundid = $roundid;
			$_datev = $datev;
			$_rectype = $rectype;
			$_treeindex = $treeindex;
			$_namev = $namev;
			$_ap = $ap;
			$_nurseid = $nurseid;
			$_patientinfo = $patientinfo;
			$_patientcare = $patientcare;
			$_visitinfo = $visitinfo;

			if (!$stmt->execute())
			{
				$this->log->error("insert failed (execute is false) (". $stmt->error.")");   // Logged because WARN >= WARN
				die('insert Error 1');
			}

			if( $stmt->affected_rows == 0)
				die('insert Error 2');

			$id = $this->_db->insert_id;
			$this->log->info("Inserted Id ".(string)$id);

			global $gdeblog;
			$str = sprintf("INSERT INTO visits (parentidv, roundid, datev, rectype, treeindex, namev,  ap, nurseid, patientinfo, patientcare, visitinfo)".
				" VALUES           (%d, %d, %s, %d, %d, %s, %d, %d ,%s, %s, %s)",
					$_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientinfo, $_patientcare, $_visitinfo);
			$gdeblog->info("createVisit: ".$str);
			
			$stmt->close();
		}
		else
			die('insert Error 3');

		return $id;  //Id of this inserted visit
	}

	private function createNewRound($visitDate)
	{
		$this->log->info("createNewRound: ".$visitDate);

		$rootId = $this->createVisit(
				null,
				null,
				$visitDate,
				10, //$rectype,
				0, //treeindex
				'root', //$namev,
				null, //$ap,
				0, //$nurseid,
				null, //$patientinfo,
				null, //$patientcare,
				null); //$visitinfo

		$queryRoundDb = new QueryRoundDb();
			
		$treeindex = 0;
		for ($ap=0; $ap<=1; $ap++)
		{
			$roundList = $queryRoundDb->getResults((object)(['ap' => (string)$ap]));
			foreach ($roundList as $round)
			{
				$apr = $round['apr'];
				$namer = $round['namer'];
					
				$this->log->info("name:".$namer."ap:".$apr." idr".$round['idr']);

				$this->createVisit(
						$rootId,
						$round['idr'],
						$visitDate,
						20, //$rectype,
						$treeindex, //treeindex
						$namer, //$namev,
						$apr, //$ap,
						0, //$nurseid,
						null, //$patientinfo,
						null, //$patientcare,
						null); //$visitinfo
				$treeindex++;
					
			}
		}
	}

	public function saveDayComment($formPacket)
	{
		$this->log->info("saveDayComment");
		$this->log->info(var_export($formPacket,true));
		unset($errorText);
		$results = array();

		$query = "UPDATE visits SET visitinfo=? WHERE idv=?";
		if($stmt = $this->_db->prepare($query))
		{
		
			$stmt->bind_param('si', $_visitinfo, $_idv);
		
			$_idv = $formPacket['idv'];
			$_visitinfo = $formPacket['daycomment'];
		
			if (!$stmt->execute())
			{
				$this->log->error("update failed (execute is false) (". $stmt->error.")");   // Logged because WARN >= WARN
				$errorText = 'update Error 1';
			}
		
			global $gdeblog;
			$str = sprintf("UPDATE visits SET visitinfo=%s WHERE idv=%d",
					$_visitinfo, $_idv);
			$gdeblog->info("saveDayComment: ".$str);
				
			$stmt->close();
		}
		else
			$errorText = 'update Error 3 ('.$this->_db->error.')';

		if (isset($errorText))
		{
			$results['success']=false;
			$results['errors']=array(
					'update' => $errorText
			);
		}
		else
			$results['success']=true;
				
		$this->log->info(var_export($results,true));
		
		return $results;
		
	}
	
	public function getDayComment($visitDate)
	{
		$this->log->info("getDayComment:".$visitDate);
		unset($errorText);
		$results = array();
		
		$query = "SELECT  idv, visitinfo".
				" FROM visits WHERE datev='".$visitDate."' and rectype=10";
		$this->log->info("Query: ".$query);
		$_result = $this->_db->query($query);
		if ($_result)
		{
			if ($_result->num_rows > 0)
			{
				//There are data corresponding to the given data, so we return the data
				$row = $_result->fetch_assoc();
				if ($row)
				{
					$results['success']=true;
					$results['data']=array(
									'idv' => $row['idv'],
									'daycomment' => $row['visitinfo'],
							);
				}
				else
					$errorText = 'No record matching'; 
			}
			else
			{
				$results['success']=true;
				$results['data']=array(
						'idv' => null,
						'daycomment' => "",
				);
         }
		}
		else
			$errorText =  'Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error;
		if (isset($errorText))
		{
			$results['success']=false;
			$results['errors']=array(
					'daycomment' => $errorText
			);
		}
		$this->log->info(var_export($results,true));
		
		return $results;
	}
	
	public function getResults(stdClass $params)
	{
		$this->log->warn(var_export($params,true));   // Logged because WARN >= WARN

		if (!property_exists($params, 'visitDate'))
		{
			//We need a visitDate to return any data
			return array();
		}
		
		//If createRount is requested, then first create the empty round
		if (property_exists($params, 'createRound') && ($params->createRound == true))
		{
			$this->log->info("visit date".$params->visitDate);
			$this->createNewRound($params->visitDate);
		}

		$this->log->warn(var_export($params,true));

		$query = "SELECT  idv, parentidv, roundid, datev, rectype, treeindex, namev, ap, nurseid, patientid, patientinfo, patientcare, visitinfo".
				" FROM visits WHERE datev='".$params->visitDate."' ORDER BY roundid,rectype,treeindex";
		$this->log->info("Query: ".$query);
		$_result = $this->_db->query($query) or die('Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);


		$results = array();
		$childLevel1 = array();
		
		if ($_result->num_rows > 0)
		{
			//There are data corresponding to the given data, so we return the data
			$row = $_result->fetch_assoc();
			$entry = null;
			while ($row)
			{
				$keepRow = false;
				if ($row['rectype'] != '10')
				{
					if ($entry == null)
					{
						$entry = array(
								"idv" => $row['idv'],
								"datev" => $row['datev'],
								"rectype" => $row['rectype'],
								"roundid" => $row['roundid'],
								"parentidv" => $row['parentidv'],
								"index" => $row['treeindex'],
								"namev" => $row['namev'],
								"ap" => $row['ap'],
								"nurseid" => $row['nurseid'],
								"patientid" => $row['patientid'],
								"patientinfo" => $row['patientinfo'],
								"patientcare" => $row['patientcare'],
								"visitinfo" => $row['visitinfo'],
								"iconCls" => "task-folder",
								"expanded" => true,
								"allowDrag" => false,
								"allowDrop" => true,
						);
						$useEntry = false;
					}
					else
					{
						if ($row['rectype'] == '20')
						{
							$keepRow = true;
							$useEntry = true;
							$entry['loaded'] = true;
						}
					   else if ($row['rectype'] == '30')
						{
							//Create the children array
							$carray = array();
							while ($row)
							{
								if ($row['rectype'] == '30')
								{
									$centry = array(
											"idv" => $row['idv'],
											"datev" => $row['datev'],
											"rectype" => $row['rectype'],
											"roundid" => $row['roundid'],
											"parentidv" => $row['parentidv'],
											"index" => $row['treeindex'],
											"namev" => $row['namev'],
											"ap" => $row['ap'],
											"nurseid" => $row['nurseid'],
											"patientid" => $row['patientid'],
											"patientinfo" => $row['patientinfo'],
											"patientcare" => $row['patientcare'],
											"visitinfo" => $row['visitinfo'],
											"iconCls" => "task",
											"expanded" => true,
											"allowDrag" => true,
											"allowDrop" => false,
											"leaf" => true
									);
									$centry['loaded'] = true;
									$this->log->info(var_export($centry,true));
									$carray[]=$centry;
								}
								else
									break;
								$row = $_result->fetch_assoc();
							}
							$keepRow = true;
							$useEntry = true;
	
							$entry['children'] = $carray;
						}
					}

					if ($useEntry)
					{
						$this->log->info(var_export($entry,true));
						$results[]=$entry;
						$entry = null;
					}
				}
				if (!$keepRow)
					$row = $_result->fetch_assoc();
			}
			if ($entry !== null)
			{
				if ($entry['rectype'] == '20')
				{
				   $entry['loaded'] = true;
   				$this->log->info(var_export($entry,true));
	   			$results[]=$entry;
					$entry = null;
				}
			}
		}
		$this->log->info(var_export($results,true));
		return $results;
	}

	public function getVisitPatientsList(stdClass $params)
	{
		$this->log->info("getVisitPatientsList");
		
		$query = "SELECT  `idp`, `namep`, `care`, `infop`".
				" FROM patients WHERE activep=1 ORDER BY namep";
		$this->log->info("Query: ".$query);
		$_result = $this->_db->query($query) or die('Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);

		while ($row = $_result->fetch_assoc())
		{
			$entry = array(
					"rectype" => "31", //This is a 30 but mark it as comming from the PatientList source
					"namev" => $row['namep'],
					"patientid" => $row['idp'],
					"patientinfo" => $row['infop'],
					"patientcare" => $row['care'],
					"visitinfo" => ""
			);
			$results[]=$entry;
				
		}
		return $results;
	}
	
	private function insertOneRecord($params)
	{
		if($stmt = $this->_db->prepare("INSERT INTO visits (parentidv, roundid, datev, rectype, treeindex, namev,  ap, nurseid, patientid, patientinfo, patientcare, visitinfo)".
				" VALUES           (?, ?, ?,   ?,      ?,     ?,    ?, ?,  ?,    ?,    ?,    ?)"))
		{
		
			$stmt->bind_param('iisiisiiisss', $_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientid, $_patientinfo, $_patientcare, $_visitinfo);
		
			$_parentidv = $params->parentId;
			$_roundid = $params->roundid;
			$_datev = $params->datev;
			$_rectype = $params->rectype;
			$_treeindex = $params->index;
			$_namev = $params->namev;
			$_ap = $params->ap;
			$_nurseid = $params->nurseid;
			$_patientid = $params->patientid;
			$_patientinfo = $params->patientinfo;
			$_patientcare = $params->patientcare;
			$_visitinfo = $params->visitinfo;
		
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
			$params->idv = $id;
		
			global $gdeblog;
			$str = sprintf("(%d) INSERT INTO visits (parentidv, roundid, datev, rectype, treeindex, namev,  ap, nurseid, patientinfo, patientcare, visitinfo)".
					" VALUES           (%d, %d, %s, %d, %d, %s, %d, %d, %d, %s, %s, %s)",
					 $id, $_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientid, $_patientinfo, $_patientcare, $_visitinfo);
			$gdeblog->info("insertOneRecord: ".$str);
				
			$stmt->close();
		}
		else
			die('insert Error 3');
		
	}
	
	public function createRecord($params)
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
		$query = "UPDATE visits SET parentidv=?, roundid=?, datev=?, rectype=?, treeindex=?, namev=?, ap=?, nurseid=?, patientid=?, patientinfo=?, patientcare=?, visitinfo=? WHERE idv=?";
		if($stmt = $this->_db->prepare($query))
		{
	
			$stmt->bind_param('iisiisiiisssi', $_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientid, $_patientinfo, $_patientcare, $_visitinfo, $_idv);
	
			$_idv = $params->idv;
			$_parentidv = $params->parentId;
			$_roundid = $params->roundid;
			$_datev = $params->datev;
			$_rectype = $params->rectype;
			$_treeindex = $params->index;
			$_namev = $params->namev;
			$_ap = $params->ap;
			$_nurseid = $params->nurseid;
			$_patientid = $params->patientid;
			$_patientinfo = $params->patientinfo;
			$_patientcare = $params->patientcare;
			$_visitinfo = $params->visitinfo;
	
			if (!$stmt->execute())
			{
				$this->log->error("update failed 1 (execute is false) (". $stmt->error.")");   // Logged because WARN >= WARN
				die('update Error 1');
			}
	
			global $gdeblog;
			$str = sprintf("UPDATE visits SET parentidv=%d, roundid=%d, datev=%s, rectype=%d, treeindex=%d, namev=%s, ap=%d, nurseid=%d, patientid=%d, patientinfo=%s, patientcare=%s, visitinfo=%s WHERE idv=%d",
					$_parentidv, $_roundid, $_datev, $_rectype, $_treeindex, $_namev, $_ap, $_nurseid, $_patientid, $_patientinfo, $_patientcare, $_visitinfo, $_idv);
			$gdeblog->info("updateOneRecord: ".$str);
				
			$stmt->close();
		}
		else
		{
			$this->log->error("update failed 3 ('.$this->_db->error.')");
			die('update Error 3 ('.$this->_db->error.')');
		}
	
		return $params;
	}
	
	public function updateRecords($params)
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
		$_db = $this->__construct();

		$idv = $params->idv;
		
		if(is_numeric($idv))
		{
			if($stmt = $_db->prepare("DELETE FROM visits WHERE idv = ? LIMIT 1")) 
			{
				$stmt->bind_param('i', $idv);
				
				if (!$stmt->execute())
				{
					$this->log->error("delete failed (execute is false) (". $stmt->error.")");
					die('delete Error 1');
				}

				global $gdeblog;
				$str = sprintf("DELETE FROM visits WHERE idv = %d LIMIT 1",
						$idv);
				$gdeblog->info("destroyRecord: ".$str);
				
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
		$this->_db->close();

		return $this;
	}
}
