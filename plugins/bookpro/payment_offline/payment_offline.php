<?php 
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/payment.php');
class plgBookproPayment_offline extends BookproPaymentPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename, 
	 *                         forcing it to be unique 
	 */
    var $_element    = 'payment_offline';

	
	function plgBookproPayment_offline(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

   
    /**
     * Prepares the payment form
     * and returns HTML Form to be displayed to the user
     * generally will have a message saying, 'confirm entries, then click complete order'
     * 
     * @param $data     array       form post data
     * @return string   HTML to display
     */
    function _prePayment( $data )
    {
        $app=JFactory::getApplication();
        $app->redirect(JUri::root().'index.php?option=com_bookpro&controller=payment&task=postpayment&method=payment_offline&order_number='.$data['order_number']);
    }
    
    /**
     * Processes the payment form
     * and returns HTML to be displayed to the user
     * generally with a success/failed message
     *  
     * @param $data     array       form post data
     * @return string   HTML to display
     */
    function _postPayment($data )
    {
    	
    	JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_bookpro/tables' );
    	$order = JTable::getInstance('Orders', 'Table');
    	$order_number=JRequest::getString('order_number');
    	$order->load(array('order_number'=>$order_number));
    	return $order;
       
    }
    
    /**
     * Prepares variables and 
     * Renders the form for collecting payment info
     * 
     * @return unknown_type
     */
    function _renderForm( $data )
    {
    	$user = JFactory::getUser();  	
        $vars = new JObject();
        
        $html = $this->_getLayout('form', $vars);
        
        return $html;
    }
   
   
   
}
