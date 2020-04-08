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
AImporter::helper('math');
//filter language file
$file_filter = JPATH_ADMINISTRATOR.'/components/com_bookpro/data/language_filter.xml';
$filter = JFactory::getXML($file_filter);
//joomla language folder
$folder					= JPATH_SITE .DS."language".DS.$this->language;
//admin language file
$adminArray = XmlHelper::getAttribute($filter->admin->file, 'name');
array_walk($adminArray, function(&$item) { $item = $this->language.".".$item; });
//site language file
$siteArray			= XmlHelper::getAttribute($filter->site->file, 'name');
array_walk($siteArray, function(&$item) { $item = $this->language.".".$item; });

$site_list_file = JFolder::files($folder);
$itemsSite = MathHelper::filterArray($site_list_file, $siteArray);

$folderadmin	= JPATH_ADMINISTRATOR .DS."language".DS.$this->language;
$admin_list_file = JFolder::files($folderadmin);
$itemsAdmin = MathHelper::filterArray($admin_list_file, $adminArray);

if(count($itemsSite) < count($siteArray) || count($itemsAdmin) < count($adminArray)){
	JToolBarHelper::custom('language.addlanguage', 'plus', 'icon over', JText::_('COM_BOOKPRO_LANGUAGE_ADD'), false, false);	
}

JToolBarHelper::back('Back','index.php?option=com_bookpro&view=languages');

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
						
						<th width="" class="center"><?php echo JText::_('File name'); ?>
						</th>

						<th width="" class="center"><?php echo JText::_('Type'); ?>
						</th>

					</tr>
				</thead>
				<tbody>
				<?php  foreach ( $itemsSite as $i => $item ) { ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center"><?php echo $i+1;?></td>
							<td class="center">
								<a href="index.php?option=com_bookpro&view=language&filename=<?php echo $item;?>&type=SITE">
									<?php echo $item; ?>
								</a>	
							</td>
							<td class="center">
								<?php echo JText::_('SITE'); ?>
							</td>
						</tr>
				<?php } ?>
				
				<?php  foreach ( $itemsAdmin as $i => $item ) { ?>
				  
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center"><?php echo $i+1;?></td>
							<td class="center">
								<a href="index.php?option=com_bookpro&view=language&filename=<?php echo $item;?>&type=ADMINISTRATOR">
									<?php echo $item; ?>
								</a>	
							</td>
							<td class="center">
								<?php echo JText::_('ADMINISTRATOR'); ?>
							</td>
						</tr>
							
				<?php } ?>
				
				
				
				</tbody>
			</table>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="lang" value="<?php echo $this->language?>"/>
</form>
</div>