<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: tours.php 21 2012-07-06 04:06:17Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

?>

<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('email_send_from'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('email_send_from'); ?></div>
</div>

<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('email_send_from_name'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('email_send_from_name'); ?></div>
</div>

<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('email_customer_subject'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('email_customer_subject'); ?></div>
</div>

<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('email_customer_body'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('email_customer_body'); ?></div>
</div>