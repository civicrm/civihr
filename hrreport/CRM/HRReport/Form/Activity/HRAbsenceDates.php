<?php

class CRM_HRReport_Form_Activity_HRAbsenceDates extends CRM_Report_Form {
  protected $_selectAliasesTotal = array();

  protected $_customGroupExtends = array(
    'Activity'
  );

  protected $_nonDisplayFields = array();

  function __construct() {
    // There could be multiple contacts. We not clear on which contact id to display.
    // Lets hide it for now.
    $this->_exposeContactID = FALSE;
    $absenceOptionValue = civicrm_api3('OptionValue', 'get', [
      'name' => 'Absence',
      'option_group_id' => 'activity_type',
      'sequential' => 1
    ])['values'][0];

    $this->absenceActivityType = [$absenceOptionValue['value'] => $absenceOptionValue['name']];
    $this->activityTypes = CRM_HRAbsence_BAO_HRAbsenceType::getActivityTypes();
    $this->activityStatus = CRM_HRAbsence_BAO_HRAbsenceType::getActivityStatus();

    $this->_columns = [
      'civicrm_contact' =>
        [
          'dao' => 'CRM_Contact_DAO_Contact',
          'fields' =>
            [
              'contact_target' =>
                [
                  'name' => 'sort_name',
                  'title' => ts('Individual'),
                  'alias' => 'civicrm_contact_target',
                  'dbAlias' => "civicrm_contact_target.sort_name",
                  'no_display' => TRUE,
                  'default' => TRUE,
                ],
              'contact_assignee' =>
                [
                  'name' => 'sort_name',
                  'title' => ts('Approved By'),
                  'alias' => 'civicrm_contact_assignee',
                  'no_display' => TRUE,
                  'dbAlias' => "civicrm_contact_assignee.sort_name",
                ],
              'contact_source' =>
                [
                  'name' => 'sort_name',
                  'title' => ts('Added By'),
                  'alias' => 'civicrm_contact_source',
                  'no_display' => TRUE,
                  'no_repeat' => TRUE,
                ],
              'contact_source_id' =>
                [
                  'name' => 'id',
                  'alias' => 'civicrm_contact_source',
                  'dbAlias' => "civicrm_contact_source.id",
                  'default' => TRUE,
                  'no_display' => TRUE,
                  'required' => TRUE,
                ],
              'contact_assignee_id' =>
                [
                  'name' => 'id',
                  'alias' => 'civicrm_contact_assignee',
                  'dbAlias' => "civicrm_contact_assignee.id",
                  'no_display' => TRUE,
                  'default' => TRUE,
                  'required' => TRUE,
                ],
              'contact_target_id' =>
                [
                  'name' => 'id',
                  'alias' => 'civicrm_contact_target',
                  'dbAlias' => "civicrm_contact_target.id",
                  'default' => TRUE,
                  'required' => TRUE,
                ],
            ],
          'filters' =>
            [
              'contact_target_id' =>
                [
                  'name' => 'id',
                  'alias' => 'civicrm_contact_target',
                  'dbAlias' => "civicrm_contact_target.id",
                  'title' => ts('Contact ID'),
                  'operator' => 'like',
                  'type' => CRM_Report_Form::OP_STRING,
                ],
            ],
          'grouping' => 'contact-fields',
        ],
      'civicrm_email' =>
        [
          'dao' => 'CRM_Core_DAO_Email',
          'fields' =>
            [
              'contact_target_email' =>
                [
                  'name' => 'email',
                  'title' => ts('Email'),
                  'alias' => 'civicrm_email_target',
                  'no_display' => TRUE,
                ],
              'contact_assignee_email' =>
                [
                  'name' => 'email',
                  'title' => ts('Approved By (Email)'),
                  'alias' => 'civicrm_email_assignee',
                  'no_display' => TRUE,
                ],
              'contact_source_email' =>
                [
                  'name' => 'email',
                  'title' => ts('Added By (Email)'),
                  'alias' => 'civicrm_email_source',
                  'no_display' => TRUE,
                ],
            ]
        ],
      'civicrm_activity' =>
        [
          'dao' => 'CRM_Activity_DAO_Activity',
          'fields' =>
            [
              'id' =>
                [
                  'no_display' => TRUE,
                  'title' => ts('Activity ID'),
                  'required' => TRUE,
                ],
              'source_record_id' =>
                [
                  'title' => ts('Absence ID'),
                  'required' => TRUE
                ],
              'activity_type_id' =>
                [
                  'title' => ts('Absence Type'),
                  'alias' => 'parent_activity',
                  'required' => TRUE,
                  'type' => CRM_Utils_Type::T_STRING,
                ],
              'activity_subject' =>
                [
                  'title' => ts('Subject'),
                  'default' => TRUE,
                  'no_display' => TRUE,
                ],
              'activity_date_time' =>
                [
                  'title' => ts('Absence Date'),
                  'required' => TRUE,
                  'type' => CRM_Utils_Type::T_DATE,
                ],
              'duration' =>
                [
                  'title' => ts('Qty'),
                  'required' => TRUE,
                ],
              'status_id' =>
                [
                  'title' => ts('Status'),
                  'default' => TRUE,
                  'type' => CRM_Utils_Type::T_STRING,
                  'alias' => 'parent_activity',
                  'required' => TRUE,
                ],
              'details' =>
                [
                  'title' => ts('Comments'),
                  'default' => TRUE,
                  'type' => CRM_Utils_Type::T_STRING,
                  'alias' => 'parent_activity',
                  'required' => TRUE,
                ],
            ],
          'filters' =>
            [
              'status_id' =>
                [
                  'title' => ts('Status'),
                  'alias' => 'parent_activity',
                  'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                  'options' => $this->activityStatus,
                  'type' => CRM_Utils_Type::T_STRING,
                ],
            ],
          'order_bys' =>
            [
              'source_record_id' =>
                ['title' => ts('Absence ID'), 'default_weight' => '1', 'dbAlias' => 'civicrm_activity_source_record_id'],
              'activity_date_time' =>
                ['title' => ts('Absence Date'), 'default_weight' => '2', 'dbAlias' => 'civicrm_activity_activity_date_time'],
            ],
          'grouping' => 'activity-fields',
          'alias' => 'activity',
        ],
      'civicrm_activity_contact' =>
        [
          'dao' => 'CRM_Activity_DAO_ActivityContact',
          'fields' =>
            [
              // so we have $this->_alias populated
            ],
        ],
    ];

    $this->_groupFilter = TRUE;
    $this->_tagFilter = TRUE;
    parent::__construct();
  }

