<?php
/**
 * @package Jbbus
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: request.php 44 2012-07-12 08:05:38Z quannv $
 */
defined('_JEXEC') or die('Restricted access'); 

$notify_url = JURI::root().'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=process&method=' . $this->_element;
$lang=JFactory::getLanguage();
$local=substr($lang->getTag(),0,2);
$logo = $this->params->get('logo');
if(empty($logo))
{
	$logo = 'https://stripe.com/img/documentation/checkout/marketplace.png';
}else{
	$logo = JURI::root().$logo;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $local?>">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js"></script>
<script type="text/javascript">
var clientToken = "<?php echo $this->clientToken?>";

braintree.setup(clientToken, "dropin", {
  container: "payment-form"
});
</script>
</head>
<body>
<form id="checkout" method="post" action="<?php echo $notify_url?>">
	<div class="row-fluid">
		<div class="span4"></div>
		<div class="span4 well well-small">
			 <div id="payment-form"></div>
			 <button type="submit" class="btn btn-primary" ><?php echo JText::sprintf('PLG_BRAINETREE_PAY_TXT',CurrencyHelper::displayPrice($this->total))?></button>
		</div>
		<div class="span4"></div>
	</div>
 
  <input type="hidden" name="order_number" value="<?php echo $this->order_number; ?>"/>
  
</form>


</body>
</html>
