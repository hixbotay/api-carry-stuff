
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
			rules: {
				name: "required",
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
				phone: {
					required: true,
					 phoneUS: true
				},
				mobile: {
					required: true,
					 phoneUS: true
				},
				company_name:  "required",
					
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
    //$group_id = $input->get('group_id');

?>
<div class="row-fluid">
    <div class="span6">
        <form class="form-validate" action="index.php" method="post" id="registerform" name="registerform">
            <fieldset>
                <legend>                     
                    <span><?php  echo JText::_('COM_BOOKPRO_CUSTOMER_REGISTER_PARTICULAR');
                        ?> 
                    </span>
                </legend>
                <p>
                    <?php echo JText::_('COM_BOOKPRO_REGISTER_NOTES')?>
                </p> 

                <div class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" for="name"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_NAME' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" name="name" autocomplete="off" id="username" size="20" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_NAME' ); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="email"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_EMAIL' ); ?>
                        </label>
                        <div class="controls">
                            <input onkeyup="checkEmail()" class="inputbox" type="email" name="email" id="email" size="30" maxlength="30" autocomplete="off" value=""  placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_EMAIL' ); ?>"/>
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
                        <label class="control-label" for="password2"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_PASSWORD_CONFIRM' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="password" name="password2" id="password2" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PASSWORD_CONFIRM'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="company_name"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_COMPANY_NAME' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="company_name" name="company_name" id="company_name" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_COMPANY_NAME' ); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="function"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_FUNCTION' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="function" name="function" id="function" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_FUNCTION' ); ?>" />
                        </div>
                    </div>                      
                    <div class="control-group">
                        <label class="control-label" for="address"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ADDRESS' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="address" name="address" id="address" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_ADDRESS' ); ?>" />
                        </div>
                    </div>
      
                     <div class="control-group">
                        <label class="control-label" for="city"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CITY' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="city" name="city" id="city" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_CITY' ); ?>" />
                        </div>
                    </div>
                    
                     <div class="control-group">
                        <label class="control-label" for="postalcode"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_POSTALCODE' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="postalcode" name="postalcode" id="postalcode" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_POSTALCODE' ); ?>" />
                        </div>
                    </div>
                    
                     <div class="control-group">
                        <label class="control-label" for="mobile"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_MOBI' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="mobile" name="mobile" id="mobile" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_MOBI' ); ?>" />
                        </div>
                    </div>
                    
                     <div class="control-group">
                        <label class="control-label" for="phone"><?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PHONE' ); ?>
                        </label>
                        <div class="controls">
                            <input class="inputbox" type="text" id="phone" name="phone" id="phone" size="30" maxlength="50" value="" placeholder="<?php echo JText::_( 'COM_BOOKPRO_CUSTOMER_PHONE' ); ?>" />
                        </div>
                    </div>

                 
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" value="" name="accept_term" checked="checked" id='accept_term' class="accept_term"> 
                                <a  href="index.php?option=com_content&id=<?php echo $params->get('privacy_content_id')?>&view=article&tmpl=component&task=preview" class='modal_term' rel="{handler: 'iframe', size: {x: 680, y: 370}}"><b><?php echo JText::_("COM_BOOKPRO_ACCEPT_PRIVACY_TERM")?>
                                </b> </a>
                            </label>
                            <input type="submit" name="submit" class="btn btn-primary" id="submit" value="<?php echo JText::_('COM_BOOKPRO_SUBMIT');?>" />
                        </div>
                    </div>
                </div>
             	<input type="hidden" name="registration_date" value="<?php echo date("Y/m/d")?>" />
             	<input type="hidden" name="type" value="2" />
             	<input type="hidden" name="state" value="0" />
                <input type="hidden" name="option" value="com_bookpro" /> 
                <input type="hidden" name="controller" value="customer" />
                <input type="hidden" name="task" value="registrationenterprise" /> 
                <input type="hidden" name="return" value="<?php echo $input->get('return')?>" /> 
                <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar(Itemid);?>" id="Itemid"/> 
                <?php echo JHtml::_( 'form.token' ); ?>
            </fieldset>
        </form>         
    </div>              
   
</div>



