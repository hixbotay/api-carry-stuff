<?php 

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: currency.php 16 2012-06-26 12:45:19Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');
AImporter::helper('currency');

?>
<div class="bpcart row-fluid">
	<h2 class='block_head'>
		<span><?php echo JText::_("COM_BOOKPRO_CART_SUMMARY")?> </span>
	</h2>
	 <table class="bookpro-table" align="left">
	 
	 
	 	<tr>
	 		<th style="text-align: left"><?php echo JText::_('COM_BOOKPRO_ORDER_TOTAL')?></th>
	 		<td><?php echo CurrencyHelper::formatprice($this->order->price->total) ?></td>
	 	</tr>
	 	<tr>
	 		<th style="text-align: left"><?php echo JText::_('COM_BOOKPRO_COUPON_CODE')?></th>
	 		<td>
	 			<div class="form-inline">
					<input type="text" value="" class="input-small" name="coupon"> <input type="submit" class="btn"
					value="<?php echo JText::_('COM_BOOKPRO_SUBMIT') ?>" id="couponbt">
				</div>
	 		</td>
	 		
	 	</tr>
	 </table>
</div>
