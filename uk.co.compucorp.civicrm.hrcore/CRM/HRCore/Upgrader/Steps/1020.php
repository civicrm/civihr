<?php

trait CRM_HRCore_Upgrader_Steps_1020 {

  /**
   * Rename Line Manager Relationship
   */
  public function upgrade_1020() {
    $this->up1020_renameLineManagerRelationship();

    return TRUE;
  }

  /**
   * Renaming Line Manager Relationship
   */
  private function up1020_renameLineManagerRelationship() {
    $relationshipType = civicrm_api3('RelationshipType', 'get', [
      'name_b_a' => 'Line Manager',
    ]);
    if (empty($relationshipType['values'])) {
      return;
    }
    civicrm_api3('RelationshipType', 'create', [
      'id' => $relationshipType['id'],
      'label_a_b' => 'Is Line Managed by',
      'label_b_a' => 'Is Line Manager of',
    ]);
  }

}