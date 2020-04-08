<?php

include_once JPATH_ADMINISTRATOR."/components/com_bookpro/classes/pn/common/config.php";
include_once JPATH_ADMINISTRATOR."/components/com_bookpro/classes/pn/common/util.php";
include_once JPATH_ADMINISTRATOR."/components/com_bookpro/classes/pn/android/FCM.php";
include_once JPATH_ADMINISTRATOR."/components/com_bookpro/classes/pn/ios/APN.php";

class PNM{
	
    function __construct() {
    }
 
    // destructor
    function __destruct() {
    }
  
  
   	public function push_message($os, $device_token, $pn_id, $message, $badge = 0, $sound = true){
		
   		write_log(LOG_FILE,"Pushing message '".json_encode($message)."' to device_token '".$device_token."', os = '".$os."'...");
   		$os = strtolower($os);
   		if($os != OS_ANDROID && $os != OS_IOS) return false;
		
   		if($os == OS_ANDROID){
   			$pns = new FCM();
   			$result = $pns->push_message($device_token, $message, $badge, $sound);
   		}
		else if($os == OS_IOS){
			$pns = new APN();
			$result = $pns->push_message($device_token, $message, $badge, $sound);
   		}
   		
		if(PUSH_MESSAGE_TRACKING_ENABLED){
 			$this->track_push_message($pn_id, $message, $badge, (int)$result);
   		}
   		return $result;
   			
   	}
    
	public function track_push_message($pn_id, $message, $badge, $status){
		$query = "INSERT INTO #__bookpro_customer_pn_log(pn_id, message, badge, created_time, status)
    				  VALUE('$pn_id', '".json_encode($message)."', '$badge', '".JHtml::_('date','now','Y-m-d H:i:s')."', '$status')";
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$result = $db->execute();
		if(!$result && ERROR_LOG_ENABLED)
			write_log(LOG_FILE, mysql_error());
    
	}

}

?>