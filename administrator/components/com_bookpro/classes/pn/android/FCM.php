<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMF
 *
 * @author Ravi Tamada
 */

/*
* Google Cloud Messaging
*/


class FCM {

    //put your code here
    // constructor
    function __construct() {
//        echo "init CM\n";
    }

    /**
     * Sending Push Notification
     */
    public function push_message($device_token, $message, $badge = 1, $sound = true){
//		echo "FCM push message\n";
        $headers = array(
            'Authorization: key=' . API_KEY_ANDROID,
            'Content-Type: application/json'
        );


        $data = array('badge' => $badge,  'message' => $message);


        $payload = array(
            'to' => $device_token,
            'data' => array('badge' => $badge, 'message' => json_encode($data), 'priority' => 'high')
        );


        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, ANDROID_PN_FCM_URL);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {

            //die('Curl failed: ' . curl_error($ch));
            write_log(LOG_FILE, 'Curl failed: ' . curl_error($ch));
        }
        else{
            write_log(LOG_FILE, 'Send push notification to FCM successfully');

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
