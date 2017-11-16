<?php

class CRM_HRCore_HookListener_ObjectBased_Page_ContactDashboard extends CRM_HRCore_HookListener_ObjectBased_ObjectBasedListener {

  protected $objectClass = 'CRM_Contact_Page_DashBoard';

  public function onPageRun() {
    if (!$this->canHandle()) {
      return;
    }

    CRM_Utils_System::setTitle(ts('CiviHR Home'));
  }
}
