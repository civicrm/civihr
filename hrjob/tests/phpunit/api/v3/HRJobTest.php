<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class api_v3_HRJobTest extends CiviUnitTestCase {
  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
    // $this->quickCleanup(array('example_table_name'));
    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  protected static function _populateDB($perClass = FALSE, &$object = NULL) {
    if (!parent::_populateDB($perClass, $object)) {
      return FALSE;
    }

    $import = new CRM_Utils_Migrate_Import();
    $import->run(
      CRM_Extension_System::singleton()->getMapper()->keyToBasePath('org.civicrm.hrjob')
        . '/xml/option_group_install.xml'
    );

    return TRUE;
  }

  /**
   * Create a job and several subordinate entities using API chaining
   */
  function testCreateChained() {
    $params = array(
      'version' => 3,
      'contract_type' => 'Volunteer',
      'api.HRJobPay.create' => array(
        'pay_amount' => 20,
      ),
      'api.HRJobHealth.create' => array(
        'plan_type' => 'Family',
        'description' => 'A test Description',
      ),
      'api.HRJobHour.create' => array(
        'hours_type' => 'part',
        'hours_amount' => 40.00,
        'hours_unit' => 'Week',
      ),
      'api.HRJobPension.create' => array(
        'is_enrolled' => 1,
        'contrib_pct' => 75.00,
      ),
      'api.HRJobRole.create' => array(
        'title' => 'Manager',
        'region' => 'Asia',
        'department' => 'Finance',
        'location' => 'Headquarters',
        'organization' => 'ABC Inc',
      ),
      'api.HRJobRole.create.1' => array(
        'title' => 'Senior Manager',
        'region' => 'Europe',
        'department' => 'HR',
        'location' => 'Home',
        'organization' => 'XYZ Inc',
      ),
      'api.HRJobLeave.create' => array(
        'leave_type' => 'Annual',
        'leave_amount' => 10,
      ),
       'api.HRJobLeave.create.1' => array(
        'leave_type' => 'Sick',
        'leave_amount' => 7
      ),
    );
    $result = civicrm_api('HRJob', 'create', $params);
    $this->assertAPISuccess($result);
    foreach ($result['values'] as $hrJobResult) {
      $this->assertEquals('Volunteer', $hrJobResult['contract_type']);

      $this->assertAPISuccess($hrJobResult['api.HRJobPay.create']);
      foreach ($hrJobResult['api.HRJobPay.create']['values'] as $hrJobPayResult) {
        $this->assertEquals(20, $hrJobPayResult['pay_amount']);
      }
      $this->assertAPISuccess($hrJobResult['api.HRJobHealth.create']);
      foreach ($hrJobResult['api.HRJobHealth.create']['values'] as $hrJobHealthResult) {
        $this->assertEquals('Family', $hrJobHealthResult['plan_type']);
        $this->assertEquals('A test Description', $hrJobHealthResult['description']);
      }
      $this->assertAPISuccess($hrJobResult['api.HRJobHour.create']);
      foreach ($hrJobResult['api.HRJobHour.create']['values'] as $hrJobHourResult) {
        $this->assertEquals('part', $hrJobHourResult['hours_type']);
        $this->assertEquals('40.00', $hrJobHourResult['hours_amount']);
        $this->assertEquals('Week', $hrJobHourResult['hours_unit']);
      }
      $this->assertAPISuccess($hrJobResult['api.HRJobPension.create']);
      foreach ($hrJobResult['api.HRJobPension.create']['values'] as $hrJobPensionResult) {
        $this->assertEquals(1, $hrJobPensionResult['is_enrolled']);
        $this->assertEquals(75.00, $hrJobPensionResult['contrib_pct']);
      }

      //assert the creation of multiple job roles
      $roleValues = array(
        'title' => array('Manager', 'Senior Manager'),
        'region' => array('Asia', 'Europe'),
        'department' => array('Finance', 'HR'),
        'location' => array('Headquarters', 'Home'),
        'organization' => array('ABC Inc', 'XYZ Inc'),
      );
      $this->assertAPISuccess($hrJobResult['api.HRJobRole.create']);
      foreach ($hrJobResult['api.HRJobRole.create']['values'] as $hrJobRoleResult) {
        foreach ($roleValues as $name => $value) {
          $this->assertEquals($value[0], $hrJobRoleResult[$name]);
        }
      }
      $this->assertAPISuccess($hrJobResult['api.HRJobRole.create.1']);
      foreach ($hrJobResult['api.HRJobRole.create.1']['values'] as $hrJobRoleResult) {
        foreach ($roleValues as $name => $value) {
          $this->assertEquals($value[1], $hrJobRoleResult[$name]);
        }
      }

      //assert the creation of multiple leaves
      $this->assertAPISuccess($hrJobResult['api.HRJobLeave.create']);
      foreach ($hrJobResult['api.HRJobLeave.create']['values'] as $key => $hrJobLeaveResult) {
        $this->assertEquals('Annual', $hrJobLeaveResult['leave_type']);
        $this->assertEquals(10, $hrJobLeaveResult['leave_amount']);
      }
      $this->assertAPISuccess($hrJobResult['api.HRJobLeave.create.1']);
      foreach ($hrJobResult['api.HRJobLeave.create.1']['values'] as $key => $hrJobLeaveResult) {
        $this->assertEquals('Sick', $hrJobLeaveResult['leave_type']);
        $this->assertEquals(7, $hrJobLeaveResult['leave_amount']);
      }
    }
  }
}
