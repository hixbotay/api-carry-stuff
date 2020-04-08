
<?php 
    defined('_JEXEC') or die('Restricted access');
    AImporter::js('master');
    $config=AFactory::getConfig();
    $params=JComponentHelper::getParams('com_bookpro');
    AImporter::css('customer');
    JHtml::_('jquery.framework'); 
    JHtml::_('behavior.framework');
    AImporter::js('master');
    JHtml::_('behavior.modal','a.modal_term');
    BookProHelper::addJqueryValidate();
    ob_start();
	
	
?>
		
<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#registerform").validate({
		    lang: '<?php echo $local ?>',
			rules: {
				firstname: "required",
				lastname: "required",
				username: {
					required: true,
					minlength: 2
				},
				password: {
					required: true,
					minlength: 5
				},
				password2: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
				email: {
					required: true,
					email: true
				},
				telephone: {
					required: true,
					 phoneUS: true
				},
				mobile: {
					required: true,
					 phoneUS: true
				},
				accept_term: {
					required: true
				}
			}
		});

		jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
		    phone_number = phone_number.replace(/\s+/g, "");
		    return this.optional(element) || phone_number.length > 9 && 
		    phone_number.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/);
		}, "<?php echo JText::_('COM_BOOKPRO_UNVALIDATE_MOBILE_TELEPHONE')?>");
	});
</script>	

<?php
    $js=ob_get_contents();

    ob_end_clean(); // get the callback function
    $find = array('<script type="text/javascript">',"</script>"); 
    $js=str_ireplace($find,'',$js);
    $this->document->addScriptDeclaration($js);
    $input=JFactory::getApplication()->input;
    $group_id = $input->get('group_id');

