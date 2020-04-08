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
BookProHelper::setSubmenu(1);

$app		= JFactory::getApplication();
?>
<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=payments');?>" method="post" id="adminForm" name="adminForm">
	<div id="filter-bar" class="btn-toolbar"></div>	
	<div class="clearfix"></div>
	<div class="row-fluid">
		<!-- -----------------------methods-------------------- -->
		<div class="span9">
			<legend><?php echo JText::_('COM_BOOKPRO_METHODS')?></legend>
			<table class= "table table-striped" id="aircraftList">
				<thead>
					<tr>	
						<th><?php echo JText::_('COM_BOOKPRO_CODE')?></th>
						<th><?php echo JText::_('COM_BOOKPRO_NAME')?></th>
						<th><?php echo JText::_('COM_BOOKPRO_STATE')?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items->methods as $i => $item) :
					?>
						<tr>
							<td><a href="<?php echo JRoute::_('index.php?option=com_bookpro&view=payment&type=methods&layout=edit&code='.$item->code); ?>" >
							<?php echo $item->code?></a></td>
							<td><?php echo BookProHelper::formatLang(json_encode($item->name),'name')?></td>
							<td><?php echo JHtml::_('jgrid.published', $item->enabled, $i);?></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<!-- -----------------------gateways-------------------- -->
		<div class="span3">
			<legend><?php echo JText::_('COM_BOOKPRO_GATEWAYS')?></legend>
			<table class= "table table-striped" id="aircraftList">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_BOOKPRO_CODE')?></th>
							<th><?php echo JText::_('COM_BOOKPRO_STATE')?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->items->gateways as $i => $gateway) :
						?>
							<tr>
								<td><a href="<?php echo JRoute::_('index.php?option=com_bookpro&view=payment&type=gateways&layout=gateway&code='.$gateway->code); ?>" >
								<?php echo $gateway->code?></a></td>
								<td>
								<?php 
								if ($gateway->enabled == 1) {
									?>												
									<a href="javascript:void(0)" class="btn btn-micro active hasTooltip" onc1lick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=payment&task=changegateway'); ?>'" title="" data-original-title="Disable">
									<span class="icon-publish"></span></a>
									<?php						
								} else {
									?>
									<a href="javascript:void(0)" class="btn btn-micro hasTooltip" oncl1ick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=payment&task=changegateway'); ?>'" title="" data-original-title="Enable">
									<span class="icon-unpublish"></span></a>
									<?php }?>		
							</td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
		</div>
	</div>
	
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="payments" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
</div>
