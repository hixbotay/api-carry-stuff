<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die;

BookProHelper::setSubmenu(1);

?>
<script type="text/javascript">
 
	jQuery(document).ready(function($){
		$("#adminForm").validate({
			
		    lang: '<?php echo 'EN' ?>',
			rules: {				
				date: {
					required: true,
					date: true
				},
				percent: {
					required: true,
					 percent: true
				}
			}
		});
		

		jQuery.validator.addMethod("date", function(date, element) {		
		    return this.optional(element) || (date.match(/(0[1-9]|[12][0-9]|3[01])[-](0[1-9]|1[012])/) && date.length ==5);
		}, "<?php echo JText::_('COM_BOOKPRO_DATE_VALIDATE')?>");
		jQuery.validator.addMethod("percent", function(percent, element) {		
		    return this.optional(element) || percent.length > 0 && 
		    percent.match(/[-+][0-9]*\.?[0-9]+$/);
		}, "<?php echo JText::_('COM_BOOKPRO_PERCENT_VALIDATE')?>");
	});

	Joomla.submitbutton = function(task)
	{
		if(task=="savePrice"){
		    var isValid = jQuery("#adminForm").valid();
		    if(isValid){
		    	submitform(task);
		    }else{
		        return false;
		    }
		}
		else{
			submitform(task);
		}
	}
</script>
<!-- Add new input -->
<script>
jQuery(document).ready(function($) { 
   
    var wrapper         = $("#date_price"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
   
   
    var i = <?php echo count($this->date)?>;
    $(add_button).click(function(e){
    	
        $('#date_price_body').append('<tr>'+
                			'<td><input type="text" required class="inputbox date" name="data['+i+'][date]" /></td>'+
                			'<td><input type="text" required class="inputbox percent" name="data['+i+'][params]" />%</td>'+
                			'<td><span class="icon-remove remove_field btn btn-primary"></span><input type="hidden" name="data['+i+'][code]" value="DATE" /></td>'+
                			'</tr>');
		i++;
   	 
    });
   
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('td').parent('tr').remove();        
    })
});
</script>
<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro'); ?>" method="post" id="adminForm" name="adminForm">
		
				<div class="span12">
   					<button class="add_field_button btn btn-primary pull-left" type="button"><i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_BOOKPRO_ADD')?></button>
    			</div>
    			<hr>
    		 
			<table class="table" id="date_price">
				<thead>
					<tr>
						<th>Date(Day-Month)</th>
						<th><?php echo JText::_('COM_BOOKPRO_PERCENT')?></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="date_price_body">
					
						<?php if($this->date) {
							foreach($this->date as $i=>$d){
						?>
						<tr>
							<td><input type="text" required class="date inputbox"name="data[<?php echo $i?>][date]" value="<?php echo $d->date?>"/></td>
							<td><input type="text" required class="percent inputbox" name="data[<?php echo $i?>][params]" value="<?php echo $d->params?>" />%</td>
							<td>
								<span class="icon-remove remove_field btn btn-primary"></span>
								<input type="hidden" name="data[<?php echo $i?>][code]" value="DATE" />
							</td>
						</tr>
						<?php 
						}
						}?>
					
				</tbody>
			</table>
			
		<?php echo JHtml::_('bootstrap.endTab');?>       	
    	<?php echo JHtml::_('bootstrap.endTabSet');?>
					
	
		<input type="hidden" name="option" value="com_bookpro" />		
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="type" value="date" />
		<?php echo JHtml::_('form.token'); ?>
	
</form>
</div>
