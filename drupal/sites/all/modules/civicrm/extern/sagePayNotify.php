<?php

// SagePay (Server) Module Notification Callback Script - 10/06/2010, AW //

// Comment out the line below to prevent each transaction being logged to a file
define('SAGEPAY_DEBUG', 1);

session_start();

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

$config =& CRM_Core_Config::singleton();

require_once 'CRM/Utils/Array.php';
$value = CRM_Utils_Array::value('module', $_GET);

require_once 'CRM/Core/Payment/SagePayServerNotify.php';
$SPN = new CRM_Core_Payment_SagePayServerNotify();

// Attempt to determine component type ...
switch ($value) {
	case 'contribute':
	case 'event':
		$SPN->main($value);
		break;
	default:
		require_once 'CRM/Core/Error.php';
		CRM_Core_Error::debug_log_message( "Could not get module name from request url" );
		echo "Could not get module name from request url<p>";
		break;
}

?>