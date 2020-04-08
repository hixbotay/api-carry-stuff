<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );

jimport ( 'joomla.form.formfield' );
/* @var $this BookingViewCustomer */
$customer_span = 12;
//$itemsCount = count($this->customer_doc);
$name=json_decode($this->item->name);
$param=(object)$this->item->params;
$param1=(object)$param->prices;
?>

<form
	action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id=' . ( int ) $this->item->id );?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<div class="span<?php echo $customer_span; ?>">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
		    <div class="form-horizontal">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo AHtml::getLanguageSelectList($this->item->name,'name')?></div>
				</div>
			</div>
				<?php //echo $this->form->renderField('name'); ?>
				<?php //echo $this->form->renderField('state'); ?>
				<?php echo JHtml::_('bootstrap.endTab');?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab2', JText::_('Price')); ?>
			<div class="span12">
				<div class="form-horizontal">				
					<div class="control-group">
		                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Default Price</strong>">
						Price</label></div>
		                <div class="controls"><input type="text" name="params[hard][]" value="<?php echo $param1->hard?>" id="standard_price" name="data[params]" /></div>
					</div>
					<div class="control-group">
		                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Distance (km) </strong>">
						Distance (km) </label></div>
		                <div class="controls"><input type="text" name="params[distance][]" value="<?php echo $param1->distance?>" id="standard_price" name="data[params]" /></div>
					</div>
					</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		<?php echo JHtml::_('bootstrap.endTabSet');?> 
			</div>
			</div>
				<input type="hidden" name="task" value="save" /> 
			<?php echo JHTML::_('form.token'); ?>
	
</div>
	</div>
</form>

