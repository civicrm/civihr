<?php

require_once 'hrabsence.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function hrabsence_civicrm_config(&$config) {
  _hrabsence_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function hrabsence_civicrm_xmlMenu(&$files) {
  _hrabsence_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function hrabsence_civicrm_install() {
  return _hrabsence_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function hrabsence_civicrm_uninstall() {
  return _hrabsence_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function hrabsence_civicrm_enable() {
  return _hrabsence_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function hrabsence_civicrm_disable() {
  return _hrabsence_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function hrabsence_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _hrabsence_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function hrabsence_civicrm_managed(&$entities) {
  return _hrabsence_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function hrabsence_civicrm_caseTypes(&$caseTypes) {
  _hrabsence_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function myext_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _myext_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_tabs
 */
function hrabsence_civicrm_tabs(&$tabs, $contactID) {
  CRM_HRAbsence_Page_EmployeeAbsencePage::registerResources($contactID);
  $tabs[] = array(
    'id'    => 'absenceTab',
    'url'   =>  CRM_Utils_System::url( 'civicrm/absences', array(
      'cid' => $contactID,
      'snippet' => 1,
    )),
    'title' => 'Absences',
    'weight' => 300 
  );
}

/**
 * Implementation of hook_civicrm_entityTypes
 */
function hrabsence_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = array(
    'name' => 'HRAbsenceType',
    'class' => 'CRM_HRAbsence_DAO_HRAbsenceType',
    'table' => 'civicrm_absence_type',
  );
   $entityTypes[] = array(
    'name' => 'HRAbsencePeriod',
    'class' => 'CRM_HRAbsence_DAO_HRAbsencePeriod',
    'table' => 'civicrm_absence_period',
  );
  $entityTypes[] = array(
    'name' => 'HRAbsenceEntitlement',
    'class' => 'CRM_HRAbsence_DAO_HRAbsenceEntitlement',
    'table' => 'civicrm_absence_entitlement',
  );
}

/**
 * Implementation of hook_civicrm_alterFilters
 *
 * @param array $wrappers list of API_Wrapper instances
 * @param array $apiRequest
 */
function hrabsence_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if (strtolower($apiRequest['entity']) == 'activity' && strtolower($apiRequest['action']) == 'get') {
    $wrappers[] = new CRM_HRAbsence_AbsenceRangeOption();
  }
}


function hrabsence_civicrm_navigationMenu( &$params ) {
  //  Get the maximum key of $params
  $maxKey = ( max( array_keys($params) ) );
  $params[$maxKey+1] = array (
    'attributes' => array (
      'label'      => 'Absences',
      'name'       => 'absences',
      'url'        => null,
      'permission' => 'access HRAbsences', 
      'operator'   => null,
      'separator'  => null,
      'parentID'   => null,
      'navID'      => $maxKey+1,
      'active'     => 1
    ),
    'child' =>  array (
      $maxKey+2 => array (
        'attributes' => array (
          'label'      => 'Absences',
          'name'       => 'absences',
          'url'        => null,
          'permission' => 'access HRAbsences',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      ) ,
      $maxKey+3 => array (
        'attributes' => array (
          'label'      => 'Calender',
          'name'       => 'calender',
          'url'        => null,
          'permission' => 'access HRAbsences',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ), 
        'child' => null
      ),
      $maxKey+4 => array (
        'attributes' => array (
          'label'      => 'New Absence',
          'name'       => 'newAbsence',
          'url'        => null,
          'permission' => 'access HRAbsences',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
      ) ,
      $maxKey+5 => array (
        'attributes' => array (
          'label'      => 'Absence Report',
          'name'       => 'absenceReport',
          'url'        => 'civicrm/report/list?grp=Absence&reset=1',
          'permission' => 'access HRAbsences',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      ) ,
      $maxKey+6 => array (
        'attributes' => array (
          'label'      => 'Manage Entitlements',
          'name'       => 'manageEntitlements',
          'url'        => null,
          'permission' => 'access HRAbsences',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      ) ,
    ) 
  );

  $absenceType = CRM_HRAbsence_BAO_HRAbsenceType::getActivityTypes();
  if (!empty($absenceType)) {
    $maxKey_child = $maxKey+7;
    foreach ($absenceType as $aTypeId => $absenceTypeName) {
      $maxKey_child = $maxKey_child + 1;
      $absenceMenuItems[$maxKey_child] = array(
        'attributes' => array(
          'label'      => "{$absenceTypeName}",
          'name'       => "{$absenceTypeName}",
          'url'        => "civicrm/absences/set?atype={$aTypeId}&action=add",
          'permission' => 'access HRAbsences',
          'operator'   => NULL,
          'separator'  => NULL,
          'parentID'   => $maxKey+4,
          'navID'      => 1,
          'active'     => 1
        )
      );
    }
  }
  if (!empty($absenceMenuItems)) {
    $params[$maxKey+1]['child'][$maxKey+4]['child'] = $absenceMenuItems;
  }
}