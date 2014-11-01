<?php
class MailCls
{

	public function __construct()
	{
		
	}
	
	public function __destruct()
	{
	}
	

	function mailAttachments($to, $from, $subject, $message, $attachments = array(), $headers = array(), $additional_parameters = '') 
	{
		$headers['From'] = $from;
	
		// Define the boundray we're going to use to separate our data with.
		$mime_boundary = '==MIME_BOUNDARY_' . md5(time());
	
		// Define attachment-specific headers
		$headers['MIME-Version'] = '1.0';
		$headers['Content-Type'] = 'multipart/mixed; boundary="' . $mime_boundary . '"';
	
		// Convert the array of header data into a single string.
		$headers_string = '';
		foreach($headers as $header_name => $header_value) {
			if(!empty($headers_string)) {
				$headers_string .= "\r\n";
			}
			$headers_string .= $header_name . ': ' . $header_value;
		}
	
		// Message Body
		$message_string  = '--' . $mime_boundary;
		$message_string .= "\r\n";
		$message_string .= 'Content-Type: text/plain; charset="iso-8859-1"';
		$message_string .= "\r\n";
		$message_string .= 'Content-Transfer-Encoding: 7bit';
		$message_string .= "\r\n";
		$message_string .= "\r\n";
		$message_string .= $message;
		$message_string .= "\r\n";
		$message_string .= "\r\n";
	
		// Add attachments to message body
		foreach($attachments as $attachment_filename => $attData ) 
		{
			if ($attData['IsFile'])
			{
				$local_filename = $attData['FileName'];
				if(is_file($local_filename)) 
				{
					$fp = @fopen($local_filename, 'rb'); // Create pointer to file
					$file_size = filesize($local_filename); // Read size of file
					$data = @fread($fp, $file_size); // Read file contents
				}
				else
				{
					$data ='';
					$file_size = 0;
				}
			}
			else
			{
				$data = $attData['Data'];
				$file_size = strlen($data);
			}
			
			$message_string .= '--' . $mime_boundary;
			$message_string .= "\r\n";
			$message_string .= 'Content-Type: application/octet-stream; name="' . $attachment_filename . '"';
			$message_string .= "\r\n";
			$message_string .= 'Content-Description: ' . $attachment_filename;
			$message_string .= "\r\n";
			$message_string .= 'Content-Disposition: attachment; filename="' . $attachment_filename . '"; size=' . $file_size.  ';';
			$message_string .= "\r\n";
			$message_string .= 'Content-Transfer-Encoding: base64';
			$message_string .= "\r\n\r\n";
			$data = chunk_split(base64_encode($data)); // Encode file contents for plain text sending
			$message_string .= $data;
			$message_string .= "\r\n\r\n";
		}
	
		// Signal end of message
		$message_string .= '--' . $mime_boundary . '--';
	
		// Send the e-mail.
		return mail($to, $subject, $message_string, $headers_string, $additional_parameters);
	}
}	
