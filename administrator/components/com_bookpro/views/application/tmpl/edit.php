<?php
/**
 * @package 	jbtracking
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: jbtracking.php 27 2012-07-08 17:15:11Z quannv $
**/
defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('index.php?option=com_bookpro&layout=edit&id='.(int)$this->item->id);?>" method="post" name="adminForm" id="adminForm" class="form-validate">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Basic configuration')); ?>
		<div class="form-horizontal">	
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
				</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('code'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('code'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?> 		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab2', JText::_('Detail')); ?> 
		<div class="form-horizontal">				
			<?php if($this->item->code){?>
				<?php echo $this->loadTemplate(strtolower($this->item->code));?>
			<?php }else{?>
				<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_send_from'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_send_from'); ?></div>
				</div>
				
				<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_send_from_name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_send_from_name'); ?></div>
				</div>
				
				<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_customer_subject'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_customer_subject'); ?></div>
				</div>
				
				<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email_customer_body'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email_customer_body'); ?></div>
				</div>
			<?php }?>
			
			
		
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?>   
		<?php if(in_array(strtolower($this->item->code), array('email','sms'))){?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab3', JText::_('Tag')); ?>
				   	<?php echo $this->loadTemplate('tags')?>
			<?php echo JHtml::_('bootstrap.endTab');?> 		
		<?php }?>
    	<?php echo JHtml::_('bootstrap.endTabSet');?>
			
			<input type="hidden" name="task" value="" />				
			<?php echo JHtml::_('form.token');?>				
		
</form>