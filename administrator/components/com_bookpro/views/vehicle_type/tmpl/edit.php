<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );
jimport ( 'joomla.form.formfield' );
AImporter::helper('image');

$name=json_decode($this->item->name);
$param=(object)$this->item->params;
$param1=(object)$param->prices;
AImporter::helper('html');
$icon=json_decode($this->item->icon);
$vehicle_type_size = AImage::getSize();
?>
<script type="text/javascript">

</script>
<form
	action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id=' . ( int ) $this->item->id );?>"
	method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
		    <div class="form-horizontal">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo AHtml::getLanguageSelectList($this->item->name,'name')?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('capacity'); ?></div>
					<div class="controls"><?php echo AHtml::getLanguageSelectList($this->item->capacity,'capacity')?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab2', JText::_('Price')); ?>
			<div class="span12">
				<div class="form-horizontal">				
					<div class="control-group">
		                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Default Price</strong>">
						<?php echo JText::_('COM_BOOKPRO_HARD_PRICE')?></label></div>
		                <div class="controls"><input type="text" name="params[hard][]" value="<?php echo $param1->hard?>" id="standard_price" name="data[params]" /></div>
					</div>
					<div class="control-group">
		                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Distance (km) </strong>">
						<?php echo JText::_('COM_BOOKPRO_DISTANCE_PRICE')?>(Km)</label></div>
		                <div class="controls"><input type="text" name="params[distance][]" value="<?php echo $param1->distance?>" id="standard_price" name="data[params]" /></div>
					</div>
					</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab3', JText::_('COM_BOOKPRO_ICON')); ?>
			<?php 
			foreach($vehicle_type_size as $size=>$val){?>
				<div class="control-group">
					<div class="control-label"><?php echo JText::_($size) ?></div>
					<div class="controls">
					<?php if(isset($icon->$size)){?>
						<img class="thumbnail" style="width:50px" src="<?php echo JUri::root().$icon->$size;?>" alt="<?php echo JText::_('COM_BOOKPRO_VEHICLE_TYPE_ICON');?>" />
						<br>
					<?php }?>
					<input type="file" name="icon[<?php echo $size?>]" accept="image/*" />
							
					</div>
				</div>
			<?php }?>
						
				
		<?php echo JHtml::_('bootstrap.endTab');?>
		
		<?php echo JHtml::_('bootstrap.endTabSet');?> 
				<input type="hidden" name="task" value="save" /> 
			<?php echo JHTML::_('form.token'); ?>
	
</div>
	</div>
</form>

