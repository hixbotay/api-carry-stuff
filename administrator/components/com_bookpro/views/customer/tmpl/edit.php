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


//allow change user type if create new user
if($this->item->id){
	JToolbarHelper::save ( 'customer.save' );
	JToolBarHelper::apply ( 'customer.apply' );
	
}else{
	JToolBarHelper::apply ( 'customer.apply');
	
	$this->form->setFieldAttribute('registration_date','type','hidden');
	$this->form->setFieldAttribute('password','required','true');
	$this->form->setFieldAttribute('password_confirm','required','true');
}
JToolbarHelper::cancel ( 'customer.cancel' );
$user_type = BookproHelper::get_customer_type();
$this->form->setFieldAttribute('user_type','readonly','false');
if(!$this->item->user_type){
	$this->item->user_type = 1;
}
?>
<script>
jQuery(document).ready(function($){
	$('#jform_user_type').change(function(){
		Joomla.submitbutton('customer.apply');
		});
});
</script>
<form
	action="<?php echo JRoute::_ ( 'index.php?option=com_bookpro&layout=edit&id=' . ( int ) $this->item->id );?>"
	method="post" name="adminForm" id="adminForm" class="form-validate"
	enctype="multipart/form-data">
	<div class="span6">
		<div class="form-horizontal">
    	<?php echo $this->form->renderField('email')?>
		<?php echo $this->form->renderField('password')?>
		<?php echo $this->form->renderField('password_confirm')?>
		<?php echo $this->form->renderField('user_id')?>

		<div class="control-group">
			<label class="control-label" ><?php echo $this->form->getLabel('user_type'); ?></label>
			<div class="controls">
				<?php echo JText::_('COM_BOOKPRO_TYPE_'.strtoupper($user_type[$this->item->user_type]))?>
				<button type="button" class="btn btn-primary btn-micro" onclick="jQuery('#user_type').toggle();"><i class="icon-edit"></i></button>
				<div id="user_type" style="display:none"><?php echo $this->form->getInput('user_type'); ?></div>
			</div>
		</div>	
		<?php echo $this->form->renderField('name')?>
		<?php echo $this->form->renderField('company_name')?>
		<?php echo $this->form->renderField('function')?>
		<?php echo $this->form->renderField('city')?>
		<?php echo $this->form->renderField('phone')?>
		<?php echo $this->form->renderField('mobile')?>
		<?php echo $this->form->renderField('post_code')?>
		<?php echo $this->form->renderField('address')?>
		
		<?php echo $this->form->renderField('registration_date')?>
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
				onclick="Joomla.submitbutton('customer_doc.uploaddoc')"><?php echo JText::_('COM_BOOKPRO_UPLOAD');?></button>
		</div>

		<table class="table-striped table" id="abc">
			<thead>
				<tr>

					<th class="title">
	        			<?php echo JText::_('Name')?>
					</th>
					<th class="title">
	        			<?php echo JText::_('File')?>
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
								<td class="left">
									<a target="_blank" href="<?php echo JURI::base().''.$item->url;?>"> <?php echo $item->name?></a>
								</td>
								<td class="left">
								<a target="_blank" href="<?php $str=$item->url;
								echo JURI::base().''.$str;?>"> <?php if(substr($str,-3)!='jpg' && substr($str,-3)!='png' ) {echo $item->name;} else {echo '<img class="thumbnail" style="width:50px" src="'.$item->url.'">';}?></a>
								</td>
								<?php $url=$item->url;?>
								<td class="right has-context"><input type="button"
						value="Delete" class="btn btn-warning"
						onclick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=customer_doc&task=delete&id='.$item->id.'&url='.$url.'&customer_id='.$item->customer_id); ?>'">


					</td>
				</tr>	
										
						<?php endforeach;?>
				
				
					</tbody>
		</table>
			<?php }?>
	
	</div>
	<input type="hidden" name="customer_id"
		value="<?php echo $this->item->id?>" />
	<input type="hidden" name="user_type_old" value="<?php echo $this->item->user_type?>" />
	<!--  <input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id?>"/>-->
	<input type="hidden" name="task" value="" /> 
	
	<?php echo JHTML::_('form.token'); ?>
</form>
