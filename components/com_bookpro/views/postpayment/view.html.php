<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
AImporter::model('orders');

/**
 * HTML View class for the BookPro Component
 */
class BookProViewPostPayment extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null)
	{

		$this->config=AFactory::getConfig();
		//get Payment methods
		// Display the view
		parent::display($tpl);
		
	}


}
