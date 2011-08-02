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

require_once 'CRM/Contact/Form/Search/Interface.php';

class CRM_Event_Form_Search_Custom_ParticipantInvoices
   implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    function __construct( &$formValues ) {     
        $this->_formValues = $formValues;

        /**
         * Define the columns for search result rows
         */
        $this->_columns = array( ts('Name'      )   => 'display_name',
                                 ts('Language'      )   => 'contact_language',
                                 ts('Invoice Created') => 'date_created',
                                 ts('Invoice Due') => 'due_date' );
    }

    function buildForm( &$form ) {
        /**
         * You can define a custom title for the search form
         */
        $this->setTitle('Delegates to be invoiced or payment overdue');

        /**
         * Define the search form fields here
         */
        $form->add( 'checkbox',
                    'include_invoiced',
                    ts( 'Include overdue?' ) );
        /**
         * If you are using the sample template, this array tells the template fields to render
         * for the search form.
         */
        $form->assign( 'elements', array( 'include_invoiced') );
    }

    /**
     * Define the smarty template used to layout the search form and results listings.
     */
    function templateFile( ) {
       return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }
       
    /**
      * Construct the search query
      */       
    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false, $onlyIDs = false ) {
        
        // SELECT clause must include contact_id as an alias for civicrm_contact.id
        if ( $onlyIDs ) {
            $select  = "DISTINCT contact_a.id as contact_id";
        } else {
            $select  = "
DISTINCT civicrm_contact.id as contact_id,
civicrm_contact.display_name as display_name,
civicrm_mtl_invoice.date_created AS date_created,
civicrm_mtl_invoice.date_due AS due_date
";
        }
        $from  = $this->from( );

        $where = $this->where( $includeContactIDs );

        $having = $this->having( );
        if ( $having ) {
            $having = " HAVING $having ";
        }

        $sql = "
SELECT $select
FROM   $from
WHERE  $where
GROUP BY civicrm_contact.id
$having
";
        //for only contact ids ignore order.
        if ( !$onlyIDs ) {
            // Define ORDER BY for query in $sort, with default value
            if ( ! empty( $sort ) ) {
                if ( is_string( $sort ) ) {
                    $sql .= " ORDER BY $sort ";
                } else {
                    $sql .= " ORDER BY " . trim( $sort->orderBy() );
                }
            } else {
                $sql .= "ORDER BY civicrm_contact.last_name";
            }
        }
        return $sql;
    }
    
    function from( ) {
        return "
civicrm_contact
INNER JOIN civicrm_participant ON civicrm_contact.id = civicrm_participant.contact_id
INNER JOIN civicrm_event ON civicrm_participant.event_id = civicrm_event.id
INNER JOIN civicrm_participant_payment ON civicrm_participant.id = civicrm_participant_payment.participant_id
LEFT JOIN civicrm_mtl_invoice ON civicrm_participant.id = civicrm_mtl_invoice.participant_id
";

    }

     /*
      * WHERE clause is an array built from any required JOINS plus conditional filters based on search criteria field values
      *
      */
    function where( $includeContactIDs = false ) {
    
        $include_invoiced = $this->_formValues['include_invoiced'];
        
        $clauses = array( );

        $clauses[] = "civicrm_event.event_type_id =  '1'";
        $clauses[] = "((DateDiff(NOW(),civicrm_mtl_invoice.date_due) > 0) OR (civicrm_mtl_invoice.date_created IS NULL))";
        $clauses[] = "NOT EXISTS (SELECT contact_id FROM civicrm_group_contact WHERE civicrm_group_contact.group_id = 40 AND civicrm_group_contact.status = 'Added' AND civicrm_group_contact.contact_id = civicrm_participant.contact_id)";
                
        if ($include_invoiced == 1) {
          $clauses[] = "EXISTS (SELECT contact_id FROM civicrm_contribution WHERE civicrm_contribution.contribution_status_id = 2 AND civicrm_participant_payment.contribution_id = civicrm_contribution.id)";
        }
        else
        {
          $clauses[] = "civicrm_mtl_invoice.date_created IS NULL";
        }
        
        return implode( ' AND ', $clauses );
        
    }

    function having( $includeContactIDs = false ) {
    /*
        $clauses = array( );
        $min = CRM_Utils_Array::value( 'min_amount', $this->_formValues );
        if ( $min ) {
            $min = CRM_Utils_Rule::cleanMoney( $min );
            $clauses[] = "sum(contrib.total_amount) >= $min";
        }

        $max = CRM_Utils_Array::value( 'max_amount', $this->_formValues );
        if ( $max ) {
            $max = CRM_Utils_Rule::cleanMoney( $max );
            $clauses[] = "sum(contrib.total_amount) <= $max";
        }

        return implode( ' AND ', $clauses );
        */
        return;
    }

    /* 
     * Functions below generally don't need to be modified
     */
    function count( ) {
           $sql = $this->all( );
           
           $dao = CRM_Core_DAO::executeQuery( $sql,
                                             CRM_Core_DAO::$_nullArray );
           return $dao->N;
    }
       
    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) { 
        return $this->all( $offset, $rowcount, $sort, false, true );
    }
       
    function &columns( ) {
        return $this->_columns;
    }

   function setTitle( $title ) {
       if ( $title ) {
           CRM_Utils_System::setTitle( $title );
       } else {
           CRM_Utils_System::setTitle(ts('Search'));
       }
   }

   function summary( ) {
       return null;
   }

}


