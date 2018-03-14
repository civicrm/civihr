<?php

use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_Service_JobContract as JobContractService;
use CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestDeletion as PublicHolidayLeaveRequestDeletion;
use CRM_HRCore_Test_Fabricator_Contact as ContactFabricator;
use CRM_Hrjobcontract_Test_Fabricator_HRJobContract as HRJobContractFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_AbsenceType as AbsenceTypeFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_LeaveRequest as LeaveRequestFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_PublicHoliday as PublicHolidayFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_PublicHolidayLeaveRequest as PublicHolidayLeaveRequestFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_WorkPattern as WorkPatternFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_ContactWorkPattern as ContactWorkPatternFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_AbsencePeriod as AbsencePeriodFabricator;

/**
 * Class CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestDeletionTest
 *
 * @group headless
 */
class CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestDeletionTest extends BaseHeadlessTest {

  use CRM_HRLeaveAndAbsences_ContractHelpersTrait;
  use CRM_HRLeaveAndAbsences_LeavePeriodEntitlementHelpersTrait;
  use CRM_HRLeaveAndAbsences_LeaveBalanceChangeHelpersTrait;
  use CRM_HRLeaveAndAbsences_PublicHolidayHelpersTrait;

  /**
   * @var CRM_HRLeaveAndAbsences_BAO_AbsenceType
   */
  private $absenceType;

  public function setUp() {
    // We delete everything two avoid problems with the default absence types
    // created during the extension installation
    $tableName = CRM_HRLeaveAndAbsences_BAO_AbsenceType::getTableName();
    CRM_Core_DAO::executeQuery("DELETE FROM {$tableName}");

    $this->absenceType = AbsenceTypeFabricator::fabricate([
      'must_take_public_holiday_as_leave' => 1
    ]);
  }

