<?php
/**
 * jBackend component for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT.'/helpers/jbackend.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';

class jBackendViewRequest extends JViewLegacy
{
  protected $item;

  protected $params;

  protected $state;

  public function display($tpl = null)
  {
	  
    // Initialise variables
    $app = JFactory::getApplication('site');

    $this->state = $this->get('State');
    $this->params = $this->state->get('parameters.menu');

    if (is_null($this->params))
    {
      $params = JComponentHelper::getParams('com_jbackend');
      $access_type = $params->get('default_access_type', 'key'); // free/user/key
      $enable_trace = $params->get('default_enable_trace', '0');
      $enable_cors = '0'; // disabled
      $force_ssl = $params->get('default_force_ssl', '0');
      $enabled_modules = '1'; // all
      $selected_modules = array();
    } else {
     // $access_type = $this->params->get('access_type', 'key'); // free/user/key
      $enable_trace = $this->params->get('enable_trace', '0');
      $enable_cors = $this->params->get('enable_cors', '0');
      $force_ssl = $this->params->get('force_ssl', '0');
      $enabled_modules = $this->params->get('enabled_modules', '1');
      $selected_modules = $this->params->get('selected_modules', array());
    }

    // Check if force to SSL
    if ($force_ssl != '0')
    {
      $uri = JUri::getInstance();
      if (strtolower($uri->getScheme()) != 'https')
      {
        // Forward to https
        $uri->setScheme('https');
        $app->redirect((string) $uri);
      }
    }

    $session = JFactory::getSession();
    //$session->set('access_type', $access_type);
    $session->set('enabled_modules', $enabled_modules);
    $session->set('selected_modules', $selected_modules);

    if ($enable_trace)
    {
      // Collect pre-execution log information
      $log = array();
      $log['duration'] = -microtime(true);
      //$log['endpoint'] = $app->getMenu()->getActive()->id;
     
    }

    $this->item = $this->get('Item');

    // Check for errors
    if (count($errors = $this->get('Errors'))) {
		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Change the suggested filename
		header('Content-Disposition: attachment;filename="response.json"');

		// Enable CORS
		if ($enable_cors != '0')
		{
		  header('Access-Control-Allow-Origin: *');
		}

		echo '{
				"status": "ko",
				"code": "100",
				"message": {
					"en": "'.$errors->getMessage().'",
					"fr": "'.$errors->getMessage().'"
				}
			}';
		AndroidHelper::write_log('plg_exception.txt',$errors->getMessage());
		JFactory::getApplication()->close();
    }

    if ($enable_trace)
    {
      // Collect post-execution log information
      $log['error'] = (int)($this->item['status'] == 'ko');
      $log['error_code'] = ($log['error']) ? $this->item['code'] : '';
      //$log['user_id'] = JFactory::getUser()->id;
      $log['duration'] += microtime(true);

      // Save log information
     // jBackendHelper::logRequest($log);
	 $resource = $app->input->get('resource');
	 if($resource!='updatelocation' && $resource != 'getclosest'){
		$request = AndroidHelper::getBodyRequest();
		unset($request->password);
		
	  AndroidHelper::write_log('request.txt',$_SERVER['REQUEST_URI'].PHP_EOL.'session_id: '.AndroidHelper::getSessionId().PHP_EOL.json_encode($log).PHP_EOL.'data request: '.json_encode($request).PHP_EOL.'--------');	  
	  
	  if($resource=='login'||$resource=='logout'){
	  AndroidHelper::write_log('login.txt',$_SERVER['REQUEST_URI'].PHP_EOL.'session_id: '.AndroidHelper::getSessionId().PHP_EOL.json_encode($log).PHP_EOL.'data request: '.json_encode($request).PHP_EOL.'--------');	 
		  }
		  
     }
	}

    // Use the correct json mime-type
    header('Content-Type: application/json');

    // Change the suggested filename
    header('Content-Disposition: attachment;filename="response.json"');

    // Enable CORS
    if ($enable_cors != '0')
    {
      header('Access-Control-Allow-Origin: *');
    }

    // Output the JSON data
    $response = $this->item;
    echo json_encode($response);

    JFactory::getApplication()->close();
  }

}
