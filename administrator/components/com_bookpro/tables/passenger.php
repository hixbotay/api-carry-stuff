<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: passenger.php 26 2012-07-08 16:07:54Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');

class TablePassenger extends JTable
{
  
    var $id;
    var $title;
    var $firstname;
    var $lastname;
    var $gender;
    
    var $age;
    var $passport;
    var $ppvalid;
    var $issueby;
    
    var $country_id;
    var $birthday;
    var $customer_id;
    var $documenttype;
    var $order_id;
    
    var $group_id;
    var $seat;
    var $return_seat;
    var $route_id;
    var $return_route_id;
    var $price;
    var $return_price;
    var $start;
    var $return_start;
    var $bag_qty;
   	var $price_bag;
   	var $return_bag_qty;
   	var $return_price_bag;
   	var $package;
   	var $return_package;
    /**
     * Construct object.
     * 
     * @param JDatabaseMySQL $db database connector
     */
    function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_passenger', 'id', $db);
    }

    /**
     * Init empty object.
     */
    function init()
    {
        $this->id = 0;
        $this->title='';
        $this->firstname = '';
        $this->lastname = '';
        $this->gender = '';
        $this->age = NULL;
        $this->passport = '';
        $this->birthday=null;
        $this->documenttype = 0;
        $this->country_id=0;
        $this->ppvalid=null;
        $this->customer_id=0;
        $this->order_id=null;
        $this->package = 0;
        $this->return_package = 0;
        
        
       
    }
    function check(){
    	$this->birthday = JFactory::getDate($this->birthday)->toSql();
    	return true;
    }
    
    public function delete($pk = null)
    {
    	if (is_null($pk))
    	{
    		$pk = array();
    
    		foreach ($this->_tbl_keys AS $key)
    		{
    			$pk[$key] = $this->$key;
    		}
    	}
    	elseif (!is_array($pk))
    	{
    		$pk = array($this->_tbl_key => $pk);
    	}
    
    	foreach ($this->_tbl_keys AS $key)
    	{
    		$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];
    
    		if ($pk[$key] === null)
    		{
    			throw new UnexpectedValueException('Null primary key not allowed.');
    		}
    		$this->$key = $pk[$key];
    	}
    
    
    	// Delete the row by primary key.
    	$query = $this->_db->getQuery(true)
    	->delete($this->_tbl);
    	$this->appendPrimaryKeys($query, $pk);
    
    	$this->_db->setQuery($query);
    
    	// Check for a database error.
    	$this->_db->execute();
    
    
    
    	return true;
    }
    
}

?>