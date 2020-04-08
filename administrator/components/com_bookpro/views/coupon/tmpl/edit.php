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
JHtml::_('behavior.formvalidation');

?>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate">
		<div class="form-horizontal">
	
		<?php echo $this->form->renderField('title'); ?>
		<?php echo $this->form->renderField('code'); ?>
		<?php echo $this->form->renderField('value');  ?>
		<?php echo $this->form->renderField('subtract_type');  ?>
		<?php echo $this->form->renderField('total');  ?>
		<?php if($this->item->id){echo $this->form->renderField('remain');}  ?>
		<?php //echo $this->form->renderField('description')?>
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
					<div class="controls"><?php echo AHtml::getLanguageTextArea($this->item->description,'description')?></div>
				</div>
		<?php echo $this->form->renderField('publish_date');  ?>
		<?php echo $this->form->renderField('unpublish_date');  ?>
				
		<?php echo $this->form->renderField('state');  ?>	
		
	</div>
	<input type="hidden" name="task" value="" />
	 <?php echo JHtml::_('form.token'); ?>
	
</form>