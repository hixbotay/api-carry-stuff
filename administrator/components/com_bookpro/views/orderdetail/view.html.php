<?php
/**
/**
 * @package 	Jb Chat Online
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: jblogistic.php 108 2012-09-04 04:53:31Z quannv $
 **/

defined('_JEXEC') or die;
AImporter::model('customers','customer');
AImporter::helper('bookpro');
class BookproViewOrderdetail extends JViewLegacy
{
		
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->get('customer_id',0);
		//debug($id); die;
		$model = new BookProModelCustomers();
	//	$serviceModel=new JbchatonlineModelService();
		//$this->items = $model->get ( 'Items' );
		//$this->pagination = $model->get ( 'Pagination' );
		//$this->state = $model->get ( 'State' );
		$this->orders = $model->getItemOrderByCustomer($id);
		$this->pagination	= $model->getPagination();
	//	debug($this->orders[0]); die;
		//$this->service=$bustripModel->getComplexItem($this->rate->service_id);
		//$this->flight=FlightHelper::getObjectInFo($this->bustrip_id);
		parent::display($tpl);
	}

	
}