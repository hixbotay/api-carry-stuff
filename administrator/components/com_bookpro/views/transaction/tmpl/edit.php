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

AImporter::helper('date');
$params = $this->item->params;
?>

<form action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&view=transaction&layout=edit&id='.( int ) $this->item->id );?>" method="post" name="adminForm" id="adminForm" class="form-validate">	
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
<div class="row-fluid">
	<div class="span8">
		 <div class="form-horizontal">
	    	<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER')?></div>
				<div class="controls"><a target="_blank" href="index.php?option=com_bookpro&view=order&layout=edit&id=<?php echo $this->item->order_id?>" ><?php echo sprintf('%010d',$this->item->order_id)?></a></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('tx_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('tx_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('total'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('total'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
			</div>
	        		
		</div>
	</div>
	<div class="span4">
		<legend><?php echo JText::_('COM_BOOKPRO_DETAIL')?></legend>
		 <div class="form-horizontal">
		 	<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_PAYMENT_GATEWAY')?></div>
				<div class="controls"><?php echo $params['gateway']?></div>
			</div>
		 	<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_MSG_DETAIL')?></div>
				<div class="controls"><?php echo $params['desc']?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_PAYMENT_METHOD')?></div>
				<div class="controls"><?php echo $params['method']?></div>
			</div>
			<?php if(isset($params['card_info'])){?>
				<div class="control-group">
					<div class="control-label"><?php echo JText::_('COM_BOOKPRO_PAYMENT_CARD_INFO')?></div>
					<div class="controls"><?php foreach ($params['card_info'] as $k=>$v){echo $k.': '.$v.'<br>';}?></div>
				</div>				
			<?php }?>
		 </div>
	</div>
</div>
   
    	<?php echo JHtml::_('bootstrap.endTab');?>      	 	
    	<?php echo JHtml::_('bootstrap.endTabSet');?>   	
		<input type="hidden" name="task" value="save" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>
