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

?>

<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=aircraft&layout=edit&id='.(int)$this->item->id);?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-horizontal">	
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('code'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('code'); ?></div>
			</div>
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('seat'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('seat'); ?></div>
			</div>
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('weight'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('weight'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('image'); ?></div>
			</div>
			
			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
	</div>
	
	<input type="hidden" name="task" value="" />				
	<?php echo JHtml::_('form.token');?>		
</form>