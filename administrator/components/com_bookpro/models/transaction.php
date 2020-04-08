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
class BookProModelTransaction extends JModelAdmin {
	function __construct() {
		parent::__construct ();
		if (! class_exists ( 'TableTransaction' )) {
			AImporter::table ( 'transaction' );
		}
		$this->_table = $this->getTable ( 'transaction' );
	}
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm ( 'com_bookpro.transaction', 'transaction', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		
		if (empty ( $form ))
			return false;
		return $form;
	}
	
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
		//	$item->agent_payment = $item->params['payment'];
		//	$item->order_manager = $item->params['order_manager'];
		}
	
		return $item;
	}
	
	protected function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.transaction.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();
		}
		return $data;
	}
	
	
	
	
}

?>