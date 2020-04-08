<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die;
AImporter::helper('math');
class BookproViewPrices extends JViewLegacy
{

	protected $items;
	protected $state;

	public function display($tpl = null)
	{		
		$this->state = $this->get('state');
		$this->state->set('list.limit',null);
		$this->items = $this->get('Items');
		$this->base = MathHelper::filterArrayObject($this->items, 'code', 'BASE');
		$this->validateend = MathHelper::filterArrayObject($this->items, 'code', 'VALIDATE_END');
		$this->week = MathHelper::filterArrayObjects($this->items, 'code', 'WEEK');	
		$this->date = MathHelper::filterArrayObjects($this->items, 'code', 'DATE');
		$this->order = MathHelper::filterArrayObject($this->items, 'code', 'ORDER');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JHtml::_('behavior.formvalidation');
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui');
		$doc = JFactory::getDocument();
		$doc->addScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
		JToolBarHelper::title(JText::_('COM_BOOKPRO_PRICE_MANAGER'));		
		JToolBarHelper::apply('prices.saveprice');
//		JToolbarHelper::custom('prices.resetPrice','refresh','',JText::_('COM_BOOKPRO_RESET'),false);		
	}
}