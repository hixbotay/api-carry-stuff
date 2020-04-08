<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
class BookProControllerOrder extends JControllerLegacy{

	var $_model;
	function __construct($config = array())
	{
		parent::__construct($config);
		if (! class_exists('BookProModelOrder')) {
			AImporter::model('order');
		}
		$this->_model = new BookProModelOrder();
	}
	
	
	
	/*Update location for trip when driver start trip*/
	//has not been used. Using updateLocation of user at the moment.
	public function cronUpdateTripLocation(){
		AImporter::helper('memory');
		debug($file);die;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$t = microtime(1);
		$query->select('o.id,s.lat,s.lng')
			->from('#__bookpro_orders as o')
			->innerJoin('#__bookpro_session as s ON s.userid = o.driver_id')
			->where('is_accepted = 1 AND is_cancelled = 0 AND trip_status =1');
		$db->setquery($query);
		$order_array=  $db->loadObjectList();
		AImporter::table('orders');
		foreach ($order_array as $i=>$item){
			$table = new TableOrders($db);
			$table->load($item->id);
			$params = json_decode($table->trip_location);
			$params[] = (object)array('latitude'=>$item->lat,'longitude'=>$item->lng,'updated_time'=>JHtml::_('date','now','Y-m-d H:i:s'));
			$table->trip_location = json_encode($params);
			$table->store();
			unset($order_array[$i]);
			$time = microtime(1) - $t;//get action time of function if exist 25s need use ping to other function
			if($time>25){
				AImporter::helper('bookpro');
				debug(memory_get_usage());
				die;
				//save to share memory
				BookProHelper::pingUrl('index.php?option=com_bookpro');
				JFactory::getApplication()->close();
			}
		}
		JFactory::getApplication()->close();
		
	}
	
	function exportpdf(){
		
		AImporter::helper('pdf');
		$input = JFactory::getApplication()->input;
		$id=$input->get('order_number');
		$ticket_view =$this->getView('ticket','html','BookProView');
		$ticket_view->setLayout('ticket');
		if($id){
				
			ob_start();
			$ticket_view->display();
			$pdf=ob_get_contents();
			ob_end_clean();
			if($input->getString('layout') == 'passenger_print'){
				$order->fontsize = '9';
			}
			$order->name = 'ticket_'.$id;
			$order->fontsize = 9;
			PrintPdfHelper::printTicket($pdf, $order,'L');
		}else{
			JFactory::getApplication()->enqueueMessage('Can not find the order for printing');
			$this->setRedirect(JURI::base().'index.php?option=com_bookpro&controller=order&task=detail&order_id='.$order_id);
				
		}
	
	}
	function exportflightDetail(){
	
		AImporter::helper('pdf');
		$id=JFactory::getApplication()->input->get('order_number');
		$ticket_view =$this->getView('ticket','html','BookProView');
		$ticket_view->setLayout('flightdetails');
		if($id){
	
			ob_start();
			$ticket_view->display();
			$pdf=ob_get_contents();
			ob_end_clean();
			$order->name = 'Flight_info_ticket_'.$id;
			PrintPdfHelper::printTicket($pdf, $order,'L');
		}else{
			JFactory::getApplication()->enqueueMessage('Can not find the order for printing');
			$this->setRedirect(JURI::base().'index.php?option=com_bookpro&controller=order&task=detail&order_id='.$order_id);
	
		}
	
	}
	
	function detail(){
		$order_id=JRequest::getInt('order_id');
		$user=JFactory::getUser();
		
		if ($user->get('guest') == 1) {
			$return = 'index.php?option=com_bookpro&controller=order&task=detail&order_id='.$order_id;
			$url    = 'index.php?option=com_users&view=login';
			$url   .= '&return='.urlencode(base64_encode($return));
			$this->setRedirect($url, false);
			return;
		} else {
			
			if (! class_exists('BookProModelOrder')) {
				AImporter::model('order');
			}
			$model= new BookProModelOrder();
			$model->setId($order_id);
			$order=$model->getObject();
			$view=&$this->getView('orderdetail','html','BookProView');
			$view->assign('order',$order);
			$view->display();
			return;
		}
	}
	
	
}