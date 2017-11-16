<?php

class CRM_HRCore_HookListener_ObjectBased_Form_Contact extends CRM_HRCore_HookListener_ObjectBased_ObjectBasedListener {

  protected $objectClass = 'CRM_Contact_Form_Contact';

  public function onAlterContent(&$content) {
    if (!$this->canHandle()) {
      return;
    }

    $content .="<script type=\"text/javascript\">
      CRM.$(function($) {
        if(!$('#internal_identifier_display').val()){
          $('#first_name').keyup(function() {
            var value = $( this ).val();
            $('#nick_name').val(value);
            });
        }
      });
    </script>";
  }

  public function onBuildForm() {
    if (!$this->canHandle()) {
      return;
    }

    CRM_Core_Resources::singleton()->addSetting(array('formName' => 'contactForm'));

    $phoneIndex = 2;

    if ($this->isPhoneEmpty($phoneIndex)) {
      $this->setPhoneTypeAsMobile($phoneIndex);
      $this->setPhoneLocationToTheDefaultLocation($phoneIndex);
    }
  }

  public function onPostProcess() {
    if (!$this->canHandle()) {
      return;
    }

    $isEnabled = $this->isExtensionEnabled('org.civicrm.hrident');

    if ($isEnabled && !empty($this->object->_submitValues['GovernmentId']) && $this->object->_contactType == 'Individual') {
      $govFieldId = CRM_HRIdent_Page_HRIdent::retreiveContactFieldId('Identify');
      $govFieldIds = CRM_HRIdent_Page_HRIdent::retreiveContactFieldValue($this->object->_contactId);

      if (!empty($govFieldId)) {
        if (empty($govFieldIds)) {
          $govFieldIds['id'] = NULL;
        }

        civicrm_api3('CustomValue', 'create', [
          "custom_{$govFieldId['Type']}{$govFieldIds['id']}" => $this->object->_submitValues['govTypeOptions'],
          "custom_{$govFieldId['Number']}{$govFieldIds['id']}" => $this->object->_submitValues['GovernmentId'],
          "custom_{$govFieldId['is_government']}{$govFieldIds['id']}" => 1,
          "entity_id" => $this->object->_contactId,
        ]);
      }
    }
  }

  /**
   * Returns if the contact form has a phone with the given index and it's empty
   *
   * @param int $phoneIndex
   *  The index of phone in the contact form
   *
   * @return bool
   */
  private function isPhoneEmpty($phoneIndex) {
    return $this->object->elementExists("phone[{$phoneIndex}][phone]") &&
      empty($this->object->getElementValue("phone[{$phoneIndex}][phone]"));
  }

  /**
   * Sets the phone type of the phone with the given index as 'Mobile'.
   *
   * @param $phoneIndex
   *  The index of phone in the contact form
   */
  private function setPhoneTypeAsMobile($phoneIndex) {
    $this->setPhoneType($phoneIndex, 'Mobile');
  }

  /**
   * Sets the location type of the phone with the given index to the default
   * location type.
   *
   * @param int $phoneIndex
   *  The index of phone in the contact form
   */
  private function setPhoneLocationToTheDefaultLocation($phoneIndex) {
    $locationId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_LocationType', 1, 'id', 'is_default');

    if ($locationId) {
      $this->object->setDefaults([
        "phone[{$phoneIndex}][location_type_id]" => $locationId
      ]);
    }
  }

  /**
   * Sets the phone type of the phone with the given index to the type given by
   * $type.
   *
   * @param int $phoneIndex
   *   The index of phone in the contact form
   * @param string $type
   *   The new phone type. Valid values are those from the phone_type option list
   */
  private function setPhoneType($phoneIndex, $type) {
    $elementName = "phone[{$phoneIndex}][phone_type_id]";

    if(!$this->object->elementExists($elementName)) {
      return;
    }

    $phoneType  = $this->object->getElement($elementName);
    $phoneValue = CRM_Core_OptionGroup::values('phone_type');
    $phoneKey   = CRM_Utils_Array::key($type, $phoneValue);

    if($phoneKey) {
      $phoneType->setSelected($phoneKey);
    }
  }
}
