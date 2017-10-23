<?php

/**
 * CRM_HRLeaveAndAbsences_Page_Dashboard
 */
class CRM_HRLeaveAndAbsences_Page_Dashboard extends CRM_Core_Page {
  public function run() {
    CRM_Utils_System::setTitle(ts('Dashboard'));

    CRM_Core_Resources::singleton()
      ->addPermissions([
        'access leave and absences',
        'administer leave and absences',
        'access leave and absences in ssp',
        'manage leave and absences in ssp',
      ])
      ->addStyleFile('uk.co.compucorp.civicrm.hrleaveandabsences', 'css/leaveandabsence.css')
      ->addScriptFile('uk.co.compucorp.civicrm.hrleaveandabsences', 'js/angular/dist/admin-dashboard.min.js', 1010)
      ->addVars('leaveAndAbsences', [
        'baseURL' => CRM_Core_Resources::singleton()->getUrl('uk.co.compucorp.civicrm.hrleaveandabsences'),
        'loggedInUserId' => CRM_Core_Session::getLoggedInContactID(),
        'attachmentToken' => CRM_Core_Page_AJAX_Attachment::createToken()
      ]);

    parent::run();
  }
}
