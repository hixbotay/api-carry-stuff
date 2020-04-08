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

AImporter::helper('date','currency');
$recipient=json_decode($this->item->recipient_info);
$from=json_decode($this->item->from);
//var_dump($from);die;
$to=json_decode($this->item->to);
$packages=json_decode($this->item->packages);
$recipient_label = array('first_name','last_name','phone','city','company_name');
if($this->item->is_cancelled){
	AImporter::model('customer');
	$model = new BookproModelCustomer();
	if($this->item->cancel){
		$cancel_user = explode(':',$this->item->cancel);
		if($cancel_user[0]=='admin'){			
			$cancel_admin = JFactory::getUser($cancel_user[1]);
		}else{
			$cancel_customer = $model->getItem($cancel_user[1]);
		}
		
	}
}
?>

  
  
<!--<script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
--><script src="http://maps.google.com/maps/api/js?sensor=false&key=<?php JComponentHelper::getParams('com_bookpro')->get('google_key')?>" type="text/javascript"></script>

<script type="text/javascript">

	jQuery(document).ready(function($){
		initialize();	
		calcRoute();init();
		
	});
</script>

<script type="text/javascript">
  var directionDisplay;
  var map;
  
  function initialize() {
    directionsDisplay = new google.maps.DirectionsRenderer();
    var melbourne = new google.maps.LatLng(<?php echo $from->latitude?>, <?php echo $from->longitude?>);
    var myOptions = {
      zoom:11,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: melbourne
    }
	
	

    
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	var marker = new google.maps.Marker({
          position: {lat: <?php echo $from->latitude?>, lng: <?php echo $from->longitude?>},
          map: map,
          title: '<?php echo $from->address?>'
        });
	var marker2 = new google.maps.Marker({
          position: {lat: <?php echo $to->latitude?>, lng: <?php echo $to->longitude?>},
          map: map,
          title: '<?php echo $to->address?>'
        });
		
    directionsDisplay.setMap(map);
	
	
  }

  var directionsService = new google.maps.DirectionsService();

  function calcRoute() {
	var lat1 = jQuery( "input[name$='from[latitude][]']").val();
   	var long1 = jQuery( "input[name$='from[longitude][]']").val(); 
    var start = lat1+", "+long1;

   	var lat2 = jQuery( "input[name$='to[latitude][]']").val();
  	var long2 = jQuery( "input[name$='to[longitude][]']").val();
    var end = lat2+", "+long2;
    
    var request = {
      origin:start,
      destination:end,
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };
    
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
        //jQuery('#distance').val(response.routes[0].legs[0].distance.value / 1000);
      }
    });
  }
</script>


<script type="text/javascript">

</script>