?>
<div class="row-fluid">
    <div class="span6">
        <form class="form-validate" action="index.php" method="post" id="registerform" name="registerform">
            <fieldset>
                <legend>                     
                    <span><?php  echo JText::_('COM_BOOKPRO_CUSTOMER_REGISTER');
                        ?> 
                    </span>
                </legend>
                <p>
                    <?php echo JText::_('COM_BOOKPRO_REGISTER_NOTES')?>
                </p> 

                <div class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" for="username"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_USERNAME' ); ?>
                        </label>
                        <div class="controls">
                            <input onkeyup="checkUsername()" class="inputbox" type="text" name="username" autocomplete="off" id="username" size="20" maxlength="50" value="<?php echo $this->customer->username;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_USERNAME' ); ?>" />
                            <span id="statusUSR"></span>                   
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="password"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PASSWORD' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="password" name="password" id="password" size="20" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PASSWORD' ); ?>"  /> 
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="password2"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_CONFIRM_PASSWORD' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="password" name="password2" id="password2" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CONFIRM_PASSWORD'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="email"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_EMAIL' ); ?>
                        </label>
                        <div class="controls">
                            <input onkeyup="checkEmail()" class="inputbox" type="email" name="email" id="email" size="30" maxlength="30" autocomplete="off" value="<?php echo $this->customer->email;?>"  placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_EMAIL' ); ?>"/>
                            <span id="statusEMAIL"></span>
                        </div>
                    </div>
		
                    <?php if($config->rsGender) {?>
                        <div class="form-inline">
                            <div class="control-group">
                                <label class="control-label" for="gender"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_GENDER' ); ?>
                                </label>
                                <?php echo JHtmlSelect::booleanlist('gender','class="radio inline" placeholder="'.JText::_( 'COM_BOOKPRO_CUSTOMER_GENDER' ).'"',$this->customer->gender,JText::_('COM_BOOKPRO_MALE'),JText::_('COM_BOOKPRO_FEMALE'))?>
                            </div>	
                        </div>
                        <?php  } ?>
                        
                    <div class="control-group">
                        <label class="control-label" for="firstname"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_FIRSTNAME' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="firstname" name="firstname" id="firstname" size="30" maxlength="50" value="<?php echo $this->customer->firstname;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_FIRSTNAME' ); ?>" />
                        </div>
                    </div>


                    <?php if($config->rsLastname) {?>
                        <div class="control-group">
                            <label class="control-label" for="lastname"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_LASTNAME' ); ?>
                            </label>
                            <div class="controls">
                                <input class="inputbox" type="text" name="lastname" id="lastname" size="30" maxlength="50" value="<?php echo $this->customer->lastname;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_LASTNAME' ); ?>" />
                            </div>
                        </div>	
                        <?php } ?>

                    <?php if($config->rsAddress) {?>
                        <div class="control-group">
                            <label class="control-label" for="address"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ADDRESS' ); ?>
                            </label>

                            <div class="controls">
                                <input class="inputbox" type="text" name="address" id="address" size="30" maxlength="50" value="<?php echo $this->customer->address;?>"  placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ADDRESS' ); ?>" />
                            </div>
                        </div>	
                        <?php } ?>
                        
					<?php if ($config->rsMobile) {  ?>
						<div class="control-group">
							<label class="control-label" for="mobile"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_MOBILE' ); ?>
							</label>
							<div class="controls">
								<input class="inputbox" type="text" name="mobile" id="mobile" size="30" maxlength="50" value="<?php echo $this->customer->mobile ?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_MOBILE' ); ?>" />
							</div>
						</div>
					<?php } ?>      
                        
                    <?php if($config->rsTelephone) {?>

                        <div class="control-group">
                            <label class="control-label" for="telephone"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PHONE' ); ?>
                            </label>
                            <div class="controls">
                                <input class="inputbox" type="text" name="telephone" id="telephone" size="30" maxlength="50" value="<?php echo $this->customer->telephone;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PHONE' ); ?>" />
                            </div>
                        </div>	
                    <?php } ?>	
                    
                    
                    <?php if($config->rsCity) {?>
                        <div class="control-group">
                            <label class="control-label" for="city"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CITY' ); ?>
                            </label>
                            <div class="controls">
                                <input class="inputbox" type="text" name="city" id="city" size="30" maxlength="50" value="<?php echo $this->customer->city;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CITY' ); ?>" />
                            </div>
                        </div>	

                        <?php } ?>
                    <?php if($config->rsState) {?>
                        <div class="control-group">
                            <label class="control-label" for="states"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_STATES' ); ?>
                            </label>

                            <div class="controls">
                                <input class="inputbox" type="text" name="states" id="states" size="30" maxlength="50" value="<?php echo $this->customer->states;?>"  placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_STATES' ); ?>" />
                            </div>
                        </div>	
                        <?php } ?>	
                    <?php if($config->rsZip) {?>
                        <div class="control-group">
                            <label class="control-label" for="zip"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ZIP' ); ?>
                            </label>

                            <div class="controls">
                                <input class="inputbox" type="text" name="zip" id="zip" size="30" maxlength="50" value="<?php echo $this->customer->zip;?>" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ZIP' ); ?>" />
                            </div>
                        </div>
                        <?php } ?>
                    <?php if($config->rsCountry) {?>
                        <div class="control-group">
                            <label class="control-label" for="country_id"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_COUNTRY' ); ?>
                            </label>

                            <div class="controls">
                                <?php echo BookProHelper::getCountryList('country_id',$this->customer->country_id,'','class="required validate-select"',$group_id); ?>
                            </div>
                        </div>	
                        <?php } ?>


                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" value="30" name="accept_term" checked="checked" id='accept_term' class="accept_term"> 
                                <a  href="index.php?option=com_content&id=<?php echo $params->get('privacy_content_id')?>&view=article&tmpl=component&task=preview" class='modal_term' rel="{handler: 'iframe', size: {x: 680, y: 370}}"><b><?php echo JText::_("COM_BOOKPRO_ACCEPT_PRIVACY_TERM")?>
                                </b> </a>
                            </label>
                            <input type="submit" name="submit" class="btn btn-primary" id="submit" value="<?php echo JText::_('COM_BOOKPRO_SUBMIT');?>" />
                        </div>
                    </div>
                </div>
                <input type="hidden" name="option" value="com_bookpro" /> 
                <input type="hidden" name="controller" value="customer" />
                <input type="hidden" name="task" value="register" /> 
                <input type="hidden" name="group_id" value="<?php echo $config->customersUsergroup; ?>" />                        
                <input type="hidden" name="return" value="<?php echo $input->get('return')?>" /> 
                <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar(Itemid);?>" id="Itemid"/> 
                <?php echo JHtml::_( 'form.token' ); ?>
            </fieldset>
        </form>         
    </div>              
   
</div>



