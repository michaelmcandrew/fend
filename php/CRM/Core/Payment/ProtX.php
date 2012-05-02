<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_ProtX extends CRM_Core_Payment {
    const
        CHARSET  = 'iso-8859-1';

    protected $_mode = null;

    static protected $_params = array();
	
	static private $_singleton = null;

    /**
     * Constructor
     *
     * @param string $mode the mode of operation: live or test
     *
     * @return void
     */
    function __construct( $mode, &$paymentProcessor ) {
        $this->_mode = $mode;

        $this->_paymentProcessor = $paymentProcessor;

        if ( $this->_paymentProcessor['payment_processor_type'] == 'PayPal_Standard' ) {
            return;
        }

        if ( ! $this->_paymentProcessor['user_name'] ) {
            CRM_Core_Error::fatal( ts( 'Could not find user name for payment processor' ) );
        }
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode, &$paymentProcessor ) {
        $processorName = $paymentProcessor['name'];
        if (self::$_singleton[$processorName] === null ) {
            self::$_singleton[$processorName] = new CRM_Core_Payment_ProtX( $mode, $paymentProcessor );
        }
        return self::$_singleton[$processorName];
    }

    /**
     * express checkout code. Check PayPal documentation for more information
     * @param  array $params assoc array of input parameters for this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @public
     */
    function setExpressCheckOut( &$params ) {
        $args = array( );

        $this->initialize( $args, 'SetExpressCheckout' );

        $args['paymentAction']  = $params['payment_action'];
        $args['amt']            = $params['amount'];
        $args['currencyCode']   = $params['currencyID'];
        $args['invnum']         = $params['invoiceID'];
        $args['returnURL'   ]   = $params['returnURL'];
        $args['cancelURL'   ]   = $params['cancelURL'];

        $result = $this->invokeAPI( $args );

        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success */
        return $result['token'];
    }

    /**
     * get details from paypal. Check PayPal documentation for more information
     *
     * @param  string $token the key associated with this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @public
     */
    function getExpressCheckoutDetails( $token ) {
        $args = array( );

        $this->initialize( $args, 'GetExpressCheckoutDetails' );
        $args['token'] = $token;

        $result = $this->invokeAPI( $args );

        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success */
        $params                           = array( );
        $params['token']                  = $result['token'];
        $params['payer_id'    ]           = $result['payerid'];
        $params['payer_status']           = $result['payerstatus'];
        $params['first_name' ]            = $result['firstname'];
        $params['middle_name']            = $result['middlename'];
        $params['last_name'  ]            = $result['lastname'];
        $params['street_address']         = $result['shiptostreet'];
        $params['supplemental_address_1'] = $result['shiptostreet2'];
        $params['city']                   = $result['shiptocity'];
        $params['state_province']         = $result['shiptostate'];
        $params['postal_code']            = $result['shiptozip'];
        $params['country']                = $result['shiptocountrycode'];

        return $params;
    }

    /**
     * do the express checkout at paypal. Check PayPal documentation for more information
     *
     * @param  string $token the key associated with this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @public
     */
    function doExpressCheckout( &$params ) {
        $args = array( );

        $this->initialize( $args, 'DoExpressCheckoutPayment' );

        $args['token']          = $params['token'];
        $args['paymentAction']  = $params['payment_action'];
        $args['amt']            = $params['amount'];
        $args['currencyCode']   = $params['currencyID'];
        $args['payerID']        = $params['payer_id'];
        $args['invnum']         = $params['invoiceID'];
        $args['returnURL'   ]   = $params['returnURL'];
        $args['cancelURL'   ]   = $params['cancelURL'];

        $result = $this->invokeAPI( $args );

        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success */
        $params['trxn_id']        = $result['transactionid'];
        $params['gross_amount'  ] = $result['amt'];
        $params['fee_amount'    ] = $result['feeamt'];
        $params['net_amount'    ] = $result['settleamt'];
        if ( $params['net_amount'] == 0 && $params['fee_amount'] != 0 ) {
            $params['net_amount'] = $params['gross_amount'] - $params['fee_amount'];
        }
        $params['payment_status'] = $result['paymentstatus'];
        $params['pending_reason'] = $result['pendingreason'];

        return $params;
    }

    function initialize( &$args, $method ) {
        $args['user'     ] = $this->_paymentProcessor['user_name' ];
        $args['pwd'      ] = $this->_paymentProcessor['password'  ];
        $args['version'  ] = 3.0;
        $args['signature'] = $this->_paymentProcessor['signature' ];
        $args['subject'  ] = $this->_paymentProcessor['subject'   ];
        $args['method'   ] = $method;
    }

    /**
     * This function collects all the information from a web/api form and invokes
     * the relevant payment processor specific functions to perform the transaction
     *
     * @param  array $params assoc array of input parameters for this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @public
     */
    function doDirectPayment( &$params ) {
        $args = array( );

        $this->initialize( $args, 'DoDirectPayment' );

        $args['paymentAction']  = $params['payment_action'];
        $args['amt']            = $params['amount'];
        $args['currencyCode']   = $params['currencyID'];
        $args['invnum']         = $params['invoiceID'];
        $args['ipaddress']      = $params['ip_address'];
        $args['creditCardType'] = $params['credit_card_type'];
        $args['acct']           = $params['credit_card_number'];
        $args['expDate']        = sprintf( '%02d', $params['month'] ) . $params['year'];
        $args['cvv2']           = $params['cvv2'];
        $args['firstName']      = $params['first_name'];
        $args['lastName']       = $params['last_name'];
        $args['email']          = $params['email'];
        $args['street']         = $params['street_address'];
        $args['city']           = $params['city'];
        $args['state']          = $params['state_province'];
        $args['countryCode']    = $params['country'];
        $args['zip']            = $params['postal_code'];
        $args['custom']         = CRM_Utils_Array::value( 'accountingCode',
                                                          $params );

        $result = $this->invokeAPI( $args );

        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success */
        $params['trxn_id']        = $result['transactionid'];
        $params['gross_amount'  ] = $result['amt'];
        return $params;
    }

    /**
     * This function checks to see if we have the right config values
     *
     * @return string the error message if any
     * @public
     */
    function checkConfig( ) {
        $error = array( );
        if ( empty( $this->_paymentProcessor['user_name'] ) ) {
            $error[] = ts( 'Vendor Name is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }

        if ( empty( $this->_paymentProcessor['password'] ) ) {
            $error[] = ts( 'Encryption Password is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }

//        if ( $this->_paymentProcessor['payment_processor_type'] != 'PayPal_Standard' ) {
//            if ( empty( $this->_paymentProcessor['signature'] ) ) {
//                $error[] = ts( 'Signature is not set in the Administer CiviCRM &raquo; Payment Processor.' );
//            }
//        }

        if ( ! empty( $error ) ) {
            return implode( '<p>', $error );
        } else {
            return null;
        }
    }

    function cancelSubscriptionURL( ) {
        if ( $this->_paymentProcessor['payment_processor_type'] == 'PayPal_Standard' ) {
            return "{$this->_paymentProcessor['url_site']}cgi-bin/webscr?cmd=_subscr-find&alias=" .
                urlencode( $this->_paymentProcessor['user_name'] );
        } else {
            return null;
        }
    }

    /*************************************************************
      Send a post request with cURL
        $url = URL to send request to
        $data = POST data to send (in URL encoded Key=value pairs)
    *************************************************************/
    function requestPost($url, $data){
       
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
	  
	  ######## start ######## by jyoti : 26may10 ###############
	  // set to follow any "Location
	  curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION  ,1);
	  ######## end ######## by jyoti : 26may10 ###############
	  
      // Return it direct, don't print it out
      curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
	  
      // This connection will timeout in 30 seconds
      curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
      
	  //The next two lines must be present for the kit to work with newer version of cURL
      //You should remove them if you have any problems in earlier versions of cURL
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

      //Send the request and store the result in an array
//if(curl_exec($curlSession) === false)
//{
//    CRM_Core_Error::debug_log_message('Curl error: '.curl_error($curlSession));
//}
//else
//{
//    CRM_Core_Error::debug_log_message('Curl was fine');
//}
      $rawresponse = curl_exec($curlSession);
      
	  ######## start ######## by jyoti : 26may10 ###############
	  $response_info = curl_getinfo( $curlSession );
	  ######## end ######## by jyoti : 26may10 ###############
	  
//echo "<pre>";
//print_r($rawresponse);
//print_r($response_info);
//echo "</pre>";
//exit;
	  
	  //Store the raw response for later as it's useful to see for integration and understanding
      $_SESSION["rawresponse"]=$rawresponse;
      
	  
	  
	  //Split response into name=value pairs
      $response = split(chr(10), $rawresponse);
	  
/*echo "<pre>";
print_r($response);
echo "</pre>";*/
	 
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
	  
	 
	  ##### start ##################### by jyoti : 25may10 ############################
	  /*if(is_array($output) && !in_array('Status', $output)){
		// parse the response in new way
		$rawresponse = strip_tags($rawresponse);
		//$status_spos = strpos($rawresponse, 'Status');
		//$status_str = substr($rawresponse, $status_spos);
		
		//$response_arr = split(chr(10), $status_str);
		$response_arr = split(chr(10), $rawresponse);
		$output = array();
		$output['Status'] = trim($response_arr[37]);
        $output['StatusDetail'] = trim($response_arr[41]);
		
		//echo "<pre>";
		//print_r($output);
		//echo "</pre>";
	  }*/
	  ##### end ##################### by jyoti : 25may10 ############################
	  
	  //exit;
	  
	  ######## start ######## by jyoti : 26may10 ###############
	  $output = array();
	  $output['NextURL'] = $response_info['url'];
	  ######## end ######## by jyoti : 26may10 ###############
	  
	  /*echo "<pre>";
	  print_r($output);
	  echo "</pre>";*/
	   
	  // Return the output
      return $output;
    } // END function requestPost()

    function doTransferCheckout( &$params, $component = 'contribute' ) {

        /* For ProtX we have to do
           Post over to SagePay
           Get a response
           Then redirect
        */
        $config =& CRM_Core_Config::singleton( );

        if ( $component != 'contribute' && $component != 'event' ) {
            CRM_Core_Error::fatal( ts( 'Component is invalid' ) );
        }

        $notifyURL =
            $config->userFrameworkResourceURL .
            "extern/protXresult.php?reset=1&contactID={$params['contactID']}" .
            "&contributionID={$params['contributionID']}" .
            "&module={$component}&qfKey={$params['qfKey']}";

        if ( $component == 'event' ) {
            $notifyURL .= "&eventID={$params['eventID']}&participantID={$params['participantID']}";
        } else {
            $membershipID = CRM_Utils_Array::value( 'membershipID', $params );
            if ( $membershipID ) {
                $notifyURL .= "&membershipID=$membershipID";
            }
            $relatedContactID = CRM_Utils_Array::value( 'related_contact', $params );
            if ( $relatedContactID ) {
                $notifyURL .= "&relatedContactID=$relatedContactID";

                $onBehalfDupeAlert = CRM_Utils_Array::value( 'onbehalf_dupe_alert', $params );
                if ( $onBehalfDupeAlert ) {
                    $notifyURL .= "&onBehalfDupeAlert=$onBehalfDupeAlert";
                }
            }
        }

        $url    = ( $component == 'event' ) ? 'civicrm/event/register' : 'civicrm/contribute/transact';
        $cancel = ( $component == 'event' ) ? '_qf_Register_display'   : '_qf_Main_display';
        $returnURL = CRM_Utils_System::url( $url,
                                            "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                            true, null, false );
        $cancelURL = CRM_Utils_System::url( $url,
                                            "$cancel=1&cancel=1&qfKey={$params['qfKey']}",
                                            true, null, false );

        // ensure that the returnURL is absolute.
        if ( substr( $returnURL, 0, 4 ) != 'http' ) {
            CRM_Core_Error::fatal( ts( 'Sending a relative URL to Sagepay is erroneous. Please make your resource URL (in Administer CiviCRM >> Global Settings) absolute' ) );
        }

        $protxUnencryptedParams =
            array( 'Vendor'             => $this->_paymentProcessor['user_name'],
                   'VPSProtocol'        => '2.23',
                   'TxType'             => 'PAYMENT',
				   
                 );
		
		##### start ################### commented by jyoti : 26may10 ####################
		/*$protxParams =
            array( 'SuccessURL'         => 'http://www.fend.org/conference/protxconfearly/', //$notifyURL,
                   'FailureURL'         => 'http://www.fend.org/conference/protxconfearly/', //$notifyURL,
                   'Description'        => $params['item_name'],
                   'Currency'           => $params['currencyID'],
                   'VendorTxCode'       => $params['invoiceID'] ,
                   'AllowGiftAid'       => 0,
                   'ApplyAVSCV2'        => 0,
                   'Apply3DSecure'      => 0,
                   'Profile'            => 'NORMAL',
                   'BillingCountry'     => 'GB',
                   'DeliveryCountry'    => 'GB'
                 );*/
		##### end ################### commented by jyoti : 26may10 ####################
		
		$country_id = $params['country-5'];
		$country_sql = "SELECT * FROM civicrm_country WHERE id = '$country_id'";
		$country_dao = CRM_Core_DAO::executeQuery( $country_sql );
		$country_dao->fetch();
    $iso_code = $country_dao->iso_code;  
		
		##### start ################### by jyoti : 26may10 ####################
        $protxParams =
            array( 
				   
				   'VendorTxCode'       => $params['invoiceID'] ,
				   'SuccessURL'         => $notifyURL,
                   'FailureURL'         => $notifyURL,
                   'Description'        => $params['item_name'],
                   'Currency'           => $params['currencyID'],
                   'AllowGiftAid'       => 0,
                   'ApplyAVSCV2'        => 0,
                   'Apply3DSecure'      => 0,
                   'BillingCountry'     => $iso_code,
                   'DeliveryCountry'    => $iso_code
                 );
		##### end ################### by jyoti : 26may10 ####################
		
        // add name and address if available, CRM-3130
        $otherVars = array( 'first_name'     => 'BillingFirstnames',
                            'last_name'      => 'BillingSurname',
                            'street_address' => 'BillingAddress1',
                            'city'           => 'BillingCity',
                            'state_province' => 'state',
                            'postal_code'    => 'BillingPostCode',
                            'email'          => 'CustomerEMail' );

        foreach ( array_keys( $params ) as $p ) {
            // get the base name without the location type suffixed to it
            $parts = split( '-', $p );
            $name  = count( $parts ) > 1 ? $parts[0] : $p;
            if ( isset( $otherVars[$name] ) ) {
                $value = $params[$p];
                if ( $value ) {
                    if ( $name == 'state_province' ) {
                        $stateName = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value );
                        $value     = $stateName;
                    }
                    // ensure value is not an array
                    // CRM-4174
                    if ( ! is_array( $value ) ) {
                        $protxParams[$otherVars[$name]] = $value;
                    }
                }
            }
        }

        // add name and address if available, CRM-3130
        $otherVars2 = array('first_name'     => 'DeliveryFirstnames',
                            'last_name'      => 'DeliverySurname',
                            'street_address' => 'DeliveryAddress1',
                            'city'           => 'DeliveryCity',
                            'postal_code'    => 'DeliveryPostCode');

        foreach ( array_keys( $params ) as $p ) {
            // get the base name without the location type suffixed to it
            $parts = split( '-', $p );
            $name  = count( $parts ) > 1 ? $parts[0] : $p;
            if ( isset( $otherVars2[$name] ) ) {
                $value = $params[$p];
                if ( $value ) {
                    if ( $name == 'state_province' ) {
                        $stateName = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value );
                        $value     = $stateName;
                    }
                    // ensure value is not an array
                    // CRM-4174
                    if ( ! is_array( $value ) ) {
                        $protxParams[$otherVars2[$name]] = $value;
                    }
                }
            }
        }
		
		
		##### start ################### by jyoti : 26may10 ####################
		
		$protxParams +=
			array( 'CustomerName'   =>  $protxParams['BillingFirstnames'].' '.$protxParams['BillingSurname'],
 	    );
		##### end ################### by jyoti : 26may10 ####################
		
		/*echo "<pre>";
		print_r($protxParams);
		echo "</pre>";*/
		
        // if recurring donations, add a few more items
        if ( ! empty( $params['is_recur'] ) ) {
            if ( $params['contributionRecurID'] ) {
                $notifyURL .= "&contributionRecurID={$params['contributionRecurID']}&contributionPageID={$params['contributionPageID']}";
                $protxParams['NotificationURL'] = $notifyURL;
            } else {
                CRM_Core_Error::fatal( ts( 'Recurring contribution, but no database id' ) );
            }

            $protxParams +=
                array( 'cmd'                => '_xclick-subscriptions',
                       'a3'                 => $params['amount'],
                       'p3'                 => $params['frequency_interval'],
                       't3'                 => ucfirst( substr( $params['frequency_unit'], 0, 1 ) ),
                       'src'                => 1,
                       'sra'                => 1,
                       'srt'                => ( $params['installments'] > 0 ) ? $params['installments'] : null,
                       'no_note'            => 1,
                       'modify'             => 0,
                       );
        } else {
			##### start ################### by jyoti : 26may10 ####################
            $protxParams +=
                array( 'Amount'             => $params['amount'],
                       );
			##### end ################### by jyoti : 26may10 ####################
        }
		
		
        // This part of the URL will be encrypted
        $crypturi = '';
        foreach ( $protxParams as $key => $value ) {
            if ( $value === null ) {
                continue;
            }

            //$value = urlencode( $value );
            if ( $key == 'return' ||
                 $key == 'cancel_return' ||
                 $key == 'notify_url' ) {
                $value = str_replace( '%2F', '/', $value );
            }
            $crypturi .= "&{$key}={$value}";
        }

        // This part of the URL will not be encrypted        
        $uri = '';
        foreach ( $protxUnencryptedParams as $key => $value ) {
            if ( $value === null ) {
                continue;
            }

            //$value = urlencode( $value );
            if ( $key == 'return' ||
                 $key == 'cancel_return' ||
                 $key == 'notify_url' ) {
                $value = str_replace( '%2F', '/', $value );
            }
            $uri .= "&{$key}={$value}";
        }

        $uri = substr( $uri, 1 );
        $crypturi = substr( $crypturi, 1 );

        // For testing
		/*$crypturi = "VendorTxCode=9b7887f4ed41f650fc8658734f1bd4f2&SuccessURL=http://204.232.205.240/PHP-kit/orderSuccessful.php&FailureURL=http://204.232.205.240/PHP-kit/orderFailed.php&Description=Online Event Registration: FEND Conference 2010&Currency=GBP&AllowGiftAid=0&ApplyAVSCV2=0&Apply3DSecure=0&BillingCountry=GB&DeliveryCountry=GB&CustomerEMail=mdsa@jh.com&CustomerName=jhg hj&BillingFirstnames=jhg&BillingSurname=hj&BillingAddress1=111 Example Street&BillingCity=Doncaster&BillingPostCode=DN5 9DZ&DeliveryFirstnames=jhg&DeliverySurname=hj&DeliveryAddress1=111 Example Street&DeliveryCity=Doncaster&DeliveryPostCode=DN5 9DZ&Amount=400";*/
        //$crypturi = "VendorTxCode=fend140197980&Amount=23.48&Currency=GBP&Description=DVDs&SuccessURL=http://204.232.205.240/PHP-kit/orderSuccessful.php&FailureURL=http://204.232.205.240/PHP-kit/orderFailed.php&CustomerName=Parvez Saleh&SendEMail=0&BillingFirstnames=Parvez&BillingSurname=Saleh&BillingAddress1=102CadoganGarden&BillingCity=London&BillingPostCode=E181LZ&BillingCountry=GB&DeliveryFirstnames=Parvez&DeliverySurname=Saleh&DeliveryAddress1=102CadoganGarden&DeliveryCity=London&DeliveryPostCode=E181LZ&DeliveryCountry=GB&AllowGiftAid=0&ApplyAVSCV2=0&Apply3DSecure=0";

        CRM_Core_Error::debug_log_message('crypturi'.$crypturi);
		
        $url                  = $this->_paymentProcessor['url_site'];
        $encryptionPassword   = $this->_paymentProcessor['password'];

        // Need to encrypt the URL with the password set in the parameters
        $strCrypt = $this->base64Encode($this->SimpleXor($crypturi,$encryptionPassword));
        CRM_Core_Error::debug_log_message('strCrypt'.$strCrypt);

        //$strCrypt = "BA8XU10jDAErLi4TbQgyFzZbTQcDaG9AUHFsNz0BIhcmV0sEHGVgXys0OAQ1ADQAby07ZxQVPQoLMyMGJAc4F28+EVISMz0KHGEOIBQddx8gBRQXVDQ2HU4SPxUzCyQKBzg1ClolLAlSbmVEYFp5S2FYVwUCZHZLXHFlJhg+ehI7HlZYQDU9Czs0KRU1HSQfJwZXR1ohfj8JKCYDIgsCKx5XEUNGIWJWR3N6Qn5cZEt8WEkCHGNsSUcRAiZ9BT4NfQULU1cjHhgBLS8Sfh4/CXQpDERGPjUcGg8rGzVTBxggHBxNEgI5FQ0pbCU1ADM8HwsQWw9hfjsBLSYfPgkRECAZDVlTPD0KVRErBCYLLV8QAxVbWz8/Kh0zJBc9C2oqMwYcXxQTMRUEKCQREQozCzcZCgYPYGhLSAIrEj8JNhdyLRhFVjQ2XyooJho5ADA6Ox4ACn4+Nh0HL2w0OQI7EDwNKVhBJRsWDCR3M2FWd0geMF91Wz00EAYmCRklACMLK1c+dRQVPRUBNy8EKSg+CyEeF1ZfNCtEOCA4ADUUcT03BhBBVyMhKh0zJBc9C2oqMwYcXxQVPRUBNy8EKS8zHSAPCkQDbGlJWmEJFzQBMBg8Sj5WQDU9F04FLxo5GDILKykQQ0tsFBYGJSUYdioyFTscHEVLATcKHAIlEjVTEkhqSkh7aHccHAQoPBMiFxQWJwQNRUtsHztOAysFOwsjRGBQO1ZGPDkXSGxqIjgLdz0zGBIXeT8xHgA1cERqV3lKZ1BIGQRlYkhYb3NPalxmV2tSQ3NXPTEPDTMzTGFUZldnWkMaH3xiSEZ0ekxhQGJJdCsVW10mHxAONQsfNFNnXxMaCVtLEA4qKxd4S2BIFgkiBgAEdgI9Gh0zL0tg";
        CRM_Core_Error::debug_log_message('strCrypt'.$strCrypt);

        $strCrypt = urlencode($strCrypt);
        CRM_Core_Error::debug_log_message('strCrypt'.$strCrypt);

        $strPurchaseURL = $url;
        $strPost = $uri."&Crypt=".$strCrypt;
        //$strPost = $uri."&Crypt=\"".$strCrypt."\"";
        //$strPost = '"'.$uri."&Crypt=".$strCrypt.'"';
        
        CRM_Core_Error::debug_log_message('strPurchaseURL'.$strPurchaseURL);
        CRM_Core_Error::debug_log_message('strPost'.$strPost);
		/*echo "<pre>";
		print_r($crypturi);
		echo "<br />";
		print_r($strPost);
		echo "</pre>";*/
		//exit;
        $arrResponse = CRM_Core_Payment_ProtX::requestPost($strPurchaseURL, $strPost);
        
        require_once 'CRM/Core/Session.php';
        CRM_Core_Session::storeSessionObjects( );

        /* Analyse the response from Sage Pay Server to check that everything is okay
        ** Registration results come back in the Status and StatusDetail fields */
        $strStatus=$arrResponse["Status"];
        $strStatusDetail=$arrResponse["StatusDetail"];
        
        CRM_Core_Error::debug_log_message('encryptionPassword='.$encryptionPassword);
		
		##### start ################### commented by jyoti : 26may10 ####################
       /* if ($strStatus != 'OK' && $strStratus != 'OK REPEATED') {
          // Any other return indicates a problem
          
          CRM_Core_Error::debug_log_message(sprintf("ProtX Transaction Request failed: %s (%s %s - %s)\n%s", $strStatus, $component, $params['invoiceID'], $params['item_name'], $strStatusDetail), ($mode == 'test'));
          
          switch ($strStatus) {
            case 'MALFORMED':
              CRM_CORE_ERROR::fatal( ts('There was a problem communicating with SagePay') . '<br >' . ts('You have not been charged.') );
              break;
              
            case 'INVALID':
              CRM_CORE_ERROR::fatal( ts('There was a problem with the information supplied') . '<br >' . ts('You have not been charged.'));
              break;
              
            default: // ie ERROR
              CRM_CORE_ERROR::fatal( ts('There was a problem with SagePay') . '<br >' . ts('You have not been charged.') );
              break;
          }  */
		  
		  /*echo "<pre>";
		print_r($strStatus);
		echo "</pre>";
		exit;*/

		/*######## start ########### by jyoti: 25 may10 ##############	
	 	echo 'encryptionPassword='.$encryptionPassword;
		echo "Status : ".$strStatus;
		echo "<br />Detail: ".$strStatusDetail;
		//exit;
		######## start ########### by jyoti: 25 may10 ##############*/	
        //}
        // else 
		##### end ################### commented by jyoti : 26may10 ####################
		
        CRM_Core_Error::debug_log_message('VPSTxId'.$arrResponse["VPSTxId"]);
        CRM_Core_Error::debug_log_message('SecurityKey'.$arrResponse["SecurityKey"]);
        CRM_Core_Error::debug_log_message('NextURL'.$arrResponse["NextURL"]);
        CRM_Core_Error::debug_log_message('Status'.$arrResponse["Status"]);
        CRM_Core_Error::debug_log_message('StatusDetail'.$arrResponse["StatusDetail"]);
        
        CRM_Utils_System::redirect( $arrResponse["NextURL"] );
    }

    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @methodName is name of API  method.
     * @nvpStr is nvp string.
     * returns an associtive array containing the response from the server.
     */
    function invokeAPI( $args, $url = null ) {

        if ( $url === null ) {
            if ( empty( $this->_paymentProcessor['url_api'] ) ) {
                CRM_Core_Error::fatal( ts( 'Please set the API URL. Please refer to the documentation for more details' ) );
            }

            $url = $this->_paymentProcessor['url_api'] . 'nvp';
        }

        if ( !function_exists('curl_init') ) {
            CRM_Core_Error::fatal("curl functions NOT available.");
        }

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // PS Commented out to try and stop the thing failing to decrypt
        //$p = array( );
        //foreach ( $args as $n => $v ) {
        //    $p[] = "$n=" . urlencode( $v );
        //}

        // PS this loop is without the URL encode
        // Hoping this will work
        $p = array( );
        foreach ( $args as $n => $v ) {
            $p[] = "$n=" . $v;
        }

        //NVPRequest for submitting to server
        $nvpreq = implode( '&', $p );

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec( $ch );
        
        

        //converting NVPResponse to an Associative Array
        $result = self::deformat( $response );

        if ( curl_errno( $ch ) ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( curl_errno( $ch ),
                      0, null,
                      curl_error( $ch ) );
            return $e;
        } else {
      curl_close($ch);
        }

        if ( strtolower( $result['ack'] ) != 'success' &&
             strtolower( $result['ack'] ) != 'successwithwarning' ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( $result['l_errorcode0'],
                      0, null,
                      "{$result['l_shortmessage0']} {$result['l_longmessage0']}" );
            return $e;
        }

        return $result;
    }

    /** This function will take NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     * @nvpstr is NVPString.
     * @nvpArray is Associative Array.
     */

    static function deformat( $str )
    {
        $result = array();

        while ( strlen( $str ) ) {
            // postion of key
            $keyPos = strpos( $str, '=' );

            // position of value
            $valPos = strpos( $str, '&' ) ? strpos( $str, '&' ): strlen( $str );

            /*getting the Key and Value values and storing in a Associative Array*/
            $key = substr( $str, 0, $keyPos );
            $val = substr( $str, $keyPos + 1, $valPos - $keyPos - 1 );

            //decoding the respose
            $result[ strtolower( urldecode( $key ) ) ] = urldecode( $val );
            $str = substr( $str, $valPos + 1, strlen( $str ) );
        }

        return $result;
    }

    /*  The SimpleXor encryption algorithm                                                                                **
    **  NOTE: This is a placeholder really.  Future releases of Form will use AES or TwoFish.  Proper encryption      **
    **  This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering        **
    **  It won't stop a half decent hacker though, but the most they could do is change the amount field to something     **
    **  else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still          **
    **  more secure than the other PSPs who don't both encrypting their forms at all                                      */

    function simpleXor($InString, $Key) {
      // Initialise key array
      $KeyList = array();
      // Initialise out variable
      $output = "";

      // Convert $Key into array of ASCII values
      for($i = 0; $i < strlen($Key); $i++){
        $KeyList[$i] = ord(substr($Key, $i, 1));
      }
      
//print "<PRE>";
//print_r($KeyList);
//print "<PRE>";

      // Step through string a character at a time
      for($i = 0; $i < strlen($InString); $i++) {
        // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
        // % is MOD (modulus), ^ is XOR
        $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
      }
//print "output=".$output;

      // Return the result
      return $output;
    }
    
    /* Base 64 Encoding function **
    ** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/

    function base64Encode($plain) {
      // Initialise output variable
      $output = "";

      // Do encoding
      $output = base64_encode($plain);

      // Return the result
      return $output;
    }
}


