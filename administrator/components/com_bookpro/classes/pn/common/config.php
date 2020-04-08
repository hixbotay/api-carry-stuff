<?php
/**
 * Database config variables
 */
define('PROJECT_FOLDER', JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn');
define('ERROR_SHOW_ENABLED', true);
define('ERROR_LOG_ENABLED', true);
define('PUSH_MESSAGE_TRACKING_ENABLED', true);
define('RUNNING_MODE', 'production');
//define('RUNNING_MODE', 'development');


//define('API_KEY_ANDROID', 'AIzaSyDleVFrtk_ET4WPIb8xYcTNLdoKHyl7K7Y');
define('API_KEY_ANDROID', 'AAAAs0lX5WI:APA91bEYE3VtmADkSZuiIkEb-EDOPqo9-EWk3-CXuSOPGkZxmQ7Tc24WWqEJZGJKbJnTHWsjYBiC5jspBH0Dq0mndOVRLf8L-s3IhfVF85TYhpUk1oApG_d7nX31diL2rn_nPVgY1hyy');

define('API_KEY_IOS', 'xf1443dfd2f3fdg273282fdqokz');

define('OS_ANDROID', 'android');
define('OS_IOS', "ios");

define('IOS_PN_CERT_PRODUCTION', JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/cert/aps_production.pem');
define('IOS_PN_CERT_DEVELOPMENT', JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/cert/aps_development.pem');
define('IOS_PN_CERT_PASSPHRASE', '123456');

define('LOG_FILE', 'pn_backend.log');
define('LOG_FILE_SIZE', 1048576);

//Apple Production APNS Gateway
define('IOS_PN_URL_PRODUCTION','ssl://gateway.push.apple.com:2195');
define('IOS_PN_URL_DEVELOPMENT','ssl://gateway.sandbox.push.apple.com:2195');
define('IOS_PN_URL_FEEDBACK_PRODUCTION','ssl://feedback.push.apple.com:2196');
define('IOS_PN_URL_FEEDBACK_DEVELOPMENT','ssl://feedback.sandbox.push.apple.com:2196');

define('ANDROID_PN_URL', 'https://android.googleapis.com/gcm/send');
define('ANDROID_PN_FCM_URL', 'https://fcm.googleapis.com/fcm/send');


?>