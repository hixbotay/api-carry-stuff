<?php


defined('_JEXEC') or die('Restricted access');
use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

require_once (JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/payment.php');

class plgBookproPayment_paypal extends BookproPaymentPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element    = 'payment_paypal';
   	protected $api_username   = '';
    protected $api_password    = '';
    protected $api_signature    = '';
    var $_isLog      = false;

    function plgBookproPayment_paypal(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( 'plg_bookpro_'.$this->_element, JPATH_ADMINISTRATOR );
		$payment_config = $this->getPaymentConfig();
		$this->api_clientId = trim($payment_config->params->client_id);//'AbfRs5zW25I4A2pg6jSVgPH5ysb7zqzqXwsH99n-mYEDt51Coyf3lLuQ467jbb9yobmFjMufPSzr139a';
		$this->api_clientSecret= trim($payment_config->params->secret);//'EBFSwAhEIo4dwekr6iw0ICcl1zRgnoMJIhIHTiqSQQZrseAy4eWYGP-z8v9wc52S86CzyE1CKcdkqVLM';
		$this->api_mode = $payment_config->params->sandbox ? 'sandbox' : 'live';
		
		$currency = trim($this->params->get('pp_api_currency'));
		if(empty($currency)){
			$config = JComponentHelper::getParams('com_bookpro');
			$currency = $config->get('main_currency');
		}
		$this->currency = $currency;
	}
	private function getPaymentConfig(){
		require_once (JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php');		
		$payment_config = AndroidHelper::getPaymentSetting();
		foreach($payment_config->gateways as $gateway){
			if($gateway->code == 'paypal'){
				return $gateway;
			}
		}
		return false;
	}
	private function _autoload() {
		require_once (JPATH_ROOT.'/plugins/bookpro/payment_paypal/lib/vendor/autoload.php');
		require_once ('lib/vendor/paypal/rest-api-sdk-php/sample/common.php');
		
	}
 	private function getApiContext(){
    	$apiContext = new ApiContext(
	        new OAuthTokenCredential(
	            $this->api_clientId,
	            $this->api_clientSecret
	        )
	    );
		
		$config = array(
			'mode' => $this->api_mode,
			'log.LogEnabled' => true,
			'log.FileName' => 'PayPal.log',
			'log.LogLevel' => 'FINE'
		  );
		 
	     $apiContext->setConfig($config);	
		
		//echo $this->api_mode;
	    return $apiContext;
    }
    
    /**
     * @param $data     array       form post data
     * @return string   HTML to display
     */
    function _prePayment(){
    	
    }

    
    function restpayment( $data )
    {
		//var_dump($data->card);die;
    	$this->_autoload();
    	$apiContext = $this->getApiContext();
		//var_dump($apiContext);die;
    	//set card token
		//$data->card_token='dfdfdfdddf';
    	$creditCardToken = new CreditCardToken();
    	$creditCardToken->setCreditCardId($data->card->token);
		$creditCardToken->setPayerId($data->card->payer_id);
    	
    	$fi = new FundingInstrument();
    	$fi->setCreditCardToken($creditCardToken);
		
		$payer = new Payer();
		$payment_method= 'credit_card';
		$payer->setPaymentMethod($payment_method)
		    ->setFundingInstruments(array($fi));
		$payerInfo = new PayerInfo();
		$payerInfo->setFirstName($data->customer->firstname);
		$payerInfo->setLastName($data->customer->lastname);
		$payerInfo->setEmail($data->customer->email);
		
// 		$payerInfo->setPayerId($data->card->payer_id);
		$payer->setPayerInfo($payerInfo);
		
		$amount = new Amount();
		$amount->setCurrency($this->currency);
		$amount->setTotal($data->order->total);
		
		$item1 = new Item();
		$item1->setName($data->order->id)
		    ->setDescription($data->desc)
		    ->setCurrency($this->currency)
		    ->setQuantity(1)
		    ->setTax(0)
		    ->setPrice($data->order->total);
		
		$itemList = new ItemList();
		$itemList->setItems(array($item1));

		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setItemList($itemList);
		$transaction->setDescription('alloandgo');
		
		$payment = new Payment();
		$payment->setIntent("sale");
		$payment->setPayer($payer);
		$payment->setTransactions(array($transaction));
		$request = clone $payment;
	    try {
	    	$payment->create($apiContext);
		
		} catch (PayPal\Exception\PayPalConnectionException $ex) {			
			//require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
			//AndroidHelper::write_log('jbpayment_paypal.txt',$ex->getMessage().PHP_EOL.$ex->getData());
			$result = array(
				'status'=>0,
        		'error'=>$ex->getCode()
        		
        	);
			$error = json_decode($ex->getData());
			
			if($error->name == "INVALID_RESOURCE_ID"){
				$result['error'] = 48;
			}
			return $result;
		   
		} catch (Exception $ex) {
			//var_dump($payment);
			
			//require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
		    //AndroidHelper::write_log('jbpayment_paypal.txt',$ex->getMessage().PHP_EOL.$ex->getData());
			$result = array(
				'status'=>0,
        		'error'=>$ex->getCode()
        		
        	);
			return $result;
		}
		
		//execute transaction
		$ack = $payment->getState();  
		
        if($ack =='approved' || $ack =='completed') { 
		
        	$transaction = $payment->getTransactions();
        	$paymentId = $payment->getId();
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
				
        	$payment = Payment::get($paymentId, $apiContext);	
        	//Execute transaction
        	$execution = new PaymentExecution();
         	$execution->setPayerId($data->card->payer_id);
        	$execution->addTransaction($transaction[0]);
        	try {
        		// Execute the payment
        		$payment->execute($execution, $apiContext);
        		try {
        			$payment = Payment::get($paymentId, $apiContext);
        		} catch (Exception $ex) {
        			//var_dump($payment);
	        		//require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
	        		//AndroidHelper::write_log('jbpayment_paypal.txt',$ex->getMessage().PHP_EOL.$ex->getData());
	        		$result = array(
	        				'status'=>0,
	        				'error'=>$ex->getCode()
	        		
	        		);
	        		return $result;
        		}
        	} catch (Exception $ex) {
        		//var_dump($payment);
        		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
        		AndroidHelper::write_log('jbpayment_paypal.txt',$ex->getMessage().PHP_EOL.$ex->getData());
        		$result = array(
        				'status'=>0,
        				'error'=>$ex->getCode()
        		
        		);
        		return $result;
        	}
        	$ack = $payment->getState();
        	if($ack =='approved' || $ack =='completed') {
        		
        	}else{
        		$result = array(
        				'status'=>0,
        				'error'=>''
        		
        		);
        	}
			
			
        } else {
        	$result = array(
				'status'=>0,
        		'error'=>''
        		
        	);
        		
        }
        return $result;
    }  
    
    public function onBookproCheckTransaction($data){
    	$this->_autoload();
    	$success_state = array('approved','completed');
		$apiContext = $this->getApiContext();
	    $paymentId = $data->transaction_code;
	    try {
		    $payment = Payment::get($paymentId, $apiContext);
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
			return false;
		}
		if(isset($payment)){
			$ack = $payment->getState();
			if(in_array($ack, $success_state)){
				$transaction = $payment->getTransactions();				
				$result = array(
					'tx_id'=>$payment->getId(),
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
//				return true;
			}
		}
		return false;
	 }

    /**
     * Processes the payment form
     * and returns HTML to be displayed to the user
     * generally with a success/failed message
     *
     * @param $data     array       form post data
     * @return string   HTML to display
     */
    function _postPayment( $data )
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_bookpro/tables' );
		$order = JTable::getInstance('Orders', 'Table');
		$order->load(array('order_number'=>JFactory::getApplication()->input->getString('order_number')));
		return $order;
    }

    /**
     * Prepares variables and
     * Renders the form for collecting payment info
     *
     * @return unknown_type
     */
    function _renderForm( $data )
    {
        $vars = new JObject();
        $vars->prepop = array();
        $vars->cctype_input   = $this->_cardTypesField();
        $html = $this->_getLayout('form', $vars);

        return $html;
    }



    /**
     * Generates a dropdown list of valid CC types
     * @param $fieldname
     * @param $default
     * @param $options
     * @return unknown_type
     */
    function _cardTypesField( $field='cardtype', $default='', $options='' )
    {
    	
    }

    /**
     * Processes the payment
     *
     * This method process only real time (simple) payments
     *
     * @return string
     * @access protected
     */
    function _process()
    {

    	//initialise the application object
    	$app = JFactory::getApplication();
        /*
         * perform initial checks
         */
        if ( ! JRequest::checkToken() ) {
            return $this->_renderHtml( JText::_( 'Invalid Token' ) );
        }

        $data = $app->input->getArray($_POST);
		$this->_log($this->_getFormattedTransactionDetails($data));
        // get order information
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_bookpro/tables' );
        $order = JTable::getInstance('Orders', 'Table');
        $order->load( $data['orderpayment_id'] );
        if ( empty($order->order_number) ) {
            return JText::_( 'PLG_BOOKPRO_PAYPAL_INVALID_ORDER' );
        }

        if ( empty($this->api_username)) {
            return JText::_( 'PLG_BOOKPRO_PAYPAL_MESSAGE_MISSING_USERNAME' );
        }
        if ( empty($this->api_password)) {
            return JText::_( 'PLG_BOOKPRO_PAYPAL_MESSAGE_MISSING_PASSWORD' );
        }
        if ( empty($this->api_signature)) {
        	return JText::_( 'PLG_BOOKPRO_PAYPAL_MESSAGE_MISSING_SIGNATURE' );
        }
        // prepare the form for submission to paypal
        $process_vars = $this->_getProcessVars($data);
        $this->_log($this->_getFormattedTransactionDetails($process_vars));
        return $this->_processSimplePayment($process_vars);

    }
   
}
