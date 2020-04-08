<?php
defined('_JEXEC') or die('Restricted access');
AImporter::js('master');
$config=AFactory::getConfig();
$customer = $displayData;
?>

<div id="profile" class="profile form-horizontal span12">
	<div class="control-group">
		<div class="control-label">
			 <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_FIRSTNAME' ); ?>&nbsp;<span class="required" style="color: red">*</span>			
		</div>
		<div class="controls">
			<input class="inputbox required  name" type="text" id="firstname"
				name="firstname" id="firstname" size="30" maxlength="50"
				value="<?php echo $customer->firstname ?>" />
		</div>
	</div>
		<?php if ($config->rsLastname){?>		
	
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_LASTNAME' ); ?>&nbsp;<span class="required" style="color: red">*</span>			
		</div>
		<div class="controls">
			<input class="inputbox required name" type="text" name="lastname"
				id="lastname" size="30" maxlength="50"
				value="<?php echo $customer->lastname ?>" />
		</div>
	</div>
		<?php } ?>  
		<?php if ($config->rsAddress){?>
	
	<div class="control-group">
		<div class="control-label">
			 <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ADDRESS' ); ?>&nbsp;<span class="required" style="color: red">*</span>
		</div>
		<div class="controls">
			<input class="inputbox required" type="text" name=address
				id="address" size="30" maxlength="50"
				value="<?php echo $customer->address ?>" />
		</div>
	</div>
		<?php } ?>
		<?php if ($config->rsCity) { ?>
	
	<div class="control-group">
		<div class="control-label">
				<label for="city"> <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CITY' ); ?>&nbsp;<span class="required" style="color: red">*</span>
				</label>
		</div>
		<div class="controls">
			<input class="inputbox required name" type="text" name="city"
					id="city" size="30" maxlength="50"
					value="<?php echo $customer->city ?>"/>
		</div>
	</div>
			<?php } ?>
			<?php if ($config->rsState) {?>
	
	<div class="control-group">
		<div class="control-label">
				<label for="states"> <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_STATES' ); ?>&nbsp;<span class="required" style="color: red">*</span>
				</label>
		</div>
		<div class="controls">
				<input class="inputbox required" type="text" name="states"
					id="states" size="30" maxlength="50"
					value="<?php echo $customer->states ?>" />
		</div>
	</div>
		<?php } ?>			
		<?php if ($config->rsZip){ ?>
	
	<div class="control-group">
		<div class="control-label">
			<label for="zip"> <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ZIP' ); ?>&nbsp;<span class="required" style="color: red">*</span>
			</label>
		</div>
		<div class="controls">
			<input class="inputbox required" type="text" name="zip" id="zip"
				size="30" maxlength="50" value="<?php echo $customer->zip ?>" />

		</div>
	</div>
		<?php } ?>
		<?php if ($config->rsCountry){ ?>
	
	<div class="control-group">
		<div class="control-label">
			 <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_COUNTRY' ); ?>&nbsp;<span class="required" style="color: red">*</span>
		</div>
		<div class="controls">
			<?php echo BookProHelper::getCountryList('country_id', $customer->country_id,'')?>
		</div>
	</div>
		<?php } ?>
		
		<?php if ($config->rsMobile) {  ?>
	
	<div class="control-group">
		<div class="control-label">
			<label for="mobilephone"> <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_MOBILE' ); ?>&nbsp;<span class="required" style="color: red">*</span>
			</label>
		</div>
		<div class="controls">
			<input class="inputbox required" type="text" name="mobile"
				id="mobile" size="30" maxlength="50"
				value="<?php echo $customer->mobile ?>" />

		</div>
	</div>
		<?php } ?>
		
		<?php if ($config->rsTelephone) { ?>
	
	<div class="control-group">
		<div class="control-label">
			 <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PHONE' ); ?>&nbsp;<span class="required" style="color: red">*</span>
		</div>
		<div class="controls">
			<input class="inputbox required" type="text" name="telephone"
				id="telephone" size="30" maxlength="50"
				value="<?php echo $customer->telephone ?>" />
		</div>
	</div>
		<?php } ?>
		
	<div class="control-group">
		<div class="control-label">
			 <?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_EMAIL' ); ?>&nbsp;<span class="required" style="color: red">*</span>
		</div>
		<div class="controls">
			<input class="inputbox required email" type="text" name="email" id="email"
				size="30"
				value="<?php echo $customer->email ?>" /> <span
				id="statusEMAIL"></span>
		</div>
	</div>
</div>
<input type="hidden" name="customer_id" value="<?php echo $customer->id ?>" />