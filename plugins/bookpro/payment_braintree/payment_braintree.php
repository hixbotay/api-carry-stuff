<?php
/**
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: request.php 44 2012-07-12 08:05:38Z quannv $
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
require_once (JPATH_ADMINISTRATOR . '/components/com_bookpro/helpers/payment.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php');


// require_once (JPATH_ADMINISTRATOR . '/components/com_bookpro/helpers/log.php');
class plgBookproPayment_braintree extends BookproPaymentPlugin {
	var $_element = 'payment_braintree';
	
	function plgBookpropayment_braintree(& $subject, $config) {
		parent::__construct ( $subject, $config );	
	}
	
	private function getConfig(){		
		$payment_config = AndroidHelper::getPaymentSetting();
		foreach($payment_config->gateways as $gateway){
			if($gateway->code == 'braintree'){
				$data = $gateway;
				$config = $data->params;
				
				$currency = JComponentHelper::getParams('com_bookpro')->get('main_currency');
				$config->currency = $currency;
				$config->testmode = $config->sandbox ? 'sandbox' : 'production';
				
				return $config;
			}
		}
		return false;		
	}
	
	private function setConfig($config = null){
		if(!$config){
			$config = $this->getConfig();
		}		
		$config->testmode = $config->sandbox ? 'sandbox' : 'production';
		Braintree_Configuration::environment($config->testmode);
		Braintree_Configuration::merchantId($config->merchantId);
		Braintree_Configuration::publicKey($config->publicKey);
		Braintree_Configuration::privateKey($config->privateKey);
		return true;
	}
	
	private function formatNumber($value){
		return number_format($value,2,'.','');
	}
	
	
	function _prePayment($data) {
		
		$this->autoload();		
		$this->clientToken = $this->generateToken();	
		
		$this->total = $data['total'];
		$this->order_number = $data['order_number'];
		$this->name = $data['first_name'].' '.$data['last_name'];
		
		
		$html = $this->_getLayout ( 'message' );
		echo $html;
		die;
	}
	
	private function format_number($value){
		return number_format($value,2);
	}
	
	function restpayment($data){
		$this->autoload();
		$input = JFactory::getApplication()->input;
		
		$config = $this->getConfig();
		$this->setConfig($config);
		$data = array(
						'paymentMethodToken' => $data->card->token,
						'amount' => $this->format_number($data->order->total),
						'orderId' => $data->desc,
						'options'=>array(
							'submitForSettlement'=> true
						)
				);
		//var_dump($data);die;
		//AndroidHelper::write_log('braintree.txt', " SALE: ".json_encode($data));
		$payment = Braintree_Transaction::sale($data);
		if($payment->success){
			$result = array(
					'status' => 1,
					'tx_id' => $payment->transaction->id,
					'desc' => $payment->transaction->orderId,
					'total' => $payment->transaction->amount,
					'currency' => $payment->transaction->currencyIsoCode,
					'created' => $payment->transaction->createdAt->format('Y-m-d H:i:s'),
					'method' => $payment->transaction->type
			);
			
			if($payment->transaction->credit_card_details){
				$result['card_info']['type'] = $payment->transaction->creditCard->cardType;
				$result['card_info']['last4'] = $payment->transaction->creditCard->last4;
			}
		}
// 		else if ($payment->errors->deepSize() > 0){
// 			$errors = '';
// 			foreach($payment->errors->deepAll() AS $error) {
				
// 				$errors .= ($error->code.'-'.$error->message);
// 			}
// 			$result = array(
// 					'status'=>0,
// 					'error'=>$payment->transaction->processorSettlementResponseCode				
// 			);
// 			AndroidHelper::write_log('braintree.txt', "ERROR - SALE: ".$errors);			
// 		}
		else {
			$error_code = $payment->transaction->processorSettlementResponseCode ? $payment->transaction->processorSettlementResponseCode : $payment->transaction->processorResponseCode;
			$error_text = $payment->transaction->processorSettlementResponseText ? $payment->transaction->processorSettlementResponseText : $payment->transaction->processorResponseText;
			$result = array(
					'status'=>0,
					'error'=> $error_code,
					'error_message' => $error_text
			);
			$errors = '';
			foreach($payment->errors->deepAll() AS $error) {
				
				$errors .= ($error->code.'-'.$error->message);
			}
			if($errors){
				$errors = PHP_EOL.$errors;
			}
			//AndroidHelper::write_log('braintree.txt', "ERROR - SALE: ".json_encode($payment));
			AndroidHelper::write_log('braintree.txt', "ERROR - SALE: ".$error_code.' '.$error_text.$errors);
			//var_dump($payment);die;
		}
		return $result;
		
		
	}
	
	
	//generate client_nonce
	public function generateToken(){
		$this->setConfig();
		try{
			$result= Braintree_ClientToken::generate();			
		}catch(Exception $e){
			AndroidHelper::write_log('braintree.txt','ERROR: '.$e->getMessage().PHP_EOL.$e->getCode());
			$result= false;
		}
		return $result;
	}
	//generate payment_method_token
	function generatePaymentToken($customer){
		$this->autoload();
		$config = $this->getConfig();
		$this->setConfig($config);
		$name = explode(' ', $customer['name'],1);
		$result = Braintree_Customer::create([
				'firstName' => $name[0],
				'lastName' => $name[1],
				'company' => $customer['company'],
				'email' => $customer['email'],
				'paymentMethodNonce' => $customer['payment_method_nonce']
		]);
		if ($result->success) {
			AndroidHelper::write_log('braintree.txt', "GENERATE PAYMENT_METHOD_TOKEN : CUSTOMER_ID ".$result->customer->id.' TOKEN: '.$result->customer->paymentMethods[0]->token);
			return array(
					'customer_id' => $result->customer->id,
					'token' => $result->customer->paymentMethods[0]->token,
					'exp_month'=>'',
					'exp_year'=>'',
					'type'=>'',
					'last4'=>'',
					'access_token'=>$config,
					'code'=>'creditcard'					
			);
		} else {
			$errors = '';
			foreach($result->errors->deepAll() AS $error) {
				$errors .= $error->code . ": " . $error->message . "\n";
			}
			AndroidHelper::write_log('braintree.txt', "ERROR - GENERATE CARD TOKEN: ".$errors);
			return false;
		}
	}
	//delete customer
	function delete($customer_id,$config=null){
		$this->autoload();
		$this->setConfig($config);
		$result = Braintree_Customer::delete($customer_id);		
		AndroidHelper::write_log('braintree.txt', "DELETE ".json_encode($result));
		return $result->success;
	}
	//create transaction
	function sale($data){
		$this->autoload();
		$this->setConfig();
		$result = Braintree_Transaction::sale(
				array(
						'paymentMethodToken' => $data->card->token,
						'amount' => $data->order->total,
						'options'=>array(
							'submit_for_settlement'=> true
						)
				)
				);
		if($result->success){
			return $result;
		}else{
			$errors = '';
			foreach($result->errors->deepAll() AS $error) {
				$errors .= $error->code . ": " . $error->message . "\n";
			}
			AndroidHelper::write_log('braintree.txt', "ERROR: ".$errors);
			return false;
		}
	}
	
	/**
	 *
	 *        
	 */
	function _postPayment($data) {
		
		
		// Process the payment
		$input = JFactory::getApplication ()->input;
		
		$paction = $input->getString ( 'paction' );
		
		$vars = new JObject ();
		
		switch ($paction) {
			
			case "display_message" :
				
				return $this->displaymsg ();
				
				break;
			case "generatepaymenttoken":
				return $this->generatePaymentToken();
				break;			
			case "process" :
				
				return $this->_processSale ();
				
				$app = JFactory::getApplication ();
				
				$app->close ();
				
				break;
			
			case "cancel" :
				
				$vars->message = JText::_ ( 'COM_BOOKPRO_EWAY_MESSAGE_CANCEL' );
				
				$html = $this->_getLayout ( 'message', $vars );
				
				break;
			
			default :
				
				$vars->message = JText::_ ( 'COM_BOOKPRO_EWAY_MESSAGE_INVALID_ACTION' );
				
				$html = $this->_getLayout ( 'message', $vars );
				
				break;
		}
		
		return $html;
	}
	function displaymsg() {
		
	}
	
	
	
	/**
	 * Prepares variables for the payment form
	 *
	 * @return unknown_type
	 *
	 */
	function _renderForm($data) {
		$user = JFactory::getUser ();
		
		$vars = new JObject ();
		
		$html = $this->_getLayout ( 'form', $vars );
		
		return $html;
	}
	
	
	/**
	 * Processes the sale payment
	 *
	 */
	function _processSale() {
// 		AndroidHelper::write_log('braintree.txt', 'Payment_method_nonce: '.$_POST['payment_method_nonce']);	
		$this->autoload();
		$input = JFactory::getApplication()->input;
		$order_id = $input->getString('order_number');
		
		$orderComplex = AndroidHelper::getOrderDetail($order_id);
		AImporter::classes('order');
		$order = new BookproOrder();
		
		$this->setConfig();
		if($this->sale($orderComplex)){
			$result = array(
					'status'=>1,
					'tx_id'=>$paymentId,
					'desc'=>$transaction[0]->description,
					'total'=>$transaction[0]->getAmount()->getTotal(),
					'currency'=>$transaction[0]->getAmount()->getCurrency(),
					'created'=>$payment->getCreateTime(),
					'method'=>$payment->getPayer()->getPaymentMethod()
			);
			$cardinfo = $payment->getPayer()->getFundingInstruments();
			if($cardinfo[0]){
				$result['card_info']['type'] = $cardinfo[0]->getCreditCardToken()->getType();
				$result['card_info']['last4'] = $cardinfo[0]->getCreditCardToken()->getLast4();
			}
			return $result;
			
		}
		
		
		
	}
	private function autoload(){
		require_once  (JPATH_ROOT.'/plugins/bookpro/payment_braintree/lib/vendor/autoload.php');
//		foreach (glob(JPATH_ROOT.'/plugins/bookpro/payment_braintree/lib/*.php') as $filename)
//		{
//			require $filename;
//		}
	}
	
	private function debug($val,$die =true){
		echo '<pre>';
		print_r($val);
		echo '</pre>';
		if($die){
			die;
		}
	}
	
}
