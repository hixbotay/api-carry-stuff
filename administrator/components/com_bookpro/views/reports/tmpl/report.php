
<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 63 2012-07-29 10:43:08Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');
	

?>
<table class="table-striped table">
	<thead>
		<tr>
			<th width="10%"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_NAME'); ?></th>					
			<th width="10%"><?php echo JText::_('COM_BOOKPRO_REPORT_DESCRIPTION'); ?></th>							
			<th width="10%"><?php echo JText::_('COM_BOOKPRO_REPORT_CREATED_TIME'); ?></th>					
			<th width="1%"><?php echo JText::_('COM_BOOKPRO_REPORT_ID'); ?></th>	
		</tr>
	</thead>
	<tbody>
		<?php if (!$this->items) { ?>
			<tr><td colspan="10"><?php echo JText::_('JLIB_HTML_NO_RECORDS_FOUND'); ?></td></tr>
		<?php } else { ?>
		    <?php foreach ($this->items as $i=>$subject) { ?>
		   		
		    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
		    		
		    		<td>				                
		                <a target="_blank" href="<?php echo JRoute::_('index.php?option=com_bookpro&task=report.edit&id='.(int) $subject->id); ?>">
						<?php echo $subject->name?></a>				                
		    		</td>
		    		<td><?php echo $subject->desc; ?></td>					 	 
		    		<td><?php echo $subject->created; ?></td>
		    		<td><?php echo $subject->id; ?></td>	 									    		
		    	</tr>
		    <?php } ?>
		<?php } ?>
	</tbody>
</table>