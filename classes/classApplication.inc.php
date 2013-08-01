<?php
class Application extends DB_Class {

	//call this function to clean input from all form fields
	function stripper($girl)
	{
		$girl = trim($girl);
		$girl = addslashes($girl);
		$girl = strip_tags($girl,'<p><br /><b><em><strong><u><ol><ul><li><i><strike>');
		return $girl;
	}

	private function createTable($table)
	{
		// create table sql
		if($table == $this->tableEvents)
		{
			$sql =	"CREATE TABLE IF NOT EXISTS `$this->tableEvents` (
					  `eventID` int(11) NOT NULL AUTO_INCREMENT,
					  `eventName` varchar(200) NOT NULL,
					  `eventDescription` text,
					  `eventStart` datetime,
					  `eventVenue` text,
					  `eventGPS` text,
					  `eventCost` int(11),
					  `eventImage` text,
					  PRIMARY KEY (`eventID`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		} elseif($table == $this->tableSponsors) {
			$sql =	"CREATE TABLE IF NOT EXISTS `$this->tableSponsors` (
					  `sponsorID` int(11) NOT NULL AUTO_INCREMENT,
					  `sponsorName` varchar(200) NOT NULL,
					  `sponsorDescription` text NULL,
					  `sponsorTel` text NULL,
					  `sponsorCell` text NULL,
					  `sponsorURL` text NULL,
					  `sponsorFacebook` text NULL,
					  `sponsorTwitter` text NULL,
					  `sponsorImage` text NULL,
					  `sponsorIcon` text NULL,
					  `sponsorType` text NULL,
					  PRIMARY KEY (`sponsorID`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";			
		} elseif($table == $this->tableResults) {
			$sql =	"CREATE TABLE IF NOT EXISTS `$this->tableResults` (
						`eventID` int(11) NOT NULL,
						`resultPos` int(200) NOT NULL,
						`resultRaceNo` int(200) DEFAULT NULL,
						`resultRaceName` text,
						`resultRaceCat` text,
						`resultRaceTime` text NOT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";								
		} elseif($table == $this->tableLog) {
			// ja ja
		}			
		if(!empty($sql))
		{
			if($this->query($sql))
			{
				//$this->logUserActivity('', '', '', 'Table created: '.$table, '4');
				if(!empty($sql_default))
				{
					if($this->query($sql_default))
					{
						//$this->logUserActivity('', '', '', 'Default record added: '.$table, '4');
					}
				}
				return true;
			}
			else
			{
				//$this->logUserActivity('', '', '', 'Error creating table: '.$table, '4');
				return false;
			}
		}
		return false;
	}
	
	function getip()
	{
		foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip)
		{
			if ($this->validip(trim($ip)))
			{
				return $ip;
			}
		}
		if ($this->validip($_SERVER["HTTP_PC_REMOTE_ADDR"]))
		{
			return $_SERVER["HTTP_PC_REMOTE_ADDR"];
		}
		elseif ($this->validip($_SERVER["HTTP_X_FORWARDED"]))
		{
			return $_SERVER["HTTP_X_FORWARDED"];
		}
		elseif ($this->validip($_SERVER["HTTP_FORWARDED_FOR"]))
		{
			return $_SERVER["HTTP_FORWARDED_FOR"];
		}
		elseif ($this->validip($_SERVER["HTTP_FORWARDED"]))
		{
			return $_SERVER["HTTP_FORWARDED"];
		}
		else
		{
			return $_SERVER["REMOTE_ADDR"];
		}
	}
	
	function validip($ip)
	{
		if (!empty($ip) && ip2long($ip)!=-1)
		{
			$reserved_ips = array (
			array('0.0.0.0','2.255.255.255'),
			array('10.0.0.0','10.255.255.255'),
			array('127.0.0.0','127.255.255.255'),
			array('169.254.0.0','169.254.255.255'),
			array('172.16.0.0','172.31.255.255'),
			array('192.0.2.0','192.0.2.255'),
			array('192.168.0.0','192.168.255.255'),
			array('255.255.255.0','255.255.255.255')
			);

			foreach ($reserved_ips as $r)
			{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
			}
			return true;
		}
		return false;
	}

	function getExtension($str)
	{
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}

	function logCustomerActivity($userID=0, $logDetail)
	{
		date_default_timezone_set($this->serverTZ);
		$ip = $this->getip();
		$thisIP = ip2long($ip);
		$this->checkTableExists($this->tableLog, 'y');
		$this->query("INSERT INTO `$this->tableLog` (userID, logIP, logSession, logIn, logDetail)
				VALUES ('$userID', '$thisIP', '".session_id()."', '".date('Y/m/d - H:i:s')."', '$logDetail')");
	}
			
	function logUserActivity($userID = '', $logTimeIn, $logTimeOut = '0000-00-00 00:00:00', $logDescription, $logCategoryID = 0)
	{
		$this->checkTableExists($this->tableLog, 'y');
		$logUseragent =  htmlspecialchars(str_replace(","," ", $_SERVER['HTTP_USER_AGENT']));
		$logIP = $_SERVER['REMOTE_ADDR'];
		$logSessionID = (isset($_SESSION['sessionID'])) ? $_SESSION['sessionID'] : session_id();
		if($userID == '')
		{
			$userID = (!isset($_SESSION['userID']))?'1':$_SESSION['userID'];
		}
		$os = $this->get_timezone_offset($this->serverTZ, $this->localTZ);
		$logTimeIn = date("Y-m-d H:i:s", time()+$os);		
		$logTimeOut = ($logTimeOut != '') ? $logTimeOut : '0000-00-00 00:00:00';
		$logDescription = htmlentities($logDescription, ENT_QUOTES);
		
		$this->query("INSERT INTO `$this->tableLog`
					(
						userID, logUseragent, logIP, logIn, logOut, logSession, logDetail
					)
					VALUES
					(
						'$userID', '$logUseragent', '$logIP', '$logTimeIn', '$logTimeOut', '$logSessionID', '$logDescription'
					);");
	}

//-----------------------------------------------------------------------------
//FUNCTION: ENSURES THAT ONLY STANDARD CHARACTERS ARE ACCEPTED / SENT OUT for SMSs
	function onlyreadables($string)
	{
		for ($i=0;$i<strlen($string);$i++)
		{
			$chr = $string{$i};
			$ord = ord($chr);
			
			if ($ord>=32 and $ord<=126)
			    continue;
			elseif ($ord>191 and $ord<198)
			    $string{$i} = 'A';
			elseif ($ord>199 and $ord<204)
			    $string{$i} = 'E';
			elseif ($ord>203 and $ord<208)
			    $string{$i} = 'I';
			elseif ($ord>209 and $ord<215)
			    $string{$i} = 'O';
			elseif ($ord>216 and $ord<221)
			    $string{$i} = 'U';
			elseif ($ord>223 and $ord<230)
			    $string{$i} = 'a';
			elseif ($ord>231 and $ord<236)
			    $string{$i} = 'e';
			elseif ($ord>235 and $ord<240)
			    $string{$i} = 'i';
			elseif ($ord>241 and $ord<247)
			    $string{$i} = 'o';
			elseif ($ord>249 and $ord<253)
			    $string{$i} = 'u';
			elseif ($ord>252 and $ord<256)
			    $string{$i} = 'y';
			elseif ($ord==241)
			    $string{$i} = 'n';
			elseif ($ord==209)
			    $string{$i} = 'N';
			else
			    $string{$i} = '.';
		}
		return $string;
	}
//END: FUNCTION: ENSURES THAT ONLY STANDARD CHARACTERS ARE ACCEPTED/SENT OUT
//-----------------------------------------------------------------------------
	
	function send_sms($target, $message, $live = 0)
	{
		if(!$this->isAllowed($target))
		{
			$debug_message = print_r($_SERVER,1) . "\n" . $target . "\n" . $message . "\n" . $live;
			$this->sendEmail($this->support_email_int, 'Alert: unauthorised SMS', '<p>An unauthorised person attempted to send an email from '.$_SESSION['companyName'].'</p><hr />'.$debug_message);
			$target = '';
			return 0;
		}

		$chrs = array (chr(150), chr(147), chr(148), chr(146));
		$repl = array ("-", "\"", "\"", "'");
		$message = str_replace($chrs, $repl, $message);
		$message = $this->onlyreadables($message);
		$message = urlencode($message);

        $body = $message;
        // only allow numbers - remove the "+" etc!!
		$to = preg_replace("@[^0-9]@",'',$target);
		$from = 'foxhorn';
		
		if( isset($this->sms_host) && strlen($this->sms_host)>0 )
		{
			$host = $this->sms_host;
			$URL = "$this->sms_script?to=$to&body=$body&from=$from";
			$port = $this->sms_port;
		}
		else
		{
			if (substr($to,0,1) != '1')
			{
				$host = "synch-rdb.mine.nu";
				$URL = "/MOT_SEND.php?to=$to&body=$body&from=$from";
				$port = "2008";
			}
			else
			{
				$host = "uk1.xpdx.net";
				$URL = "/spi/send_uk2.php?to=$to&body=$body&from=$from";
				$port = "80";
			}
		}

		if($live == 1)
		{
			// the @ sign suppresses errors!!!
			$fp = @fsockopen("$host", $port, $errno, $errstr, 30);
			if ($errno)
			{
				// fail
				$this->sendEmail($this->support_email_int, 'SMS ERROR: '.$_SESSION['campaign'], '<p class="warning">SOCKET</p><p>Could not open socket for <strong>'.$_SESSION['campaign'].'</strong> on '.$_SERVER['HTTP_HOST'].'</p><p>Details: '.$host.$port.'</p>');				
				return 0;
			}
			elseif (!$fp)
			{
				// fail
				$this->sendEmail($this->support_email_int, 'SMS ERROR: '.$_SESSION['campaign'], '<p class="warning">SOCKET</p><p>Could not send SMS '.$_SESSION['campaign'].' on '.$_SERVER['HTTP_HOST'].'</p>');
				return 0;
			}
			else
			{
				$out = "GET $URL HTTP/1.1\r\n";
				$out .= "Host: $host\r\n";
				$out .= "Connection: Close\r\n\r\n";
	
				fwrite($fp, $out);
				while (!feof($fp))
				{
				   $buffer .= fgets($fp, 128);
				}
				// close the socket / connection
				fclose($fp);
				// success
				return 1;
			}
        }
		elseif($live == 0) // for testing, pretend it was sent...
		{
			return 1;
		}			
	}
	
	function makeLookupDropdown($table, $selected)
	{
		$sql = "SELECT * FROM `$table`;";
		$langs = $this->fetch($sql);

		$dropdown = '<select name="'.$table.'">';
		foreach($langs as $lang)
		{
			$dropdown .= '<option';
			if($selected == $lang['langID'])
			{
				$dropdown .= ' selected="selected"';
			}
			$dropdown .= ' value="'.$lang['langID'].'">'.$lang['langFile'].'</option>';
		}
		$dropdown .= '</select>';
		return $dropdown;
	}

	function checkValueExists($tableName,$columnName,$val)
	{
		// check to see if a specific value exists in spec field in a spec table...
		// first make sure the table exists...
		$this->checkTableExists($tableName);
		
		$sql = "SELECT * FROM `$tableName` WHERE `".$columnName."` = '".$val."' LIMIT 1;";
		if($this->fetch($sql))
		{
			// the value does exist!
			return true;
		}
		else
		{
			return false;
		}
	} //end of function 
	
	function checkFieldExists($tableName, $fieldName)
	{
		// check for the table first, creating it if it does not...
		$this->checkTableExists($tableName);
		// check for field name
		$sql = "SHOW COLUMNS FROM `$tableName` LIKE '".$fieldName."';";
		if($this->fetch($sql))
		{
			return true;
		}
		else
		{
			return false;
		}
		return false;
	} //end of function
	
	function getFieldType($tableName, $fieldName)
	{
		// check for the table first, creating it if it does not...
		$this->checkTableExists($tableName);
		// check for field type
		$sql = "SHOW COLUMNS FROM `$tableName` LIKE '".$fieldName."';";

		if($field = $this->fetch($sql))
		{
			return $field[0]['Type'];
		}
		else
		{
			return false;
		}
		return false;
	} //end of function	
		
	function checkTableExists($table, $create = 'y')
	{
		// check if table exists in database
		$thissql = "SHOW TABLES LIKE '".$table."';";
		if(mysql_num_rows(mysql_query($thissql)))
		{
			return true;
		}
		elseif($create == 'y') // run create function
		{
			if($this->createTable($table))
			{
				error_log("Table created: ".$table, 0);
				return true;	
			}
		}
		return false;
	}
	

	
	/**    Returns the offset from the remote timezone (say, USA)  to the origin timezone(local server), in seconds.
	*    @param $remote_tz;
	*    @param $origin_tz; If null the servers current timezone is used as the origin.
	*    @return int;
	*/
	function get_timezone_offset($remote_tz, $origin_tz = null)
	{
		$dst = $this->getDST($date='');
		if($origin_tz === null)
	    {
	        if(!is_string($origin_tz = date_default_timezone_get()))
	        {
	            return false; // A UTC timestamp was returned -- bail out!
	        }
	    }
	    $origin_dtz = new DateTimeZone($origin_tz);
	    $remote_dtz = new DateTimeZone($remote_tz);
	    $origin_dt = new DateTime("now", $origin_dtz);
	    $remote_dt = new DateTime("now", $remote_dtz);
	    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	    $offset = $offset - ($dst*3600);
		return $offset;
	}
	
	private function getDST($date='')
	{
		$this->checkTableExists($this->tableDST, 'y');
		// check to see if a date is in Summer DST
		// if now date is set as parameter, get DST for now...
		date_default_timezone_set($this->serverTZ);
		$date = ($date == '')?date("Y-m-d H:i:s"):$date;
		$year = date("Y", strtotime($date));
		$sql = "SELECT * FROM `$this->tableDST` WHERE '$date' >= `dstStart`  AND '$date' <= `dstEnd` AND `dstYear` = '".$year."';";
		$result = $this->query($sql);
		//error_log('SQL: '.$sql.'<br />Count: '.count($result));
		return count($result);
	}

	function notifymail($to, $subject, $message)
	{
		$body = '<html><head><style>
		<!--
		h1,h2	{ color: #808080; font-family: Tahoma, Arial; font-size: 14pt; font-weight: bold }
		body	{font-family: Tahoma;}
		-->
		</style>
		</head><body><img src="http://www.stylus.co.za/logos/slogo_banner.gif" />
		<h1>Mail from stylus.co.za</h1>
		<p>'.$message.'</p>
		<p>DISCLAIMER: You have received this mail as a result of using the <strong>'.$_SESSION['COMPANY_NAME'].'</strong> website.</p>
		<p>To unsubscribe, please click <a href="http://www.stylus.co.za/eCommerce/unsubscribe.php?un='.$to.'">here...</a></p></body></html>';
	
	
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n".
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'Reply-To: david@stylus.co.za' . "\r\n".
			'From: david@stylus.co.za' . "\r\n".
			'Bcc: david@stylus.co.za' . "\r\n";
			
		
		// error says to use ini_set() for From address if I can't edit php.ini....
			//specify your SMTP server name here; for ex i've mentioned mail.yoursmtpserver.com replace it with yours
				//ini_set("SMTP","mail.yoursmtpserver.com"); 
				
		ini_set("sendmail_from","david@stylus.co.za"); 
		if(mail($to, $subject, $body, $headers))
		{
			return TRUE;
		}
		return FALSE;
	}
		
	function sendEmail($to, $subject, $body)
	{
$msg=<<<_MAIL_
<html>
<head>
<style type="text/css">
h1 {font-size: 28px;}
h2 {font-size: 14px;}
p.warning {border: 1px solid #CC0000; color: #363636; background-color: #e06969; padding: 2em; margin: 2em; font-weight: bold; text-align: center;}
p.alert {border: 1px solid #FED22F; color: #363636; background-color: #FFF0A5; padding: 2em; margin: 2em; font-weight: bold; text-align: center;}
caption {font-weight: bold;}
table {font-family: Arial;}
tr.odd {background-color:#FFFFFF;}
tr.even {background-color:#F0F5FE;}
</style>
</head>
<body style="font-family: Arial">
<div id="mailBody">$body</div>
<hr />
<p>If you did not request this email, or received this email in error, please report it to the Administrator: <a href="mailto:$this->support_email_ext" title="click to send email">$this->support_email_ext</a></p>
<p><img src="http://www.stylus.co.za/logos/slogo_banner.gif" alt="stylus logo" /></p>
</body>
</html>
_MAIL_;
		// send details in an email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .=	'Reply-To: '.$this->product_name.' Admin <'.$this->email_address.'>' . "\r\n";
		$headers .=	'From: '.$this->product_name.' Admin <'.$this->email_address.'>' . "\r\n";
					
		if(mail($to, $subject, $msg, $headers))
		{
			$msg = 'SUCCESS';
		}
		else
		{
			$msg = '<p class="error">Mail problem. <a href="index.php">Home</a>...</p>';
		}
		return $msg;
	}
	
	function checkFolderExists($folder)
	{
		// create a folder for the images for this Branch
		// THIS CODE NEEDS TO BE COMPLETED
		// die($dirName);
		$path = $_SERVER["DOCUMENT_ROOT"].'/eCommerce/';
		//die($path);
		//$path_to_new_folder = $path.$folder;
		//die($path_to_new_folder);
		$pathname = $folder;
		//die($pathname);
		$mode = '0777';
		// make dir if it does not already exist
		if(!is_dir($pathname))
		{
			mkdir($pathname, $mode);
		}
	}
	
	function isAllowed($target)
	{
		// for testing etc
		$allowed_numbers = array('+27844233359'); // David
		if(!$this->isLive())
		{
			if (!in_array($target, $allowed_numbers))
			{
				return false;
			}
		}
		return true;
	}
	
	function isLive()
	{
		$allowed_servers = array('www.stylus.co.za', 'stylus.co.za');
		if(in_array($_SERVER['HTTP_HOST'], $allowed_servers))
		{
			return true; //	this is live
		}
		return false; // this is on a dev box
	}
	
	function processSMS()
	{
		// check to see if the server is online...
		$online = false;
		if($this->isLive())
		{
			$online = $this->isServerOnline($this->server);
		}

		// check the smsOut table for unsent SMS's (not sent and failed) where there is a Reply
		// get the cell number from the _text table
		$sql = "SELECT so.*
				FROM `$this->sms_table` so
				WHERE so.smsSent < '2' AND so.smsMessage != '';";
		if($to_go = $this->fetch($sql))
		{
			if(count($to_go)>0)
			{
				// need to adjust the time of tagging to local time...
				$os = $this->get_timezone_offset($this->serverTZ, $this->localTZ);
				$time = date("Y-m-d H:i:s", time()+$os);
				
				// if there are any unsent SMS's, send them
				foreach($to_go as $key=>$message)
				{
					// only actually send if LIVE!!!!!
					if($online)
					{
						// if the last parameter is 1 -> sets the function "Live"
						$result = $this->send_sms($message['smsOut'], $message['smsMessage'], 1);
					}
					else
					{
						// if the last parameter is 0 -> for testing...
						$result = $this->send_sms($message['smsOut'], $message['smsMessage'], 0);				
					}
					if($result == '1')
					{
						$success = 'sent';
						$sql = "UPDATE `$this->sms_table` SET 
								smsSent = '2',
								sendoutAttempt = '$time'
								WHERE ID = '".$message['ID']."';";
						// charge credits for SMS...
						$this->useCredit(1);
					}
					else
					{
						$success = 'failed';
						$sql = "UPDATE `$this->sms_table` SET 
								smsSent = '1',
								sendoutAttempt = '$time'
								WHERE ID = '".$message['ID']."';";
					}
					// update the smsOut table
					$this->query($sql);
					$this->logUserActivity('1','','','SMS ('.$message['smsMessage'].') '.$success.' (to '.$message['MSISDN_S'].') for '.$message['ID']);
				}
				return 1;
			}
			else
			{
				return 0;	
			}

		}
		else
		{
			error_log('The '.$this->server.' SMS server is not online or you are on a dev machine...');
			return 0;	
		}
		return 0;
	}
	

	function isServerOnline($server='local')
	{

		$content='just testing';
		$from='test';
		if ($this->server=='local')
		{
			$server = "synch-rdb.mine.nu";
			$uri = "/MOT_SEND.php?to=$to&body=$body&from=$from";
			$port = "2008";
		}
		else
		{
			$server = "uk1.xpdx.net";
			$uri = "/spi/send_uk2.php?to=$to&body=$body&from=$from";
			$port = "80";
		}

		$post_results = $this->httpPost($server,$port,$uri,$content);

		if (!is_string($post_results))
		{
    		//die('uh oh, something went wrong');
			$this->sendEmail($this->support_email_int, $_SESSION['companyDataset'].': ERROR', '<p class="warning">Could not open socket for <strong>'.$_SESSION['campaign'].'</strong> on '.$_SERVER['HTTP_HOST'].'</p><p>Details: '.$server.$uri.$port.'</p>');				
			return false;
    	}
		else
		{
    		//die('Here are your results: ' . $post_results);
			return true;
    	}
	}

//
// Post provided content to an http server and optionally
// convert chunk encoded results.  Returns false on errors,
// result of post on success.  This example only handles http,
// not https.
//
	private function httpPost($ip=null,$port=80,$uri=null,$content=null)
	{
		if (empty($ip))         { return false; }
	    if (!is_numeric($port)) { return false; }
	    if (empty($uri))        { return false; }
	    if (empty($content))    { return false; }

		// generate headers in array.
	    $t   = array();
	    $t[] = 'POST ' . $uri . ' HTTP/1.1';
	    $t[] = 'Content-Type: text/html';
	    $t[] = 'Host: ' . $ip . ':' . $port;
	    $t[] = 'Content-Length: ' . strlen($content);
	    $t[] = 'Connection: close';
	    $t   = implode("\r\n",$t) . "\r\n\r\n" . $content;
	    //
	    // Open socket, provide error report vars and timeout of 10
	    // seconds.
	    //
	    $timeout = 1;
		$fp  = @fsockopen($ip,$port,$errno,$errstr,$timeout);
	    // If we don't have a stream resource, abort.
		//die($errno.' '.$errstr);
		if ($errno) { return false; }
	    if (!(get_resource_type($fp) == 'stream')) { return false; }
	    //
	    // Send headers and content.
	    //
		if (!fwrite($fp,$t))
		{
			fclose($fp);
			return false;
		}
	    //
	    // Read all of response into $rsp and close the socket.
	    //
	    $rsp = '';
	    while(!feof($fp)) { $rsp .= fgets($fp,8192); }
	    fclose($fp);
	    //
	    // Call parseHttpResponse() to return the results.
	    //
	    return $this->parseHttpResponse($rsp);
    }

//
// Accepts provided http content, checks for a valid http response,
// unchunks if needed, returns http content without headers on
// success, false on any errors.
//
	private function parseHttpResponse($content=null)
	{
	//echo '<pre>'; print_r($content);
	//die();
	if (empty($content)) { return false; }
    // split into array, headers and content.
    $hunks = explode("\r\n\r\n",trim($content));
    if (!is_array($hunks) or count($hunks) < 2) {
        return false;
        }
	//die('CONTENT : '.$content);		
    $header  = $hunks[count($hunks) - 2];
    $body    = $hunks[count($hunks) - 1];
    $headers = explode("\n",$header);
    unset($hunks);
    unset($header);
    
	if (!$this->validateHttpResponse($headers)) { return false; }
    if (in_array('Transfer-Coding: chunked',$headers)) {
        return trim($this->unchunkHttpResponse($body));
        } else {
        return trim($body);
        }
    }

//
// Validate http responses by checking header.  Expects array of
// headers as argument.  Returns boolean.
//
	private function validateHttpResponse($headers=null)
	{
		//die('$headers: '.$headers);
		if (!is_array($headers) or count($headers) < 1) { return false; }
	    switch(trim(strtolower($headers[0])))
		{
	        case 'http/1.0 100 ok':
	        case 'http/1.0 200 ok':
	        case 'http/1.1 100 ok':
	        case 'http/1.1 200 ok':
	            return true;
	        break;
		}
	 	return false;
    }

//
// Unchunk http content.  Returns unchunked content on success,
// false on any errors...  Borrows from code posted above by
// jbr at ya-right dot com.
//
	private function unchunkHttpResponse($str=null)
	{
	    if (!is_string($str) or strlen($str) < 1) { return false; }
	    $eol = "\r\n";
	    $add = strlen($eol);
	    $tmp = $str;
	    $str = '';
	    do {
	        $tmp = ltrim($tmp);
	        $pos = strpos($tmp, $eol);
	        if ($pos === false) { return false; }
	        $len = hexdec(substr($tmp,0,$pos));
	        if (!is_numeric($len) or $len < 0) { return false; }
	        $str .= substr($tmp, ($pos + $add), $len);
	        $tmp  = substr($tmp, ($len + $pos + $add));
	        $check = trim($tmp);
	        } while(!empty($check));
	    unset($tmp);
	    return $str;
    }
}
?>