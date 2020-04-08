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

<form action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id='.( int ) $this->item->id );?>" method="post" name="adminForm" id="adminForm" class="form-validate">	
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('updorder.save')">
				<i class="icon-new"></i> <?php echo JText::_('COM_ORDERS_BUTTON_SAVE_AND_CLOSE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('updorder.apply')">
				<i class="icon-new"></i> <?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('updorder.cancel')">
				<i class="icon-cancel"></i> <?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
		<fieldset>
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
    <div class="form-horizontal">
    	<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('customer_id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('customer_id'); ?></div>
		</div>
        
        <div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('driver_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('driver_id'); ?></div>
			</div>
			
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
			</div>
		
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('recipient_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('recipient_name'); ?></div>
			</div>
		
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('total'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('total'); ?></div>
			</div>
	</div>
    	<?php echo JHtml::_('bootstrap.endTab');?> 	
      		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab3', JText::_('Status')); ?>   
    			<div class="form-horizontal">
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('pay_status'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('pay_status'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('order_status'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('order_status'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
					<div class="controls"><?php 
					$this->form->setFieldAttribute('created', 'format',DateHelper::getConvertDateFormat('M'), $group = null);
					echo $this->form->getInput('created'); ?></div>
			</div>
			
			
	</div>
    	<?php echo JHtml::_('bootstrap.endTab');?>     	
    	<?php echo JHtml::_('bootstrap.endTabSet');?>   	
		<input type="hidden" name="task" value="save" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>
