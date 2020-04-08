<?php 
	/**
	 * @package 	Bookpro
	 * @author 		Ngo Van Quan
	 * @link 		http://joombooking.com
	 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
	 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
	 * @version 	$Id$
	 **/
	
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$price_type = $this->config->get('economy') || $this->config->get('business');
	$data = new JObject();
	$data->flights=	FlightHelper::getFlightDetailByPassenger($this->orderComplex->passengers[0]);
	$data->type = $price_type;
	
	$data->order = $this->orderComplex->order;
	echo BookProHelper::renderLayout('email_flight', $data);
?>

