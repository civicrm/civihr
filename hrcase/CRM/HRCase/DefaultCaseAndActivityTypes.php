<?php
/**
 * This file declares a managed database record of type "CaseType" and "OptionValue".
 * The record will be automatically inserted, updated, or deleted from the
 * database as appropriate. For more details, see "hook_civicrm_managed" at:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/Hook+Reference
 */
class CRM_HRCase_DefaultCaseAndActivityTypes {

  private static $defaultActivityTypes = [
    'Schedule Exit Interview',
    'Get "No Dues" certification',
    'Conduct Exit Interview',
    'Revoke Access to Database',
    'Block work email ID',
    'Background Check',
    'References Check',
    'Schedule joining date',
    'Issue appointment letter',
    'Fill Employee Details Form',
    'Submission of ID/Residence proofs and photos',
    'Program and work induction by program supervisor',
    'Enter employee data in CiviHR',
    'Group Orientation to organization, values, policies',
    'Probation appraisal (start probation workflow)',
    'Confirm End of Probation Date',
    'Start Probation workflow'
  ];

  private static $defaultCaseTypes = [
    [
      'name' => 'Exiting',
      'title' => 'Exiting',
      'is_active' => 1,
      'weight' => 1,
      'definition' =>
        [
          'activityTypes' => [
            ['name' => 'Schedule Exit Interview'],
            ['name' => 'Get "No Dues" certification'],
            ['name' => 'Conduct Exit Interview'],
            ['name' => 'Revoke Access to Database'],
            ['name' => 'Block work email ID'],
            ['name' => 'Background Check'],
            ['name' => 'References Check']
          ],

          'activitySets' => [
            [
              'name' => 'standard_timeline',
              'label' => 'Standard Timeline',
              'timeline' => 1,
              'activityTypes' => [
                [
                  'name' => 'Schedule Exit Interview',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Get "No Dues" certification',
                  'reference_offset' => -7,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Conduct Exit Interview',
                  'reference_offset' => -3,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Revoke Access to Database',
                  'reference_offset' => 0,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Block work email ID',
                  'reference_offset' => 0,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ]
              ]
            ]
          ],

          'caseRoles' => [
            ['name' => 'HR Manager', 'creator' => 1, 'manager' => 1]
          ]
        ]
    ],

    [
      'name' => 'Joining',
      'title' => 'Joining',
      'is_active' => 1,
      'weight' => 2,
      'definition' =>
        [
          'activityTypes' => [
            ['name' => 'Schedule joining date'],
            ['name' => 'Issue appointment letter'],
            ['name' => 'Fill Employee Details Form'],
            ['name' => 'Submission of ID/Residence proofs and photos'],
            ['name' => 'Program and work induction by program supervisor'],
            ['name' => 'Enter employee data in CiviHR'],
            ['name' => 'Group Orientation to organization, values, policies'],
            ['name' => 'Probation appraisal (start probation workflow)'],
            ['name' => 'Background Check'],
            ['name' => 'References Check'],
            ['name' => 'Confirm End of Probation Date'],
            ['name' => 'Start Probation workflow']
          ],

          'activitySets' => [
            [
              'name' => 'standard_timeline',
              'label' => 'Standard Timeline',
              'timeline' => 1,
              'activityTypes' => [
                [
                  'name' => 'Schedule joining date',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Issue appointment letter',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Fill Employee Details Form',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Submission of ID/Residence proofs and photos',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Enter employee data in CiviHR',
                  'reference_offset' => -7,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Program and work induction by program supervisor',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Group Orientation to organization, values, policies',
                  'reference_offset' => 7,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Confirm End of Probation Date',
                  'reference_offset' => 30,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Start Probation workflow',
                  'reference_offset' => 30,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'P45',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Passport',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
              ]
            ],

            [
              'name' => 'non_eea_timeline',
              'label' => 'Non EEA Staff Member Timeline',
              'timeline' => 2,
              'activityTypes' => [
                [
                  'name' => 'Schedule joining date',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Issue appointment letter',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Fill Employee Details Form',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Submission of ID/Residence proofs and photos',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Enter employee data in CiviHR',
                  'reference_offset' => -7,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Program and work induction by program supervisor',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Group Orientation to organization, values, policies',
                  'reference_offset' => 7,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Confirm End of Probation Date',
                  'reference_offset' => 30,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Start Probation workflow',
                  'reference_offset' => 30,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'P45',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'Passport',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
                [
                  'name' => 'VISA',
                  'reference_offset' => -10,
                  'reference_activity' => 'Open Case',
                  'status' => 'Scheduled',
                  'reference_select' => 'newest'
                ],
              ]
            ]
          ],

          'caseRoles' => [
            ['name' => 'HR Manager', 'creator' => 1, 'manager' => 1],
            ['name' => 'Recruiting Manager', 'creator' => 1, 'manager' => 1]
          ]
        ]
    ],
  ];

  private static $defaultCiviCRMCaseTypes = [
    'adult_day_care_referral',
    'housing_support'
  ];

  /**
   * Gets list of the default activity types
   *
   * @return array
   */
  public static function getDefaultActivityTypes() {
    return self::$defaultActivityTypes;
  }

  /**
   * Gets list of the default case types
   *
   * @return array
   */
  public static function getDefaultCaseTypes() {
    return self::$defaultCaseTypes;
  }

  /**
   * Gets list of the default civicrm case types
   *
   * @return array
   */
  public static function getDefaultCiviCRMCaseTypes() {
    return self::$defaultCiviCRMCaseTypes;
  }

}
