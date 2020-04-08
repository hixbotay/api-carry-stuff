<?php
/**
 * Payment status.
 *
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @version $Id$
 */


defined('_JEXEC') or die('Restricted access');

class OrderStatus {

	static $FINISHED = null;
	static $CONFIRMED = null;
	static $PENDING = null;
	static $CANCELLED = null;
	static $NEW = null;
	static $REBOOK = null;

	public $value = null;

	public static $map;
	public static $all;
	private $key=null;
	public $text=null;

	public function __construct($value) {
		$this->value = $value;
		$this->text= JText::_('COM_BOOKPRO_ORDER_STATUS_'.strtoupper($this->value));
	}

	public static function init () {
		self::$FINISHED  = new OrderStatus("FINISHED");
		self::$PENDING  = new OrderStatus("PENDING");
		self::$CANCELLED = new OrderStatus("CANCELLED");
		self::$NEW = new OrderStatus("NEW");
		self::$CONFIRMED = new OrderStatus("CONFIRMED");
		self::$REBOOK = new OrderStatus("REBOOK");
		//static map to get object by name - example Enum::get("INIT") - returns Enum::$INIT object;
		self::$all = array (self::$PENDING,self::$CONFIRMED,self::$CANCELLED);
		self::$map = array (self::$PENDING,self::$CONFIRMED,self::$CANCELLED);
		
	}
	static function format($status){
		return JText::_('COM_BOOKPRO_ORDER_STATUS_'.strtoupper($status));
	}
	
	static function getHtmlList($name,$attribute,$select,$id=null) {
		self::init();
		return JHtmlSelect::genericlist(self::$all,$name, $attribute ,'value', 'text', $select,$id);
	}
	
	public function getText() {
		return JText::_('COM_BOOKPRO_ORDER_STATUS_'.strtoupper($this->value));
	}

	public static function get($element) {
		if($element == null)
			return null;
		return self::$map[$element];
	}

	public function getValue() {
		return $this->value;
	}
	
	public function __getKey() {
		return $this->value;
	}

	public function __setKey($key){
		$this->key=$key;
	}
	
	public function equals(OrderStatus $element) {
		return $element->getValue() == $this->getValue();
	}

	public function __toString () {
		return $this->value;
	}
}