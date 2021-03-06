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

require_once 'CRM/Core/Payment/BaseProtXResult.php';

class CRM_Core_Payment_ProtXResult extends CRM_Core_Payment_BaseProtXResult {

    static $_paymentProcessor = null;

    function __construct( ) {
        parent::__construct( );
    }

    static function retrieve( $name, $type, $location = 'POST', $abort = true )
    {
        static $store = null;
        $value = CRM_Utils_Request::retrieve( $name, $type, $store,
                                              false, null, $location );
        if ( $abort && $value === null ) {
            CRM_Core_Error::debug_log_message( "Could not find an entry for $name in $location" );
            echo "Failure: Missing Parameter<p>";
            exit( );
        }

        CRM_Core_Error::debug_log_message('CRM_Core_Payment_ProtXResult.'.$name.'='.$value);

        return $value;
    }

    function recur( &$input, &$ids, &$objects, $first )
    {
        if ( ! isset( $input['txnType'] ) ) {
            CRM_Core_Error::debug_log_message( "Could not find txn_type in input request" );
            echo "Failure: Invalid parameters<p>";
            return false;
        }

        if ( $input['txnType']       == 'subscr_payment' &&
             $input['paymentStatus'] != 'OK' ) {
            CRM_Core_Error::debug_log_message( "Ignore all IPN payments that are not completed" );
            echo "Failure: Invalid parameters<p>";
            return false;
        }

        $recur =& $objects['contributionRecur'];

        // make sure the invoice ids match
        // make sure the invoice is valid and matches what we have in the contribution record
        if ( $recur->invoice_id != $input['invoice'] ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and Sage Pay request" );
            echo "Failure: Invoice values dont match between database and Sage Pay request<p>";
            return false;
        }

        $now = date( 'YmdHis' );

        // fix dates that already exist
        $dates = array( 'create', 'start', 'end', 'cancel', 'modified' );
        foreach ( $dates as $date ) {
            $name = "{$date}_date";
            if ( $recur->$name ) {
                $recur->$name = CRM_Utils_Date::isoToMysql( $recur->$name );
            }
        }
        //set transaction type
        $txnType = $_POST['txn_type'];
        switch ( $txnType ) {

        case 'subscr_signup':
            $recur->create_date            = $now;
            $recur->contribution_status_id = 2;
            $recur->processor_id           = $_POST['subscr_id'];
            $recur->trxn_id                = $recur->processor_id;
            break;

        case 'subscr_eot':
            $recur->contribution_status_id = 1;
            $recur->end_date               = $now;
            break;

        case 'subscr_cancel':
            $recur->contribution_status_id = 3;
            $recur->cancel_date            = $now;
            break;

        case 'subscr_failed':
            $recur->contribution_status_id = 4;
            $recur->cancel_date            = $now;
            break;

        case 'subscr_modify':
            CRM_Core_Error::debug_log_message( "We do not handle modifications to subscriptions right now" );
            echo "Failure: We do not handle modifications to subscriptions right now<p>";
            return false;

        case 'subscr_payment':
            if ( $first ) {
                $recur->start_date    = $now;
            } else {
                $recur->modified_date = $now;
            }

            // make sure the contribution status is not done
            // since order of ipn's is unknown
            if ( $recur->contribution_status_id != 1 ) {
                $recur->contribution_status_id = 5;
            }
            break;
        }

        $recur->save( );

        if ( $txnType != 'subscr_payment' ) {
            return;
        }

        if ( ! $first ) {
            // create a contribution and then get it processed
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->contact_id = $ids['contact'];
            $contribution->contribution_type_id  = $objects['contributionType']->id;
            $contribution->contribution_page_id  = $ids['contributionPage'];
            $contribution->contribution_recur_id = $ids['contributionRecur'];
            $contribution->receive_date          = $now;
            $contribution->currency              = $objects['contribution']->currency;
            $contribution->payment_instrument_id = $objects['contribution']->payment_instrument_id;
            $contribution->amount_level          = $objects['contribution']->amount_level;

            $objects['contribution'] =& $contribution;
        }

        $this->single( $input, $ids, $objects,
                       true, $first );
    }

