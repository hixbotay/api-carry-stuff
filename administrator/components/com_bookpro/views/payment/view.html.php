<?php
/**
 * 
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.filesystem.file');
class BookproViewPayment extends JViewLegacy{
	protected $items;
	public $code;
	public $type;
	public $dev;
	
	public function display($tpl = null){
		$data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
		$this->items = json_decode($data);
		$input = JFactory::getApplication()->input;
		$this->type = $input->getString('type');
		$this->code = $input->getString('code');
		$this->dev = $input->getInt('dev',0);
		if(empty($this->code)){
			echo '<legend>FOR DEVELOPER ONLY</legend>';
		}
		foreach ($this->items->{$this->type} as $item){
			if($item->code == $this->code){
				$this->item = $item;
			}
		}
		//debug($this->item);
		
	
				

		if (count($error = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar(){
		JToolbarHelper::title(JText::_('COM_BOOKPRO_PAYMENT_MANAGER').': Edit','cube');
		//JToolbarHelper::custom('payment.savedata','save','icon over','Save & Close', false);
		//JToolbarHelper::savedata('payment.saveZ');
		//JToolbarHelper::apply('payment.apply');		
		//JToolbarHelper::cancel('payment.cancel', 'JTOOLBAR_CLOSE');
	
	}
	
	
}

?>