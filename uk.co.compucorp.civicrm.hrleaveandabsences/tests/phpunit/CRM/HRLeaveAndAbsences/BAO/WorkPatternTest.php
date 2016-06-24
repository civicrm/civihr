<?php

use Civi\Test\HeadlessInterface;

/**
 * Class CRM_HRLeaveAndAbsences_BAO_WorkPatternTest
 *
 * @group headless
 */
class CRM_HRLeaveAndAbsences_BAO_WorkPatternTest extends CiviUnitTestCase implements HeadlessInterface
{
    protected $_tablesToTruncate = [
        'civicrm_hrleaveandabsences_work_pattern',
        'civicrm_hrleaveandabsences_work_week',
        'civicrm_hrleaveandabsences_work_day',
    ];

    public function setUpHeadless() {
      return \Civi\Test::headless()->installMe(__DIR__)->apply();
    }

    public function testWeightShouldAlwaysBeMaxWeightPlus1OnCreate()
    {
        $firstEntity = $this->createBasicWorkPattern();
        $this->assertNotEmpty($firstEntity->weight);

        $secondEntity = $this->createBasicWorkPattern();
        $this->assertNotEmpty($secondEntity->weight);
        $this->assertEquals($firstEntity->weight + 1, $secondEntity->weight);
    }

    /**
     * @expectedException PEAR_Exception
     * @expectedExceptionMessage DB Error: already exists
     */
    public function testWorkPatternLabelsShouldBeUnique() {
        $this->createBasicWorkPattern(['label' => 'Pattern 1']);
        $this->createBasicWorkPattern(['label' => 'Pattern 1']);
    }

    public function testThereShouldBeOnlyOneDefaultTypeOnCreate() {
        $basicEntity = $this->createBasicWorkPattern(['is_default' => true]);
        $entity1 = $this->findWorkPatternByID($basicEntity->id);
        $this->assertEquals(1, $entity1->is_default);

        $basicEntity = $this->createBasicWorkPattern(['is_default' => true]);
        $entity2 = $this->findWorkPatternByID($basicEntity->id);
        $entity1 = $this->findWorkPatternByID($entity1->id);
        $this->assertEquals(0,  $entity1->is_default);
        $this->assertEquals(1, $entity2->is_default);
    }

    public function testThereShouldBeOnlyOneDefaultTypeOnUpdate() {
        $basicEntity1 = $this->createBasicWorkPattern(['is_default' => false]);
        $basicEntity2 = $this->createBasicWorkPattern(['is_default' => false]);
        $entity1 = $this->findWorkPatternByID($basicEntity1->id);
        $entity2 = $this->findWorkPatternByID($basicEntity2->id);
        $this->assertEquals(0,  $entity1->is_default);
        $this->assertEquals(0,  $entity2->is_default);

        $this->updateBasicWorkPattern($basicEntity1->id, ['is_default' => true]);
        $entity1 = $this->findWorkPatternByID($basicEntity1->id);
        $entity2 = $this->findWorkPatternByID($basicEntity2->id);
        $this->assertEquals(1, $entity1->is_default);
        $this->assertEquals(0,  $entity2->is_default);

        $this->updateBasicWorkPattern($basicEntity2->id, ['is_default' => true]);
        $entity1 = $this->findWorkPatternByID($basicEntity1->id);
        $entity2 = $this->findWorkPatternByID($basicEntity2->id);
        $this->assertEquals(0,  $entity1->is_default);
        $this->assertEquals(1, $entity2->is_default);
    }

    public function testFindWithNumberOfWeeksAndHours()
    {

        $this->createWorkPatternWith40HoursWorkWeek('Pattern 1');
        $this->createWorkPatternWithTwoWeeksAnd31AndHalfHours('Pattern 2');

        $object = new CRM_HRLeaveAndAbsences_BAO_WorkPattern();
        $object->findWithNumberOfWeeksAndHours();
        $this->assertEquals(2, $object->N);

        $object->fetch();
        $this->assertEquals('Pattern 1', $object->label);
        $this->assertEquals(1, $object->number_of_weeks);
        $this->assertEquals(40.0, $object->number_of_hours);

        $object->fetch();
        $this->assertEquals('Pattern 2', $object->label);
        $this->assertEquals(2, $object->number_of_weeks);
        $this->assertEquals(31.5, $object->number_of_hours);
    }