  function select($recordType = NULL) {
    if (!array_key_exists("contact_{$recordType}", $this->_params['fields']) && $recordType != 'final') {
      $this->_nonDisplayFields[] = "civicrm_contact_contact_{$recordType}";
      $this->_params['fields']["contact_{$recordType}"] = 1;
    }
    parent::select();

    if ($recordType == 'final' && !empty($this->_nonDisplayFields)) {
      foreach ($this->_nonDisplayFields as $fieldName) {
        unset($this->_columnHeaders[$fieldName]);
      }
    }

    if (empty($this->_selectAliasesTotal)) {
      $this->_selectAliasesTotal = $this->_selectAliases;
    }

    $removeKeys = array();
    if ($recordType == 'target') {
      foreach ($this->_selectClauses as $key => $clause) {
        if (strstr($clause, 'civicrm_contact_assignee.') ||
          strstr($clause, 'civicrm_contact_source.') ||
          strstr($clause, 'civicrm_email_assignee.') ||
          strstr($clause, 'civicrm_email_source.')
        ) {
          $removeKeys[] = $key;
          unset($this->_selectClauses[$key]);
        }
      }
    } else if ($recordType == 'assignee') {
      foreach ($this->_selectClauses as $key => $clause) {
        if (strstr($clause, 'civicrm_contact_target.') ||
          strstr($clause, 'civicrm_contact_source.') ||
          strstr($clause, 'civicrm_email_target.') ||
          strstr($clause, 'civicrm_email_source.')
        ) {
          $removeKeys[] = $key;
          unset($this->_selectClauses[$key]);
        }
      }
    } else if ($recordType == 'source') {
      foreach ($this->_selectClauses as $key => $clause) {
        if (strstr($clause, 'civicrm_contact_target.') ||
          strstr($clause, 'civicrm_contact_assignee.') ||
          strstr($clause, 'civicrm_email_target.') ||
          strstr($clause, 'civicrm_email_assignee.')
        ) {
          $removeKeys[] = $key;
          unset($this->_selectClauses[$key]);
        }
      }
    } else if ($recordType == 'final') {
      $this->_selectClauses = $this->_selectAliasesTotal;
      foreach ($this->_selectClauses as $key => $clause) {
        if (strstr($clause, 'civicrm_contact_contact_target') ||
          strstr($clause, 'civicrm_contact_contact_assignee') ||
          strstr($clause, 'civicrm_contact_contact_source') ) {
          $this->_selectClauses[$key] = "GROUP_CONCAT($clause SEPARATOR ';') as $clause";
        }
      }
    }

    if ($recordType) {
      foreach ($removeKeys as $key) {
        unset($this->_selectAliases[$key]);
      }

      $this->_select = "SELECT " . implode(', ', $this->_selectClauses) . " ";
    }
  }

