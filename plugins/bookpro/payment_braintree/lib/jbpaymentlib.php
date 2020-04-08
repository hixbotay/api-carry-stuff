<?php 
Class JbPaymentLib{
	static function submitForm($data,$actionUrl){
		echo '<form action="'.$actionUrl.'" method="POST" name="jb_payment_form" id="jb_payment_form">';
		foreach($data as $key=>$val){
			echo '<input name="'.$key.'" value="'.$val.'" type="hidden" />';
		}
		echo '</form>';
		echo JText::_('COM_BOOKPRO_LOADING');
		echo '<script>document.jb_payment_form.submit();</script>';
		return;
	}
	
	static function write_log($log_file, $error, $type = E_USER_NOTICE){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$date = date('d/m/Y H:i:s');
		$error = $date.": ".$error."\n";
		
		$log_file = JPATH_ROOT."/logs/".$log_file;
		if(filesize($log_file) > 1048576 || !file_exists($log_file)){
			$fh = fopen($log_file, 'w');
		}
		else{
			//echo "Append log to log file ".$log_file;
			$fh = fopen($log_file, 'a');
		}
		
		fwrite($fh, $error);
		fclose($fh);
	}
	
	
}
?>