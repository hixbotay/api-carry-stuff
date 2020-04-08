<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customer.php 80 2012-08-10 09:25:35Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');

class TableUpload extends JTable
{
  
  //  var $id;
    var $name;
    var $url;
    

	function __construct(& $db) {
		parent::__construct ( '#__bookpro_customer_doc', 'id', $db );
	}
}

?>