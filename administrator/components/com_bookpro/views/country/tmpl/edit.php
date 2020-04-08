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
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate">
	
	
	<div class="row-fluid">
		<div class="span10 form-horizontal">
		<fieldset>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('country_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('country_name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('country_code'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('country_code'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('region_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('region_id'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('flag'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('flag'); ?></div>
				</div>
				
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('visainfo'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('visainfo'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
				</div>
			

				
			</fieldset>	
			</div>
			<?php echo JLayoutHelper::render('joomla.edit.details', $this); ?>
		
	</div>
	
	
	
	<div>
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>