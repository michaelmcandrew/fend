<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'civicrm_rules_utils.inc';

function civicrm_rules_rules_action_mailing_send_email( $to, $subject, $message, $from = NULL, $settings, RulesState $state, RulesPlugin $element ) {
    if( isset($state->variables['mailing']) && $state->variables['mailing']->id ) {
        $to       = str_replace( array( "\r", "\n" ), ',', $to );
        $toEmails = explode( ',', $to );
        if ( empty( $toEmails ) ) { 
            return;
        }
        require_once 'CRM/Utils/Token.php';
        $tokens['mailing'] = array( 'mailing.editUrl',
                                    'mailing.scheduleUrl', 
                                    'mailing.html', 
                                    'mailing.creatorEmail', 
                                    'mailing.creator' );

        $params = array( );
        $params['from']    = !empty( $from ) ? str_replace( array( "\r", "\n" ), '', $from ) : 'Admin';
        $params['subject'] = CRM_Utils_Token::replaceMailingTokens( $subject, $state->variables['mailing'], null, $tokens );
        $params['html']  = CRM_Utils_Token::replaceMailingTokens( $message, $state->variables['mailing'], null, $tokens );
        foreach ( $toEmails as $toEmail ) {
            $params['toEmail']    = CRM_Utils_Token::replaceMailingTokens( $toEmail, $state->variables['mailing'], null, $tokens );
            CRM_Utils_Mail::send( $params );
        }
    }
}

function civicrm_rules_condition_mailing_approved($mailing, $settings) {
    return $mailing->approval_status_id == 1; 
}

function civicrm_rules_condition_mailing_rejected($mailing, $settings) {
    return $mailing->approval_status_id != 1; 
}