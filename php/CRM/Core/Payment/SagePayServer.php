<?php

// SagePay Server Module for CiviCRM - Core Class File //
// Updated for version 3.4 - aw@circle                 //

require_once 'CRM/Core/Payment.php';
require_once 'api/v2/Contact.php'; 

class CRM_Core_Payment_SagePayServer extends CRM_Core_Payment {
    
	const CHARSET    = 'iso-8859-1';
	//const debug = true;
	const debug = false;
    
    protected $_mode = null;
    static private $_singleton = null; 

    static function &singleton($mode, &$paymentProcessor) {
        $processorName = $paymentProcessor['name'];
        if (self::$_singleton[$processorName] === null ) {
            self::$_singleton[$processorName] = new CRM_Core_Payment_SagePayServer($mode, $paymentProcessor);
        }
        return self::$_singleton[$processorName];
    }
    
    function __construct($mode, &$paymentProcessor) {
        
		$this->_mode             = $mode;
        $this->_paymentProcessor = $paymentProcessor;
        $this->_processorName    = ts('SagePay');

        if ( $this->_paymentProcessor['payment_processor_type'] == 'SagePayServer') {
            $this->_processorName = ts('Sage Pay');
            return;
        }
        
        if ( ! $this->_paymentProcessor['user_name']) {
            CRM_Core_Error::fatal(ts('Could not find user name for payment processor'));
        }
    
	}
	
	function checkConfig() {
		$error = array();	
        if (!empty($error)) 
            return implode('<p>', $error);
        return null;
	}
	
	function doDirectPayment(&$params) {
		return null;	
	}
	