  function from($recordType) {
    $activityContacts = CRM_Core_OptionGroup::values('activity_contacts', FALSE, FALSE, FALSE, NULL, 'name');
    $assigneeID = CRM_Utils_Array::key('Activity Assignees', $activityContacts);
    $targetID   = CRM_Utils_Array::key('Activity Targets', $activityContacts);
    $sourceID   = CRM_Utils_Array::key('Activity Source', $activityContacts);

    if ($recordType == 'target') {
      $this->_from = "
        FROM civicrm_activity {$this->_aliases['civicrm_activity']}
             INNER JOIN civicrm_activity_contact  {$this->_aliases['civicrm_activity_contact']}
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = {$this->_aliases['civicrm_activity_contact']}.activity_id AND
                       {$this->_aliases['civicrm_activity_contact']}.record_type_id = {$targetID}
             INNER JOIN civicrm_contact civicrm_contact_target
                    ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_contact_target.id
             INNER JOIN civicrm_activity parent_activity 
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = parent_activity.id
             {$this->_aclFrom}";

      if ($this->isTableSelected('civicrm_email')) {
        $this->_from .= "
            LEFT JOIN civicrm_email civicrm_email_target
                   ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_email_target.contact_id AND
                      civicrm_email_target.is_primary = 1";
      }
      $this->_aliases['civicrm_contact'] = 'civicrm_contact_target';
    }

    if ($recordType == 'assignee') {
      $this->_from = "
        FROM civicrm_activity {$this->_aliases['civicrm_activity']}
             INNER JOIN civicrm_activity_contact {$this->_aliases['civicrm_activity_contact']}
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = {$this->_aliases['civicrm_activity_contact']}.activity_id AND
                       {$this->_aliases['civicrm_activity_contact']}.record_type_id = {$assigneeID}
             INNER JOIN civicrm_contact civicrm_contact_assignee
                    ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_contact_assignee.id
             INNER JOIN civicrm_activity parent_activity 
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = parent_activity.id
             {$this->_aclFrom}";

      if ($this->isTableSelected('civicrm_email')) {
        $this->_from .= "
            LEFT JOIN civicrm_email civicrm_email_assignee
                   ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_email_assignee.contact_id AND
                      civicrm_email_assignee.is_primary = 1";
      }
      $this->_aliases['civicrm_contact'] = 'civicrm_contact_assignee';
    }

    if ($recordType == 'source') {
      $this->_from = "
        FROM civicrm_activity {$this->_aliases['civicrm_activity']}
             INNER JOIN civicrm_activity_contact {$this->_aliases['civicrm_activity_contact']}
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = {$this->_aliases['civicrm_activity_contact']}.activity_id AND
                       {$this->_aliases['civicrm_activity_contact']}.record_type_id = {$sourceID}
             INNER JOIN civicrm_contact civicrm_contact_source
                    ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_contact_source.id
             INNER JOIN civicrm_activity parent_activity 
                    ON {$this->_aliases['civicrm_activity']}.source_record_id = parent_activity.id
             {$this->_aclFrom}";

      if ($this->isTableSelected('civicrm_email')) {
        $this->_from .= "
            LEFT JOIN civicrm_email civicrm_email_source
                   ON {$this->_aliases['civicrm_activity_contact']}.contact_id = civicrm_email_source.contact_id AND
                      civicrm_email_source.is_primary = 1";
      }
      $this->_aliases['civicrm_contact'] = 'civicrm_contact_source';
    }

  }

