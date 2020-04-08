<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('_JEXEC') or die('Restricted access');
AImporter::model('order','orders','customer','application');
AImporter::helper('currency','date');

class EmailHelper {
	/**
	 *
	 * @param String $input
	 * @param CustomerTable $customer
	 */
	var $config;
	var $app;
	var $tempalte='default';
	function __construct()
	{
		$this->config=AFactory::getConfig();
	}
	public function setTemplate($value){
		$this->tempalte=$value;
	}
	
	public function sendEmailCustomerApprove($customer_id){
		if(empty($customer_id))
			return false;
		//AImporter::helper('flight');
		$customerModel		= new BookProModelCustomer();
		$applicationModel	= new BookProModelApplication();
		//$customerModel  	= new BookProModelCustomer();
		$this->customerComplex = $customerModel->getItem($customer_id);
		$customer			= $this->customerComplex;
	//debug($customer); die;
		$this->app=$applicationModel->getObjectByCode('EMAIL_APPROVE_CUSTOMER');
		$body_customer=$this->app->email_customer_body;
		$body_customer=$this->fillCustomerApprove($body_customer, $customer);
		BookProHelper::sendMail($this->app->email_send_from, $this->app->email_send_from_name, $customer->email, $this->app->email_customer_subject, $body_customer,true);
	
		return true;
	}
	
	public function sendMail($order_id){
		if(empty($order_id))
			return false;
		AImporter::helper('flight');
		$orderModel			= new BookProModelOrder();
		$applicationModel	= new BookProModelApplication();
		$customerModel  	= new BookProModelCustomer();
		$this->orderComplex = $orderModel->getComplexItem($order_id);
		$order				= $this->orderComplex->order;		
		$customer			= $this->orderComplex->customer;
		
		$this->app=$applicationModel->getObjectByCode($order->type);
		$body_customer=$this->app->email_customer_body;
		$body_customer=$this->fillCustomer($body_customer, $customer);
		$body_customer=$this->fillOrder($body_customer,$order);
		BookProHelper::sendMail($this->app->email_send_from, $this->app->email_send_from_name, $customer->email, $this->app->email_customer_subject, $body_customer,true);

		$body_admin=$this->app->email_admin_body;
		$body_admin=$this->fillCustomer($body_admin, $customer);
		$body_admin=$this->fillOrder($body_admin,$order);

		BookProHelper::sendMail($this->app->email_send_from, $this->app->email_send_from_name, $this->app->email_admin, $this->app->email_admin_subject, $body_admin,true);
		return true;
	}
	
	//TODO attachment
	private static function getInvoiceAttachment($order){
		if($order->pay_status == "SUCCESS"){
			return null;
		}
		
		AImporter::helper('pdf');
		$controller = new JControllerLegacy();
		$ticket_view =$controller->getView('ticket','html','BookProView');
		$option = new JObject();
		$option->fontsize = 8;
		AImporter::table('orders');
		$db = JFactory::getDbo();
		$table = new TableOrders($db);
		$table->load($order->id);
		if(empty($table->invoice) && $table->id){
			$table->invoice = $table->getInvoiceNumber(); 
			$table->store();
		}			
		$option->order_number= JFactory::getApplication()->getCfg('tmp_path')."/Invoice_".$table->order_number."_".$table->invoice;
		$ticket_view->assignRef('order_id', $order->id);
		$ticket_view->setLayout('invoice');				
		ob_start();
		$ticket_view->display();
		$pdf=ob_get_contents();
		ob_end_clean();
		
//		return PrintPdfHelper::printTicket($pdf,$option,'P','E');
		PrintPdfHelper::printTicket($pdf,$option,'P','F');
		return $option->order_number.'.pdf';
	}

	public function sendCustomMail($order_id,$subject,$content){
		AImporter::helper('flight');
		$orderModel			= new BookProModelOrder();
		$applicationModel	= new BookProModelApplication();
		$customerModel  	= new BookProModelCustomer();
		$this->orderComplex = $orderModel->getComplexItem($order_id);
		$order				= $this->orderComplex->order;		
		$customer			= $this->orderComplex->customer;
		$passengers			= $this->orderComplex->passengers;
		
		$content = utf8_encode($content);
		$subject = utf8_encode($subject);
		
		$this->app=$applicationModel->getObjectByCode($order->type);
		$content	= $this->fillCustomer($content, $customer);
		$content 	= $this->fillOrder($content,$order,$passengers);
		
		if(strpos($content,"{ticket_detail}")){			
			$body_customer=$this->app->email_customer_body;
			$body_customer=$this->fillCustomer($body_customer, $customer);
			$body_customer=$this->fillOrder($body_customer,$order,$passengers);
			$content = str_replace("{ticket_detail}", $body_customer, $content);
		}		
//		echo $body_customer;die;
		BookProHelper::sendMail($this->app->email_send_from, $this->app->email_send_from_name, $customer->email, $subject, $content,true,$invoice);	
		
	}
	
