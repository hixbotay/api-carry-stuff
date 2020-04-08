<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );
AImporter::helper ( 'paystatus', 'orderstatus', 'currency' );
class BookProControllerPayment extends JControllerLegacy {
	function BookProControllerPayment() {
		parent::__construct ();
	}
	function process() {
		JSession::checkToken () or jexit ( 'Invalid Token' );
		$input = JFactory::getApplication ()->input;
		
		$payment_plugin = $input->getString ( 'payment_plugin', '', 'bookpro' );
		
		$element = explode ( '_', $payment_plugin );
		$order_id = $input->getInt ( 'order_id' );
		
		JTable::addIncludePath ( JPATH_COMPONENT_ADMINISTRATOR . '/tables' );
		$order = JTable::getInstance ( 'orders', 'table' );
		$order->load ( $order_id );
		$db = JFactory::getDbo ();
		$customer = $db->setQuery ( "select * from #__bookpro_customer where id=$order->customer_id" )->loadObject ();
	
		$values ['payment_plugin'] = $payment_plugin;
		$values ['email'] = $customer->email;
		$values ['name'] = $customer->name;
		$values ['address'] = $customer->address;
		$values ['mobile'] = $customer->mobile;
		$values ['phone'] = $customer->phone;
		$values ['post_code'] = $customer->post_code;
		$values ['city'] = $customer->city;
		$values ['company_name'] = $customer->company_name;
		$values ['total'] = $order->total;
		$values ['order_number'] = $order->order_number;
		
		$dispatcher = JDispatcher::getInstance ();
		JPluginHelper::importPlugin ( 'bookpro' );
		$results = $dispatcher->trigger ( "onBookproPrePayment", array (
				$payment_plugin,
				$values 
		) );
		if (isset ( $results [0] ))
			echo $results;
		exit ();
	}
	function getPaymentForm($element = '') {
		$app = JFactory::getApplication ();
		$values = JRequest::get ( 'post' );
		$html = '';
		$text = "";
		$user = JFactory::getUser ();
		if (empty ( $element )) {
			$element = JRequest::getVar ( 'payment_element' );
		}
		$results = array ();
		$dispatcher = JDispatcher::getInstance ();
		JPluginHelper::importPlugin ( 'bookpro' );
		
		$results = $dispatcher->trigger ( "onBookproGetPaymentForm", array (
				$element,
				$values 
		) );
		for($i = 0; $i < count ( $results ); $i ++) {
			$result = $results [$i];
			$text .= $result;
		}
		$html = $text;
		// set response array
		$response = array ();
		$response ['msg'] = $html;
		// encode and echo (need to echo to send back to browser)
		echo json_encode ( $response );
		// $app->close();
		return;
	}
	function postpayment() {
		$app = JFactory::getApplication ();
		$plugin = $app->input->getString ( 'method' );
		$pluginsms = $app->input->get ( 'methodsms', 'product_sms', 'string' );
		$dispatcher = JDispatcher::getInstance ();
		JPluginHelper::importPlugin ( 'bookpro' );
		$values = new JObject ();
		$results = $dispatcher->trigger ( "onBookproPostPayment", array (
				$plugin,
				$values 
		) );
		
		// / Send email
		
		if ($results) {
			$smsresult = $dispatcher->trigger ( 'onBookproSendSms', array (
					$results [0] 
			) );
			if (! $results [0]->sendemail) {
				$url = JUri::root () . 'index.php?option=com_bookpro&task=payment.urlsendmail&order_id=' . $results [0]->id;
				$response = BookProHelper::pingUrl ( $url );
			}
		}
		
		$view = $this->getView ( 'postpayment', 'html', 'Bookproview' );
		$view->assign ( 'order', $results [0] );
		$view->display ();
	}
	private function sendMail($order_id) {
		AImporter::helper ( 'email' );
		$mail = new EmailHelper ();
		return $mail->sendMail ( $order_id );
	}
	
	// send mail via post curl
	public function urlSendmail() {
		// JLog::addLogger(array('text_file' => 'bookpro.txt','text_file_path'=>'logs','text_file_no_php'=>1,'text_entry_format' => '{DATE} {TIME} {MESSAGE}'),JLog::ALERT);
		// JLog::add('ping',JLog::ALERT,'com_bookpro');
		$order_id = JFactory::getApplication ()->input->getInt ( 'order_id' );
		$this->sendMail ( $order_id );
		JFactory::getApplication ()->close ();
	}
}
