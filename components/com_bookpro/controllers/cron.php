<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
class BookProControllerCron extends JControllerLegacy{

	
	
	public function cancelOrder(){
		AImporter::helper('android');
		AndroidHelper::write_log('cancel_order.txt','clear session');
		$app = JFactory::getApplication();
//		$time = (int)( JFactory::getDate()->toUnix()- JComponentHelper::getParams('com_bookpro')->get('timeout_order',60) );
		$time = JFactory::getDate('now',$app->getCfg('offset'));
		$time->modify('- '.JComponentHelper::getParams('com_bookpro')->get('timeout_order',60).'seconds');
//		debug($time->format('Y-m-d H:i:s',true));die;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__bookpro_orders')
			->set('is_cancelled = 1')
			->where('is_cancelled = 0 AND is_accepted = 0')
			->where('created_time < '.$db->quote($time->format('Y-m-d H:i:s',true)));
		$db->setQuery($query);
		$db->execute();
		echo $query;
		$app->close();	
	}
	
	//clear login expired
	public function clearSession(){
		AImporter::helper('android');
		AndroidHelper::write_log('clear_session.txt','clear session');
		$app = JFactory::getApplication();
		$day = 3600*24;
		$time = (int)( JFactory::getDate()->toUnix() -  $day );
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__bookpro_session')
			->where('time < '.$time);
		$db->setQuery($query);
		$db->execute();
		echo $query;
		$app->close();	
	}
	
	
}