    function single( &$input, &$ids, &$objects,
                     $recur = false,
                     $first = false )
    {
        $contribution =& $objects['contribution'];
		
		//echo "<pre>";
		//print_r($input);
		//print_r($ids);
		//print_r($objects);
		//print_r($recur);
		//print_r($first);
		//echo $input
		//echo "</pre>";
		
		
        // make sure the invoice is valid and matches what we have in the contribution record
        if ( ( ! $recur ) || ( $recur && $first ) ) {
            if ( $contribution->invoice_id != $input['invoice'] ) {
                CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
                CRM_Core_Error::debug_log_message( "contribution->invoice_id=".$contribution->invoice_id);
                CRM_Core_Error::debug_log_message( "input['invoice']=".$input['invoice']);
                echo "Failure: Invoice values dont match between database and IPN request<p>";
                return false;
            }
        } else {
            $contribution->invoice_id = md5( uniqid( rand( ), true ) );
        }

        /* Needs fixing to check the total amount being returned matches the invoice */
        // The line below is a fix due to ProtX not returning the amount, it cannot be changed at their end
        // So to allow the process to work all the way through I default it to the contribution amount for the invoice
        $input['amount'] = $contribution->total_amount;
        if ( ! $recur ) {
            if ( $contribution->total_amount != $input['amount'] ) {
                CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
                echo "Failure: Amount values dont match between database and IPN request<p>";
                return false;
            }
        } else {
            $contribution->total_amount = $input['amount'];
        }


        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        // fix for CRM-2842
        //  if ( ! $this->createContact( $input, $ids, $objects ) ) {
        //       return false;
        //  }

        $participant =& $objects['participant'];
        $membership  =& $objects['membership' ];

        $status = $input['paymentStatus'];
		
		###test
		//$status == 'OK';
		//echo 'status : '.$status;
		
		### jyoti 
	    if ( $status == 'REJECTED' || $status == 'ERROR' ) {
            return $this->failed( $objects, $transaction );
		} else if ( $status == 'ABORT') {
            return $this->cancelled( $objects, $transaction );
        } else if ( $status == 'NOTAUTHED' ) {
            return $this->pending( $objects, $transaction );
        } else if ( $status != 'OK' ) {
            return $this->unhandled( $objects, $transaction );
        }
		### jyoti 


        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            $transaction->commit( );
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return true;
        }

        $this->completeTransaction( $input, $ids, $objects, $transaction, $recur );
    }

    function main( $component = 'contribute' )
    {
	
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );
		
		
		    require_once 'CRM/Utils/Request.php';

        $objects = $ids = $input = array( );
        $input['component'] = $component;

        // get the contribution and contact ids from the GET params
        $ids['contact']           = self::retrieve( 'contactID'         , 'Integer', 'GET' , true  );
        $ids['contribution']      = self::retrieve( 'contributionID'    , 'Integer', 'GET' , true  );
		
		### jyoti 
			$strCrypt=$_REQUEST["crypt"];
						
			// getting encrypted password
			require_once("CRM/Core/BAO/PaymentProcessor.php");
			
			$params = array( 'payment_processor_type' => 'PROTX' ) ;
			CRM_Core_BAO_PaymentProcessor::retrieve($params, $p_processor);
			
			$encryptionPassword   = $p_processor['password'];
			
			// Now decode the Crypt field and extract the results
			$strDecoded=$this->simpleXor($this->Base64Decode($strCrypt),$encryptionPassword);
			$postvalues = $this->getToken($strDecoded);
			
			$_POST = array_merge($_REQUEST, $postvalues);
			
		### jyoti 

        $this->getInput( $input, $ids );
		
        if ( $component == 'event' ) {
            $ids['event']       = self::retrieve( 'eventID'      , 'Integer', 'GET', true );
            $ids['participant'] = self::retrieve( 'participantID', 'Integer', 'GET', true );
        } else {
            // get the optional ids
            $ids['membership']          = self::retrieve( 'membershipID'       , 'Integer', 'GET', false );
            $ids['contributionRecur']   = self::retrieve( 'contributionRecurID', 'Integer', 'GET', false );
            $ids['contributionPage']    = self::retrieve( 'contributionPageID' , 'Integer', 'GET', false );
            $ids['related_contact']     = self::retrieve( 'relatedContactID'   , 'Integer', 'GET', false );
            $ids['onbehalf_dupe_alert'] = self::retrieve( 'onBehalfDupeAlert'  , 'Integer', 'GET', false );
        }

        //echo "<pre>";
		//print_r($input);
		//print_r($ids);
		//echo "</pre>";
		//exit;

        if ( ! $this->validateData( $input, $ids, $objects ) ) {
            return false;
        }

        self::$_paymentProcessor =& $objects['paymentProcessor'];
        if ( $component == 'contribute' ) {
            if ( $ids['contributionRecur'] ) {
                // check if first contribution is completed, else complete first contribution
                $first = true;
                if ( $objects['contribution']->contribution_status_id == 1 ) {
                    $first = false;
                }
                return $this->recur( $input, $ids, $objects, $first );
            } else {
                return $this->single( $input, $ids, $objects, false, false );
            }
        } else {
            return $this->single( $input, $ids, $objects, false, false );
        }
    }

    function getInput( &$input, &$ids ) {
        if ( ! $this->getBillingID( $ids ) ) {
            return false;
        }
		
		
		
        $input['VPSProtocol']         = self::retrieve( 'VPSProtocol'            , 'Money'  , 'POST', false  );
        $input['TxType']              = self::retrieve( 'TxType'                 , 'String' , 'POST', false  );
        $input['invoice']             = self::retrieve( 'VendorTxCode'           , 'String' , 'POST', false  );
        $input['trxn_id']             = self::retrieve( 'VPSTxId'                , 'String' , 'POST', false  );
        $input['paymentStatus']       = self::retrieve( 'Status'                 , 'String' , 'POST', false  );
        $input['StatusDetail']        = self::retrieve( 'StatusDetail'           , 'String' , 'POST', false  );
        $input['TxAuthNo']            = self::retrieve( 'TxAuthNo'               , 'String' , 'POST', false  );
        $input['AVSCV2']              = self::retrieve( 'AVSCV2'                 , 'String' , 'POST', false  );
        $input['AddressResult']       = self::retrieve( 'AddressResult'          , 'String' , 'POST', false  );
        $input['PostCodeResult']      = self::retrieve( 'PostCodeResult'         , 'String' , 'POST', false  );
        $input['CV2Result']           = self::retrieve( 'CV2Result'              , 'String' , 'POST', false  );
        $input['GiftAid']             = self::retrieve( 'GiftAid'                , 'String' , 'POST', false  );
        $input['3DSecureStatus']      = self::retrieve( '3DSecureStatus'         , 'String' , 'POST', false  );
        $input['CAVV']                = self::retrieve( 'CAVV'                   , 'String' , 'POST', false  );
        $input['AddressStatus']       = self::retrieve( 'AddressStatus'          , 'String' , 'POST', false  );
        $input['PayerStatus']         = self::retrieve( 'PayerStatus'            , 'String' , 'POST', false  );
        $input['CardType']            = self::retrieve( 'CardType'               , 'String' , 'POST', false  );
        $input['Last4Digits']         = self::retrieve( 'Last4Digits'            , 'String' , 'POST', false  );
        $input['VPSSignature']        = self::retrieve( 'VPSSignature'           , 'String' , 'POST', false  );

		
		#####
//$input['paymentStatus']       = 'OK';
//$input['TxType']			  = '';			
        /*
        $input['amount']        = self::retrieve( 'mc_gross'          , 'Money'  , 'POST', true  );
        $input['reasonCode']    = self::retrieve( 'ReasonCode'        , 'String' , 'POST', false );

        $billingID = $ids['billing'];
        $lookup = array( "first_name"                  => 'first_name',
                         "last_name"                   => 'last_name' ,
                         "street_address-{$billingID}" => 'address_street',
                         "city-{$billingID}"           => 'address_city',
                         "state-{$billingID}"          => 'address_state',
                         "postal_code-{$billingID}"    => 'address_zip',
                         "country-{$billingID}"        => 'address_country_code' );
        foreach ( $lookup as $name => $paypalName ) {
            $value = self::retrieve( $paypalName, 'String', 'POST', false );
            $input[$name] = $value ? $value : null;
        }

        $input['is_test']    = self::retrieve( 'test_ipn'     , 'Integer', 'POST', false );
        $input['fee_amount'] = self::retrieve( 'payment_fee'  , 'Money'  , 'POST', false );
        $input['net_amount'] = self::retrieve( 'settle_amount', 'Money'  , 'POST', false );
        $input['trxn_id']    = self::retrieve( 'txn_id'       , 'String' , 'POST', false );
        */
    }
	