  function where($recordType = NULL) {
    $IN = 'IN('. implode(', ', array_keys($this->absenceActivityType)) .')';
    $this->_where = " WHERE {$this->_aliases['civicrm_activity']}.is_test = 0 AND
                                {$this->_aliases['civicrm_activity']}.is_deleted = 0 AND
                                {$this->_aliases['civicrm_activity']}.is_current_revision = 1 AND
                                {$this->_aliases['civicrm_activity']}.activity_type_id $IN";

    $clauses = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {

        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if ($fieldName != 'contact_' . $recordType &&
            (strstr($fieldName, '_target') ||
              strstr($fieldName, '_assignee') ||
              strstr($fieldName, '_source')
            )
          ) {
            if($fieldName != 'contact_' . $recordType . '_id'){
              continue;
            }

          }
          if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            if ($fieldName == 'absence_date') {
              continue;
            }

            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
            if ($op && ($op != 'nnll' || $op != 'nll')) {
              if ($field['name'] == 'activity_type_id' && empty($this->_params["{$fieldName}_value"])) {
                $this->_params["{$fieldName}_value"] = array_keys($this->absenceActivityType);
              }
              $clause = $this->whereClause($field,
                $op,
                CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
              );
            }
          }

          if ($field['name'] == 'current_user') {
            if (CRM_Utils_Array::value("{$fieldName}_value", $this->_params) == 1) {
              // get current user
              $session = CRM_Core_Session::singleton();
              if ($contactID = $session->get('userID')) {
                $clause = "{$this->_aliases['civicrm_activity_contact']}.activity_id IN
                           (SELECT activity_id FROM civicrm_activity_contact WHERE contact_id = {$contactID})";
              }
              else {
                $clause = NULL;
              }
            }
            else {
              $clause = NULL;
            }
          }
          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }

    if (empty($clauses)) {
      $this->_where .= " ";
    }
    else {
      $this->_where .= " AND " . implode(' AND ', $clauses);
    }

