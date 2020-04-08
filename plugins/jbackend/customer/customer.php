<?php
/**
 * jBackend user plugin for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJBackendCustomer extends JPlugin
{
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
    JFactory::getLanguage()->load('com_bookpro');
  }

  public static function generateError($errorCode)
  {
    $error = array();
    $error['status'] = 'ko';
    switch($errorCode) {
      case 'REQ_ANS':
        $error['error_code'] = 'REQ_ANS';
        $error['error_description'] = 'Action not specified';
        break;
      case 'USR_LIF':
        $error['error_code'] = 'USR_LIF';
        $error['error_description'] = 'Login failed';
        break;
      case 'USR_LOF':
        $error['error_code'] = 'USR_LOF';
        $error['error_description'] = 'Logout failed';
        break;
      case 'USR_UNR':
        $error['error_code'] = 'USR_UNR';
        $error['error_description'] = 'Username required';
        break;
      case 'USR_EMR':
        $error['error_code'] = 'USR_EMR';
        $error['error_description'] = 'Email required';
        break;
      case 'USR_NMR':
        $error['error_code'] = 'USR_NMR';
        $error['error_description'] = 'Name required';
        break;
      case 'USR_ALI':
        $error['error_code'] = 'USR_ALI';
        $error['error_description'] = 'Already logged in';
        break;
      case 'USR_UNE':
        $error['error_code'] = 'USR_UNE';
        $error['error_description'] = 'User not enabled';
        break;
      case 'USR_RNA':
        $error['error_code'] = 'USR_RNA';
        $error['error_description'] = 'Registration not allowed';
        break;
      case 'USR_EDR':
        $error['error_code'] = 'USR_EDR';
        $error['error_description'] = 'Error during registration';
        break;
      case 'USR_UAX':
        $error['error_code'] = 'USR_UAX';
        $error['error_description'] = 'Username already exists';
        break;
      case 'USR_EAX':
        $error['error_code'] = 'USR_EAX';
        $error['error_description'] = 'Email already exists';
        break;
      case 'USR_RRE':
        $error['error_code'] = 'USR_RRE';
        $error['error_description'] = 'Reset request error';
        break;
      case 'USR_RRF':
        $error['error_code'] = 'USR_RRF';
        $error['error_description'] = 'Reset request failed';
        break;
    }
    return $error;
  }

  private static function sendNotifications($data, $useractivation, $sendpassword, $mailtoadmin)
  {
    $sendresult = true;

    $config = JFactory::getConfig();
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    // Compile the notification mail values
    $data['fromname'] = $config->get('fromname');
    $data['mailfrom'] = $config->get('mailfrom');
    $data['sitename'] = $config->get('sitename');
    $data['siteurl'] = JUri::root();

    if ($useractivation > 0)
    {
      // Set the link to confirm the user email
      $uri = JUri::getInstance();
      $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
      $data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
    }

    $emailSubject = JText::sprintf(
      'COM_USERS_EMAIL_ACCOUNT_DETAILS',
      $data['name'],
      $data['sitename']
    );

    if ($sendpassword)
    {
      switch ($useractivation)
      {
        case 2: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY'; break;
        case 1: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY'; break;
        default: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_BODY';
      }
      $emailBody = JText::sprintf(
        $emailBodyLABEL,
        $data['name'],
        $data['sitename'],
        $data['activate'],
        $data['siteurl'],
        $data['username'],
        $data['password_clear']
      );
    } else {
      switch ($useractivation)
      {
        case 2: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW'; break;
        case 1: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW'; break;
        default: $emailBodyLABEL = 'COM_USERS_EMAIL_REGISTERED_BODY_NOPW';
      }
      $emailBody = JText::sprintf(
        $emailBodyLABEL,
        $data['name'],
        $data['sitename'],
        $data['activate'],
        $data['siteurl'],
        $data['username']
      );
    }

    // Send the registration email
    try {
      $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
      if ($return !== true) $sendresult = false;
    } catch (Exception $e) {
      $sendresult = false;
    }

    // Send Notification mail to administrators
    if (($useractivation < 2) && ($mailtoadmin == 1))
    {
      $emailSubject = JText::sprintf(
        'COM_USERS_EMAIL_ACCOUNT_DETAILS',
        $data['name'],
        $data['sitename']
      );

      $emailBodyAdmin = JText::sprintf(
        'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
        $data['name'],
        $data['username'],
        $data['siteurl']
      );

      // Get all admin users
      $query->clear()
        ->select($db->quoteName(array('name', 'email', 'sendEmail')))
        ->from($db->quoteName('#__users'))
        ->where($db->quoteName('sendEmail') . ' = ' . 1);

      $db->setQuery($query);

      $status = true;
      try
      {
        $rows = $db->loadObjectList();
        if ($rows === null) $status = false;
      }
      catch (RuntimeException $e)
      {
        $status = false;
      }

      if ($status)
      {
        // Send mail to all superadministrators id
        foreach ($rows as $row)
        {
          $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
          if ($return !== true) $status = false;
        }
      }

      $sendresult = $sendresult && $status;
    }

    // Check for an error
    if (!$sendresult)
    {
      // Send a system message to administrators receiving system mails
      $db = JFactory::getDbo();
      $query->clear()
        ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
        ->from($db->quoteName('#__users'))
        ->where($db->quoteName('block') . ' = ' . (int) 0)
        ->where($db->quoteName('sendEmail') . ' = ' . (int) 1);
      $db->setQuery($query);

      $status = true;
      $sendEmail = 0;
      try
      {
        $sendEmail = $db->loadObjectList();
        if ($sendEmail === null) $status = false;
      }
      catch (RuntimeException $e)
      {
        $status = false;
      }

      if ($status)
      {
        if (count($sendEmail) > 0)
        {
          $jdate = new JDate;

          JFactory::getLanguage()->load('com_users', JPATH_SITE);

          // Build the query to add the messages
          foreach ($sendEmail as $user)
          {
            $values = array($db->quote($user->id), $db->quote($user->id), $db->quote($jdate->toSql()), $db->quote(JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')), $db->quote(JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', JText::_('PLG_JBACKEND_USER_MAIL_SEND_FAILURE_BODY_MSG'), $data['username'])));
            $query->clear()
              ->insert($db->quoteName('#__messages'))
              ->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
              ->values(implode(',', $values));
            $db->setQuery($query);

            try
            {
              $db->execute();
            }
            catch (RuntimeException $e)
            {
              break;
            }
          }
        }
      }

    }

    return $sendresult;
  }

  /**
   * Action login
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function actionLogin(&$response, &$status = null)
  {
    $app = JFactory::getApplication();
    if (JFactory::getUser()->get('guest') != 1)
    {
      $response = plgJBackendCustomer::generateError('USR_ALI'); // Already logged in
      return false;
    }

    $credentials = array();
    $credentials['username'] = $app->input->getString('username');
    $credentials['password'] = $app->input->getString('password');

    $options = array();
    $options['silent'] = true;

    $result = false;
    if ($app->login($credentials, $options) === true)
    {
      $user = JFactory::getUser();
      $userid= $user->get('id');
      if ($userid == 0)
      {
        $response = plgJBackendCustomer::generateError('USR_UNE'); // User not enabled
        return false;
      } else {
        // Success
        $response['status'] = 'ok';
        $response['userid'] = $userid;
        $response['username'] = $user->get('username');
        $result = true;
      }
    } else {
      // Login failed
      $response = plgJBackendCustomer::generateError('USR_LIF'); // Login failed
    }
    return $result;
  }

  /**
   * Action logout
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function actionLogout(&$response, &$status = null)
  {
    $app = JFactory::getApplication();
    $user = JFactory::getUser();
    if ($user->get('guest'))
    {
      $response = plgJBackendCustomer::generateError('USR_LOF'); // Logout failed
      return false;
    } else {
      $userid = JFactory::getUser()->get('id');
      $app->logout($userid);
      $response['status'] = 'ok';
      return true;
    }
  }

  /**
   * Action register
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function actionRegister(&$response, &$status = null)
  {
  	
//  	return false;
    $app = JFactory::getApplication();
    $username = $app->input->getString('username');
    if (is_null($username)) {
      $response = plgJBackendCustomer::generateError('USR_UNR'); // Username required
      return false;
    }
    $password = $app->input->getString('password');
    if (is_null($password)) {
      $response = plgJBackendCustomer::generateError('USR_PWR'); // Password required
      return false;
    }
    $email = $app->input->getString('email');
    if (is_null($email)) {
      $response = plgJBackendCustomer::generateError('USR_EMR'); // Email required
      return false;
    }
    $firstname = $app->input->getString('firstname', '');
    $lastname = $app->input->getString('lastname', '');
    $name = trim($firstname . ' ' . $lastname);
    if ($name == '') {
      $response = plgJBackendCustomer::generateError('USR_NMR'); // Name required
      return false;
    }

    $usersParams = JComponentHelper::getParams('com_users');
    if ($usersParams->get('allowUserRegistration') == '0')
    {
      $data = array();
      $data['username'] = $username;
      $data['password'] = $password;
      $data['password2'] = $password;
      $data['password_clear'] = $password;
      $data['email'] = JStringPunycode::emailToPunycode($email);
      $data['name'] = $name;

      // Get the groups the user should be added to after registration.
      $data['groups'] = array();

      // Get the default new user group, Registered if not specified.
      $usertype = $usersParams->get('new_usertype', 2);

      $data['groups'][] = $usertype;

      // Get global user params
      $useractivation = $usersParams->get('useractivation');
      $sendpassword = $usersParams->get('sendpassword');
      $mailtoadmin = $usersParams->get('mail_to_admin');

      // Check user activation
      if (($useractivation == 1) || ($useractivation == 2))
      {
        // Need activation
        $data['block'] = 1;
        $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
      } else {
        // No need activation
         $data['block'] = 0;
      }

      $user = new JUser;
      if (!$user->bind($data)) {
        $response = plgJBackendCustomer::generateError('USR_EDR'); // Error during registration
        return false;
      }

      if (!$user->save()) {
        $error = $user->getError();
        if ($error == JText::_('JLIB_DATABASE_ERROR_USERNAME_INUSE'))
        {
          $response = plgJBackendCustomer::generateError('USR_UAX'); // Username already exists
        }
        elseif ($error == JText::_('JLIB_DATABASE_ERROR_EMAIL_INUSE'))
        {
          $response = plgJBackendCustomer::generateError('USR_EAX'); // Email already exists
        }
        else
        {
          $response = plgJBackendCustomer::generateError('USR_EDR'); // Error during registration
        }
        return false;
      }

      // Handle account activation/confirmation emails
      plgJBackendCustomer::sendNotifications($data, $useractivation, $sendpassword, $mailtoadmin);

      $response['status'] = 'ok';
      return true;
    } else {
      $response = plgJBackendCustomer::generateError('USR_RNA'); // Registration not allowed
      return false;
    }
  }

  /**
   * Action remind
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function actionRemind(&$response, &$status = null)
  {
    $app = JFactory::getApplication();

    $email = $app->input->getString('email');
    if (is_null($email)) {
      $response = plgJBackendCustomer::generateError('USR_EMR'); // Email required
      return false;
    }

    jimport('joomla.application.component.model');
    JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_users/models');
    JForm::addFormPath(JPATH_SITE.'/components/com_users/models/forms');
    require_once JPATH_SITE . '/components/com_users/helpers/route.php';
    $model = JModelLegacy::getInstance('Remind', 'UsersModel');

    $data  = array('email' => $email);

    // Submit the username remind request
    $return = $model->processRemindRequest($data);

    // Check for a hard error
    if ($return instanceof Exception)
    {
      // An error (e.g. failed sending email)
      $response = plgJBackendCustomer::generateError('USR_RRE'); // Reset request error
      return false;
    }
    elseif ($return === false)
    {
      // The request failed
      $response = plgJBackendCustomer::generateError('USR_RRF'); // Reset request failed
      return false;
    }
    // The request succeeded
    $response['status'] = 'ok';
    return true;
  }

  /**
   * Action reset
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function actionReset(&$response, &$status = null)
  {
    $app = JFactory::getApplication();

    $email = $app->input->getString('email');
    if (is_null($email)) {
      $response = plgJBackendCustomer::generateError('USR_EMR'); // Email required
      return false;
    }

    jimport('joomla.application.component.model');
    JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_users/models');
    JForm::addFormPath(JPATH_SITE.'/components/com_users/models/forms');
    require_once JPATH_SITE . '/components/com_users/helpers/route.php';
    $model = JModelLegacy::getInstance('Reset', 'UsersModel');

    $data  = array('email' => $email);

    // Submit the password reset request
    $return = $model->processResetRequest($data);

    // Check for a hard error
    if ($return instanceof Exception)
    {
      // An error (e.g. failed sending email)
      $response = plgJBackendCustomer::generateError('USR_RRE'); // Reset request error
      return false;
    }
    elseif ($return === false)
    {
      // The request failed
      $response = plgJBackendCustomer::generateError('USR_RRF'); // Reset request failed
      return false;
    }
    // The request succeeded
    $response['status'] = 'ok';
    return true;
  }

  /**
   * Fulfills requests for user module
   *
   * @param   object    $module      The module invoked
   * @param   object    $response    The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function onRequestCustomer($module, &$response, &$status = null)
  {
    if ($module !== 'customer') return true;

    // Add to module call stack
    jBackendHelper::moduleStack($status, 'customer');

    $app = JFactory::getApplication();
    $action = $app->input->getString('action');

    if (is_null($action)) {
      $response = plgJBackendCustomer::generateError('REQ_ANS'); // Action not specified
      return false;
    }

    $resource = $app->input->getString('resource');

    switch ($resource)
    {
      case 'login':
        if ($action == 'post')
        {
          return $this->actionLogin($response, $status);
        }
        break;
      case 'logout':
        if ($action == 'get')
        {
          return $this->actionLogout($response, $status);
        }
        break;
      case 'register':
        if ($action == 'post')
        {
          return $this->actionRegister($response, $status);
        }
        break;
      case 'remind':
        if ($action == 'get')
        {
          return $this->actionRemind($response, $status);
        }
        break;
      case 'reset':
        if ($action == 'get')
        {
          return $this->actionReset($response, $status);
        }
        break;
    }

    return true;
  }

  /**
   * Check if request is authentication request and if user is guest
   *
   * @param   object    $module           The module invoked
   * @param   object    $session_data     The session information
   *
   * @return  boolean   true if session is set or is an authentication request
   */
  public function onCheckSession($module, &$session_data)
  {
    $session_data = array('is_guest' => true, 'is_auth_request' => false);
    $app = JFactory::getApplication();
    $action = $app->input->getString('action');
    $resource = $app->input->getString('resource');

    // Check if is an authentication request
    if (($action === 'post') && ($module === 'customer') && ($resource === 'login')) {
      $session_data['is_auth_request'] = true;
    }

    // Check if user is guest
    $session_data['is_guest'] = (JFactory::getUser()->get('guest') == 1);

    return ( !($session_data['is_guest']) || ($session_data['is_auth_request']) );
  }

}
