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
$name=json_decode($this->item->name);
?>

<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=package&layout=edit&id='.(int)$this->item->id);?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 		
	<div class="form-horizontal">	
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo AHtml::getLanguageSelectList($this->item->name,'name')?></div>
			</div>
	</div>
	
	<input type="hidden" name="task" value="" />				
	<?php echo JHtml::_('form.token');?>		
</form>