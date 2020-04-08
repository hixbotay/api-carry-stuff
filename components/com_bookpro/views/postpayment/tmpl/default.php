<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
AImporter::helper ( 'currency' );
?>

<div class="row-fluid">
	<div class="span12">
		<center style="background: #ccc;">
<?php echo JText::_( "COM_BOOKPRO_CHECKOUT_RESULTS" ); ?>
</center>

<br>

		<table class="table">
			<tr>
				<td>

<?php echo JText::_('COM_BOOKPRO_ORDER_REFERENCE_NO')?>

</td>
				<td>
<?php echo $this->order->order_number?>
</td>
			</tr>
			<tr>
				<td>
<?php echo JText::_('COM_BOOKPRO_ORDER_ORDER_STATUS')?>
</td>
				<td>
<?php
if ($this->order->order_status == "CONFIRMED") {
	echo JText::_ ( 'COM_BOOKPRO_ORDER_ORDER_CONFIRMED_MSG' );
} else {
	echo JText::_ ( 'COM_BOOKPRO_ORDER_ORDER_PENDING_MSG' );
}

?>

</td>


			</tr>


			<tr>
				<td>
<?php echo JText::_('COM_BOOKPRO_INVOICE')?>
</td>
				<td> <a class="btn btn-success" href="<?php echo JURI::root () . 'index.php?option=com_bookpro&view=ticket&layout=ticket&order_number=' . $this->order->order_number;?>">
				<?php 
				echo JText::_ ( 'COM_BOOKPRO_PRINT' );
				
				?>
				
				</a>

</td>


			</tr>

		</table>

	</div>
</div>