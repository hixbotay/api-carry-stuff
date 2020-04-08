<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 66 2012-07-31 23:46:01Z quannv $
 **/
// No direct access to this file <?php echo $this->loadTemplate('config')?
defined('_JEXEC') or die('Restricted Access');

JToolBarHelper::title('Allo&Go','chart');
JToolBarHelper::preferences('com_bookpro');
?>
<script type='text/javascript'>
	
	
	function checkCurrentState(){
		 jQuery.ajax({
			   url: "index.php?option=com_bookpro&controller=bookpro&task=getOnlineCustomer&type=-3",
			   beforeSend: function() {
			       },
			   success : function(result) { 
			    jQuery("#online_customer").html(result);
			   },
			 });
		 jQuery.ajax({
			   url: "index.php?option=com_bookpro&controller=bookpro&task=getCurrentOrderDelivery",
			   beforeSend: function() {
			       },
			   success : function(result) { 
			   		jQuery("#current_delivery_order").html(result);
			   },
			 });
		 jQuery.ajax({
			   url: "index.php?option=com_bookpro&controller=bookpro&task=getOnlineCustomer&type=3",
			   beforeSend: function() {
			       },
			   success : function(result) { 
			    jQuery("#online_driver").html(result);
			   },
			 });
		 return;
	}
	
	function timeout(){
		window.setInterval(checkCurrentState, 10000);
	}
	jQuery(document).ready(function($){
		checkCurrentState();
		var frequen_check = timeout();
	
	});
	
</script>
<div class="span10" id="j-main-container">
	<div class="row-fuild">
	
	<div class="lead">
		<h1 style="font-weight: normal; border-bottom: 1px #eeeeee solid;"><?php echo JText::_('COM_BOOKPRO_DASHBOARD'); ?></h1>
		
	</div> 
	<div class="row-fluid">
		<!-- -----------------------TOP SIDEBAR------------------------------ -->
		<?php echo $this->loadTemplate('topbar');?>
		<div class="row-fluid">
			<div class="span8" id="report">
			
				<?php echo $this->loadTemplate('report')?>
			</div>
			<div class="span4" id="chart">
				<!-- -----------------------CHART------------------------------ -->
				
				<?php echo $this->loadTemplate('chart');?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12" id="booking">
				<div class="row-fuild">
	<legend>
		<?php echo JText::_('COM_BOOKPRO_LATEST_BOOKING'); ?>
	</legend>
	<iframe width="100%" height="315" src="<?php echo JUri::base().'index.php?option=com_bookpro&view=orders&layout=report&tmpl=component'?>" frameborder="0" allowfullscreen></iframe>
	
</div>
			</div>
		</div>
	</div>
	</div>
</div>



<div class="span12" style="height:30px"></div>