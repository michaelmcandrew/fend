<?php

// SagePay (Server) Callback Class - 10/06/2010 //

require_once 'CRM/Core/Payment/BaseSagePayServerNotify.php';

class CRM_Core_Payment_SagePayServerNotify extends CRM_Core_Payment_BaseSagePayServerNotify {

    static $_paymentProcessor = null;
	
    function __construct() {
        parent::__construct();
    }
	
	function main($component = 'contribute') {
		
		require_once 'CRM/Utils/Request.php';
		require_once 'CRM/Core/DAO.php';
		
        $objects = $ids = $input = array();
        $this->component = $input['component'] = $component;

        // get the contribution and contact ids from the GET params
        $ids['contact']      = self::retrieve('contactID', 'Integer', 'GET', true);
        $ids['contribution'] = self::retrieve('contributionID', 'Integer', 'GET', true);
		$ids['vendor']       = self::retrieve('vendor', 'String', 'GET', true);
		
		define('SAGEPAY_QFKEY', self::retrieve('qfkey', 'String', 'GET', false));
				
		$this->getInput($input, $ids);
		
		/** Rebuild the POST message, including our security key, and use the MD5 Hash **
		 ** component that is included to create our own signature to compare with **
		 ** the contents of the VPSSignature field in the POST.  Check the Sage Pay Server protocol **
		 ** if you need clarification on this process **/
		
		$dao = CRM_Core_DAO::executeQuery("SELECT security_key FROM civicrm_sagepay WHERE contribution_id='" . $ids['contribution'] . "'");
		while ($dao->fetch())
			$security_key = $dao->security_key;
		
		// Delete security key entry in civicrm_sagepay (so it can't be used again)
		CRM_Core_DAO::executeQuery("DELETE FROM civicrm_sagepay WHERE contribution_id='" . $ids['contribution'] . "'");
		
		$strMessage = $input['trxn_id'] . $input['invoice'] . $input['paymentStatus'] . $input['TxAuthNo'] .
					  $ids['vendor'] . $input['AVSCV2'] . $security_key . $input['AddressResult'] .
					  $input['PostCodeResult'] . $input['CV2Result'] . $input['GiftAid'] . $input['3DSecureStatus'] .
					  $input['CAVV'] . $input['AddressStatus'] . $input['PayerStatus'] . $input['CardType'] .
					  $input['Last4Digits'];
		
		$strMySignature = strtoupper(md5($strMessage));
		
		/** Compare our MD5 Hash signature with that from Sage Pay Server **/
		if ($strMySignature !== $input['VPSSignature']) {
			
			// If not matched, send INVALID response and return ... 
			$url    = ($component == 'event') ? 'civicrm/event/register' : 'civicrm/contribute/transact';
        	$cancel = ($component == 'event') ? '_qf_Register_display'   : '_qf_Main_display';

        	$cancelURL = CRM_Utils_System::url( $url,
                                            "$cancel=1&cancel=1&qfKey=" . SAGEPAY_QFKEY,
                                            true, null, false ); 
			
			$eoln = chr(13) . chr(10);
			echo "Status=INVALID" . $eoln;
			echo "StatusDetail=Unable to match VPS signature." . $eoln;
			echo "RedirectURL=$cancelURL" . $eoln;
			
			if (defined('SAGEPAY_DEBUG') and SAGEPAY_DEBUG === 1)
				CRM_Core_Error::debug_log_message(
					date('d-m-y h:i:s') . ' - TRANSACTION FAILED: INVALID VPS SIGNATURE' . PHP_EOL . 
					print_r($input, true) . PHP_EOL . PHP_EOL
				);
			
			return;
		}
				
        if ($component == 'event') {
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

        if (!$this->validateData($input, $ids, $objects)) {
            
            if (defined('SAGEPAY_DEBUG') and SAGEPAY_DEBUG === 1)
				CRM_Core_Error::debug_log_message(
					date('d-m-y h:i:s') . ' - TRANSACTION FAILED: UNABLE TO VALIDATE DATA' . PHP_EOL . 
					print_r($input, true) . PHP_EOL . PHP_EOL
				);
				
            return false;
        }

        self::$_paymentProcessor =& $objects['paymentProcessor'];
        if ($component == 'contribute') {
            if ($ids['contributionRecur']) {
                // check if first contribution is completed, else complete first contribution
                $first = true;
                if ($objects['contribution']->contribution_status_id == 1)
                    $first = false;
                
                return $this->recur($input, $ids, $objects, $first);
            } else {
            	
            	if (defined('SAGEPAY_DEBUG') and SAGEPAY_DEBUG === 1)
					CRM_Core_Error::debug_log_message(
						date('d-m-y h:i:s') . ' - TRANSACTION SUCCESS: CONTRIB' . PHP_EOL . 
						print_r($input, true) . PHP_EOL . PHP_EOL
					);
					
                return $this->single($input, $ids, $objects, false, false);
            }
        } else {
			
			if (defined('SAGEPAY_DEBUG') and SAGEPAY_DEBUG === 1)
				CRM_Core_Error::debug_log_message(
					date('d-m-y h:i:s') . ' - TRANSACTION SUCCESS: EVENT' . PHP_EOL . 
					print_r($input, true) . PHP_EOL . PHP_EOL
				);
				
            return $this->single($input, $ids, $objects, false, false);
        }
		
	}
	
	static function retrieve($name, $type, $location = 'POST', $abort = true) {
        static $store = null;
        $value = CRM_Utils_Request::retrieve($name, $type, $store, false, null, $location);
        if ( $abort && $value === null ) {
            CRM_Core_Error::debug_log_message( "Could not find an entry for $name in $location" );
            echo "Failure: Missing Parameter<p>";
            exit();
        }
        return $value;
    }
	
	protected function getInput(&$input, &$ids) {
		
        if (!$this->getBillingID($ids))
            return false;
	
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
		
	}
	
	   function recur(&$input, &$ids, &$objects, $first) {
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

        $now = date('YmdHis');

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

        $recur->save();

        if ($txnType != 'subscr_payment') 
            return;

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

    function single(&$input, &$ids, &$objects, $recur = false, $first = false) {
        
		$contribution =& $objects['contribution'];
	
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
        $transaction = new CRM_Core_Transaction();

        $participant =& $objects['participant'];
        $membership  =& $objects['membership' ];

        $status = $input['paymentStatus'];
		
		// Changed NOTAUTHED to 'failed' rather than 'pending' - AW
	    if ($status == 'REJECTED' || $status == 'ERROR' || $status == 'NOTAUTHED') {
            return $this->failed($objects, $transaction);
		} else if ($status == 'ABORT') {
            return $this->cancelled( $objects, $transaction);
        } else if ($status != 'OK') {
            return $this->unhandled($objects, $transaction);
		}
		
		// If we arrived here, Status = OK
		
        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            $transaction->commit( );
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return true;
        }

        $this->completeTransaction($input, $ids, $objects, $transaction, $recur);
		
    }
	
	protected function getToken($thisString) {

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
			"PayerStatus","CardType"
		
		);
		
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
};

?>