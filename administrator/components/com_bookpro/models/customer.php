<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customer.php 108 2012-09-04 04:53:31Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class BookProModelCustomer extends JModelAdmin {
	function __construct() {
		parent::__construct ();
		
	}
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm ( 'com_bookpro.customer', 'customer', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		
		
		//$form->setFieldAttribute('birthday', 'format', DateHelper::getConvertDateFormat('M'));
		if (empty ( $form ))
			return false;
		return $form;
	}
	

	/*
	 * function getIdByUserId() { $user = &JFactory::getUser(); @var $user JUser $customer_id=null; if ($user->id) { $query = 'SELECT `customer`.`id` '; $query .= 'FROM `' . $this->_table->getTableName() . '` AS `customer` '; $query .= 'LEFT JOIN `#__users` AS `user` ON `customer`.`user` = `user`.`id` '; // is active customer $query .= 'WHERE `customer`.`user` = ' . $user->id; // juser is active //$query .= ' AND `user`.`block` = 0'; $this->_db->setQuery($query); $customer_id = (int) $this->_db->loadResult(); } return $customer_id; }
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.customer.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();
		}
		return $data;
	}
	public function getComplexItem($pk) {
		$db =  JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( 'a.*,c.country_name,u.username,c.country_code' );
		$query->from ( '#__bookpro_customer AS a' );
		$query->leftJoin ( '#__bookpro_country AS c ON a.country_id = c.id' );
		$query->leftJoin ( '#__users AS u ON a.user = u.id' );
		$query->where ( 'a.id = ' . ( int ) $pk );
		$db->setQuery ( $query );
		$item = $db->loadObject ();
		return $item;
	}
	function getItemByUser() {
		$user = JFactory::getUser ();
		if ($user->id) {
			$db = JFactory::getDBO ();
			$query = $db->getQuery ( true );
			$query->select ( 'c.*,u.username' )->from ( '#__bookpro_customer AS c' )->innerJoin ( '#__users AS u ON u.id=c.user' )->where ( 'u.id=' . $user->id );
			$db->setQuery ( $query );           
			return $db->loadObject ();
		} else {
			return null;
		}
	}
	public function getTable($type = 'Customer', $prefix = 'Table', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	
	/**
	 * Save customer.
	 *
	 * @param array $data
	 *        	request data
	 * @return customer id if success, false in unsuccess
	 */
	public function publish(&$pks, $value = 1) {
		$user = JFactory::getUser ();
		$table = $this->getTable ();
		$pks = ( array ) $pks;
		
		// Attempt to change the state of the records.
		if (! $table->publish ( $pks, $value, $user->get ( 'id' ) )) {
			$this->setError ( $table->getError () );
			
			return false;
		}
		
		return true;
	}
	public function save($data) {
// 		debug($data);die;
		if (!$data ['user_id']) {
				if($data ['password'] != $data ['password_confirm']){
					JFactory::getApplication()->enqueueMessage(JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH'),'error');
					return false;
				}
				$user = JFactory::getUser(0);
				$user->password_clear=$data ['password'];
				$user->email = $data ['email'];
				$user->name= $data ['name'];
				$user->username = $data ['email'];
				$user->groups=array(JComponentHelper::getParams('com_bookpro')->get('customers_usergroup',2));
				$user->password =JUserHelper::getCryptedPassword($data ['password']);
				$user->sendEmail=1;
				$user->active = '';
				if(!$user->save ()){
					JFactory::getApplication()->enqueueMessage($user->getError(),'error');
					return false;
				}
	
				$data['user_id']=$user->id;
				$data['active'] = 1;
				$data['state'] = 1;
				
				
		}else {
			//debug($data['id']); die;
			$this->customer_user=$this->getItemCustomer_user($data['id']);
			
			//debug($this->customer_user->user_id); die;
			//initialization table user and save in database, $user->block default
			$user = JFactory::getUser( $this->customer_user->user_id);
			$user->email = $data ['email'];
			$user->name= $data ['name'];
			$user->username = $data ['email'];
			
			if(!$user->save ()){
				JFactory::getApplication()->enqueueMessage($user->getError(),'error');
				return false;
			}
			
			if($data['user_type'] != JFactory::getApplication()->input->get('user_type_old')){
				//clear session of the user if user type change
				$this->clear_session($data['id']);
			}
				
		}
		return parent::save ( $data );
	}
	
	function clear_session($user_id){
		$user_id = (int)$user_id;
		$this->_db->setQuery("delete from #__bookpro_session where userid={$user_id}");
		return $this->_db->execute();
	}
	
	function getItemCustomer_user($customer_id){
		$user = JFactory::getUser ();
		if($user->id){
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('a.*');
			$query->from('#__bookpro_customer AS a')->where('a.id=' . $customer_id);
			$db->setQuery($query);
			//echo $query->dump();
			return $db->loadObject();
	
		} else {
			return null;
		}
	}
	

	
}

?>