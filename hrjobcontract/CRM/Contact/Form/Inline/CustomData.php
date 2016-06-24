<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
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
 * @copyright CiviCRM LLC (c) 2004-2014
 * $Id$
 *
 */

/**
 * form helper class for custom data section
 */
class CRM_Contact_Form_Inline_CustomData extends CRM_Contact_Form_Inline {

  /**
   * custom group id
   *
   * @int
   * @access public
   */
  public $_groupID;

  /**
   * entity type of the table id
   *
   * @var string
   */
  protected $_entityType;

  /**
   * call preprocess
   */
  public function preProcess() {
    parent::preProcess();

    $this->_groupID = CRM_Utils_Request::retrieve('groupID', 'Positive', $this, TRUE, NULL, $_REQUEST);
    $this->assign('customGroupId', $this->_groupID);
    $customRecId = CRM_Utils_Request::retrieve('customRecId', 'Positive', $this, FALSE, 1, $_REQUEST);
    $cgcount = CRM_Utils_Request::retrieve('cgcount', 'Positive', $this, FALSE, 1, $_REQUEST);
    $subType = CRM_Contact_BAO_Contact::getContactSubType($this->_contactId, ',');
    CRM_Custom_Form_CustomData::preProcess($this, null, $subType, $cgcount,
      $this->_contactType, $this->_contactId);
  }

  /**
   * build the form elements for custom data
   *
   * @return void
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
    CRM_Custom_Form_CustomData::buildQuickForm($this);
  }

  /**
   * set defaults for the form
   *
   * @return array
   * @access public
   */
  public function setDefaultValues() {
    return CRM_Custom_Form_CustomData::setDefaultValues($this);
  }

  /**
   * process the form
   *
   * @return void
   * @access public
   */
  public function postProcess() {
    // Process / save custom data
    // Get the form values and groupTree
    $params = $this->controller->exportValues($this->_name);
    CRM_Core_BAO_CustomValueTable::postProcess($params,
      'civicrm_contact',
      $this->_contactId,
      $this->_entityType
    );

    $this->log();

    // reset the group contact cache for this group
    CRM_Contact_BAO_GroupContactCache::remove();
    
    $customGroupResult = civicrm_api3('CustomGroup', 'get', array(
        'id' => $this->_groupID,
        'sequential' => 1,
    ));
    if ($customGroupResult['count']) {
        $customGroup = CRM_Utils_Array::first($customGroupResult['values']);
        if ($customGroup['name'] === 'HRJobContract_Summary') {
            $jobContractsResult = civicrm_api3('HRJobContract', 'get', array(
                'sequential' => 1,
                'contact_id' => $this->_contactId,
                'return' => "period_start_date,period_end_date",
            ));
            foreach ($jobContractsResult['values'] as $jobContract) {
                if ($jobContract['is_current'] && !$jobContract['deleted']) {
                    $jobContractSummaryDates = $this->_getJobContractSummaryDates($this->_groupTree[$this->_groupID]['fields'], $params);
                    $createParams = array(
                        'sequential' => 1,
                        'jobcontract_id' => $jobContract['id'],
                    );
                    if (empty($jobContract['period_start_date']) && !empty($jobContractSummaryDates['startDate'])) {
                        $createParams['period_start_date'] = $jobContractSummaryDates['startDate'];
                    }
                    if (
                        (
                            empty($jobContract['period_end_date']) ||
                            ($jobContract['period_end_date'] > $jobContractSummaryDates['endDate'])
                        )
                        && !empty($jobContractSummaryDates['endDate'])
                       ) {
                        $createParams['period_end_date'] = $jobContractSummaryDates['endDate'];
                    }
                    $result = civicrm_api3('HRJobDetails', 'create', $createParams);
                }
            }
        }
    }
    
    $this->response();
  }
  
  protected function _getJobContractSummaryDates($fields, $params) {
      $startDate = null;
      $endDate = null;
      
      foreach ($fields as $field) {
          if (isset($field['column_name']) && $field['column_name'] === 'initial_join_date_56') {
              $startDate = $params[$field['element_name']];
              continue;
          }
          if (isset($field['column_name']) && $field['column_name'] === 'final_termination_date_57') {
              $endDate = $params[$field['element_name']];
              continue;
          }
      }
      
      return array(
          'startDate' => $startDate ? date('Y-m-d', strtotime($startDate)) : null,
          'endDate' => $endDate ? date('Y-m-d', strtotime($endDate)) : null,
      );
  }
}