<form action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id='.( int ) $this->item->id );?>" method="post" name="adminForm" id="adminForm" class="form-validate">	
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab1', JText::_('Details')); ?> 	
	<div class="span6">
		<div class="form-horizontal">
			<?php echo $this->form->renderField('id');?>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CUSTOMER'); ?></div>
				<div class="controls"><a target="blank" href="index.php?option=com_bookpro&view=customer&layout=edit&id=<?php echo $this->item->customer_id?>"><?php echo $this->customer->name?></a></div>
			</div>
			<?php echo $this->form->renderField('driver_id');?>
			<?php echo $this->form->renderField('note');?>
			<?php echo $this->form->renderField('is_booked');?>
			<?php //echo $this->form->renderField('transport_type_id');?>
			<?php echo $this->form->renderField('vehicle_type_id');?>
			<?php echo $this->form->renderField('is_paid');?>
			<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('start_time'); ?></div>
						<div class="controls"><?php 
						$this->form->setFieldAttribute('start_time', 'format',DateHelper::getConvertDateFormat('M'), $group = null);
						echo $this->form->getInput('start_time'); ?></div>
				</div>
				
			
			<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('from'); ?>
					</div>	
					<div class="controls"><input class="input" type="text" name="from[address][]"  value="<?php echo $from->address;?>"/></div>
			</div>
			<div class="control-group">	
				<div class="controls">	
						<input class="input-small" type="text" name="from[latitude][]" value="<?php echo $from->latitude;?>"/>			
						<input class="input-small" type="text" name="from[longitude][]" value="<?php echo $from->longitude;?>"/>													
				</div>
			</div>	
			<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('to'); ?>
					</div>	
					<div class="controls"><input class="input" type="text" name="to[address][]"  value="<?php echo $to->address;?>"/></div>
			</div>		
			<div class="control-group">
				<div class="controls">	
						<input class="input-small" type="text" name="to[latitude][]"  value="<?php echo $to->latitude;?>"/>	
						<input class="input-small" type="text" name="to[longitude][]"  value="<?php echo $to->longitude;?>"/>	
				</div>
			</div>	
			<?php echo $this->form->renderField('distance');?>	
			<?php echo $this->form->renderField('discount');?>
			<?php echo $this->form->renderField('total');?>
			<div class="clearfix"><?php $total_real = $this->item->discount+$this->item->total;
			echo JText::_('COM_BOOKPRO_ORDER_REAL_PRICE').': '.CurrencyHelper::formatprice($total_real);?></div>
			<div class="clearfix"><?php if($this->item->total<0){
				$price=0;
			}else{
				$price = $this->item->total;
			}
			echo JText::_('COM_BOOKPRO_ORDER_REAL_PAYMENT_PRICE').': '.CurrencyHelper::formatprice($price); ?></div>
			<?php echo $this->form->renderField('is_accepted');?>	
			<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('is_cancelled'); ?>
					</div>	
					<div class="controls"><?php echo $this->item->is_cancelled ? 'YES' : 'NO';?>&nbsp;<a class="btn btn-primary" href="index.php?option=com_bookpro&task=order.cancelorder&id=<?php echo $this->item->id?>"><?php echo JText::_('COM_BOOKPRO_CHANGE_CANCEL_STATUS')?></a></div>
			</div>	
			<?php echo $this->form->renderField('created_time');?>	
		</div>
	</div>
    <div class="span6" >
		<div id="map_canvas" style="width: 100% ; height: 350px"></div>
		<!--Cancel comment-->
		<?php if($this->item->is_cancelled){?>
			<div class="well well-small">
				<div class="control-group">
					<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CANCEL_CUSTOMER')?></div>	
					<div class="controls">
						<?php if($cancel_customer){?>
							<a href="index.php?option=com_bookpro&task=customer.edit&id=<?php echo $cancel_customer->id?>"><?php echo $cancel_customer->name?></a>
						<?php }?>
						<?php if($cancel_admin){?>
							<a href="index.php?option=com_users&task=user.edit&id=<?php echo $cancel_admin->id?>"><?php echo $cancel_admin->username?></a>
						<?php }?>
					</div>
				</div>	
				<div class="control-group">
					<div class="control-label"><?php echo JText::_('COM_BOOKPRO_CANCEL_COMMENT')?></div>	
					<div class="controls">
						<?php echo $this->params->cancel_comment?>
					</div>
				</div>					
			</div>
		<?php }?>
		<!--transaction-->
		<?php if($this->transactions){?>
			<div class="well well-small">
				<div class="control-group">
					<div class="control-label"><?php echo JText::_('COM_BOOKPRO_TRANSACTION')?></div>	
					<div class="controls">
						<table class="table-striped table">
							<thead>
								<tr>
									<th ><?php echo JText::_('COM_BOOKPRO_TRANSACTION_ID'); ?></th>		
									<th ><?php echo JText::_('Gateway'); ?></th>	
									<th ><?php echo JText::_('COM_BOOKPRO_ORDER_CREATED'); ?></th>										
								</tr>
							</thead>
							<tbody>
								<?php foreach($this->transactions as $transaction){
									$tx_params = json_decode($transaction->params);?>
									<tr>
										<td><a target="_blank" href="index.php?option=com_bookpro&task=transaction.edit&id=<?php echo $transaction->id?>"><?php echo $transaction->tx_id?></a></td>
										<td><?php echo $tx_params->gateway?></td>
										<td><?php echo $transaction->created?></td>
									</tr>
									
								<?php }?>
							</tbody>
						</table>
						
					</div>
				</div>				
			</div>
		<?php }?>
	</div>
    <?php echo JHtml::_('bootstrap.endTab');?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab2', JText::_('COM_BOOKPRO_ORDERS_RECIPIENT')); ?>
		
			<div class="form-horizontal">
				<?php echo $this->form->renderField('delivery_code');?>
				<?php echo $this->form->renderField('recipient_validate');?>
				<?php echo $this->form->renderField('receiver_name');?>	
				<?php foreach ($recipient_label as $key){?>
					<div class="control-group">
						<div class="control-label"><?php echo JText::_('COM_BOOKPRO_'.strtoupper($key))?></div>	
						<div class="controls"><input class="input" type="text" name="recipient_info[<?php echo $key?>]"  value="<?php echo $recipient->$key;?>"/></div>
					</div>
				<?php }?>
				
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('packages'); ?></div>
								<?php 	
					if(!empty($packages)){
					foreach ($packages as $i=>$pack){
						//var_dump($pack->package_nature->name);die;	
					?>		
					<div class="priceclone" style="margin-top: 10px;">
						<div class="controls">
						<?php foreach ($pack->package_nature->name as $key=>$val){?>			
								<input class="input" type="text" name="packages[<?php echo $i?>][package_nature][name][<?php echo $key?>]" title="<?php echo JText::_('COM_BOOKPRO_PACKAGE_NATURE_NAME')?>" value="<?php echo $val;?>"/>	
							
							<?php }?>
							<input class="input-small" type="text" name="packages[<?php echo $i?>][package_number]" title="<?php echo JText::_('COM_BOOKPRO_PACKAGE_NUMBER')?>" value="<?php echo $pack->package_number;?>"/>
							<input class="input" type="hidden" name="packages[<?php echo $i?>][package_nature][id]" value="<?php echo $pack->package_nature->id;?>"/>
						</div>
						
					</div>
					<?php }
				}?>
				</div>
			</div>
	
		<?php echo JHtml::_('bootstrap.endTab');?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab3', JText::_('COM_BOOKPRO_ORDERS_TRIP')); ?>
	<div class="span6">	
		<div class="form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('trip_status'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('trip_status'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('trip_start_time'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('trip_start_time'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('end_time'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('end_time'); ?></div>
			</div>
		</div>
	</div>
	<div class="span6" >
		<iframe src="index.php?option=com_bookpro&view=order&layout=iframe&tmpl=component&id=<?php echo $this->item->id?>" style="border:none; width:100%; height: 500px"></iframe>
	</div>
		<?php echo JHtml::_('bootstrap.endTab');?>	
	
	<?php echo JHtml::_('bootstrap.endTabSet');?>     
		<input type="hidden" name="task" value="save" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>

