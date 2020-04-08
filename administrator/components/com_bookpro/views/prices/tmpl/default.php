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

BookProHelper::setSubmenu(1);

?>
<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro'); ?>" method="post" id="adminForm" name="adminForm">
		
			<div class="form-horizontal">				
				 <div class="control-group">
	                    <div class="control-label" style="width:250px"><?php echo JText::_('COM_BOOKPRO_PRICE_BASE_DESC')?></div>
	                    <div class="controls"><input type="text" value="<?php echo $this->base->params?>" name="data[base][params]" /></div>
	                	<input type="hidden" name="data[base][code]" value="BASE" />
				</div>
			</div>
			<div class="form-horizontal">				
				 <div class="control-group">
	                    <div class="control-label" style="width:250px"><?php echo JText::_('COM_BOOKPRO_PRICE_VALIDATEEND_DESC')?></div>
	                    <div class="controls"><input type="text" value="<?php echo $this->validateend->params?>" name="data[validateend][params]" /></div>
	                	<input type="hidden" name="data[validateend][code]" value=VALIDATE_END />
				</div>
			</div>

					
	
		<input type="hidden" name="option" value="com_bookpro" />		
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="type" value="base" />
		<?php echo JHtml::_('form.token'); ?>
	
</form>
</div>