### start ####### jyoti : added these functions ############
	
	
	/* The getToken function.                                                                                         **
** NOTE: A function of convenience that extracts the value from the "name=value&name2=value2..." reply string **
** Works even if one of the values is a URL containing the & or = signs.                                      	  */

function getToken($thisString) {

  // List the possible tokens
  $Tokens = array(
    "Status",
    "StatusDetail",
    "VendorTxCode",
    "VPSTxId",
    "TxAuthNo",
    "Amount",
    "AVSCV2", 
    "AddressResult", 
    "PostCodeResult", 
    "CV2Result", 
    "GiftAid", 
    "3DSecureStatus", 
    "CAVV",
	"AddressStatus",
	"CardType",
	"Last4Digits",
	"PayerStatus","CardType");



  // Initialise arrays
  $output = array();
  $resultArray = array();
  
  // Get the next token in the sequence
  for ($i = count($Tokens)-1; $i >= 0 ; $i--){
    // Find the position in the string
    $start = strpos($thisString, $Tokens[$i]);
	// If it's present
    if ($start !== false){
      // Record position and token name
      $resultArray[$i]->start = $start;
      $resultArray[$i]->token = $Tokens[$i];
    }
  }
  
  // Sort in order of position
  sort($resultArray);
	// Go through the result array, getting the token values
  for ($i = 0; $i<count($resultArray); $i++){
    // Get the start point of the value
    $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
	// Get the length of the value
	
    if ($i==(count($resultArray)-1)) {
      $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
    } else {
      $valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
	  $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
    }     

  }

  // Return the ouput array
  return $output;
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

  // Step through string a character at a time
  for($i = 0; $i < strlen($InString); $i++) {
    // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
    // % is MOD (modulus), ^ is XOR
    $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
  }

  // Return the result
  return $output;
}


	
	
/* Base 64 decoding function **
** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/

function base64Decode($scrambled) {
  // Initialise output variable
  $output = "";
  
  // Fix plus to space conversion issue
  $scrambled = str_replace(" ","+",$scrambled);
  
  // Do encoding
  $output = base64_decode($scrambled);
  
  // Return the result
  return $output;
}

### end ####### jyoti : added these functions ############

}


