<?php

function get_param_value($param_name, $value_type="string"){
	if(isset($_GET[$param_name]))
		$param_value = $_GET[$param_name];
	else 
		if(isset($_POST[$param_name]))
			$param_value = $_POST[$param_name];
		else {
			$param_value = "";
			if($value_type == "int")
				$param_value = 0;
			else if($value_type == "boolean")
				$param_value = "false";
		}
			 
	return $param_value;
}

function check_valid_cert($cert_file){
	
	if(!file_exists($cert_file)) 
		write_log(LOG_FILE, 'Missing Certificate.', E_USER_ERROR);
	
	clearstatcache();
	$cert_mod = substr(sprintf('%o', fileperms($cert_file)), -3);
	
	if($cert_mod>644)  
		write_log(LOG_FILE, 'Certificate' .$cert_file . 'is insecure! Suggest chmod 644.');
}

function write_log($log_file, $error, $type = E_USER_NOTICE){
	//if(!file_exists($log_file))
	//	fopen($log_file, '+w');
	
//	$backtrace = debug_backtrace();
//	$backtrace = array_reverse($backtrace);
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$date = date('d/m/Y H:i:s');
	$error = $date.": ".$error."\n";
	
	$i=1;
//	foreach($backtrace as $errorcode){
//		$file = ($errorcode['file']!='') ? "-> File: ".basename($errorcode['file'])." (line ".$errorcode['line'].")":"";
//		$error .= "\n\t".$i.") ".$errorcode['class']."::".$errorcode['function']." {$file}";
//		$i++;
//	}
//	$error .= "\n\n";
	
	if(ERROR_LOG_ENABLED){
		$log_file =PROJECT_FOLDER."/log/".$log_file;
		if(filesize($log_file) > LOG_FILE_SIZE || !file_exists($log_file)){
			//echo "Log file ".$log_file." exceeds max size ".LOG_FILE_SIZE." or does not exist";
			$fh = fopen($log_file, 'w');
		}
		else{
			//echo "Append log to log file ".$log_file;
			$fh = fopen($log_file, 'a');
		}
		
		fwrite($fh, $error);
		fclose($fh);
	}
	
			
//	if(ERROR_SHOW_ENABLED)
//		trigger_error($error, $type);
	}

?>