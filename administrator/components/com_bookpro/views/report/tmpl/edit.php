<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 27 2012-07-08 17:15:11Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');

AImporter::helper('date');

?>

<form action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&view=report&layout=edit&id='.( int ) $this->item->id );?>" method="post" name="adminForm" id="adminForm" class="form-validate">	
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
<div class="row-fluid">
	<div class="span6">
		 <div class="form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
			</div>	        		
		</div>
	</div>
	<div class="span6">
		<div class="form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_REPORT_CUSTOMER_NAME'); ?></div>
				<div class="controls"><?php echo $this->customer->name?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_TYPE'); ?></div>
				<div class="controls"><?php if($this->customer->user_type == 1){ echo 'Particular';} elseif($this->customer->user_type == 2) {echo 'Enterprise';} else{ echo 'Driver';}; ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_EMAIL'); ?></div>
				<div class="controls"><?php echo $this->customer->email?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_PHONE'); ?></div>
				<div class="controls"><?php echo $this->customer->phone?></div>
			</div>	        		
		</div>
	</div>
</div>
   
    	<?php echo JHtml::_('bootstrap.endTab');?>      	 	
    	<?php echo JHtml::_('bootstrap.endTabSet');?>   	
		<input type="hidden" name="task" value="save" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>
