<?php

require_once 'hrcase.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function hrcase_civicrm_config(&$config) {
  _hrcase_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function hrcase_civicrm_xmlMenu(&$files) {
  _hrcase_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function hrcase_civicrm_install() {
  $sql = "INSERT INTO `civicrm_relationship_type`
(`name_a_b`, `label_a_b`, `name_b_a`, `label_b_a`, `description`, `contact_type_a`, `contact_type_b`, `contact_sub_type_a`, `contact_sub_type_b`, `is_reserved`, `is_active`)
VALUES
('HR Manager is','HR Manager is','HR Manager','HR Manager','HR Manager','Individual','Individual',NULL,NULL,0,1),
('Line Manager is','Line Manager is','Line Manager','Line Manager','Line Manager','Individual','Individual',NULL,NULL,0,1)";
  CRM_Core_DAO::executeQuery($sql);

  $sql = "SELECT count(id) as count FROM `civicrm_relationship_type` WHERE `name_b_a`='Recruiting Manager'";
  $dao = CRM_Core_DAO::executeQuery($sql);
  while($dao->fetch()) {
    if ($dao->count == 0) {
      $sql = "INSERT INTO `civicrm_relationship_type`
    (`name_a_b`, `label_a_b`, `name_b_a`, `label_b_a`, `description`, `contact_type_a`, `contact_type_b`, `contact_sub_type_a`, `contact_sub_type_b`, `is_reserved`, `is_active`)
    VALUES
    ('Recruiting Manager is','Recruiting Manager is','Recruiting Manager','Recruiting Manager','Recruiting Manager','Individual','Individual',NULL,NULL,0,1)";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  return _hrcase_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_postInstall
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */

function hrcase_civicrm_postInstall() {
  //disable example case types
  hrcase_example_caseType(FALSE);

  // Import custom group
  require_once 'CRM/Utils/Migrate/Import.php';
  $import = new CRM_Utils_Migrate_Import();

  $files = glob(__DIR__ . '/xml/*_customGroupCaseType.xml');
  if (is_array($files)) {
    foreach ($files as $file) {
      $import->run($file);
    }
  }
  $scheduleActions = hrcase_getActionsSchedule();
  foreach($scheduleActions as $actionName=>$scheduleAction) {
  	$result = civicrm_api3('action_schedule', 'get', array('name' => $actionName));
  	if (empty($result['id'])) {
  	  $result = civicrm_api3('action_schedule', 'create', $scheduleAction);
  	}
  }
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function hrcase_civicrm_uninstall() {
  $scheduleActions = hrcase_getActionsSchedule(TRUE);
  $scheduleAction = implode("','",$scheduleActions );
  CRM_Core_DAO::executeQuery("DELETE FROM civicrm_action_schedule WHERE name IN ('{$scheduleAction}')");
  $sql = "DELETE FROM civicrm_option_value WHERE name IN ('Issue appointment letter','Fill Employee Details Form','Submission of ID/Residence proofs and photos','Program and work induction by program supervisor','Enter employee data in CiviHR','Group Orientation to organization values policies','Probation appraisal','Conduct appraisal','Collection of appraisal paperwork','Issue confirmation/warning letter','Get \"No Dues\" certification','Conduct Exit interview','Revoke access to databases','Block work email ID','Follow up on progress','Collection of Appraisal forms','Issue extension letter','Schedule joining date','Group Orientation to organization, values, policies','Probation appraisal (start probation workflow)','Schedule Exit Interview','Prepare formats','Print formats','Collate and print goals','Background_Check','References Check','Prepare and email schedule')";
  CRM_Core_DAO::executeQuery($sql);

  hrcase_example_caseType(TRUE);
  //delete custom group and custom field
  foreach (array('Joining_Data', 'Exiting_Data') as $cgName) {
    $customGroup = civicrm_api3('CustomGroup', 'getsingle', array('return' => "id",'name' => $cgName));
    civicrm_api3('CustomGroup', 'delete', array('id' => $customGroup['id']));
  }
  return _hrcase_civix_civicrm_uninstall();
}

/**
 * Enable/Disable example case type
 */
function hrcase_example_caseType($is_active) {
  $isActive = $is_active ? 1 : 0;
  $sql = "Update civicrm_case_type SET is_active = {$isActive} where name IN ('AdultDayCareReferral', 'HousingSupport', 'adult_day_care_referral', 'housing_support')";
  CRM_Core_DAO::executeQuery($sql);
  CRM_Core_BAO_Navigation::resetNavigation();
}

/**
 * Implementation of hook_civicrm_enable
 */
function hrcase_civicrm_enable() {
  _hrcase_setActiveFields(1);
  return _hrcase_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function hrcase_civicrm_disable() {
  _hrcase_setActiveFields(0);
  return _hrcase_civix_civicrm_disable();
}

function _hrcase_setActiveFields($setActive) {
  //disable/enable all custom group and fields
  $sql = "UPDATE civicrm_custom_field JOIN civicrm_custom_group
ON civicrm_custom_group.id = civicrm_custom_field.custom_group_id
SET civicrm_custom_field.is_active = {$setActive}
WHERE civicrm_custom_group.name IN ('Joining_Data', 'Exiting_Data')";

  CRM_Core_DAO::executeQuery($sql);
  CRM_Core_DAO::executeQuery("UPDATE civicrm_custom_group SET is_active = {$setActive} WHERE name IN ('Joining_Data', 'Exiting_Data')");

  //disable/enable activity type
  $query = "UPDATE civicrm_option_value
SET is_active = {$setActive}
WHERE name IN ('Attach Probation Notification', 'Attach Appraisal Document', 'Attach Objectives Document', 'Attach Signed Job Contract', 'Attach Draft Job Contract', 'Attach Reference', 'Attach Offer Letter', 'Attach Application Documents', 'Exit Interview', 'Send Termination Letter','Issue appointment letter','Fill Employee Details Form','Submission of ID/Residence proofs and photos','Program and work induction by program supervisor','Enter employee data in CiviHR','Group Orientation to organization values policies','Probation appraisal','Conduct appraisal','Collection of appraisal paperwork','Issue confirmation/warning letter','Get \"No Dues\" certification','Conduct Exit interview','Revoke access to databases','Block work email ID','Follow up on progress','Collection of Appraisal forms','Issue extension letter','Schedule joining date','Group Orientation to organization, values, policies','Probation appraisal (start probation workflow)','Schedule Exit Interview','Prepare formats','Print formats','Collate and print goals','Background_Check','References Check','Prepare and email schedule')";

  CRM_Core_DAO::executeQuery($query);

  //disable/enable action schedule
  $scheduleActions = hrcase_getActionsSchedule(TRUE);
  $scheduleAction = implode("','",$scheduleActions );
  $query = "UPDATE civicrm_action_schedule SET is_active = {$setActive} WHERE name IN ('{$scheduleAction}')";
  CRM_Core_DAO::executeQuery($query);

  $sqlrel = "UPDATE `civicrm_relationship_type` SET is_active={$setActive} WHERE name_b_a IN ('HR Manager','Line Manager')";
  CRM_Core_DAO::executeQuery($sqlrel);
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
function hrcase_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _hrcase_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function hrcase_civicrm_managed(&$entities) {
  return _hrcase_civix_civicrm_managed($entities);
}

function hrcase_civicrm_buildForm($formName, &$form) {
  if ($form instanceof CRM_Case_Form_Activity OR $form instanceof CRM_Case_Form_Case OR $form instanceof CRM_Case_Form_CaseView) {
    $optionID = CRM_Core_BAO_OptionValue::getOptionValuesAssocArrayFromName('activity_status');
    $completed = array_search( 'Completed', $optionID );
    CRM_Core_Resources::singleton()->addSetting(array(
      'hrcase' => array(
        'statusID' => $completed,
      ),
    ));
    if( $form instanceof CRM_Case_Form_CaseView ) {
    CRM_Core_Resources::singleton()->addSetting(array(
      'hrcase' => array(
        'manageScreen' => 1,
      ),
    ));
    }
    CRM_Core_Resources::singleton()->addScriptFile('org.civicrm.hrcase', 'js/hrcase.js');
  }
}

function hrcase_civicrm_navigationMenu(&$params) {
  // process only if civiCase is enabled
  if (!array_key_exists('CiviCase', CRM_Core_Component::getEnabledComponents())) {
    return;
  }
  $values = array();
  $caseMenuItems = array();

  // the parent menu
  $referenceMenuItem['name'] = 'New Case';
  CRM_Core_BAO_Navigation::retrieve($referenceMenuItem, $values);

  if (!empty($values)) {
    // fetch all the case types
    $caseTypes = CRM_Case_PseudoConstant::caseType();
    $appValue = array_search('Application', $caseTypes);
    unset($caseTypes[$appValue]);

    $parentId = $values['id'];
    $maxKey = (max(array_keys($params)));

    // now create nav menu items
    if (!empty($caseTypes)) {
      foreach ($caseTypes as $cTypeId => $caseTypeName) {
        $maxKey = $maxKey + 1;
        $caseMenuItems[$maxKey] = array(
          'attributes' => array(
            'label'      => "New {$caseTypeName}",
            'name'       => "New {$caseTypeName}",
            'url'        => $values['url'] . "&ctype={$cTypeId}",
            'permission' => $values['permission'],
            'operator'   => $values['permission_operator'],
            'separator'  => NULL,
            'parentID'   => $parentId,
            'navID'      => $maxKey,
            'active'     => 1
          )
        );
      }
    }
    if (!empty($caseMenuItems)) {
      $params[$values['parent_id']]['child'][$values['id']]['child'] = $caseMenuItems;
    }
  }
}

/**
 * Implementation of hook_civicrm_alterContent
 *
 * @return void
 */
function hrcase_civicrm_alterContent( &$content, $context, $tplName, &$object ) {
  if ($context == "form" && $tplName == "CRM/Case/Form/Case.tpl" ) {
    $content .="<script type=\"text/javascript\">
      CRM.$(function($) {
        if ($('#activity_subject').val().length < 1)
          $('#activity_subject').val($( '#case_type_id option:selected').html());

        $('#case_type_id').on('change', function() {
          $('#activity_subject').val($('#case_type_id option:selected').html());
        });
      });
    </script>";
  }
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function hrcase_civicrm_caseTypes(&$caseTypes) {
  _hrcase_civix_civicrm_caseTypes($caseTypes);
}

function hrcase_getActionsSchedule($getNamesOnly = FALSE) {
  $actionsForActivities = array(
    'Issue_appointment_letter' => 'Issue appointment letter',
    'Fill_Employee_Details_Form' => 'Fill Employee Details Form',
    'Submission_of_ID/Residence_proofs_and_photos' => 'Submission of ID/Residence proofs and photos',
    'Program_and_work_induction_by_program_supervisor' => 'Program and work induction by program supervisor',
    'Enter_employee_data_in_CiviHR' => 'Enter employee data in CiviHR',
    'Group_Orientation_to_organization_values_policies' => 'Group Orientation to organization, values, policies',
    'Probation_appraisal' => 'Probation appraisal (start probation workflow)',
    'Conduct_appraisal' => 'Conduct appraisal',
    'Collection_of_appraisal_paperwork' => 'Collection of appraisal paperwork',
    'Issue_confirmation/warning_letter' => 'Issue confirmation/warning letter',
    'Get_"No Dues"_certification' => 'Get "No Dues" certification',
    'Conduct_Exit_interview' => 'Conduct Exit interview',
    'Revoke_access_to_databases' => 'Revoke access to databases',
    'Block_work_email_ID' => 'Block work email ID',
    'Follow_up_on_progress' => 'Follow up on progress',
    'Collection_of_Appraisal_forms' => 'Collection of Appraisal forms',
    'Issue_extension_letter' => 'Issue extension letter',
  );
  if ($getNamesOnly) {
    return array_keys($actionsForActivities);
  }
  $schedules = array();
  $activityContacts = CRM_Core_OptionGroup::values('activity_contacts', FALSE, FALSE, FALSE, NULL, 'name');
  $assigneeID = CRM_Utils_Array::key('Activity Assignees', $activityContacts);
  $targetID = CRM_Utils_Array::key('Activity Targets', $activityContacts);
  $scheduledStatus = CRM_Core_OptionGroup::getValue('activity_status', 'Scheduled', 'name');
  $mappingId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionMapping', 'activity_type', 'id', 'entity_value');
  // looping to build schedule params
  foreach ($actionsForActivities as $reminderName => $activityType) {
    $activityTypeId = CRM_Core_OptionGroup::getValue('activity_type', $activityType, 'name');
    if (!empty($activityTypeId)) {
      $reminderTitle = str_replace('_', ' ', $reminderName);
      $schedules[$reminderName] = array(
        'name' => $reminderName,
        'title' => $reminderTitle,
        'recipient' => $assigneeID,
        'limit_to' => 1,
        'entity_value' => $activityTypeId,
        'entity_status' => $scheduledStatus,
        'is_active' => 1,
        'record_activity' => 1,
        'mapping_id' => $mappingId,
      );
      if  ($reminderName == 'Issue_appointment_letter') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Issue appointment letter on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder to Issue appointment letter';
      }
      elseif ($reminderName == 'Fill_Employee_Details_Form') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Fill Employee Details Form on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder to Fill Employee Details Form';
      }
      elseif ($reminderName == 'Submission_of_ID/Residence_proofs_and_photos') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Submission of ID/Residence proofs and photos on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder to Submit ID/Residence proofs and photos';
      }
      elseif ($reminderName == 'Program_and_work_induction_by_program_supervisor') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Program and work induction by program supervisor on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder for Program and work induction by program supervisor';
      }
      elseif ($reminderName == 'Enter_employee_data_in_CiviHR') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Enter employee data in CiviHR on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder to Enter employee data in CiviHR';
      }
      elseif ($reminderName == 'Group_Orientation_to_organization_values_policies') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Group Orientation to organization values policies on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder for Group Orientation to organization values policies';
      }
      elseif ($reminderName == 'Probation_appraisal') {
      	$schedules[$reminderName]['recipient'] = $targetID;
      	$schedules[$reminderName]['start_action_offset'] = 2;
      	$schedules[$reminderName]['start_action_unit'] = 'week';
      	$schedules[$reminderName]['start_action_condition'] = 'before';
      	$schedules[$reminderName]['start_action_date'] = 'activity_date_time';
      	$schedules[$reminderName]['is_repeat'] = 1;
      	$schedules[$reminderName]['repetition_frequency_unit'] = 'week';
      	$schedules[$reminderName]['repetition_frequency_interval'] = 1;
      	$schedules[$reminderName]['end_frequency_unit'] = 'month';
      	$schedules[$reminderName]['end_frequency_interval'] = 2;
      	$schedules[$reminderName]['end_action'] = 'after';
      	$schedules[$reminderName]['end_date'] = 'activity_date_time';
      	$schedules[$reminderName]['body_html'] = '<p>Probation appraisal on {activity.activity_date_time}</p>';
      	$schedules[$reminderName]['subject'] = 'Reminder for Probation appraisal';
      }
      elseif ($reminderName == 'Conduct_appraisal') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Conduct appraisal on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Conduct appraisal';
      }
      elseif ($reminderName == 'Collection_of_appraisal_paperwork') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Collection of appraisal paperwork on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder for Collection of appraisal paperwork';
      }
      elseif ($reminderName == 'Issue_confirmation/warning_letter') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Issue confirmation/warning letter on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Issue confirmation/warning letter';
      }
      elseif ($reminderName == 'Get_"No Dues"_certification') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Get "No Dues" certification on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Get "No Dues" certification';
      }
      elseif ($reminderName == 'Conduct_Exit_interview') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Conduct Exit interview on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Conduct Exit interview';
      }
      elseif ($reminderName == 'Revoke_access_to_databases') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Revoke access to databases on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Revoke access to databases';
      }
      elseif ($reminderName == 'Block_work_email_ID') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Block work email ID on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Block work email ID';
      }
      elseif ($reminderName == 'Follow_up_on_progress') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Follow up on progress on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Follow up on progress';
      }
      elseif ($reminderName == 'Collection_of_Appraisal_forms') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Collection of Appraisal forms on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Collect the Appraisal forms';
      }
      elseif ($reminderName == 'Issue_extension_letter') {
        $schedules[$reminderName]['recipient'] = $targetID;
        $schedules[$reminderName]['start_action_offset'] = 2;
        $schedules[$reminderName]['start_action_unit'] = 'week';
        $schedules[$reminderName]['start_action_condition'] = 'before';
        $schedules[$reminderName]['start_action_date'] = 'activity_date_time';
        $schedules[$reminderName]['is_repeat'] = 1;
        $schedules[$reminderName]['repetition_frequency_unit'] = 'week';
        $schedules[$reminderName]['repetition_frequency_interval'] = 1;
        $schedules[$reminderName]['end_frequency_unit'] = 'month';
        $schedules[$reminderName]['end_frequency_interval'] = 2;
        $schedules[$reminderName]['end_action'] = 'after';
        $schedules[$reminderName]['end_date'] = 'activity_date_time';
        $schedules[$reminderName]['body_html'] = '<p>Issue extension letter on {activity.activity_date_time}</p>';
        $schedules[$reminderName]['subject'] = 'Reminder to Issue extension letter';
      }
    }
  }
  return $schedules;
}
