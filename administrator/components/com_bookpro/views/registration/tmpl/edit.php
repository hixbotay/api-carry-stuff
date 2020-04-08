<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 47 2012-07-13 09:43:14Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.form.formfield' );
JToolBarHelper::apply ( 'customer.apply' );
JToolbarHelper::back ( 'Cancel', 'index.php?option=com_bookpro&view=customers&state=0&layout=registration' );
JToolbarHelper::custom('customers.deleteregistration','delete','','Delete', false);

?>

<script type="text/javascript">
jQuery(document).ready(function($){
	
	$("#newaccount").hide();
	
	$('[name="jform[createacc]"]').change(function(){

		var val=$('[name="jform[createacc]"]:checked').val();
		
		if(val==0){
			$("#existing").hide();
			$("#newaccount").show();
		}
		if(val==1) {
			$("#newaccount").hide();
			$("#existing").show();
		}
	});
	
	
});
</script>
<form
	action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=registration&id=' . ( int ) $this->item->id );?>"
	method="post" name="adminForm" id="adminForm" class="form-validate"
	enctype="multipart/form-data">
	<div class="span6">
		<div class="form-horizontal">
    	<?php if ($this->item->id) { ?>
    	<?php if ($this->item->user_id) { ?>
        <div class="control-group">
				<label class="control-label" for="id"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_USER'); ?></label>
				<div class="controls">
					<a
						href="index.php?option=com_users&task=user.edit&id=<?php echo $this->item->user_id; ?>"
						title=""><?php echo $this->item->name; ?></a>
				</div>
			</div>
       	<?php } else { ?>
       	
				<?php echo $this->form->renderField('user_id')?>
			
        <?php } ?>
        	<?php echo $this->form->renderField('email')?>
       <?php } else { ?>
       	
       		<?php echo $this->form->renderField('createacc')?>
       		
       		<div id="existing" class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
			</div>

			<div id="newaccount">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('password'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('password'); ?></div>
				</div>
				

	
				<?php echo $this->form->renderField('sendmail')?>
			</div>
       		
       		<?php
					}
					?>
		 				<?php echo $this->form->renderField('user_type'); ?>
		
		<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('company_name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('company_name'); ?></div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('function'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('function'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('city'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('city'); ?></div>
			</div>



			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
			</div>



			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('mobile'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('mobile'); ?></div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('post_code'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('post_code'); ?></div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('address'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('registration_date'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('registration_date'); ?></div>
			</div>

		</div>
	</div>
	<div class="span6">
	 		<?php if ($this->item->id) { ?>
			<p><?php echo JText::_("COM_BOOKPRO_UPLOAD_DETAIL");?></p>
		<!-- Get Upload form -->
		<br>
		<div class="controller-group">
			<div class="control-label">
				<b><?php echo JText::_('COM_BOOKPRO_UPLOAD');?></b>
			</div>
			<div class="controls">
				<input class="hasTooltip required" type="file" name="fileUpload"
					id="fileUpload" />
			</div>
		</div>
		<div id="upload-button">
			<button class="btn btn-primary"
				onclick="Joomla.submitbutton('upload.save')"><?php echo JText::_('COM_BOOKPRO_UPLOAD');?></button>
		</div>

		<table class="table-striped table" id="abc">
			<thead>
				<tr>

					<th class="title" width="10%">
				        			<?php echo JText::_('Name Document')?>
								</th>
					<th width="1%"><?php echo JText::_('Delete'); ?></th>

				</tr>
			</thead>
			<tbody>
						<?php
					// debug($this->customer_doc);
					?>
						
							<?php if (empty($this->customer_doc)) { ?>
							<tr>
					<td colspan="5" class="emptyListInfo"><?php echo JText::_('No items found.'); ?></td>
				</tr>
							<?php }?>
							<?php foreach ( $this->customer_doc as $i => $item ) :?>
							<tr class="row<?php echo $i%2;?>" sorttable-group-id="1">
								<?php //debug($item); die;?>
							
								<td class="left"><a target="_blank"
						href="<?php echo JURI::base().''.$item->url;?>"> <?php echo $item->name;?></a>
					</td>
								<?php $url=$item->url;?>
								<td class="right has-context"><input type="button"
						value="Delete" class="btn btn-remove"
						onclick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=customer_doc&task=delete&id='.$item->id.'&url='.$url.'&customer_id='.$item->customer_id); ?>'">
								
								
								<?php //echo $item->name; ?>
								</td>
				</tr>	
										
						<?php endforeach;?>
				
				
					</tbody>
		</table>
			<?php }?>
	
	</div>
	
	<input type="hidden" name="cid[]"
		value="<?php echo $this->item->id?>" />
	<!--  <input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id?>"/>-->
	<input type="hidden" name="task" value="" /> 
	<?php echo JHTML::_('form.token'); ?>
</form>