    if ($this->_aclWhere) {
      $this->_where .= " AND {$this->_aclWhere} ";
    }
  }

  function groupBy() {
    $this->_groupBy = "GROUP BY {$this->_aliases['civicrm_activity']}.id";
  }

  function buildACLClause($tableAlias = 'contact_a') {
    //override for ACL( Since Contact may be source
    //contact/assignee or target also it may be null )

    if (CRM_Core_Permission::check('view all contacts')) {
      $this->_aclFrom = $this->_aclWhere = NULL;
      return;
    }

    $session = CRM_Core_Session::singleton();
    $contactID = $session->get('userID');
    if (!$contactID) {
      $contactID = 0;
    }
    $contactID = CRM_Utils_Type::escape($contactID, 'Integer');

    CRM_Contact_BAO_Contact_Permission::cache($contactID);
    $clauses = array();
    foreach ($tableAlias as $k => $alias) {
      $clauses[] = " INNER JOIN civicrm_acl_contact_cache aclContactCache_{$k} ON ( {$alias}.id = aclContactCache_{$k}.contact_id OR {$alias}.id IS NULL ) AND aclContactCache_{$k}.user_id = $contactID ";
    }

    $this->_aclFrom = implode(" ", $clauses);
    $this->_aclWhere = NULL;
  }

  function add2group($groupID) {
    if (CRM_Utils_Array::value("contact_target_op", $this->_params) == 'nll') {
      CRM_Core_Error::fatal(ts('Current filter criteria didn\'t have any target contact to add to group'));
    }

    $query = "{$this->_select}
    FROM civireport_activity_temp_target tar
    GROUP BY civicrm_activity_id {$this->_having} {$this->_orderBy}";
    $select = 'AS addtogroup_contact_id';
    $query = str_ireplace('AS civicrm_contact_contact_target_id', $select, $query);
    $dao = CRM_Core_DAO::executeQuery($query);

    $contactIDs = array();
    // Add resulting contacts to group
    while ($dao->fetch()) {
      if ($dao->addtogroup_contact_id) {
        $contact_id = explode(';', $dao->addtogroup_contact_id);
        if ($contact_id[0]) {
          $contactIDs[$contact_id[0]] = $contact_id[0];
        }
      }
    }

    if ( !empty($contactIDs) ) {
      CRM_Contact_BAO_GroupContact::addContactsToGroup($contactIDs, $groupID);
      CRM_Core_Session::setStatus(ts("Listed contact(s) have been added to the selected group."), ts('Contacts Added'), 'success');
    }
    else {
      CRM_Core_Session::setStatus(ts("The listed records(s) cannot be added to the group."));
    }
  }

  function postProcess() {
    $this->beginPostProcess();

    //Assign those recordtype to array which have filter operator as 'Is not empty' or 'Is empty'
    $nullFilters = array();
    foreach (array('target', 'source', 'assignee') as $type) {
      if (CRM_Utils_Array::value("contact_{$type}_op", $this->_params) == 'nnll' ||
        CRM_Utils_Array::value("contact_{$type}_value", $this->_params)) {
        $nullFilters[] = " civicrm_contact_contact_{$type} IS NOT NULL ";
      }
      else if (CRM_Utils_Array::value("contact_{$type}_op", $this->_params) == 'nll') {
        $nullFilters[] = " civicrm_contact_contact_{$type} IS NULL ";
      }
    }

    // 1. fill temp table with target results
    $this->buildACLClause(array('civicrm_contact_target'));
    $this->select('target');
    $this->_columnHeaders = self::alterColumns($this->_columnHeaders);
    $this->assign('columnHeaders', $this->_columnHeaders);

    $this->from('target');
    $this->customDataFrom();
    $this->where('target');
    $insertCols = implode(',', $this->_selectAliases);
    $tempQuery  = "CREATE TEMPORARY TABLE civireport_activity_temp_target CHARACTER SET utf8 COLLATE utf8_unicode_ci AS
    {$this->_select} {$this->_from} {$this->_where} ";
    CRM_Core_DAO::executeQuery($tempQuery);

    // 2. add new columns to hold assignee and source results
    // fixme: add when required
    $tempQuery = "
    ALTER TABLE  civireport_activity_temp_target
    ADD COLUMN civicrm_contact_contact_assignee VARCHAR(128),
    ADD COLUMN civicrm_contact_contact_source VARCHAR(128),
    ADD COLUMN civicrm_contact_contact_assignee_id VARCHAR(128),
    ADD COLUMN civicrm_contact_contact_source_id VARCHAR(128),
    ADD COLUMN civicrm_email_contact_assignee_email VARCHAR(128),
    ADD COLUMN civicrm_email_contact_source_email VARCHAR(128)";
    CRM_Core_DAO::executeQuery($tempQuery);

    // 5. show final result set from temp table
    $rows = array();
    $this->select('final');
    $this->_having = "";
    if (!empty($nullFilters)) {
      $this->_having = "HAVING " . implode(' AND ', $nullFilters);
    }
    $this->orderBy();

    $sql = "{$this->_select}
    FROM civireport_activity_temp_target tar
    GROUP BY civicrm_activity_id {$this->_having} {$this->_orderBy}";
    $this->buildRows($sql, $rows);

    // format result set.
    $this->formatDisplay($rows, FALSE);

    // assign variables to templates
    $this->doTemplateAssignment($rows);

    // do print / pdf / instance stuff if needed
    $this->endPostProcess($rows);
  }

  function alterColumns($columnHeaders) {
    $inserted = FALSE;
    foreach ($columnHeaders as $key => $value) {
      if (!$inserted && $key === 'civicrm_activity_status_id') {
        // Add Special headers
        $newColumnHeaders['start_date'] = [
          'type' => CRM_Utils_Type::T_DATE,
          'title' => 'Start Date',
        ];
        $newColumnHeaders['end_date'] = [
          'type' => CRM_Utils_Type::T_DATE,
          'title' => 'End Date',
        ];
        $newColumnHeaders['total_qty'] = [
          'title' => 'Total Qty',
        ];
        $inserted = TRUE;
      }

      $newColumnHeaders[$key] = $value;
    }

    return $newColumnHeaders;
  }

  function alterDisplay(&$rows) {
    // custom code to alter rows
    if (!isset($this->_params['absence_date_from']) && !isset($this->_params['absence_date_to'])) {
      $this->_params['absence_date_from'] = date('m/d/Y');
      $this->_params['absence_date_to'] = date("m/d/Y",strtotime("+2 months"));
    }

    if (!empty($rows)) {
      $IN = 'activity_type_id IN('. implode(', ', array_keys($this->absenceActivityType)) .')';
      $sql = "SELECT source_record_id, SUM(duration) as total_qty, 
              MIN(activity_date_time) as start_date, MAX(activity_date_time) as end_date 
              FROM civicrm_activity
              WHERE source_record_id IS NOT NULL AND $IN GROUP BY source_record_id";

      $result = [];
      $dao = CRM_Core_DAO::executeQuery($sql);
      while ($dao->fetch()) {
        $result[$dao->source_record_id]['start_date'] = $dao->start_date;
        $result[$dao->source_record_id]['end_date'] = $dao->end_date;
        $result[$dao->source_record_id]['total_qty'] = $dao->total_qty;
      }
    }

    foreach ($rows as $rowNum => $row) {
      if (array_key_exists('civicrm_activity_source_record_id', $row)) {
        $rows[$rowNum]['start_date'] = $result[$row['civicrm_activity_source_record_id']]['start_date'];
        $rows[$rowNum]['end_date'] = $result[$row['civicrm_activity_source_record_id']]['end_date'];
        $rows[$rowNum]['total_qty'] = $result[$row['civicrm_activity_source_record_id']]['total_qty']/480;
      }

      if (array_key_exists('civicrm_activity_activity_type_id', $row)) {
        if ($value = $row['civicrm_activity_activity_type_id']) {
          $rows[$rowNum]['civicrm_activity_activity_type_id'] = $this->activityTypes[$value];
        }
      }

      if (array_key_exists('civicrm_activity_status_id', $row)) {
        if ($value = $row['civicrm_activity_status_id']) {
          $rows[$rowNum]['civicrm_activity_status_id'] = $this->activityStatus[$value];
        }
      }

      if (array_key_exists('civicrm_activity_duration', $row)) {
        if ($value = $row['civicrm_activity_duration']) {
          $rows[$rowNum]['civicrm_activity_duration'] = $value/480;
        }
      }

      if (array_key_exists('civicrm_activity_activity_date_time', $row) && array_key_exists('civicrm_activity_status_id', $row)) {
        if (CRM_Utils_Date::overdue($rows[$rowNum]['civicrm_activity_activity_date_time']) &&
          $this->activityStatus[$row['civicrm_activity_status_id']] != 'Approved'
        ) {
          $rows[$rowNum]['class'] = "status-overdue";
        }
      }
    }
  }
}

