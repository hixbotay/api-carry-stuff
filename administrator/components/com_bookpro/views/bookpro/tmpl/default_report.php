<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 66 2012-07-31 23:46:01Z quannv $
 **/
// No direct access to this file <?php echo $this->loadTemplate('config')?
defined('_JEXEC') or die('Restricted Access');
?>
<div class="row-fuild">
	<legend>
		<?php echo JText::_('COM_BOOKPRO_LATEST_REPORT'); ?>
	</legend>
	<iframe width="100%" height="200" src="<?php echo JUri::base().'index.php?option=com_bookpro&view=reports&layout=report&tmpl=component'?>" frameborder="0" allowfullscreen></iframe>
</div>