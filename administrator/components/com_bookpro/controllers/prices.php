<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('_JEXEC') or die;

class BookproControllerPrices extends JControllerAdmin
{
	public function getModel($name = 'Prices', $prefix = 'BookProModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	function savePrice(){
		$app = JFactory::getApplication();
		$input 		= $app->input;
		$type = $input->get('type');
		AImporter::table('price');
		$db = JFactory::getDbo();
		$data = $input->get('data',array(),'array');
		try {
			$db->transactionStart ();
			//save price for specificdate
			if($type == 'date'){
				$query = $db->getQuery ( true );
				$query->delete ( '#__bookpro_price' )
					->where ('code LIKE "DATE"');
				$db->setQuery ( $query );
				$db->execute ();
				
				foreach ($data as $d){
					$table = new TablePrice($db);
					$save = $table->save($d);	
							
				}
				
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices&layout=date', false));
				
			}
			else if( $type == 'week'){
			
				$query = $db->getQuery ( true );
				$query->delete ( '#__bookpro_price' )
					->where ('code LIKE "WEEK"');
				$db->setQuery ( $query );
				$db->execute ();
				
				foreach ($data as $w){
					$table = new TablePrice($db);
					$save = $table->save($w);
				}
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices&layout=week', false));
			}
			else if( $type == 'order'){
			
				$table = new TablePrice($db);
				$table->load(array('code'=>'ORDER'));
				$data_save = array('code'=>'ORDER','params'=>json_encode((object)$data['order']));
				$save = $table->save($data_save);
				
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices&layout=order', false));		
			}
			else {
				//base 
				$table = new TablePrice($db);
				$table->load(array('code'=>'BASE'));
				$save = $table->save($data['base']);
				//validate end	
				$table = new TablePrice($db);
				$table->load(array('code'=>'VALIDATE_END'));
				$save = $table->save($data['validateend']);			
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices', false));				
			}
			$app->enqueueMessage ( JText::_('JLIB_APPLICATION_SAVE_SUCCESS') );
			$db->transactionCommit ();
				
		}
		catch ( Exception $e ) {
			$db->transactionRollback ();
			$app->enqueueMessage ( $e->getMessage () );
			$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices', false));
		}
		return;
		
	}
	
	function resetPrice(){
		$data[type]  = 'BASE';
		AImporter::table('price');
		$db = JFactory::getDbo();
		$table = new TablePrice($db);
		$table->load(array('type'=>$data[type]));
		$save = $table->save($data);
		if($save)
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_SAVE_SUCCESS'), 'message');
		else 
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=prices', false));
	}
	
}