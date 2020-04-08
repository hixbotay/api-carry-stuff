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
JToolbarHelper::custom('payment.savegateway','apply','icon over','Save', false);
JToolbarHelper::cancel('payment.cancel', 'JTOOLBAR_CLOSE');
?>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=payment&layout=edit');?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('COM_BOOKPRO_GATEWAYS')); ?> 		 	

		<div class="span6">
			<div class="form-horizontal">
				<div class="control-group">
					<div class="control-label">							
							Code
					</div>	
					<div class="controls"><input type="text" name="gateway[code]" <?php if(!empty($this->code)){?>readonly="true" value="<?php echo $this->item->code?>"<?php }?> ></div>
				</div>
				<div class="control-group">
					<div class="control-label">								
								State
					</div>	
					<div class="controls">
						<?php if(!empty($this->code)){?>
							<?php echo JHtmlSelect::booleanlist('gateway[enabled]','class="radio pull-left"',$this->item->enabled,JText::_('JYES'),JText::_('JNO'))?>
						<?php }else{?>
							<?php echo JHtmlSelect::booleanlist('gateway[enabled]','class="radio pull-left"',0,JText::_('JYES'),JText::_('JNO'))?>
						<?php }?>
					</div>
				</div>
				<?php if(!empty($this->code)){?>
				
				<?php foreach ($this->item->params as $name=>$val){
					if($name != 'sandbox'){?>
					<div class="control-group">
						<div class="control-label"><?php echo strtoupper($name)?></div>	
						<div class="controls"><textarea class="input" type="text" rows="5" name="gateway[params][<?php echo $name?>]"  value=""><?php echo $val;?></textarea></div>
					</div>
				<?php 
					}
				}?>
				<?php }?>
				
				<!--TOOL FOR DEV MODE-->
				<?php if($this->dev){?>
					<div id="dev">
						<div class="control-group">
							<div class="control-label"><button type="button" class="btn" id="add_new">Add params</button></div>	
							<div class="controls">
								<input type="text" id="new_params" value=""/>
							</div>
						</div>
					</div>
					
					<script>
						jQuery(document).ready(function($){
							$('#add_new').click(function(){
								var name = $('#new_params').val();
								var input = '<div><input type="text" name="gateway[params]['+name+']" value="'+name+'">&nbsp;<button class="btn btn-micro" onclick="jQuery(this).parent().remove();"><i class="icon-unpublish"></i></button></div>';
								$('#dev').append(input);
								$('#new_params').val('');
							});
							
						});
					</script>
				<?php }?>
				
				
				<div class="control-group">
					<div class="control-label">Sandbox</div>	
					<div class="controls">
						<?php echo JHtmlSelect::booleanlist('gateway[params][sandbox]','class="radio pull-left"',$this->code?$this->item->params->sandbox:1,JText::_('JYES'),JText::_('JNO'))?>
					</div>
				</div>
			</div>
		</div>	
		
	<?php echo JHtml::_('bootstrap.endTab');?>		
	<?php echo JHtml::_('bootstrap.endTabSet');?> 
	<input type="hidden" name="task" value="" />				
	<?php echo JHtml::_('form.token');?>		
</form>