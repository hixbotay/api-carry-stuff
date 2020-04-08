<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/


// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ();

class AdminUIHelper {
	
	static function startAdminArea($backEnd=true) {
	
	
		$view = JFactory::getApplication()->input->getString('view');
		$layout = JFactory::getApplication()->input->getString('layout');
		
		echo ' <div id="j-sidebar-container" class="span2">';
	
		JHtmlSidebar::addEntry(JText::_('Dashboard'),'index.php?option=com_bookpro');		
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_ORDERS_MANAGER'),JRoute::_(ARoute::view('orders')),$view == 'orders');	
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_TRANSACTION'),'index.php?option=com_bookpro&view=transactions');
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_REGISTRATION'),'index.php?option=com_bookpro&view=customers&state=0&layout=registration');
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_CUSTOMER'),'index.php?option=com_bookpro&view=customers');
//		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_TRANSPORT_TYPE'),'index.php?option=com_bookpro&view=transport_types');
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_PACKAGE'),'index.php?option=com_bookpro&view=packages');
		JHtmlSidebar::addEntry(JText::_('COM_BOOKPRO_REPORT'),'index.php?option=com_bookpro&view=reports');
	
	
		echo JHtmlSidebar::render();
		
		//submenu price
		echo ' <div class="sidebar-nav">';
		echo ' <ul class="nav nav-list" style="margin:0px;">';
	
		echo ' <li class="dropdown-submenu"><a href="index.php?option=com_bookpro&view=prices" class="" data-toggle="dropdown" href="#">'.JText::_(JText::_('COM_BOOKPRO_PRICE'),'index.php?option=com_bookpro&view=prices').'</a>
					<ul class="dropdown-menu">';
		echo 			self::listLink(JText::_('COM_BOOKPRO_PRICE_BASE'),'index.php?option=com_bookpro&view=prices');
		echo 			self::listLink(JText::_('COM_BOOKPRO_PRICE_DAY'),'index.php?option=com_bookpro&view=prices&layout=date');
		echo 			self::listLink(JText::_('COM_BOOKPRO_PRICE_WEEK'),'index.php?option=com_bookpro&view=prices&layout=week');
		echo 			self::listLink(JText::_('COM_BOOKPRO_ORDERS_ORDER_TYPES'),'index.php?option=com_bookpro&view=prices&layout=order');
		echo 			self::listLink(JText::_('COM_BOOKPRO_VEHICLE_TYPE'),'index.php?option=com_bookpro&view=vehicle_types');
		//echo 			self::listLink(JText::_('COM_BOOKPRO_VEHICLES_MANAGER'),'index.php?option=com_bookpro&view=vehicles');	
		echo 		'</ul>
				</li>';
				
		echo '  </ul>';
		echo ' </div>';
		//end submenu
		
		//submenu vehicle
		echo ' <div class="sidebar-nav">';
		echo ' <ul class="nav nav-list" style="margin:0px;">';
	
		echo ' <li class="dropdown-submenu"><a href="index.php?option=com_bookpro&view=vehicle_types" class="" data-toggle="dropdown" href="#">'.JText::_(JText::_('COM_BOOKPRO_VEHICLE_TYPE'),'index.php?option=com_bookpro&view=vehicle_types').'</a>
					<ul class="dropdown-menu">';
		echo 			self::listLink(JText::_('COM_BOOKPRO_VEHICLES_MANAGER'),JRoute::_(ARoute::view('vehicles')),$view == 'vehicles');
		echo 			self::listLink(JText::_('COM_BOOKPRO_VEHICLE_TYPE'),JRoute::_(ARoute::view('vehicle_types')),$view == 'vehicle_types');	
		echo 		'</ul>
				</li>';
				
		echo '  </ul>';
		echo ' </div>';
		//end submenu
		
		//submenu
		echo ' <div class="sidebar-nav">';
		echo ' <ul class="nav nav-list" style="margin:0px;">';
	
		echo ' <li class="dropdown-submenu"><a class="" data-toggle="dropdown" href="#">'.JText::_('More >>').'</a>
					<ul class="dropdown-menu">';
		echo 			self::listLink(JText::_('COM_BOOKPRO_EMAIL_SETTING'),JRoute::_(ARoute::view('applications')),$view == 'passengers');
		echo 			self::listLink(JText::_('COM_BOOKPRO_COUPONS'),JRoute::_(ARoute::view('coupons')),$view == 'coupons');
		echo 			self::listLink(JText::_('COM_BOOKPRO_PAYMENT_MANAGER'),JRoute::_(ARoute::view('payments')),$view == 'payments');	
		echo 			self::listLink(JText::_('COM_BOOKPRO_LANGUAGE'),JRoute::_('index.php?option=com_bookpro&view=languages',false));
		echo 		'</ul>
				</li>';
				
		echo '  </ul>';
		echo ' </div>';
		//end submenu
		
		echo '</div>';
		
	
	}
	
	private static function link($text,$link){		
		return '<a href="'.$link.'">'.$text.'</a>';
	}
	
	private static function listLink($text,$link,$active=false,$option=''){
		$active_class= $active ? 'active' : '';
		return '<li '.$option.' class="'.$active_class.'">'.self::link($text, $link, $active=false).'</li>';
	}

}

