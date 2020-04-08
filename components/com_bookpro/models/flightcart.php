<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: flightcart.php  23-06-2012 23:33:14
 **/
defined('_JEXEC') or die('Restricted access');
include_once 'cart.php';
class BookProFlightCart extends BookproCart{

	var $type_cart = "flightcart"; //cart,wishlist
	var $sum = 0;
	var $total=0;
	var $service_fee=0;
	var $tax=0;
	var $start;
	var $end;
	var $children;
	var $adult;
	var $infant;
	var $notes;
	var $orderinfos;
	var $roundtrip;
	var $passengers=array();
	var $price;
	var $return_price;
	var $package;
	var $return_package;
	var $flight_id;
	var $return_flight_id;

	function saveToSession() {
		$session =JFactory::getSession();
		$session->set($this->type_cart, serialize($this));
	}
 
	function load($type_cart = "flightcart"){
		$this->type_cart = $type_cart;
		$session =JFactory::getSession();
		$objcart = $session->get($this->type_cart);

		if (isset($objcart) && $objcart!='') {
			$temp_cart = unserialize($objcart);
			$this->from = $temp_cart->from;
			$this->to = $temp_cart->to;
			$this->start = $temp_cart->start;
			$this->end = $temp_cart->end;
			$this->subtotal=$temp_cart->subtotal;
			$this->service_fee=$temp_cart->service_fee;
			$this->tax=$temp_cart->tax;
			$this->total=$temp_cart->total;
			$this->notes=$temp_cart->notes;
			$this->customer=$temp_cart->customer;
			$this->orderinfos=$temp_cart->orderinfos;
			$this->adult=$temp_cart->adult;
			$this->children=$temp_cart->children;
			$this->infant=$temp_cart->infant;
			$this->roundtrip=$temp_cart->roundtrip;
			$this->passengers=$temp_cart->passengers;
			$this->price=$temp_cart->price;
			$this->return_price=$temp_cart->return_price;
			$this->package = $temp_cart->package;
			$this->return_package = $temp_cart->return_package;
			$this->flight_id = $temp_cart->flight_id;
			$this->return_flight_id = $temp_cart->return_flight_id;
		}


	}
	function clear(){
		$session =& JFactory::getSession();
        $this->products = null;
        $this->passengers = null;
        $this->orderinfo = null;
        $this->customer=null;
        $this->sum = 0;
        $this->no_room = 0;
        $this->notes = "";        
        $this->total = 0; 
        $this->adult=0;
        $this->children=0;
        $this->enfant=0;
	}

}