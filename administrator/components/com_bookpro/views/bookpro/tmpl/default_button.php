
<?php 
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

$buttons = array(
		
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=bussearch'),
				'image' => JUri::root().'components/com_bookpro/assets/images/createticket.jpg',
				'text' => JText::_('COM_BOOKPRO_TICKET_CREATE'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_content', 'core.create', 'option=com_bookpro', )
		),
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=orders'),
				'image' => JUri::root().'components/com_bookpro/assets/images/booking.jpg',
				'text' => JText::_('COM_BOOKPRO_ORDERS'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_bookpro')
		),
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=airports'),
				'image' => JUri::root().'components/com_bookpro/assets/images/icon-48-busstations.png',
				'text' => JText::_('COM_BOOKPRO_AIRPORTS'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_bookpro')
		),
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=bustrips'),
				'image' => JUri::root().'components/com_bookpro/assets/images/icon-48-bustrips.png',
				'text' => JText::_('COM_BOOKPRO_BUSTRIPS'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_bookpro')
		),
		
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=passengers'),
				'image' => 'header/icon-48-user.png',
				'text' => JText::_('COM_BOOKPRO_PASSENGERS'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_bookpro')
		),
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=cgroups'),
				'image' => JUri::root().'components/com_bookpro/assets/images/icon-48-cgroups.png',
				'text' => JText::_('COM_BOOKPRO_CGROUPS'),
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'access' => array('core.manage', 'com_bookpro')
		),
		array(
				'link' => JRoute::_('index.php?option=com_bookpro&view=currencies'),
				'image' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'icon' => JUri::root().'components/com_bookpro/assets/images/icon-48-currency.png',
				'text' => JText::_('COM_BOOKPRO_CURRENCIES'),
				'access' => array('core.manage', 'com_modules')
		),
	
		);

	$html = JHtml::_('icons.buttons', $buttons);
	?>
	<?php if (!empty($html)): ?>
	<div class="cpanel"><?php echo $html;?></div>
	<?php endif;?>


