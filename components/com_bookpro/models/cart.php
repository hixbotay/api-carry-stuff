<?php
defined('_JEXEC') or die('Restricted access');
/**
 * 
 * @author sony
 *
 */
class BookproCart {
	
	
	var $type_cart ;
	var $customer;
	var $adult;
	var $children;
	var $vat;
	var $total;
	var $subtotal;
	var $order;
	var $orderinfo;
	var $from;
	var $to;
	
	function saveToSession() {
		$session =& JFactory::getSession();
		$session->set($this->type_cart, serialize($this));
	
	}
	function setCustomer($post){
		
		$customer=array();
		if($post['firstname'])
			$customer['firstname']=$post['firstname'];
		if($post['lastname'])
			$customer['lastname']=$post['lastname'];
		if($post['city'])
			$customer['city']=$post['city'];
		if($post['states'])
			$customer['states']=$post['states'];
		if($post['address'])
			$customer['address']=$post['address'];
		if($post['country_id'])
			$customer['country_id']=$post['country_id'];
		if($post['zip'])
			$customer['zip']=$post['zip'];
		if($post['fax'])
			$customer['fax']=$post['fax'];
		if($post['email'])
			$customer['email']=$post['email'];
		if($post['telephone'])
			$customer['telephone']=$post['telephone'];
		if($post['customer_id'])
			$customer['id']=$post['customer_id'];
		$this->customer=$customer;
	}
}
