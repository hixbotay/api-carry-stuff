<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/


defined('_JEXEC') or die('Restricted access');

class OrderHelper {
	
	/**
	 * Get total revenue of orders
	 * @param $start: booking date 
	 * @param $end: booking date
	 * @param String $type: application type 
	 */
	static function getTotal($start,$end,$type=null){
		$db = JFactory::getDbo();
		
		$where=array();
		if($start){
			$where[]="created >= '".$start."'";
		}
		if($end) {
			$where[]="created <= '".$end."'";
		}
		if($type) {
			$where[]="LOWER(type) LIKE ".$db->quote('%' . JString::strtolower($type) . '%');
		}
		
		$query = "SELECT sum(total) FROM #__bookpro_orders";
		
		$query.=" WHERE ".implode(" AND ", $where);
		$db->setQuery($query);
		return $db->loadResult();
	
	}
	
	
	 static function approve($value = 0, $state = 1, $i)
    {
        // Array of image, task, title, action
        $state = ( int ) $state;
        //$state  = JArrayHelper::getValue($states, (int) $value, $states[1]);
        $html   = JHtml::_('image', '', JText::_($state), NULL, true);
        //if ($canChange) {
            $html   = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state.'\')" title="'.JText::_($state).'">'
                    . $html.'</a>';
        //}

        return $html;
    }
	
	
}