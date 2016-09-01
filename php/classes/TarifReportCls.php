<?php

require_once('ConfigCls.php');
require_once('MailCls.php');
require_once('QueryVisitsDb.php');
require_once('QueryNursesDb.php');

function keepName($a)
{
   if (!strncasecmp($a,"MELLE ", 6))
   	return substr($a, 6);	
   if (!strncasecmp($a,"MR ", 3))
   	return substr($a, 3);	
   if (!strncasecmp($a,"MME ", 4))
   	return substr($a, 4);	
   if (!strncasecmp($a,"SOEUR ", 6))
   	return substr($a, 6);
   return $a;	
}

function patientSortFn($a, $b)
{
	$a = keepName($a);
	$b = keepName($b);
	
	if ($a == $b) 
	{
		return 0;
	}
	return ($a < $b) ? -1 : 1;	
} 

class TarifReportCls
{
	private $_db, $log;
	protected $_result;
	private $_month;
	private $_year;
	private $pdfContent;

	public function __construct($visitDate)
	{
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('TarifReportCls');

		$dbConfig = ConfigCls::getDbConfig();
		//                 'hostname', 'username' ,'password', 'database'
		$this->_db = new mysqli($dbConfig['hostname'], $dbConfig['username'] ,$dbConfig['password'], $dbConfig['database']);
		
		if ($this->_db->connect_error) {
			die('Connection Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		}

		$this->_db->set_charset('utf8');
		//		$this->_db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$date = new DateTime($visitDate);
		$this->_month = $date->format('m');
		$this->_year = $date->format('Y');

		$this->log->info(var_export($date,true));
		
		$this->pdfContent = null;
		
		return $this->_db;
	}
	
	private function getData()
	{
		$data = array();
		
		$data['Date'] = $this->_month."/".$this->_year;
		
		$queryNursesDb = new QueryNursesDb();
		$nurseList = $queryNursesDb->getNurseList();
		
		$queryVisit = new QueryVisitsDb();
		
		$dateRef = $this->_year."-".$this->_month;
		$query = "SELECT idv, parentidv, roundid, datev, rectype, treeindex, namev, ap, nurseid, patientid FROM visits WHERE (datev ".
					"BETWEEN  '".$dateRef."-01' AND '".$dateRef."-31') AND (rectype=30)";

		$this->log->info("Query: ".$query);
		$_result = $this->_db->query($query) or die('Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		
		$results = array();
		$aPatientList = array();
		for ($i=1; $i<=31; $i++)
		{
			$results[$i] = array();
		}
		
		if ($_result->num_rows > 0)
		{
			//There are data corresponding to the given data, so we return the data
			$row = $_result->fetch_assoc();
			while ($row)
			{
				$date=new DateTime($row['datev']);
				$day= intval($date->format('d'));
				if (($day>=1 && $day<=31) && 
						isset($nurseList[(int)($row['nurseid'])]))
				{
					$this->log->info("day:".$day);					
					if (!isset($results[(int)($day)][(int)($row['patientid'])]))
					{
						$this->log->info("Create array: ".$row['patientid']);
						
						$results[(int)($day)][(int)($row['patientid'])] = array('entryList' => array(), 'tblText' => "");
						$prefix = "";
					}
					else
						$prefix = "<br>";
					if ($row['ap'] == 1)
						$apTxt = "s";
					else
						$apTxt = "m";
					$results[(int)($day)][(int)($row['patientid'])]['tblText'] .= $prefix.$nurseList[(int)($row['nurseid'])]['shortname'].'('.(($row['nurseid']==0)?"M":"A").$row['nurseid'].")".$apTxt; 
					$aPatientList[$row['patientid']] = $row['namev'];
				}
		
				$row = $_result->fetch_assoc();
			}
		}
		//Before I save the patients list, I will sort them by alfabetical order
		uasort($aPatientList, 'patientSortFn'); 
		
		$data['List'] = $results;
		$data['patients'] = $aPatientList;

		$this->log->info(var_export($data,true));
		
		return $data;
		
	}
	
	public function output()
	{
		//Compute what has to go in the template
		$data = $this->getData();
				
		//------------- Now, generate the HTML
		// get the HTML
		ob_start();
		include(dirname(__FILE__).'/reports_layout/tarifReportLayout.php');
		$content = ob_get_clean();

		// convert to PDF
		require_once(dirname(__FILE__).'/../html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'fr');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			
			$html2pdf->Output('tarif'.$this->_month.'-'.$this->_year.'.pdf');
			file_put_contents("/opt/ewondev/extjs/inficare/reporthtml/tarif.html", $content);
				
			$result = null;
		}
		catch(HTML2PDF_exception $e) 
		{
			$result = array( 'success' => false, 'message' => $e);
		}
		
		return $result;
	}

	public function __destruct()
	{
		$this->_db->close();

		return $this;
	}
}
