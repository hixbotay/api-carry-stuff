<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/



/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class BookproPN extends JObject
{	
	public function __construct($array = null){
		$this->params = JComponentHelper::getParams('com_bookpro');
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	public function push_message($os, $device_token, $pn_id, $message, $badge = 1, $sound = true){
		
   		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/common/PNM.php';
   		$pn = new PNM();
   		return $pn->push_message($os, $device_token, $pn_id, $message, $badge, $sound);
   			
   	}
   	
   	public function push_message_ios($device_token, $message, $badge = 0, $sound = true, $user_id = NULL){
   		$this->apns_data = array(
			'production'=>array(
				'certificate'=> '../cert/aps_production.pem',
				'ssl'		=> 'ssl://gateway.push.apple.com:2195',
				'feedback'	=> 'ssl://feedback.push.apple.com:2196'
			),
			'development'=>array(
				'certificate'	=> '../cert/aps_development.pem',
				'ssl'			=> 'ssl://gateway.sandbox.push.apple.com:2195',
				'feedback'		=> 'ssl://feedback.sandbox.push.apple.com:2196'
			)
		);
		check_valid_cert(IOS_PN_CERT_DEVELOPMENT);
		check_valid_cert(IOS_PN_CERT_PRODUCTION);
   	}
   	
   	public function push_message_android($device_token, $message, $badge = 0, $sound = true, $user_id = NULL){
       
   		$pn_url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => array($device_token),
            'data' => array(
            					'badge' => $badge,
            					'message' => $message 
								)
        );

        $headers = array(
            'Authorization: key=' . $this->params->get('pn_key_android'),
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $pn_url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        
        curl_close($ch);
        
        return $result;
    }
    
    
   	
}
?>
