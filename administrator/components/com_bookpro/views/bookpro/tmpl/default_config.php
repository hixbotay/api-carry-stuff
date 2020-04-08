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
$db=JFactory::getDbo();
$query= $db->getQuery(true);
$query->select('count(*)')->from('#__bookpro_tour');
$db->setQuery($query);
$total_tour=$db->loadResult();
//var_dump($total_tour);
//echo "ok";
//die();

$query= $db->getQuery(true);
$query->select('count(*)')->from('#__bookpro_customer');
$db->setQuery($query);
$total_customer=$db->loadResult();


$query= $db->getQuery(true);
$query->select('count(*)')->from('#__bookpro_orders');
$db->setQuery($query);
$total_booking=$db->loadResult();

$query= $db->getQuery(true);
$query->select('count(*)')->from('#__bookpro_passenger');
$db->setQuery($query);
$total_passenger=$db->loadResult();

$query= $db->getQuery(true);
$query->select('sum(total)')->from('#__bookpro_orders');
$db->setQuery($query);
$total_revenue=$db->loadResult();
$rul_default = "index.php?option=com_bookpro&view=";

$manifest=new SimpleXMLElement(JPATH_COMPONENT_ADMINISTRATOR.'/manifest.xml',null,true);

?>
<h3>Welcome to  JBTour</h3>
<h4>Version: <?php echo $manifest->version ?></h4>

