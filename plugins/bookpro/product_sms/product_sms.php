<?php

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

use Clickatell\Api\ClickatellHttp;
jimport ( 'joomla.plugin.plugin' );
class plgBookproProduct_sms extends JPlugin {
	var $_url;
	var $_text;
	
	// plugin core
	function __construct(&$subject, $config) {
		parent::__construct ( $subject, $config );
	}
	/**
	 * Twilio SMS api
	 *
	 * @return
	 *
	 */
	public function twilioSMS($user,$password,$api_id,$sms) {
		// this line loads the library
		require_once ('lib/Twilio.php');
		
		$account_sid =$user;
		// $auth_token = '[AuthToken]';
		$auth_token = $password;
		$client = new Services_Twilio ( $account_sid, $auth_token );
		
		// Valiud number is +XXX ex: +849311111
		$sms ['mobile'] = "+" . $sms ['mobile'];
		
		return $client->account->messages->create ( array (
				'From' => $api_id,
				'To' => $sms['mobile'],
				'Body' => $sms['sms'] ,
		) );
	}
	
	public function onBookproCancelSMS($id){
		
		require ('lib/vendor/autoload.php');
		//$sms ['mobile']="+84 912348149";
		
		$user = $this->getParam ( 'user' );
		$password = $this->getParam ( 'password' );
		$api_id = $this->getParam ( 'api_id' );
		$from_number = $this->getParam ( 'from_number' );
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'od.*,c.mobile,c.firstname,c.lastname,c.email' );
		$query->from ( '#__bookpro_orders AS od' );
		$query->leftJoin ( '#__bookpro_customer AS c ON c.id=od.user_id' );
		$query->where ( 'od.id =' . $id );
		$db->setQuery ( ( string ) $query );
		
		$order1 = $db->loadObject ();
		
		$ticket = $this->getParam ( 'cancel' );
		
		$ticket = str_replace ( '{order_number}', $order1->order_number, $ticket );
		
		$sms=array('mobile'=>$order1->mobile,'sms'=>$ticket);
		
		$country_code = $this->getParam ( 'country_code' );
		$sms ['mobile']=$country_code.$sms ['mobile'];
		$clickatell = new ClickatellHttp ( $user, $password, $api_id );
		$response = $clickatell->sendMessage ( array (
				$sms ['mobile']
		), $sms ['sms'] );
		
		foreach ( $response as $message ) {
			echo $message->id;
				
			// Message response fields:
			$message->id;
			$message->destination;
			$message->error;
			$message->errorCode;
		}
		return $response;
		
	}
	public function onBookproMassSend($data){
		require ('lib/vendor/autoload.php');
		
			
		$user = $this->getParam ( 'user' );
		$password = $this->getParam ( 'password' );
		$api_id = $this->getParam ( 'api_id' );
		$from_number = $this->getParam ( 'from_number' );
		
		//$sms ['mobile']="+84 912348149";
		$country_code = $this->getParam ( 'country_code' );
		
		$mobiles=implode(',',$data['mobile']);
		$mobs=array();
		
		for ($i = 0; $i < count($mobiles); $i++) {
			$mobs [$i]=$country_code.$mobiles [$i];
		}
				
		$clickatell = new ClickatellHttp ( $user, $password, $api_id );
		$response = $clickatell->sendMessage ( $mobs, $data ['sms'],array('mo'=> 1) );
		
		$results=array();
		foreach ( $response as $message ) {
			$result=array();
			$result['id']=$message->id;
			$result['destination']=$message->destination;
			$result['error']=$message->error;
			$result['errorCode']=$message->errorCode;
			$results[]=$result;			
			
		}
		return $results;
		
	}
	
	private function getPhoneNumber($phone){
		$country_code =$this->getParam ( 'country_code' );
		if($phone[0] == 0){
			$phone = substr($phone,1);
		}
		if(strlen($phone) < 10){
			$result = $country_code.$phone;
		}else{
			$result = $phone;
		}
		if($result[0] != '+'){
			$result = '+'.$result;
		}
		return $result;
	}
	
	private function clickatellSMS($user, $password, $api_id, $sms) {
		
		require ('lib/vendor/autoload.php');
		
		//$sms ['mobile']=$country_code.$sms ['mobile'];
		
		$clickatell = new ClickatellHttp ( $user, $password, $api_id );
		$messages=array();
		$phone = $this->getPhoneNumber($sms ['mobile']);
		//$sms ['mobile']="912348149";
		//var_dump($response);die;
		$response = $clickatell->sendMessage ( array (	$phone), $sms ['sms'],array('mo'=> 1) );
		//var_dump($response);die;
		foreach ( $response as $message ) {
			//echo $message->id;
			// Message response fields:
			$message->id;
			$message->destination;
			$message->error;
			$message->errorCode;
			$messages[]=$message;
		}
		
		return ($messages);
		
		
	}
	
	/**
	 * Clickatell SMS api
	 *
	 * @return
	 *
	 */
	
	public function onBookproSendSms($data) {
		global $option;
		$user = $this->getParam ( 'user' );
		$password = $this->getParam ( 'password' );
		$api_id = $this->getParam ( 'api_id' );
		$from_number = $this->getParam ( 'from_number' );
		
		$gateway = $this->getParam ( 'gateway' );
		// Switch gateway
		$sms = $this->createSMS ($data["sms"], $data["phone"]);
		
		if ($gateway == 1) {
			// Send sms by clickatellSMS
			return $this->clickatellSMS ( $user, $password, $api_id, $sms );
		} else if ($gateway == 2) {
			return $this->twilioSMS ($user,$password,$from_number, $sms );
			
		} 
		// end if
	}
	
	// short function to receive $_params value
	function getParam($param) {
		return $this->params->get ( $param );
		// bookpro_orders
	}
	private function  getBustrip($id){
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'bustrip.*' );
		$query->select ( 'CONCAT(`dest1`.`title`,' . $db->quote ( '-' ) . ',`dest2`.`title`) AS title' );
		$query->from ( '#__bookpro_bustrip AS bustrip' );
		$query->select ( 'agent.company,agent.brandname,agent.image as agent_logo' );
		$query->join ( 'LEFT', '#__bookpro_agent AS agent ON agent.id = bustrip.agent_id' );
		$query->select ( 'bus.title AS bus_name' );
		$query->join ( 'LEFT', '#__bookpro_bus AS bus ON bus.id = bustrip.bus_id' );
		$query->select ( 'dest1.title AS from_name' );
		$query->join ( 'LEFT', '#__bookpro_dest AS dest1 ON bustrip.from = dest1.id' );
		$query->select ( 'dest2.title AS to_name' );
		$query->join ( 'LEFT', '#__bookpro_dest AS dest2 ON bustrip.to = dest2.id' );
		$query->where ( 'bustrip.id = ' . $id );
		$db->setQuery ( $query );
		$bustrip = $db->loadObject ();
		return $bustrip;
		
	}
	/**
	 * Create sms content
	 * @param $html
	 * @param $phone
	 */
	private function createSMS($html,$phone) {	
		$cmobile = preg_replace ( "/[^0-9]/", "", $phone);			
		$smss= array (
				'sms' => $html,
				'mobile' => $cmobile
		);
		//echo "<pre>";print_r($smss);die;
		return $smss;
		
	}
}