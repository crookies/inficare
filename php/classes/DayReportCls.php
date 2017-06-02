<?php

require_once('ConfigCls.php');
require_once('MailCls.php');
require_once('QueryVisitsDb.php');
require_once('QueryNursesDb.php');

class DayReportCls
{
	private $_db, $log;
	protected $_result;
	private $mailTo, $_visitDate;
	private $pdfContent;


	public function __construct($visitDate)
	{
		$this->_visitDate = $visitDate;
		$this->pdfContent = null;
		
		// Fetch a logger, it will inherit settings from the root logger
		$this->log = Logger::getLogger('DayReportCls');

		$dbConfig = ConfigCls::getDbConfig();
		//                 'hostname', 'username' ,'password', 'database'
		$this->_db = new mysqli($dbConfig['hostname'], $dbConfig['username'] ,$dbConfig['password'], $dbConfig['database']);
		
		if ($this->_db->connect_error) {
			die('Connection Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		}

		$this->_db->set_charset('utf8');
		//		$this->_db->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		
		$this->mailTo = array();

		return $this->_db;
	}
	
	private function getData($mailNurseList)
	{
		$data = array();
		
		$data['Date'] = $this->_visitDate;
		
		//Get the nurse list as array keyed on nurseid, then add the email to the list of receipients
		$this->log->info(var_export($mailNurseList,true));
		
		$queryNursesDb = new QueryNursesDb();
		$nurseList = $queryNursesDb->getNurseList();
		foreach ($mailNurseList as $nurseId)
		{
			if (isset($nurseList[(int)($nurseId)]))
			{
				$this->mailTo[$nurseList[(int)($nurseId)]['namen']]=$nurseList[(int)($nurseId)]['email'];
			}
		}
		
		$queryVisit = new QueryVisitsDb();
		$dayComment = $queryVisit->getDayComment($this->_visitDate);
		if ($dayComment['success'] == true)
		{
			$data['DayComment'] = $dayComment['data']['daycomment'];
			if ($data['DayComment'] == "")
				$data['DayComment'] = "Néant";
		}
		else
			$data['DayComment'] = 'error';
		
		
		$query = "SELECT  idv, parentidv, roundid, datev, rectype, treeindex, namev, ap, nurseid, namen, email, patientid, address, tel, patientinfo,".
				"patientcare, visitinfo FROM visits AS v ".
				"LEFT JOIN patients AS p ON v.patientid = p.idp ".
				"LEFT JOIN nurses AS n ON v.nurseid = n.idn ".
				"WHERE v.datev='".$this->_visitDate."' AND ((v.rectype=20) OR (v.rectype=30)) ORDER BY v.roundid,v.rectype,v.treeindex";
		
		$this->log->info("Query: ".$query);
		$_result = $this->_db->query($query) or die('Query Error (' . $this->_db->connect_errno . ') ' . $this->_db->connect_error);
		
		$results = array();
		$entryCount = 0;
		unset($TopKey);
		if ($_result->num_rows > 0)
		{
			//There are data corresponding to the given data, so we return the data
			$row = $_result->fetch_assoc();
			$entry = null;
			while ($row)
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
						"address" => $row['address'],
						"tel" => $row['tel'],
						"nursename" => $row['namen'],
						"nurseemail" => $row['email'],
						"count" => 0
				);
		
				$results[]=$entry;
				
				$this->mailTo[$entry['nursename']]=$entry['nurseemail'];
				
				if ($entry['rectype'] == '20')
				{
					if (isset($TopKey))
						$results[$TopKey]['count'] = $entryCount;
					$entryCount = 0;
		
					end($results);
					$TopKey=key($results);
						
				}
				else if ($entry['rectype'] == '30')
					$entryCount++;
		
				$row = $_result->fetch_assoc();
			}
			if ($TopKey != null)
				$results[$TopKey]['count'] = $entryCount;
		
				
		}
		
		$data['List'] = $results;
		$this->log->info(var_export($data,true));
		
		return $data;
		
	}
	
	private function mailReport($mailMessage)
	{
		$errorMsg = "";
		$status = false;
		
		$this->log->info(var_export($this->mailTo,true));
		unset($to);
		foreach($this->mailTo as $name => $email)
		{
			if (($email != null) && strpos($email,"@") )
			{
				$dest = $name." <".$email.">";
				if (!isset($to))
					$to = $dest;
				else
					$to = $to.",".$dest;
			}
		}
		if (!isset($to))
		{
			$errorMsg = "Pas de destinataires";
			$status = false;
		}
		else
		{
			$from = 'info@infidi.be';
			$subject = 'Tournée du '.$this->_visitDate;
			$message = $mailMessage;
			
			// Define a list of FILES to send along with the e-mail. Key = File to be sent. Value = Name of file as seen in the e-mail.
			$attachments = array(
					'tournee'.$this->_visitDate.'.pdf' => array( 'IsFile' => false, 'Data' =>$this->pdfContent)
			);
			
			// Define any additional headers you may want to include
			$headers = array(
					'Reply-to' => $from
			);
			
			$mailer = new MailCls();
			
			$status = $mailer->mailAttachments($to, $from, $subject, $message, $attachments, $headers);
			if($status === True) 
			{
				$errorMsg = 'Email envoyé à: '.$to;
			} 
			else 
			{
				$errorMsg = 'Echec d\'envoi de mail.';
			}
		}
		
		$result = array( 'success' => $status, 'message' => $errorMsg);
		return $result;
	}

	public function output($email, $mailMessage, $mailNurseList)
	{
		$this->log->info("date:".$this->_visitDate." ".(($email)?"true":"false"));
		
		//Compute what has to go in the template
		$data = $this->getData($mailNurseList);
				
		//------------- Now, generate the HTML
		// get the HTML
		ob_start();
		include(dirname(__FILE__).'/reports_layout/dayReportLayout.php');
		$content = ob_get_clean();

		// convert to PDF
		require_once(dirname(__FILE__).'/../html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'fr');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			if ($email)
			{
				$this->pdfContent = $html2pdf->Output('tournee'.$this->_visitDate.'.pdf', true);
				$result = $this->mailReport($mailMessage);
		   }
			else
			{
				$html2pdf->Output('tournee'.$this->_visitDate.'.pdf');
				$result = null;
			}
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
