<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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

require_once 'CRM/Report/Form.php';
require_once 'CRM/Event/PseudoConstant.php';
require_once 'CRM/Core/OptionGroup.php';

class CRM_Report_Form_Event_ConferenceParticipantListing extends CRM_Report_Form {

    protected $_summary = null;

    
    function __construct( ) {
        $this->_columns = array( );
        parent::__construct( );
    }

    
    function preProcess( ) {
        parent::preProcess( );
    }
    
    function postProcess( ) {
        $this->beginPostProcess( );

        // note1: your query
        // $myQuery = "select first_name, last_name from civicrm_contact";
        $myQuery = "
SELECT
civicrm_contact.id,
civicrm_contact.job_title,
civicrm_contact.last_name,
civicrm_contact.display_name,
civicrm_value_conference_delegate_9.organisation_name_14,
civicrm_value_conference_delegate_9.country_34
FROM
civicrm_participant
INNER JOIN civicrm_contact ON civicrm_participant.contact_id = civicrm_contact.id
INNER JOIN civicrm_event ON civicrm_participant.event_id = civicrm_event.id
INNER JOIN civicrm_value_conference_delegate_9 ON civicrm_participant.id = civicrm_value_conference_delegate_9.entity_id
WHERE civicrm_event.event_type_id = 1
ORDER BY civicrm_contact.last_name";

        // note2: register columns you want displayed-
        $this->_columnHeaders =
                array( 'display_name' => array( 'title' => 'Display Name' ),
                       'job_title' => array( 'title' => 'Job Title' ),
                       'organisation_name_14'  => array( 'title' => 'Organisation' ),
                       'country_34'  => array( 'title' => 'Country' ,'no_display' => true  ),
                       'id'  => array( 'title' => 'ID', 'no_display' => true )
                       );
                       
        // note3: let report do the fetching of records for you
        $this->buildRows ( $myQuery, $rows );
        $this->alterDisplay ( $rows );
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( $rows );
    }    

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        $entryFound = false;

        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            
            // print "<PRE>";            
            // print_r ($row);
            // print "</PRE>";            
            
            // change contact name with link
            if ( array_key_exists('display_name', $row) && 
                 array_key_exists('id', $row) ) {
                
                $url = CRM_Utils_System::url( "civicrm/contact/view",  
                                              'reset=1&cid=' . $row['id'],
                                              $this->_absoluteUrl );
                $rows[$rowNum]['display_name_link' ] = $url;
                $rows[$rowNum]['display_name_hover'] = 
                    ts("View Contact Summary for this Contact");
                $entryFound = true;
                unset($rows[$rowNum]['id']);
            }
            
            // handle country
            if ( array_key_exists('country_34', $row) ) {
                if ( $value = $row['country_34'] ) {
                    $rows[$rowNum]['country_34'] = CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
            
            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }    
}