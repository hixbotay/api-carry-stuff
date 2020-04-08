<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

class BookProHelper
{
	
	static $local;
	static $trip_status;
	static $customer_type;
	
	static function get_trip_status(){
		if(!isset(self::$trip_status)){
			self::$trip_status = array('0'=>'new','1'=>'start','2'=>'end','-2'=>'not_end');
		}		
		return self::$trip_status;	
		
	}
	
	static function get_customer_type(){
		if(!isset(self::$customer_type)){
			self::$customer_type = array('1'=>'particular','2'=>'enterprise','3'=>'driver');
		}		
		return self::$customer_type;		
	}
	
	static function get_accept_status(){
		return array('0'=>'no','1'=>'accepted');
	}
	static function get_cancel_status(){
		return array('0'=>'no','1'=>'cancelled');
	}
	static function get_payment_status(){
		return array('0'=>'not_paid','1'=>'paid');
	}
	static function get_order_type(){
		return array('0'=>'order','1'=>'booking');
	}
	
	
	static function get($key_val,$type=false){
		$function = 'get_'.$key_val;
		$data = self::$function();
		if($type){
			switch ($type) {
				case 'arrayObject':
					$result = array();
					foreach ($data as $key=>$val){
						$result[] = (object)array('value'=>$key,'text'=>JText::_(strtoupper('COM_BOOKPRO_'.($key_val).'_'.$val)));
					}
					return $result;
					break;		
				case 'array':
					$result = array();
					foreach ($data as $key=>$val){
						$result[$val] = $key;
					}
					return $result;
					break;		
				default:
					break;
			}
		}
		return $data;
	}
	

	
	static function formatLang($string){
		if(!isset(self::$local)){
			$lang=JFactory::getLanguage();
			self::$local=substr($lang->getTag(),0,2);
		}
		$local = self::$local;
		if(is_object($string)){
			$object = ($string);
		}else{
			$object = json_decode($string);
		}
		
		if(!$object){			
			return $string;
		}
		return $object->$local;
	}
	
