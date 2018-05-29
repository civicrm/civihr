<?php

trait CRM_HRLeaveAndAbsences_ACL_LeaveInformationTrait {

  use CRM_HRLeaveAndAbsences_Service_SettingsManagerTrait;

  /**
   * This method creates ACL clause that can be added to queries in order to
   * filter results to follow leave request ACL rules.
   *
   * @return string
   *   Query String
   */
  public function getLeaveInformationACLClauses() {
    $query = "IN ({$this->getLeaveInformationACLQuery()})";

    return $query;
  }

  /**
   * For any leave information (Entitlement, Leave Requests etc) the access
   * rules are:
   *   - Staff members can only see their own information
   *   - Managers can see their own information + the information from their
   *     managees
   *
   * This method creates an ACL query that selects contact IDs that the currently
   * logged in contact has access to based on these rules, i.e
   *
   * The manager > managee relationship is determined by checking if there's an
   * active relationship between the two contacts and that the type of this
   * relationship is one of those configured as a "Leave Approver Relationship
   * Type" on the extension's General Settings.
   *
   * @return string
   *   Query String
   */
  public function getLeaveInformationACLQuery() {
    $contactsTable = CRM_Contact_BAO_Contact::getTableName();
    $relationshipTable = CRM_Contact_BAO_Relationship::getTableName();
    $relationshipTypeTable = CRM_Contact_BAO_RelationshipType::getTableName();

    $conditions = $this->getLeaveInformationACLWhereConditions('c.id');

    $query = "
    SELECT c.id
    FROM {$contactsTable} c
    LEFT JOIN {$relationshipTable} r ON c.id = r.contact_id_a
    LEFT JOIN {$relationshipTypeTable} rt ON rt.id = r.relationship_type_id
    WHERE $conditions";

    return $query;
  }

  /**
   * Builds the conditions that will be used in the WHERE part of the
   * ACL clause created by getLeaveInformationACLClauses.
   *
   * These conditions are supposed to work in different SELECT queries, so it's
   * possible to pass the name/alias of the field of the contact ID. For
   * example, if the query is based on Leave Requests (that is, we want leave
   * requests linked to a given contact), then we can pass something like
   * leave_request.contact_id to $contactIDField.
   *
   * @param string $contactIDField
   *
   * @return string
   */
  public function getLeaveInformationACLWhereConditions($contactIDField) {
    $loggedInUserID = (int) CRM_Core_Session::getLoggedInContactID();

    $conditions = [];
    $conditions[] = "({$contactIDField} = {$loggedInUserID})";

    $whereClause = $this->getLeaveApproverRelationshipWhereClause();
    if($whereClause) {
      $conditions[] = $whereClause;
    }

    $conditions = implode(' OR ', $conditions);

    return $conditions;
  }

  /**
   * Build the where clause to filter entities based on an existing active
   * relationship of the current logged in user with another contact, where the
   * logged in user is a leave approver.
   *
   * It assumes this will be added to a query where there are joins with the
   * civicrm_relationship and civicrm_relationship_type tables, where the alias
   * are r and rt, respectively.
   *
   * @return string
   */
  private function getLeaveApproverRelationshipWhereClause() {
    $leaveApproverRelationships = $this->getLeaveApproverRelationshipsTypes();
    $loggedInUserID = (int) CRM_Core_Session::getLoggedInContactID();

    $clause = [];
    if (!empty($leaveApproverRelationships)) {
      $clause = $this->activeLeaveManagerCondition();
      $clause[] = "r.contact_id_b = {$loggedInUserID}";
      $clause = "(" . implode(' AND ', $clause) . ")";
    }

    return $clause;
  }

  /**
   * Returns the conditions needed to add to the Where clause for
   * contacts that have active leave managers
   *
   * @return array
   */
  public function activeLeaveManagerCondition() {
    $today = date('Y-m-d');
    $leaveApproverRelationshipTypes = $this->getLeaveApproverRelationshipsTypesForWhereIn();

    $conditions = [];
    $conditions[] = 'rt.is_active = 1';
    $conditions[] = 'rt.id IN(' . implode(',', $leaveApproverRelationshipTypes) . ')';
    $conditions[] = 'r.is_active = 1';
    $conditions[] = "(r.start_date IS NULL OR r.start_date <= '$today')";
    $conditions[] = "(r.end_date IS NULL OR r.end_date >= '$today')";

    return $conditions;
  }
}
