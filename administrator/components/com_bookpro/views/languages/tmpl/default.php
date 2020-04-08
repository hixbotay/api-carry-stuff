<?php
/**
 * Support for work with request params.
 *
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: request.php 44 2012-07-12 08:05:38Z quannv $
 */
defined ( '_JEXEC' ) or die ();
BookProHelper::setSubmenu (1);
$folder					= JPATH_SITE .DS."language";
$itemsSite = JFolder::folders($folder);
//remove overrides folder
$key = array_search('overrides', $itemsSite);
if ($key) {
    unset($itemsSite[$key]);
}

$stt=1;

?>
<div id="j-main-container" class="span10">
<form
	action="<?php echo JRoute::_('index.php?option=com_bookpro&view=languages'); ?>"
	method="post" name="adminForm" id="adminForm">

			<table class="table-striped table">
				<thead>
					<tr>
						<th width="" class="center"><?php echo JText::_('#'); ?>
						</th>
						
						<th width="" class="center"><?php echo JText::_('Language'); ?>
						</th>


					</tr>
				</thead>
				<tbody>
				<?php  foreach ( $itemsSite as $i => $item ) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center"><?php echo $stt; $stt++?></td>
						<td class="center">
							<a href="index.php?option=com_bookpro&view=languages&layout=list&language=<?php echo $item;?>">
								<?php echo $item; ?>
							</a>	
						</td>
					</tr>
				<?php } ?>
				<tr >
					<td class="center"><?php echo $stt; $stt++?></td>
					<td class="center">
						<a href="index.php?option=com_bookpro&view=language&filename=en-GB.com_bookpro_msg_group.ini&type=SITE">APP Message</a>	
					</td>
				</tr>
				
				</tbody>
			</table>
</form>
</div>