	// Main function to initialize transaction before sending the sending the user to Sagepay
	function doTransferCheckout(&$params, $component = 'contribute') {
        
		$config =& CRM_Core_Config::singleton();
        if ($component != 'contribute' && $component != 'event')
            CRM_Core_Error::fatal(ts('Component is invalid'));
		
		$notifyURL = 
            $config->userFrameworkResourceURL . 
            "extern/sagePayNotify.php?reset=1&contactID={$params['contactID']}" .
            "&contributionID={$params['contributionID']}" .
            "&module={$component}";
		if ($component == 'event') {
            $notifyURL .= "&eventID={$params['eventID']}&participantID={$params['participantID']}";
        } else {
            $membershipID = CRM_Utils_Array::value('membershipID', $params);
            if ($membershipID) {
                $notifyURL .= "&membershipID=$membershipID";
            }
            $relatedContactID = CRM_Utils_Array::value( 'related_contact', $params);
            if ($relatedContactID) {
                $notifyURL .= "&relatedContactID=$relatedContactID";
                $onBehalfDupeAlert = CRM_Utils_Array::value('onbehalf_dupe_alert', $params);
                if ($onBehalfDupeAlert) {
                    $notifyURL .= "&onBehalfDupeAlert=$onBehalfDupeAlert";
                }
            }
        }
		$notifyURL .= "&vendor={$this->_paymentProcessor['user_name']}&qfkey={$params['qfKey']}"; 
		
		
		// Query contact details ...
		if ($relatedContactID) {
			// If contributing on behalf of	an organization, use
			// relatedContactID's details
			$get_params = array('contact_id' => $relatedContactID);
			$contact    = civicrm_contact_get($get_params);
			$contact    = $contact[$relatedContactID];
		} else {
			$get_params = array('contact_id' => $params['contactID']);
			$contact    = civicrm_contact_get($get_params);
			$contact    = $contact[$params['contactID']];
		}

		// Query ISO Country code for this country_id ...
		if ($contact['country_id'])
			$country_iso_code = CRM_Core_PseudoConstant::countryIsoCode($contact['country_id']);
		
		// Construct params list to send to SagePay ...
		$sageParams = array(
							
			'Vendor'             => $this->_paymentProcessor['user_name'],
			'VPSProtocol'        => '2.23',
			'TxType'             => 'PAYMENT',
			'VendorTxCode'       => $params['invoiceID'],
			'Amount'             => sprintf("%.2f", $params['amount']),
			'Currency'           => $params['currencyID'],
			'Description'        => substr($params['item_name'], 0, 100),
			'NotificationURL'    => $notifyURL,
			'FailureURL'         => $notifyURL,
			'BillingFirstnames'  => $contact['first_name'],
			'BillingSurname'     => $contact['last_name'],
			'BillingAddress1'    => $contact['street_address'],
			'BillingCity'        => $contact['city'],
			'BillingPostCode'    => $contact['postal_code'],
			'BillingCountry'     => $country_iso_code,
			'DeliveryFirstnames' => $contact['first_name'],
			'DeliverySurname'    => $contact['last_name'],
			'DeliveryAddress1'   => $contact['street_address'],
			'DeliveryCity'       => $contact['city'],
			'DeliveryPostcode'   => $contact['postal_code'],
			'DeliveryCountry'    => $country_iso_code,
			'CustomerEMail'      => $contact['email'],
			'Basket'             => '',
			'AllowGiftAid'       => 0,
			'Apply3DSecure'      => 0,
			'ApplyAVSCV2'        => '',
			'Profile'            => 'NORMAL'
			
		);
		
		// Construct post string
		$post = '';
		foreach ($sageParams as $key => $value)
			$post .= ($key != 'Vendor' ? '&' : '') . $key . '=' . urlencode($value);
	
		// Send payment registration POST
		$url      = $this->_paymentProcessor['url_site'];
		$response = $this->requestPost($url, $post);
		
		
		//	watchdog('CiviCRM Sagepay', 'Request = <pre>'  . print_r($sageParams, true) . '</pre>');
		//	watchdog('CiviCRM Sagepay', 'Response = <pre>' . print_r($response, true) . '</pre>');
		
		
		// If OK ...
		if ($response['Status'] == 'OK') {
			// Make a note of security key (will be compared during notification callback)
			CRM_Core_DAO::executeQuery("INSERT IGNORE INTO civicrm_sagepay (contribution_id, security_key) 
										VALUES ('" . $params['contributionID'] . "', '" .
										$response['SecurityKey'] . "')");
			// Redirect user to SagePay
			CRM_Utils_System::redirect($response["NextURL"]);
		} else {
			
			// If we got to here, this has apparently not gone according to plan, so ...
			
			// Construct an error message ...
			
			$errmsg = '';
			
			if (empty($sageParams['Amount']))
				$errmsg .= "Amount field was empty.<br />";
			if (empty($sageParams['BillingFirstnames']) or empty($sageParams['BillingSurname']))
				$errmsg .= "Missing name field(s).$advice<br />";
			if (empty($sageParams['BillingAddress1']) or empty($sageParams['BillingCity']))
				$errmsg .= "Missing address field(s).$advice<br />";
			if (empty($sageParams['BillingPostCode']))
				$errmsg .= "Missing postcode field.$advice<br />";
			if (empty($sageParams['BillingCountry']))
				$errmsg .= "Missing country field.$advice<br />";
			if (!$errmsg)
				$errmsg .= "{$response['StatusDetail']}";
			if ($errmsg)
				$errmsg = "The following errors occurred when submitting payment to Sage Pay:<br />" .
						  $errmsg . "<br />Please contact the site administrator.";
						  
			// Improved error handling - redirect user back to where they started and display error(s)
			CRM_Core_Error::fatal($errmsg);
			
				
		}
		
	}
	
	// Protected functions ---------------------------------------------------------------------------------- //
	
	// Helper function - does string begin with substring?
	protected function beginsWith($str, $sub) {
		return (substr($str, 0, strlen($sub)) == $sub);
	}
	
	protected function trimFirst($str) {
		return substr($str, 1);
	}
	
	protected function getErrorURL() {
		
		$error_page_url = $this->_paymentProcessor['password']; // (We're using the processor password field to store an error page url)
		
		if ($this->beginsWith($error_page_url, '/'))
			$error_page_url = $this->trimFirst($error_page_url);
			
		switch (true) {
			case empty($error_page_url):
				return;
			case $this->beginsWith($error_page_url, 'http://') or
				 $this->beginsWith($error_page_url, 'https://'):
				return $error_page_url;
			default:
				return 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . 
						$_SERVER['HTTP_HOST'] . '/' . $error_page_url;
				 
		}	
	}
	
	/*************************************************************
	Send a post request with cURL
		$url = URL to send request to
		$data = POST data to send (in URL encoded Key=value pairs)
	*************************************************************/
	
	protected function requestPost($url, $data){
		
		// Set a one-minute timeout for this script
		set_time_limit(60);
	
		// Initialise output variable
		$output = array();
	
		// Open the cURL session
		$curlSession = curl_init();
	
		// Set the URL
		curl_setopt ($curlSession, CURLOPT_URL, $url);
		// No headers, please
		curl_setopt ($curlSession, CURLOPT_HEADER, 0);
		// It's a POST request
		curl_setopt ($curlSession, CURLOPT_POST, 1);
		// Set the fields for the POST
		curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $data);
		// Return it direct, don't print it out
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1); 
		// This connection will timeout in 30 seconds
		curl_setopt($curlSession, CURLOPT_TIMEOUT,30); 
		//The next two lines must be present for the kit to work with newer version of cURL
		//You should remove them if you have any problems in earlier versions of cURL
		curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
	
		//Send the request and store the result in an array
		
		$rawresponse = curl_exec($curlSession);
		//Store the raw response for later as it's useful to see for integration and understanding 
		$_SESSION["rawresponse"]=$rawresponse;
		//Split response into name=value pairs
		$response = split(chr(10), $rawresponse);
		// Check that a connection was made
		if (curl_error($curlSession)){
			// If it wasn't...
			$output['Status'] = "FAIL";
			$output['StatusDetail'] = curl_error($curlSession);
		}
	
		// Close the cURL session
		curl_close ($curlSession);
	
		// Tokenise the response
		for ($i=0; $i<count($response); $i++){
			// Find position of first "=" character
			$splitAt = strpos($response[$i], "=");
			// Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
			$output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
		} // END for ($i=0; $i<count($response); $i++)
	
		// Return the output
		return $output;
	}

};

?>