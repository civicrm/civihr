<?php

trait CRM_HRCore_Upgrader_Steps_1016 {

  /**
   * Disables relationship types that are not necessary for CiviHR and
   * removes existing relationships belonging to these types.
   */
  public function upgrade_1016() {
    $relationshipTypesToBeDisabled = $this->up1016_getRelationshipTypesToBeDisabled();

    $this->up1016_disableRelationshipTypes($relationshipTypesToBeDisabled);
    $this->up1016_removeRelationshipsForTypes($relationshipTypesToBeDisabled);

    return TRUE;
  }

  /**
   * Returns the Ids of the the relationship types to be disabled.
   *
   * @return int[]
   */
  private function up1016_getRelationshipTypesToBeDisabled() {
    $relationshipsToBeDeleted = [
      'Case Coordinator is',
      'Employee of',
      'Head of Household for',
      'Household member of'
    ];

    $result = civicrm_api3('RelationshipType', 'get', [
      'name_a_b' => [ 'IN' => $relationshipsToBeDeleted ]
    ]);

    return array_column($result['values'], 'id');
  }

  /**
   * Disables relationship types.
   *
   * @param int[] $relationshipTypeIds
   */
  private function up1016_disableRelationshipTypes($relationshipTypeIds) {
    foreach ($relationshipTypeIds as $relationshipTypeId) {
      civicrm_api3('RelationshipType', 'create', [
        'id' => $relationshipTypeId,
        'is_active' => 0
      ]);
    }
  }

  /**
   * Removes relationships for the given relationship types.
   *
   * @param int[] $relationshipTypeIds
   */
  private function up1016_removeRelationshipsForTypes($relationshipTypeIds) {
    foreach ($relationshipTypeIds as $relationshipTypeId) {
      $relationships = $this->up1016_getRelationshipsForType($relationshipTypeId);
      $this->up1016_removeRelationships($relationships);
    }
  }

  /**
   * Returns all the relationships associated with a given type.
   *
   * @param int $relationshipTypeId
   *
   * @return array
   */
  private function up1016_getRelationshipsForType($relationshipTypeId) {
    $result = civicrm_api3('Relationship', 'get', [
      'relationship_type_id' => $relationshipTypeId
    ]);

    return $result['values'];
  }

  /**
   * Removes the relationships provided.
   *
   * @param array $relationships
   */
  private function up1016_removeRelationships($relationships) {
    foreach ($relationships as $relationship) {
      civicrm_api3('Relationship', 'delete', [
        'id' => $relationship['id']
      ]);
    }
  }

}