    public function testGetValuesArrayShouldReturnWorkPatternValues()
    {
        $params = [
            'label' => 'Pattern Label',
            'description' => 'Pattern Description',
            'is_active' => 1,
            'is_default' => 1
        ];
        $entity = $this->createBasicWorkPattern($params);
        $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray($entity->id);
        $this->assertEquals($params['label'], $values['label']);
        $this->assertEquals($params['description'], $values['description']);
        $this->assertEquals($params['is_active'], $values['is_active']);
        $this->assertEquals($params['is_default'], $values['is_default']);
        $this->assertEmpty($values['weeks']);
    }

    public function testGetValuesArrayShouldReturnWorkPatternValuesWithWeeksAndDays()
    {
      $label = 'Pattern Label ' . microtime();
      $entity = $this->createWorkPatternWith40HoursWorkWeek($label);
      $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray($entity->id);

      $this->assertEquals($label, $values['label']);
      $this->assertCount(1, $values['weeks']);
      $this->assertCount(7, $values['weeks'][0]['days']);
    }

    public function testCanCreateWorkPatternWithWeeksAndDays()
    {
      $params = [
        'weeks' => [
          [
            'days' => [
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 1],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 2],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 3],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 4],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 5],
              ['type' => 3, 'day_of_the_week' => 6],
              ['type' => 3, 'day_of_the_week' => 7],
            ]
          ]
        ]
      ];

      $workPattern = $this->createBasicWorkPattern($params);
      $this->assertNotEmpty($workPattern->id);
      $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray($workPattern->id);
      $this->assertCount(1, $values['weeks']);
      $this->assertCount(7, $values['weeks'][0]['days']);

      $weekDays = $values['weeks'][0]['days'];
      foreach($values['weeks'][0]['days'] as $i => $day) {
        $this->assertEquals($day['type'], $weekDays[$i]['type']);
        $this->assertEquals($day['day_of_the_week'], $weekDays[$i]['day_of_the_week']);
        if($day['type'] == 2) {
          $this->assertEquals($day['time_from'], $weekDays[$i]['time_from']);
          $this->assertEquals($day['time_to'], $weekDays[$i]['time_to']);
          $this->assertEquals($day['break'], $weekDays[$i]['break']);
          $this->assertEquals($day['leave_days'], $weekDays[$i]['leave_days']);
        }
      }
    }

    public function testCanUpdateWorkPatternWithWeeksAndDays()
    {
      $workPattern = $this->createBasicWorkPattern();
      $this->assertNotEmpty($workPattern->id);
      $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray($workPattern->id);
      $this->assertCount(0, $values['weeks']);

      $params = [
        'weeks' => [
          [
            'days' => [
              ['type' => 2, 'time_from' => '15:00', 'time_to' => '22:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 1],
              ['type' => 2, 'time_from' => '13:00', 'time_to' => '23:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 2],
              ['type' => 2, 'time_from' => '09:00', 'time_to' => '18:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 3],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 4],
              ['type' => 2, 'time_from' => '10:00', 'time_to' => '19:00', 'break' => 1, 'leave_days' => 1, 'day_of_the_week' => 5],
              ['type' => 3, 'day_of_the_week' => 6],
              ['type' => 3, 'day_of_the_week' => 7],
            ]
          ]
        ]
      ];

      $workPattern = $this->updateBasicWorkPattern($workPattern->id, $params);
      $this->assertNotEmpty($workPattern->id);
      $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray($workPattern->id);
      $this->assertCount(1, $values['weeks']);
      $this->assertCount(7, $values['weeks'][0]['days']);

      $weekDays = $values['weeks'][0]['days'];
      foreach($values['weeks'][0]['days'] as $i => $day) {
        $this->assertEquals($day['type'], $weekDays[$i]['type']);
        $this->assertEquals($day['day_of_the_week'], $weekDays[$i]['day_of_the_week']);
        if($day['type'] == 2) {
          $this->assertEquals($day['time_from'], $weekDays[$i]['time_from']);
          $this->assertEquals($day['time_to'], $weekDays[$i]['time_to']);
          $this->assertEquals($day['break'], $weekDays[$i]['break']);
          $this->assertEquals($day['leave_days'], $weekDays[$i]['leave_days']);
        }
      }
    }

    public function testGetValuesArrayShouldReturnEmptyArrayWhenWorkPatternDoesntExists()
    {
        $values = CRM_HRLeaveAndAbsences_BAO_WorkPattern::getValuesArray(1);
        $this->assertEmpty($values);
    }

    private function createBasicWorkPattern($params = [])
    {
        $basicRequiredFields = ['label' => 'Pattern ' . microtime() ];

        $params = array_merge($basicRequiredFields, $params);
        return CRM_HRLeaveAndAbsences_BAO_WorkPattern::create($params);
    }

    private function updateBasicWorkPattern($id, $params)
    {
        $params['id'] = $id;
        return $this->createBasicWorkPattern($params);
    }

    private function findWorkPatternByID($id)
    {
        $entity = new CRM_HRLeaveAndAbsences_BAO_WorkPattern();
        $entity->id = $id;
        $entity->find(true);

        if($entity->N == 0) {
            return null;
        }

        return $entity;
    }

    /**
     * This creates a WorkPattern with a single WorkWeek containing
     * 7 WorkDays (5 Working Days with 8 working hours each and 2 days
     * of weekend).
     *
     * @param string $label the label of the Work Pattern
     *
     * @return \CRM_HRLeaveAndAbsences_DAO_WorkPattern|NULL
     */
    private function createWorkPatternWith40HoursWorkWeek($label)
    {
        $pattern = $this->createBasicWorkPattern(['label' => $label]);
        $result  = $this->callAPISuccess(
            'WorkWeek',
            'create',
            ['pattern_id' => $pattern->id, 'sequential' => 1]
        );
        $week1   = $result['values'][0];

        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 1,
            'time_from'       => '09:00',
            'time_to'         => '18:00',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 2,
            'time_from'       => '09:00',
            'time_to'         => '18:00',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 3,
            'time_from'       => '09:00',
            'time_to'         => '18:00',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 4,
            'time_from'       => '09:00',
            'time_to'         => '18:00',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 5,
            'time_from'       => '09:00',
            'time_to'         => '18:00',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 6
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 7
        ]);

        return $pattern;
    }

    /**
     * This creates a WorkPattern with two WorkWeeks.
     *
     * The first WorkWeek contains 3 working days, with 7.5 working hours each,
     * 2 non working days and 2 days of weekend.
     *
     * The second WorkWeek contains 2 working days, with 4.5 working hours each,
     * 3 non working days and 2 days of weekend.
     *
     * @param string $label the label of the Work Pattern
     *
     * @return \CRM_HRLeaveAndAbsences_DAO_WorkPattern|NULL
     */
    private function createWorkPatternWithTwoWeeksAnd31AndHalfHours($label)
    {
        $pattern = $this->createBasicWorkPattern(['label' => $label]);

        $result = $this->callAPISuccess(
            'WorkWeek',
            'create',
            ['pattern_id' => $pattern->id, 'sequential' => 1]
        );
        $week1 = $result['values'][0];

        $result = $this->callAPISuccess(
            'WorkWeek',
            'create',
            ['pattern_id' => $pattern->id, 'sequential' => 1]
        );
        $week2 = $result['values'][0];

        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 1,
            'time_from'       => '07:00',
            'time_to'         => '15:30',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_NO,
            'day_of_the_week' => 2,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 3,
            'time_from'       => '07:00',
            'time_to'         => '15:30',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_NO,
            'day_of_the_week' => 4,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 5,
            'time_from'       => '07:00',
            'time_to'         => '15:30',
            'break'           => 1,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 6,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week1['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 7,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_NO,
            'day_of_the_week' => 1,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 2,
            'time_from'       => '07:00',
            'time_to'         => '12:00',
            'break'           => 0.5,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_NO,
            'day_of_the_week' => 3,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_YES,
            'day_of_the_week' => 4,
            'time_from'       => '07:00',
            'time_to'         => '12:00',
            'break'           => 0.5,
            'leave_days'      => 1
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_NO,
            'day_of_the_week' => 5,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 6,
        ]);
        $this->callAPISuccess('WorkDay', 'create', [
            'week_id'         => $week2['id'],
            'type'            => CRM_HRLeaveAndAbsences_BAO_WorkDay::WORK_DAY_OPTION_WEEKEND,
            'day_of_the_week' => 7,
        ]);
    }
}
