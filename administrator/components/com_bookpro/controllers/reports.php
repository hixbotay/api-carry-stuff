
<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: orders.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

class BookproControllerReports extends JControllerAdmin{
	public function getModel($name = 'Report', $prefix = 'BookproModel', $config =array('ignore_request' => true)){
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

}