	public static function getSessionById($id){		
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)->select('*')->from('#__bookpro_session')->where('session_id LIKE' .$db->quote($id));
		$db->setQuery($query);
		$result =  $db->loadObject();
		if($result->session_id){
			return $result;
		}
		return false;
	}
	public static function getSessionByUser($id){
		
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)->select('*')->from('#__bookpro_session')->where('userid='.$id);
		$db->setQuery($query);
		$result =  $db->loadObject();
		if($result->session_id){
			return $result;
		}
		return false;
	}
	
	static function getTypeCustomer($name, $select, $att = '', $ordering = "id") {
		if (! class_exists ( 'BookProModelRegistrations' )) {
			AImporter::model ( 'registrations' );
		}
		$model = new BookProModelRegistrations();
		$state=$model->getState();
		$state->set('list.start',0);
		$state->set('list.limit', 0);
		$list = $model->getItems();
		foreach ($list as $item  ){
			//$item->service_name=$item->service_name .' ('. $item->cost .' Eur/hour)';
			if($item->type==1)
			{
				$item->type=$item->type . ' Particular';	
			}
			if($item->type==2)
			{
				$item->type=$item->type . ' Enterprise';
			}
			if($item->type==3)
			{
				$item->type=$item->type . ' Driver';
			}
		}
		return AHtml::getFilterSelect ( $name, JText::_ ( "COM_BOOKPRO_TYPE_REGISTRATION_SELECT" ), $list, $select, false, $att, 'type', 'type' );
	
	}
	
	static function getCustomer($name, $select, $att = '') {
		if (! class_exists ( 'BookProModelCustomers' )) {
			AImporter::model ( 'customers' );
		}
		$model = new BookProModelCustomers();		
		$list = $model->getItems();
		//var_dump($list);die;
		return AHtml::getFilterSelect ( $name, JText::_ ( "COM_BOOKPRO_CUSTOMER_SELECT" ), $list, $select, false, $att, 'id', 'name' );
	
	}
	static function getCustomerName($selected) {
		if (! class_exists ( 'BookProModelCustomers' )) {
			AImporter::model ( 'customers' );
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__bookpro_customer')
			->where ('(user_type=1 or user_type=2)');
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$select = new JObject();
		$select->id= '';
		$select->name = JText::_("COM_BOOKPRO_CUSTOMER_SELECT");
		array_unshift($list , $select);
		//var_dump($list);		
		return JHtml::_('select.genericlist', $list, 'filter_customer_id','class="input input-medium"','id','name',$selected);
	}
	
	static function getDriverName($selected) {
		if (! class_exists ( 'BookProModelCustomers' )) {
			AImporter::model ( 'customers' );
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__bookpro_customer')
			->where ('user_type=3');
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$select = new JObject();
		$select->id= '';
		$select->name = JText::_("COM_BOOKPRO_DRIVER_SELECT");
		array_unshift($list , $select);
		//var_dump($list);
		return JHtml::_('select.genericlist',$list,'filter_driver_id','class="input input-medium"','id','name',$selected);
	}
	
	static function getValidateStatusCustomer($selected) {
	
		$config=JComponentHelper::getParams('com_bookpro');
		$option[] = JHtml:: _('select.option', '', JText::_("Select status"));
		$option[] = JHtml:: _('select.option', 1, JText::_("Validated"));
		$option[] = JHtml:: _('select.option', -1, JText::_("Invalidated"));
	
		return JHtml::_('select.genericlist',$option,'filter_active','class="input input-medium" id="filter_active"','value','text',$selected);
	
	}
	
	static function getTypeRegistrationSelect($selected) {

		$config=JComponentHelper::getParams('com_bookpro');
		$option[] = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_TYPE_REGISTRATION_SELECT"));
		$option[] = JHtml:: _('select.option', 1, JText::_("COM_BOOKPRO_TYPE_PARTICULAR"));
		$option[] = JHtml:: _('select.option', 2, JText::_("COM_BOOKPRO_TYPE_ENTERPRISE"));
		$option[] = JHtml:: _('select.option', 3, JText::_("COM_BOOKPRO_TYPE_DRIVER"));

		return JHtml::_('select.genericlist',$option,'filter_user_type','class="input input-large" id="filter_user_tpye"','value','text',$selected);
		
	}
	static function getTypeCustomerSelect($selected) {
	
		$config=JComponentHelper::getParams('com_bookpro');
		$option[] = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_TYPE_CUSTOMER_SELECT"));
		$option[] = JHtml:: _('select.option', 1, JText::_("COM_BOOKPRO_TYPE_PARTICULAR"));
		$option[] = JHtml:: _('select.option', 2, JText::_("COM_BOOKPRO_TYPE_ENTERPRISE"));
		$option[] = JHtml:: _('select.option', '-3', JText::_("COM_BOOKPRO_TYPE_PARTICULAR_AND_ENTERPRISE"));
		$option[] = JHtml:: _('select.option', 3, JText::_("COM_BOOKPRO_TYPE_DRIVER"));
	
		return JHtml::_('select.genericlist',$option,'filter_user_type','class="input input-large" id="filter_user_tpye"','value','text',$selected);
	
	}
	static function getTypeVehicleSelect($selected) {
	
		$config=JComponentHelper::getParams('com_bookpro');
		$option[] = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_VEHICLE_TYPE_SELECT"));
		$option[] = JHtml:: _('select.option', 1, JText::_("COM_BOOKPRO_VEHICLE_TYPE_EXPRESS"));
		$option[] = JHtml:: _('select.option', 2, JText::_("COM_BOOKPRO_VEHICLE_TYPE_STANDARD"));
	
		return JHtml::_('select.genericlist',$option,'filter_vehicle_type','class="input input-large" id="filter_vehicle_type"','value','text',$selected);
	
	}
	static function getTypeOrderSelect($selected) {
	
		//$config=JComponentHelper::getParams('com_bookpro');
		$option[] = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_ORDERS_TYPE_SELECT"));
		$option[] = JHtml:: _('select.option', 0, JText::_("COM_BOOKPRO_ORDERS_ORDER"));
		$option[] = JHtml:: _('select.option', 1, JText::_("COM_BOOKPRO_ORDERS_BOOKING"));
	
		return JHtml::_('select.genericlist',$option,'filter_order_type','class="input input-large" id="filter_order_tpye"','value','text',$selected);
	
	}

	
	static function isAgent($id = null){
		$checked = false;
		$user = JFactory::getUser($id);
		if(JComponentHelper::getParams('com_bookpro')->get('agent_usergroup') && $user->groups){
			if(in_array(JComponentHelper::getParams('com_bookpro')->get('agent_usergroup'), $user->groups)){
				$checked = true;
			}
		}

		return $checked;
	}
	
	static function setCurrentVehicle($vehicle_id,$customer_id,$disable = false){
		$save = false;
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
		$vehicleTable= JTable::getInstance('Vehicle', 'Table');
		//check vehicle exists
		$vehicleTable->load($vehicle_id);		
		if($vehicleTable->id){			
			if($disable){				
				$vehicleTable->current = 0;
			}else{
				self::emptyCurrentByDriver($customer_id);
				$vehicleTable->current = 1;
			}
			$save = $vehicleTable->store();
			
			
		}
		return $save;
	}
	static function setCurrentTransaction($vehicle_id,$customer_id,$disable = false){
		$save = false;
		$data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
		$this->items = json_decode($data);	
			if($disable){				
				$this->items->enabled = 0;
			}else{
				$this->items->enabled = 1;
			}
			$save = $vehicleTable->store();
		return $save;
	}
	
	static function emptyCurrentByDriver($customer_id){
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->update('#__bookpro_vehicle')
		->set('current=0')
		->where('driver_id='.$customer_id);
		$db->setQuery($query);
		return $db->execute();
	}
	
	static function getAgentParams($id = null){
			
		$user = JFactory::getUser($id);
	
		if(empty($user)){
			return false;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->quoteName('#__bookpro_customer'));
		$query->where($db->quoteName('user').' = '.$user->id);
		$db->setQuery($query);
		$data = $db->loadResult();
		$result = json_decode($data);
		return $result;
	}
	
	static function renderLayout($name,$data,$path='/components/com_bookpro/layouts'){
		$path=JPATH_ROOT .$path;
		return JLayoutHelper::render($name,$data,$path);
	}
	/**
	 *
	 * @param unknown $selected
	 * @return mixed
	 */
	static function getCustomerGroupSelect($selected){

		$config=JComponentHelper::getParams('com_bookpro');
		$agent_group=$config->get('agent_usergroup');
		$option[] = JHtml:: _('select.option',0, JText::_("COM_BOOKPRO_SELECT_CUSTOMER_GROUP"));
		$option[] = JHtml:: _('select.option', $agent_group, JText::_("COM_BOOKPRO_AGENT"));
		$option[] = JHtml:: _('select.option',-1, JText::_("COM_BOOKPRO_GUEST"));

		return JHtml::_('select.genericlist',$option,'filter_group_id','class="input input-medium" id="filter_group_id"','value','text',$selected);

	}

	static function  getCustomersSelectByGroupid($group_id=null, $select=null){
		if (!class_exists('BookProModelCustomers')) {
			AImporter::model('customers');
		}
		$model = new BookProModelCustomers();
		$state = $model->getState();
		$state->set('filter.group_id', $group_id);
		$state->set('list.limit',NULL);
		$fullList = $model->getItems();
			
		if($group_id>0 && count($fullList)>0){
			//$destitems[] = JHTML::_('select.option',  null, '&nbsp;&nbsp;&nbsp;'. JText::_("COM_BOOKPRO_SELECT_CUSTOMER") );	
			foreach ( $fullList as $item ) {
				$destitems[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'. $item->firstname.' '.$item->lastname );
			}
			$output = JHTML::_('select.genericlist',   $destitems, 'filter_user_id', 'class="input-medium chosen-select"', 'value', 'text', $select );
				
			return $output;
		}else{
			return null;
		}

	}
	static function getRangeSelect($selected){

		$option[] = JHtml:: _('select.option',0, JText::_("COM_BOOKPRO_QUICK_FILTER_DATE"));
		$option[] = JHtml:: _('select.option','today', JText::_("COM_BOOKPRO_TODAY"));
		$option[] = JHtml:: _('select.option','past_week', JText::_("COM_BOOKPRO_LAST_WEEK"));
		$option[] = JHtml:: _('select.option', 'past_1month', JText::_("COM_BOOKPRO_LAST_MONTH"));
		return JHtml::_('select.genericlist',$option,'filter_range','onchange="this.form.submit();" class="input input-medium"','value','text',$selected);

	}


	/**
	 * Set pages submenus.
	 *
	 * @param $set
	 */

	static function setSubmenu() {
		AImporter::helper ( 'adminui' );
		AdminUIHelper::startAdminArea ();
	}
	

	

	static function  getCountryList($name,$select,$att='',$ordering="id"){

		if (! class_exists('BookProModelCountries')) {
			AImporter::model('countries');
		}
		$model = new BookProModelCountries();
			
		$state = $model->getState();
		$state->set('list.order', $ordering);
		$state->set('list.order_dir', 'ASC');
		$state->set('list.limit',NULL);

		$fullList = $model->getItems();

		return AHtml::getFilterSelect($name, JText::_("COM_BOOKPRO_SELECT_COUNTRY"), $fullList, $select, false,$att, 'id', 'country_name');
			
	}

	/**
	 * Gets string value of week day by day number code.
	 *
	 * @param int $code
	 * @return string
	 */
	function dayCodeToString($code)
	{
		switch ($code) {
			case 1:
				return 'monday';
			case 2:
				return 'tuesday';
			case 3:
				return 'wednesday';
			case 4:
				return 'thursday';
			case 5:
				return 'friday';
			case 6:
				return 'saturday';
			case 7:
				return 'sunday';
		}
	}



	static function addJqueryValidate(){
		$lang=JFactory::getLanguage();
		$local=substr($lang->getTag(),0,2);

		$document = JFactory::getDocument();
		$document->addScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js");
		if($local !='en'){
			$document->addScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/localization/messages_".$local.".js");
		}
		$document->addScript("http://jqueryvalidation.org/files/dist/additional-methods.min.js");

	}

	function sendMail($from, $fromname, $email, $subject, $body, $htmlMode)
	{
			
		if (! $htmlMode)
		$body = BookProHelper::html2text($body);

		if (is_array(($froms = explode(',', str_replace(';', ',', $from)))) && count($froms))
		$from = reset($froms);
		else {
			$mainframe = &JFactory::getApplication();
			/* @var $mainframe JApplication */
			$from = $mainframe->getCfg('mailfrom');
		}
		if (is_array(($emails = explode(',', str_replace(';', ',', $email))))) {
			$mail = &JFactory::getMailer();
			/* @var $mail JMail */
			foreach ($emails as $email){
				$mail->sendMail($from, $fromname, $email, $subject, $body, $htmlMode, null, null, $attachments);

			}


		}

			
	}
	//ping to url with timeout 1s
	static function pingUrl($url=NULL)  
	{  
	    if($url == NULL) return false;  
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT,1);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    $data = curl_exec($ch);  
//	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);  
	    return $data;
	}
}




?>
