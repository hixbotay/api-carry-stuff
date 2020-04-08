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
$param=(object)$this->item->params;
$param1=(object)$param->prices;
//var_dump($param1->distance);die;
AImporter::helper('date');
?>

<form action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id='.( int ) $this->item->id );?>" method="post" name="adminForm" id="adminForm" class="form-validate">	
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
    <div class="form-horizontal">
    	<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('driver_id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('driver_id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
		</div>
        
        <div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('vehicle_type_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('vehicle_type_id'); ?></div>
			</div>
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('capacity'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('capacity'); ?></div>
		</div>
			
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('plate_number'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('plate_number'); ?></div>
			</div>
		
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('current'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('current'); ?></div>
			</div>
		
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('desc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('desc'); ?></div>
			</div>
			<!--
		<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('default'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('default'); ?></div>
			</div>-->			
	</div>
    	<?php echo JHtml::_('bootstrap.endTab');?>   
		<?php if(0){?>
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
		<?php }?>
    	<?php echo JHtml::_('bootstrap.endTabSet');?>   	
		<input type="hidden" name="task" value="save" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>
