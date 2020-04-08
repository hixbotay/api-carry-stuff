<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/

defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.controlleradmin' );

class BookproControllerPassengers extends JControllerAdmin {

	public function __construct($config = array()) {
		$this->view_list = 'passengers';
		parent::__construct ( $config );
	}

	public function getModel($name = 'Passenger', $prefix = 'BookproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );

		return $model;
	}
	protected function postDeleteHook(JModelLegacy $model, $ids = null) {
	}
	/**
	 * Print passenger manifest to pdf
	 */
	function exportpdf() {
		$app = JFactory::getApplication();
		$input= $app->input;
		AImporter::helper ( 'pdf','date','flight' );
		AImporter::model('passengers');
		$model= new BookproModelpassengers();
		$depart_date = DateHelper::createFromFormatYmd($input->get('filter_depart_date'));
		$state = $model->getState();
		$state->set('filter.depart_date', $depart_date);
		$state->set('filter.route_id', $input->get('filter_route_id'));
		$state->set('filter.search', $input->get('filter_search'));
		$state->set('filter.order_status', $input->get('filter_order_status'));
		$state->set('list.limit',NULL);
		$state->set('list.start',0);
		$state->set('list.ordering','lastname');
		$state->set('list.direction','ASC');
		
		if($app->isAdmin()){
			$ticket_view = $this->getView('Passengers', 'html', 'BookProView' );
			$ticket_view->setModel($model,true);
			$ticket_view->setLayout ('report' );
		}else{
			$ticket_view = $this->getView('AgentPassengers', 'html', 'BookProView' );
			$ticket_view->setModel($model,true);
			$ticket_view->setLayout ('report' );
		}
		

		ob_start ();
		$ticket_view->display ();
		$pdf = ob_get_contents ();
		ob_end_clean ();
		
		//get flight name
		$flight = FlightHelper::getFlightDetail($input->get('filter_route_id'));
		$order = new JObject();
		$order->name="passengers_flight-".$flight->flightnumber.'_'.$input->get('filter_depart_date').'_'.JHtml::_('date','now','YmdHis');
		$order->fontsize = 9;
		PrintPdfHelper::printTicket ( $pdf, $order,'P');
		return;
	}
}
