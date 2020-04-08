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

<div class="row-fluid">
	<legend><?php echo JText::_('COM_BOOKPRO_REVENUE')?></legend>
	<?php 			
		$chart = ChartHelper::getRevenueChart('lastyear','','',1,'PieChart','title:"'.JText::_('COM_BOOKPRO_REVENUE').'",backgroundColor:"#F7F7F7",vAxis: {title: "Total '.JComponentHelper::getParams('com_bookpro')->get('currency_symbol').'"}');
		$layout = new JLayoutFile('statistic_chart', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
		$html = $layout->render($chart);
		echo $html;
	?>
</div>