  public function testCanDeleteAPublicHolidayLeaveRequestForASingleContact() {
    $absencePeriod = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date' => CRM_Utils_Date::processDate('2016-12-31')
    ]);
    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('2016-12-31')
    );
    $periodEntitlement->contact_id = 2;
    $periodEntitlement->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => $absencePeriod->start_date]
    );

    $publicHoliday = $this->instantiatePublicHoliday('2016-01-01');

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($periodEntitlement->contact_id, $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteForContact($periodEntitlement->contact_id, $publicHoliday);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
  }

  public function testSoftDeleteForContactSoftDeletesThePublicHolidayRequest() {
    $absencePeriod = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2017-01-01'),
      'end_date' => CRM_Utils_Date::processDate('2017-12-31')
    ]);
    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2017-01-01'),
      new DateTime('2017-12-31')
    );
    $periodEntitlement->contact_id = 2;
    $periodEntitlement->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => $absencePeriod->start_date]
    );

    $publicHoliday = $this->instantiatePublicHoliday('2017-01-01');

    $publicHolidayRequest = $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($periodEntitlement->contact_id, $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->softDeleteForContact($periodEntitlement->contact_id, $publicHoliday);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    //Check that the public holiday leave request is soft deleted.
    $publicHolidayLeaveRequestRecord = new LeaveRequest();
    $publicHolidayLeaveRequestRecord->id = $publicHolidayRequest->id;
    $publicHolidayLeaveRequestRecord->is_deleted = 1;
    $publicHolidayLeaveRequestRecord->find(TRUE);

    $this->assertNotNull($publicHolidayLeaveRequestRecord->id);
    $this->assertEquals($periodEntitlement->contact_id, $publicHolidayLeaveRequestRecord->contact_id);
  }

  public function testItUpdatesOverlappingLeaveRequestDatesAfterDeletingAPublicHolidayLeaveRequests() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date' => CRM_Utils_Date::processDate('2016-12-31')
    ]);

    // We need the Work Pattern and the contract in order to be able to
    // recalculate the deduction after deleting the Public Holiday Leave Request
    WorkPatternFabricator::fabricateWithA40HourWorkWeek(['is_default' => 1]);
    $this->createContract();
    $this->setContractDates('2016-01-01', '2016-12-31');

    $publicHoliday = $this->instantiatePublicHoliday('2016-10-10');

    $leaveRequest = LeaveRequestFabricator::fabricateWithoutValidation([
      'from_date' => CRM_Utils_Date::processDate($publicHoliday->date),
      'to_date' => CRM_Utils_Date::processDate($publicHoliday->date),
      'contact_id' => $this->contract['contact_id'],
      'type_id' => $this->absenceType->id,
      'status_id' => 1
    ], true);

    $this->assertEquals(-1, LeaveBalanceChange::getTotalBalanceChangeForLeaveRequest($leaveRequest));

    PublicHolidayLeaveRequestFabricator::fabricate($this->contract['contact_id'], $publicHoliday);

    $this->assertEquals(0, LeaveBalanceChange::getTotalBalanceChangeForLeaveRequest($leaveRequest));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteForContact($this->contract['contact_id'], $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getTotalBalanceChangeForLeaveRequest($leaveRequest));
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForFuturePublicHolidays() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date' => CRM_Utils_Date::processDate('+10 days')
    ]);
    $contact = ContactFabricator::fabricate();

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ], [
      'period_start_date' => '2016-01-01',
    ]);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));

    $publicHoliday1 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('yesterday')
    ]);
    $publicHoliday2 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+5 days')
    ]);
    $publicHoliday3 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+2 years')
    ]);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday1);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday2);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday3);

    $this->assertEquals(-3, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllInTheFuture();

    // It's -1 instead of 0 because the public holiday 1 is in the past and its
    // respective leave request will not be deleted
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
  }

  public function testItDeletesLeaveRequestsForPublicHolidaysOverlappingTheContractDates() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('yesterday'),
      'end_date' => CRM_Utils_Date::processDate('+300 days')
    ]);
    $contact = ContactFabricator::fabricate(['first_name' => 'Contact 1']);

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('yesterday'),
      new DateTime('+300 days')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    $contract = HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ], [
      'period_start_date' => CRM_Utils_Date::processDate('yesterday'),
      'period_end_date' => CRM_Utils_Date::processDate('+100 days')
    ]);

    $publicHoliday1 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('yesterday')
    ]);
    $publicHoliday2 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+5 days')
    ]);
    $publicHoliday3 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+201 days')
    ]);

    // The Fabricator will create the leave request even for public holidays in
    // the past
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday1);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday2);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday3);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday1));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday2));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday3));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllForContract($contract['id']);

    // It's -1 instead of 0 because the public holiday 1 and public holiday 2
    // will be deleted and the public holiday 3 is after the contract end date
    // and will not be deleted.
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday1));
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday2));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday3));
  }

  public function testItDeletesLeaveRequestsForAllPublicHolidaysOverlappingAContractWithNoEndDate() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('-10 days'),
      'end_date' => CRM_Utils_Date::processDate('+400 days')
    ]);
    $contact = ContactFabricator::fabricate(['first_name' => 'Contact 1']);

    $contract = HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ], [
      'period_start_date' =>  CRM_Utils_Date::processDate('yesterday'),
    ]);

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('-10 days'),
      new DateTime('+400 days')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    $publicHoliday1 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('yesterday')
    ]);
    $publicHoliday2 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+5 days')
    ]);
    $publicHoliday3 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+201 days')
    ]);

    // The Fabricator will create the leave request even for public holidays in
    // the past
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday1);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday2);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday3);

    $this->assertEquals(-3, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllForContract($contract['id']);

    //all leave requests within the contract dates are deleted.
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
  }

  public function testItDoesntDeleteAnythingIfTheContractIDDoesntExist() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('yesterday'),
      'end_date' => CRM_Utils_Date::processDate('+10 days')
    ]);
    $contact = ContactFabricator::fabricate(['first_name' => 'Contact 1']);

    $publicHoliday = $this->instantiatePublicHoliday('today');
    PublicHolidayLeaveRequestFabricator::fabricate($contact['id'], $publicHoliday);

    $this->assertEquals(1, $this->countNumberOfPublicHolidayBalanceChanges());

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllForContract(9998398298);

    $this->assertEquals(1, $this->countNumberOfPublicHolidayBalanceChanges());
  }

  public function testItDeletesLeaveRequestsForAllContactsWithContractsOverlappingTheGivenPublicHoliday() {
    $contact1 = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact1['id']
    ], [
      'period_start_date' =>  CRM_Utils_Date::processDate('yesterday'),
    ]);

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact2['id']
    ], [
      'period_start_date' =>  CRM_Utils_Date::processDate('yesterday'),
    ]);

    $publicHoliday = $this->instantiatePublicHoliday('+5 days');

    PublicHolidayLeaveRequestFabricator::fabricate($contact1['id'], $publicHoliday);
    PublicHolidayLeaveRequestFabricator::fabricate($contact2['id'], $publicHoliday);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact1['id'], $publicHoliday));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteForAllContacts($publicHoliday);

    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact1['id'], $publicHoliday));
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));
  }

  public function testItDoesntDeleteLeaveRequestsForAllContactsWithoutContractsOverlappingTheGivenPublicHoliday() {
    $contact1 = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact1['id']
    ], [
      'period_start_date' =>  CRM_Utils_Date::processDate('-10 days'),
      'period_end_date' => CRM_Utils_Date::processDate('today')
    ]);

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact2['id']
    ], [
      'period_start_date' =>  CRM_Utils_Date::processDate('+2 days'),
    ]);

    $publicHoliday = $this->instantiatePublicHoliday('+1 day');

    PublicHolidayLeaveRequestFabricator::fabricate($contact1['id'], $publicHoliday);
    PublicHolidayLeaveRequestFabricator::fabricate($contact2['id'], $publicHoliday);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact1['id'], $publicHoliday));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteForAllContacts($publicHoliday);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact1['id'], $publicHoliday));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));
  }

  private function countNumberOfPublicHolidayBalanceChanges() {
    $balanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id', 'validate'));

    $bao = new LeaveBalanceChange();
    $bao->type_id = $balanceChangeTypes['public_holiday'];
    $bao->source_type = LeaveBalanceChange::SOURCE_LEAVE_REQUEST_DAY;

    return $bao->count();
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForFuturePublicHolidaysForSelectedContact() {
    $contact = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    $periodEntitlement2 = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement2->contact_id = $contact2['id'];
    $periodEntitlement2->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact2['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $publicHoliday = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+2 months')
    ]);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllInTheFuture([$contact['id']]);

    //Only public Holiday leave requests for $contact will be deleted
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForFuturePublicHolidaysForWorkPatternContacts() {
    $contact = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    $periodEntitlement2 = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement2->contact_id = $contact2['id'];
    $periodEntitlement2->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact2['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    $workPattern1 = WorkPatternFabricator::fabricate();
    $workPattern2 = WorkPatternFabricator::fabricate();

    ContactWorkPatternFabricator::fabricate([
      'contact_id' => $contact['id'],
      'pattern_id' => $workPattern1->id,
      'effective_date' => CRM_Utils_Date::processDate('2015-01-10'),
    ]);

    ContactWorkPatternFabricator::fabricate([
      'contact_id' => $contact2['id'],
      'pattern_id' => $workPattern2->id,
      'effective_date' => CRM_Utils_Date::processDate('2015-01-15'),
    ]);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $publicHoliday = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+2 months')
    ]);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllInTheFutureForWorkPatternContacts($workPattern1->id);

    //Only public Holiday leave requests for $contact will be deleted since it uses work pattern1
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForFuturePublicHolidaysForWorkPatternWhenItIsTheDefault() {
    $contact = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    $periodEntitlement = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement->contact_id = $contact['id'];
    $periodEntitlement->type_id = $this->absenceType->id;

    $periodEntitlement2 = $this->createLeavePeriodEntitlementMockForBalanceTests(
      new DateTime('2016-01-01'),
      new DateTime('+2 years')
    );
    $periodEntitlement2->contact_id = $contact2['id'];
    $periodEntitlement2->type_id = $this->absenceType->id;

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    HRJobContractFabricator::fabricate([
      'contact_id' => $contact2['id']
    ],
    [
      'period_start_date' => '2016-01-01',
    ]);

    $workPattern1 = WorkPatternFabricator::fabricate(['is_default' => 1]);
    $workPattern2 = WorkPatternFabricator::fabricate();

    ContactWorkPatternFabricator::fabricate([
      'contact_id' => $contact['id'],
      'pattern_id' => $workPattern2->id,
      'effective_date' => CRM_Utils_Date::processDate('2015-01-10'),
    ]);

    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $publicHoliday = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' => CRM_Utils_Date::processDate('+2 months')
    ]);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday);

    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(-1, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));

    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllInTheFutureForWorkPatternContacts($workPattern1->id);

    //Leave Requests for all contacts are deleted
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement));
    $this->assertEquals(0, LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($periodEntitlement2));
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForAbsencePeriod() {
    $contact = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    $period1 = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2015-09-01'),
      'end_date' => CRM_Utils_Date::processDate('2015-12-31'),
    ]);

    $period2 = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date' => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $contact['id']],
      ['period_start_date' => '2016-01-01']
    );

    HRJobContractFabricator::fabricate(
      ['contact_id' => $contact2['id']],
      ['period_start_date' => '2016-01-01']
    );

    //Public holiday is within the first absence period
    $publicHoliday1 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' =>  CRM_Utils_Date::processDate('2015-12-31')
    ]);

    //pubic holiday is within the second absence period
    $publicHoliday2 = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' =>  CRM_Utils_Date::processDate('2016-01-01')
    ]);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday1);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday1);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday2);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday2);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday1));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday2));

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday1));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday2));

    //Delete all Public holiday leave requests for absence period 2.
    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllForAbsencePeriod($period2);

    //Leave Request for PublicHoliday2 which falls within the absence period will be deleted for both contacts
    //While that of Public Holiday1 will not be deleted.
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday1));
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday2));

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday1));
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday2));
  }

  public function testCanDeleteAllPublicHolidayLeaveRequestsForAbsencePeriodForSpecificContact() {
    $contact = ContactFabricator::fabricate();
    $contact2 = ContactFabricator::fabricate();

    $period = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date' => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $contact['id']],
      ['period_start_date' => '2016-01-01']
    );

    HRJobContractFabricator::fabricate(
      ['contact_id' => $contact2['id']],
      ['period_start_date' => '2016-01-01']
    );

    //pubic holiday is within the second absence period
    $publicHoliday = PublicHolidayFabricator::fabricateWithoutValidation([
      'date' =>  CRM_Utils_Date::processDate('2016-01-01')
    ]);

    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact['id'], $publicHoliday);
    $this->fabricatePublicHolidayLeaveRequestWithMockBalanceChange($contact2['id'], $publicHoliday);

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday));
    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));

    //Delete all Public holiday leave requests for absence period for first contact only
    $deletionLogic = new PublicHolidayLeaveRequestDeletion(new JobContractService());
    $deletionLogic->deleteAllForAbsencePeriod($period, [$contact['id']]);

    //Leave Request for PublicHoliday will be deleted for contact1 only
    $this->assertNull(LeaveRequest::findPublicHolidayLeaveRequest($contact['id'], $publicHoliday));

    $this->assertNotNull(LeaveRequest::findPublicHolidayLeaveRequest($contact2['id'], $publicHoliday));
  }
}
