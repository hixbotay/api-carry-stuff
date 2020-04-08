<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Bookpro1Passenger Controller
 *
 * @package    Bookpro1
 * @subpackage Controllers
 */
class BookproControllerPassenger extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'passenger';
		$this->view_list = 'passengers';
		parent::__construct($config);
	}	
}// class
?>