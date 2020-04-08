<?php

/**

* @package 	Bookpro

* @author 		Ngo Van Quan

* @link 		http://joombooking.com

* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan

* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

* @version 	$Id: default.php  23-06-2012 23:33:14

**/

// No direct access to this file

defined('_JEXEC') or die('Restricted access');
JHtmlBehavior::modal('a.amodal');
// debug($this->orderComplex);
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('var siteURL="'.JUri::root().'"');
?>

<form name="frontForm" method="post" action="index.php" id="paymentForm">
	<div class="row-fluid">
		<div class="span7">
		<div class="well well-small">
			<div class="well well-small" style="background-color: white; ">
			<?php echo $this->loadTemplate('cart')?>
			</div>
		 </div>
		</div>	
		<div class="span5">
			<h2 class="block_head">
				<span> <?php echo JText::_('COM_BOOKPRO_PAYMENT_SELECT')?></span>
			</h2>
		
       
			<?php
			if ($this->plugins)
			{
				foreach ($this->plugins as $plugin)
				{
				?>
					<input value="<?php echo $plugin->element; ?>" class="payment_plugin" onclick="getPaymentForm('<?php echo $plugin->element; ?>', 'payment_form_div');"
					name="payment_plugin" type="radio"	<?php echo (!empty($plugin->checked)) ? "checked" : ""; ?>/>

				<?php
					$params= new JRegistry;
					$params->loadString($plugin->params);
					$title = $params->get('display_name', '');
					if(!empty($title)) {
						echo $title;
					} else {
						echo JText::_($plugin->name );
					}
				?>
				<br />
			<?php
				}
			}
			?>
			<div id='payment_form_div' style="padding-top: 10px;">
				<?php
				if (!empty($this->payment_form_div))
				{
					echo $this->payment_form_div;
				}
				?>
			</div>
			
			<div class="form-inline">
				<input type="checkbox" value="30" name="license_confirm" checked="checked" id='license_confirm' class="controls"> 
				<label class="controls" for="term_condition">
				 <a href="index.php?option=com_content&id=<?php echo $this->config->get('term_content_id'); ?>&view=article" target="_blank">
				<?php echo JText::_("COM_BOOKPRO_ACCEPT_TERM")?></a>
				</label>
			</div>
			<br />
	
			<div class="center-button">
				<input class="btn btn-large btn-primary" type="submit" value="<?php echo JText::_('COM_BOOKPRO_PAYNOW')?>" name="btnSubmit" id="submitpayment" />
			</div>
			<?php echo FormHelper::bookproHiddenField(array('controller'=>'payment','task'=>'process','Itemid'=>JRequest::getVar('Itemid'),'order_id'=>$this->order->order_id))?>
		</div>
	</div>
</form>

<script type="text/javascript">

function getPaymentForm(element,container){
	
	jQuery(document).ready(function($) {
	container = '#'+container;
	$.ajax({
		url : siteURL + 'index.php?option=com_bookpro&controller=payment&task=getPaymentForm&format=raw&payment_element='+ element,
		type : 'post',
		cache: false,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        beforeSend: function() {
           	 
             },
        complete: function() {
        	 
         },
        success: function(json) {
        	$(container).html(json.msg);
			return true;
		}
	});
	});
	}
				
	jQuery(document).ready(function($) {

	 $("#submitpayment").click(function() {

		if(jQuery("input:radio[name='payment_plugin']").is(":checked")==false)
		{
			alert("<?php echo JText::_('COM_BOOKPRO_SELECT_PAYMENT_METHOD_WARN') ?>");
   	 		return false; 
		}
		if(jQuery('#license_confirm').is(':checked')==false){
    		alert("<?php echo JText::_('COM_BOOKPRO_ACCEPT_TERM_WARN') ?>");
   	 		return false;      
    	}
		$("#paymentForm").submit();
	});

	 $("#couponbt").click(function() {

			if($("input:text[name=coupon]").val()){
				$("input:hidden[name=controller]").val('order');
		    	$("input:hidden[name=task]").val('applycoupon');
		    	$("#paymentForm").submit();
				}
			else {
				alert('Empty coupon code');
				return false;
			}

		    });
	
});
	
	
</script>