	public function changeOrderStatus($order_id){

		$orderModel=new BookProModelOrder();
		$applicationModel=new BookProModelApplication();
		$customerModel= new BookProModelCustomer();

		$order=$orderModel->getItem($order_id);
		$customerModel->setId($order->user_id);
		$customer=$customerModel->getObject();
		$this->app=$applicationModel->getObjectByCode($order->type);
		$msg='COM_BOOKPRO_ORDER_STATUS_'.$order->order_status.'_EMAIL_BODY';
		$body_customer=JText::_($msg);
		$body_customer=$this->fillCustomer($body_customer, $customer);
		$body_customer=$this->fillOrder($body_customer,$order);

		BookProHelper::sendMail($this->app->email_send_from, $this->app->email_send_from_name, $customer->email, JText::_('COM_BOOKPRO_ORDER_STATUS_CHANGE_EMAIL_SUB') , $body_customer,true);
	}

	/**
	 *
	 * @param html $input
	 * @param Customer $customer
	 * @return mixed
	 */
	public function fillCustomerApprove($input, $customer){
		$input = str_replace('{email}', $customer->email, $input);
		$input = str_replace('{name}', $customer->name, $input);
		$input = str_replace('{company_name}', $customer->company_name, $input);
		return $input;
	}
	
	public function fillCustomer($input, $customer){
		$input = str_replace('{email}', $customer->email, $input);
		$input = str_replace('{firstname}', $customer->firstname, $input);
		$input = str_replace('{lastname}', $customer->lastname, $input);
		$input = str_replace('{address}', $customer->address, $input);
		$input = str_replace('{city}', $customer->city, $input);
		$input = str_replace('{gender}', BookProHelper::formatGender($customer->gender), $input);
		$input = str_replace('{telephone}', $customer->telephone, $input);
		$input = str_replace('{states}', $customer->states, $input);
		$input = str_replace('{zip}', $customer->zip ? $customer->zip : 'N/A', $input);
		$input = str_replace('{country}', $customer->country_name, $input);
		return $input;
	}

	public function fillOrder($input, $order){
		$input = str_replace('{order_number}', $order->order_number, $input);
		$input = str_replace('{total}', CurrencyHelper::formatprice($order->total), $input);
		$input = str_replace('{subtotal}', CurrencyHelper::formatprice($order->subtotal), $input);
		$input = str_replace('{note}', $order->note, $input);
		$input = str_replace('{payment_status}', $order->pay_status, $input);
		$input = str_replace('{deposit}', $order->deposit, $input);
		$input = str_replace('{pay_method}', $order->pay_method, $input);
		$input = str_replace('{created}', $order->created, $input);
		$input = str_replace('{order_status}', $order->order_status, $input);
		$order_link = JURI::root().'index.php?option=com_bookpro&view=ticket&layout=ticket&order_number='.$order->order_number;
		$input = str_replace('{order_link}', $order_link, $input);
		if($order->type=='FLIGHT'){
				
			AImporter::helper('flight');
			$passengers	=	$this->orderComplex->passengers;
			//get flight information
			$object=FlightHelper::getFlightDetail($passengers[0]->route_id);
			$object->date = $passengers[0]->start;
			$object->pricetype = FlightHelper::getPackageOfPassengerByParamsAndFlightId($passengers[0]->params, $passengers[0]->route_id); 
			
			$flight_info[] = $object;
			if($passengers[0]->return_route_id){
				$return_object			= FlightHelper::getFlightDetail($passengers[0]->return_route_id);
				$return_object->date 	= $passengers[0]->return_start;
				$return_object->pricetype = FlightHelper::getPackageOfPassengerByParamsAndFlightId($passengers[0]->params, $passengers[0]->return_route_id); 
				$flight_info[] 			= $return_object;
			
			}
			$data = new JObject();
			$data->flights=	$flight_info;
			$layout = new JLayoutFile('email_flight', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
			$flight = $layout->render($data);
			$input = str_replace('{tripdetail}', $flight, $input);							
			
			$data->passengers=$passengers;
			$data->order = $this->orderComplex->order;
			$order_model = new BookProModelOrder();
			$data->addons = $order_model->getAddonDetails($this->orderComplex->order->id);
			
			$layout = new JLayoutFile('email_passenger', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
			$passengers_html = $layout->render($data);
			$input = str_replace('{passenger}', $passengers_html, $input);	

			//import params in $data->order			
			$layout = new JLayoutFile('email_addon', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
			$addon_html = $layout->render($data);
			$input = str_replace('{addon}', $addon_html, $input);
			
			$layout = new JLayoutFile('email_customer', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
			$agent_customer =  $layout->render($this->orderComplex);
			$input = str_replace('{customer_agent}', $agent_customer, $input);
		}
		return $input;

	}



}
