<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCM
 *
 * @author Ravi Tamada
 */

 /*
 * Google Cloud Messaging
 */

 
class GCM {

    //put your code here
    // constructor
    function __construct() {
//        echo "init GCM\n";
    }

    /**
     * Sending Push Notification
     */
    public function push_message($device_token, $message, $badge = 1, $sound = true){
//		echo "GCM push message\n";
        $fields = array(
            'registration_ids' => array($device_token),
            'data' => array(
            					'badge' => $badge,
            					'message' => $message 
								)
        );

        $headers = array(
            'Authorization: key=' . API_KEY_ANDROID,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, ANDROID_PN_URL);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
        	
            //die('Curl failed: ' . curl_error($ch));
            write_log(LOG_FILE, 'Curl failed: ' . curl_error($ch));
	    }
		else{
			write_log(LOG_FILE, 'Send push notification to GCM successfully');
				
		}
        // Close connection
        curl_close($ch);
        //echo $result;
        write_log(LOG_FILE, $result);
		$result = json_decode($result);
        return $result->success;
    }

}

?>
