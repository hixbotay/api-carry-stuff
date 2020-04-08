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
$gate=$this->items->gateways;
$method=$this->items->methods;
JToolbarHelper::custom('payment.savemethod','apply','icon over','Save', false);
JToolbarHelper::cancel('payment.cancel', 'JTOOLBAR_CLOSE');
//var_dump(json_decode($this->item->params));
//debug($gate);die;

?>
<script type="text/javascript">
	var x= $('input[name="enable"]:checked').val(); 
	alert(x);
</script>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=payment&layout=edit');?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('COM_BOOKPRO_METHODS')); ?> 		
	<div class="span12">
			<table class= "table table-striped">
				<thead>
					<tr>	
						<th><?php echo JText::_('COM_BOOKPRO_CODE')?></th>
						<th><?php echo JText::_('COM_BOOKPRO_NAME')?></th>
						<th><?php echo JText::_('COM_BOOKPRO_ENABLE')?></th>
					</tr>
				</thead>
				<tbody>					
						<tr>
							<td><input type="text" name="name[cd]" readonly="true" value="<?php echo $this->item->code?>" /></td>
							<td><?php echo AHtml::getLanguageSelectList(json_encode($this->item->name),'name')?></td>							
							<td><?php 
								if ($this->item->enabled == 1) {
									?>												
									<fieldset id="enabled" class="radio btn-group">
                                        <input type="radio" id="enable0" checked="checked" value="<?php echo $this->item->enabled ?>" name="name[enable]">
                                        <label for="enable0" class="btn">Yes</label>
                                        <input type="radio" id="enable1" value="0" name="name[enable]" >
                                        <label for="enable1" class="btn">No</label>
									</fieldset>	
									<?php						
								} else {
									?>
									<fieldset id="enabled" class="radio btn-group">
                                        <input type="radio" id="enable0" value="1" name="name[enable]">
                                        <label for="enable0" class="btn">Yes</label>
                                        <input type="radio" id="enable1" checked="checked" value="<?php echo $this->item->enabled ?>" name="name[enable]" >
                                        <label for="enable1" class="btn btn-danger">No</label>
									</fieldset>	
									<?php }?>		
								</td>		
						</tr>
				</tbody>
			</table>
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?>	
	<?php echo JHtml::_('bootstrap.endTabSet');?> 
	<input type="hidden" name="task" value="" />				
	<?php echo JHtml::_('form.token');?>		
</form>