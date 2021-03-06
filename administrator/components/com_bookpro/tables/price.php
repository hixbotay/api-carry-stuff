<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('_JEXEC') or die;

class TablePrice extends JTable{
	
	function __construct(& $db)
	{
		parent::__construct('#__bookpro_price', 'id', $db);
	}
	
	public function bind($array, $ignore=''){
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false){
		
		return parent::store($updateNulls);
	
	}
}