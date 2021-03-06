<?php

use CRM_HRLeaveAndAbsences_BAO_AbsenceType as AbsenceType;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequestDate as LeaveRequestDate;
use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_BAO_PublicHoliday as PublicHoliday;
use CRM_HRLeaveAndAbsences_Service_JobContract as JobContractService;
use CRM_HRLeaveAndAbsences_Service_LeaveBalanceChange as LeaveBalanceChangeService;
use CRM_HRLeaveAndAbsences_BAO_WorkPattern as WorkPattern;
use CRM_HRLeaveAndAbsences_BAO_ContactWorkPattern as ContactWorkPattern;
use CRM_HRLeaveAndAbsences_BAO_AbsencePeriod as AbsencePeriod;
use CRM_HRLeaveAndAbsences_Service_LeavePeriodEntitlement as LeavePeriodEntitlementService;

class CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestCreation {

  /**
   * @var \CRM_HRLeaveAndAbsence_Service_JobContract
   */
  private $jobContractService;

  /**
   * @var \CRM_HRLeaveAndAbsences_Service_LeaveBalanceChange
   */
  private $leaveBalanceChangeService;

  /**
   * @var \CRM_HRLeaveAndAbsences_Service_LeavePeriodEntitlement
   */
  private $leavePeriodEntitlementService;

  /**
   * @var \CRM_HRLeaveAndAbsences_BAO_AbsenceType[]
   *   Absence Types with MTPHL set to true
   */
  private $absenceTypes;

  public function __construct(
    JobContractService $jobContractService,
    LeaveBalanceChangeService $leaveBalanceChangeService,
    LeavePeriodEntitlementService $leavePeriodEntitlementService)
  {
    $this->jobContractService = $jobContractService;
    $this->leaveBalanceChangeService = $leaveBalanceChangeService;
    $this->leavePeriodEntitlementService = $leavePeriodEntitlementService;
    $this->absenceTypes = AbsenceType::getAllWithMustTakePublicHolidayAsLeaveRequest();
  }

  /**
   * Creates Public Holiday Leave Requests for all the contacts with contracts
   * overlapping the date of the given Public Holiday.
   *
   * The Public Holiday leave request will not be created if contact has no
   * entitlement for the absence type with MTPHL in the absence period the
   * public holiday date falls in.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   */
  public function createForAllContacts(PublicHoliday $publicHoliday) {
    $absencePeriod = AbsencePeriod::getPeriodOverlappingDate(new DateTime($publicHoliday->date));

    if (!$absencePeriod || empty($this->absenceTypes)) {
      return;
    }

    $contracts = $this->jobContractService->getContractsForPeriod(
      new DateTime($publicHoliday->date),
      new DateTime($publicHoliday->date)
    );

    $contactIDs = array_column($contracts, 'contact_id');
    $entitlements = $this->getEntitlementsForContacts($contactIDs, $absencePeriod);

    foreach($contracts as $contract) {
      foreach ($this->absenceTypes as $absenceType) {
        if ($this->contactHasEntitlement($entitlements, $contract['contact_id'], $absenceType)) {
          $this->createForContact($contract['contact_id'], $publicHoliday, $absenceType);
        }
      }
    }
  }

  /**
   * Creates Public Holiday Leave Requests for all the existing Public Holidays
   * int the future
   *
   * For each contract overlapping one Public Holiday, a Leave Request will be
   * created for the contract's contact and the public holiday date.
   *
   * The Public Holiday leave request will not be created if contact has no
   * entitlement for the absence type with MTPHL in the absence period the
   * public holiday date falls in.
   *
   * @param array $contactID
   *  If not empty, Public Holiday Leave Requests are created for only these contacts
   */
  public function createAllInTheFuture(array $contactID = []) {
    if (empty($this->absenceTypes)) {
      return;
    }

    $today = new DateTime();
    $absencePeriods = AbsencePeriod::getPeriodsBetweenDates($today);

    if(!$absencePeriods) {
      return;
    }

    $this->adjustPeriodDates($absencePeriods, $today);
    foreach($absencePeriods as $absencePeriod) {
      $this->createAllForAbsencePeriod($absencePeriod, $contactID);
    }
  }

  /**
   * Adjusts the absence periods array to conform to the start and end
   * date interval. It does this by setting the from date of the first
   * absence period to the startDate and the end date of the last absence
   * period to the endDate if present.
   *
   * @param array $absencePeriods
   * @param DateTime $startDate
   * @param DateTime|null $endDate
   */
  private function adjustPeriodDates($absencePeriods, DateTime $startDate, DateTime $endDate = null){
    $firstPeriod = reset($absencePeriods);

    if(new DateTime($firstPeriod->start_date) < $startDate) {
      $firstPeriod->start_date = $startDate->format('Y-m-d');
    }

    if($endDate) {
      $lastPeriod = end($absencePeriods);
      if(new DateTime($lastPeriod->end_date) > $endDate) {
        $lastPeriod->end_date = $endDate->format('Y-m-d');
      }
    }
  }

  /**
   * Creates Public Holiday Leave Requests for all Public Holidays
   * overlapping the start and end dates of the given contract
   * The Public Holiday leave request will not be created if contact has no
   * entitlement for the absence type with MTPHL in the absence period the
   * public holiday date falls in.
   *
   * @param int $contractID
   */
  public function createAllForContract($contractID) {
    $contract = $this->jobContractService->getContractByID($contractID);

    if (!$contract || empty($this->absenceTypes)) {
      return;
    }

    $contractStartDate = new DateTime($contract['period_start_date']);
    $contractEndDate = $contract['period_end_date'] ? new DateTime($contract['period_end_date']) : null;
    $absencePeriods = AbsencePeriod::getPeriodsBetweenDates($contractStartDate, $contractEndDate);

    if(!$absencePeriods) {
      return;
    }

    $this->adjustPeriodDates($absencePeriods, $contractStartDate, $contractEndDate);
    foreach($absencePeriods as $absencePeriod) {
      $publicHolidays = PublicHoliday::getAllForPeriod(
        $absencePeriod->start_date,
        $absencePeriod->end_date
      );

      $entitlements = $this->getEntitlementsForContacts([$contract['contact_id']], $absencePeriod);
      foreach($publicHolidays as $publicHoliday) {
        foreach ($this->absenceTypes as $absenceType) {
          if ($this->contactHasEntitlement($entitlements, $contract['contact_id'], $absenceType)) {
            $this->createForContact($contract['contact_id'], $publicHoliday, $absenceType);
          }
        }
      }
    }
  }

  /**
   * Creates a Public Holiday Leave Request for the contact with the
   * given $contactId
   *
   * @param int $contactID
   * @param AbsenceType $absenceType
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   *
   * @return LeaveRequest|null
   */
  public function createForContact($contactID, PublicHoliday $publicHoliday, AbsenceType $absenceType) {
    if (!$absenceType->must_take_public_holiday_as_leave) {
      return;
    }

    $existingLeaveRequest = LeaveRequest::findPublicHolidayLeaveRequestEvenIfDeleted(
      $contactID,
      $publicHoliday,
      $absenceType
    );

    if ($existingLeaveRequest) {
      return;
    }

    $leaveRequest = $this->createLeaveRequest($contactID, $absenceType, $publicHoliday);
    $this->createLeaveBalanceChangeRecord($leaveRequest);
    $this->recalculateExpiredBalanceChange($leaveRequest);

    return $leaveRequest;
  }

  /**
   * Creates a Leave Request for the given $contactID and $absenceType with the
   * date of the given Public Holiday
   *
   * @param int $contactID
   * @param \CRM_HRLeaveAndAbsences_BAO_AbsenceType $absenceType
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   *
   * @return \CRM_HRLeaveAndAbsences_BAO_LeaveRequest|NULL
   */
  private function createLeaveRequest($contactID, AbsenceType $absenceType, PublicHoliday $publicHoliday) {
    $leaveRequestStatuses = array_flip(LeaveRequest::buildOptions('status_id', 'validate'));
    $leaveRequestDayTypes = array_flip(LeaveRequest::buildOptions('from_date_type', 'validate'));
    $publicHolidayDate = new DateTime($publicHoliday->date);
    $publicHolidayDate->setTime(00, 00, 00);

    return LeaveRequest::create([
      'contact_id' => $contactID,
      'type_id' => $absenceType->id,
      'status_id' => $leaveRequestStatuses['admin_approved'],
      'from_date' => $publicHolidayDate->format('YmdHis'),
      'from_date_type' => $leaveRequestDayTypes['all_day'],
      'to_date' => $publicHolidayDate->format('YmdHis'),
      'to_date_type' => $leaveRequestDayTypes['all_day'],
      'request_type' => LeaveRequest::REQUEST_TYPE_PUBLIC_HOLIDAY
    ], LeaveRequest::VALIDATIONS_OFF);
  }

  /**
   * Creates LeaveBalanceChange records for the dates of the given $leaveRequest.
   *
   * For PublicHolidays, the deducted amount will be the amount specified by the Work Pattern.
   *
   * If there is already a leave request to this on the same date, the deduction
   * amount for that specific date will be updated to be 0, in order to not
   * deduct the same day twice.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   */
  private function createLeaveBalanceChangeRecord(LeaveRequest $leaveRequest) {
    $leaveBalanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id', 'validate'));

    $dates = $leaveRequest->getDates();
    foreach($dates as $date) {
      $this->zeroDeductionForOverlappingLeaveRequestDate($leaveRequest, $date);
      $amount = $this->leaveBalanceChangeService
                     ->calculateAmountToBeDeductedForDate($leaveRequest, new DateTime($date->date));

      LeaveBalanceChange::create([
        'source_id'   => $date->id,
        'source_type' => LeaveBalanceChange::SOURCE_LEAVE_REQUEST_DAY,
        'type_id'     => $leaveBalanceChangeTypes['public_holiday'],
        'amount'      => $amount
      ]);
    }
  }

  /**
   * First, searches for an existing balance change for the same contact and absence
   * type of the given $leaveRequest and linked to a LeaveRequestDate with the
   * same date as $leaveRequestDate. Next, if such balance change exists, update
   * it's amount to 0.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequestDate $leaveRequestDate
   */
  private function zeroDeductionForOverlappingLeaveRequestDate(LeaveRequest $leaveRequest, LeaveRequestDate $leaveRequestDate) {
    $date = new DateTime($leaveRequestDate->date);

    $leaveBalanceChange = LeaveBalanceChange::getBalanceChangeToModifyForLeaveDate($leaveRequest, $date);

    if($leaveBalanceChange) {
      LeaveBalanceChange::create([
        'id' => $leaveBalanceChange->id,
        'amount' => 0
      ]);
    }
  }

  /**
   * Checks if the date of the given PublicHoliday overlaps the start and end
   * dates of the given $contract
   *
   * @param array $contract
   *   An contract as returned by the HRJobContract.getcontractswithdetailsinperiod API
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   *
   * @return bool
   */
  private function publicHolidayOverlapsContract($contract, PublicHoliday $publicHoliday) {
    $startDate = new DateTime($contract['period_start_date']);
    $endDate = empty($contract['period_end_date']) ? null : new DateTime($contract['period_end_date']);
    $publicHolidayDate = new DateTime($publicHoliday->date);

    return $startDate <= $publicHolidayDate && (!$endDate || $endDate >= $publicHolidayDate);
  }

  /**
   * Creates Public Holiday Leave Requests for all Public Holidays in the
   * Future for the contacts using the given workPatternID. If it is the default Work Pattern
   * the Leave Requests are created for all contacts.
   *
   * @param int $workPatternID
   */
  public function createAllInFutureForWorkPatternContacts($workPatternID) {
    $workPattern = WorkPattern::findById($workPatternID);
    $contacts = [];

    if (!$workPattern->is_default) {
      $contacts = ContactWorkPattern::getContactsUsingWorkPatternFromDate(
        new DateTime(),
        $workPatternID
      );
    }

    $this->createAllInTheFuture($contacts);
  }

  /**
   * Creates Public Holiday Leave Requests for all public Holidays
   * within the given Absence Period for contacts with contracts and
   * entitlements for the absence type with MTPHL within this period.
   * If the contactID is present will create only for the contacts in the
   * contactID array
   *
   * The Public Holiday leave request will not be created if contact has no
   * entitlement for the absence type with MTPHL in the absence period the
   * public holiday date falls in.
   *
   * @param AbsencePeriod $absencePeriod
   * @param array $contactID
   */
  public function createAllForAbsencePeriod($absencePeriod, array $contactID = []) {
    if(empty($this->absenceTypes)) {
      return;
    }

    $publicHolidays = PublicHoliday::getAllForPeriod(
      $absencePeriod->start_date,
      $absencePeriod->end_date
    );

    $contracts = $this->jobContractService->getContractsForPeriod(
      new DateTime($absencePeriod->start_date),
      new DateTime($absencePeriod->end_date),
      $contactID
    );

    $contactIDs = array_unique(array_column($contracts, 'contact_id'));
    $entitlements = $this->getEntitlementsForContacts($contactIDs, $absencePeriod);

    foreach($contracts as $contract) {
      foreach($publicHolidays as $publicHoliday) {
        if($this->publicHolidayOverlapsContract($contract, $publicHoliday)) {
          foreach ($this->absenceTypes as $absenceType) {
            if ($this->contactHasEntitlement($entitlements, $contract['contact_id'], $absenceType)) {
              $this->createForContact($contract['contact_id'], $publicHoliday, $absenceType);
            }
          }
        }
      }
    }
  }

  /**
   * Creates Public Holiday Leave Requests for all Public Holidays in all
   * absence periods for all contacts. It does not re-create a leave request
   * that already exists.
   *
   * The Public Holiday leave request will not be created if a contact has no
   * entitlement for the absence type with MTPHL in the absence period the
   * public holiday date falls in or If the contact does not have a contract
   * overlapping the public holiday date.
   *
   */
  public function createAll() {
    $absencePeriods = $this->getAllAbsencePeriods();

    if(!$absencePeriods || empty($this->absenceTypes)) {
      return;
    }

    foreach($absencePeriods as $absencePeriod) {
      $this->createAllForAbsencePeriod($absencePeriod);
    }
  }

  private function getAllAbsencePeriods() {
    $absencePeriod = new AbsencePeriod();
    $absencePeriod->find();

    $absencePeriods = [];
    while($absencePeriod->fetch()) {
      $absencePeriods[] = clone $absencePeriod;
    }

    return $absencePeriods;
  }
  /**
   * Checks if the contract's contact has entitlement for the given
   * absence type ID.
   *
   * @param array $entitlements
   * @param int $contactID
   * @param AbsenceType $absenceType
   *
   * @return bool
   */
  private function contactHasEntitlement($entitlements, $contactID, $absenceType) {
    return !empty($entitlements[$absenceType->id][$contactID][$absenceType->id]) && $entitlements[$absenceType->id][$contactID][$absenceType->id] > 0;
  }

  /**
   * Returns the entitlements for the given contact(s) during the absence
   * period for the absence types with MTPHL.
   *
   * @param array $contactIDs
   * @param AbsencePeriod $absencePeriod
   *
   * @return array
   */
  private function getEntitlementsForContacts($contactIDs, AbsencePeriod $absencePeriod) {
    $entitlements = [];
    foreach ($this->absenceTypes as $absenceType) {
      $entitlements[$absenceType->id] = $this->leavePeriodEntitlementService->getEntitlementsForContacts(
        $contactIDs,
        $absencePeriod->id,
        $absenceType->id
      );
    }

    return $entitlements;
  }

  /**
   * Recalculates expired Balance changes for the contact of a Public Holiday leave request
   * with past dates and having expired LeaveBalanceChanges that expired on or after
   * the LeaveRequest past date.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   */
  private function recalculateExpiredBalanceChange(LeaveRequest $leaveRequest) {
    $today = new DateTime();
    $leaveRequestDate = new DateTime($leaveRequest->from_date);

    if($leaveRequestDate < $today) {
      $this->leaveBalanceChangeService->recalculateExpiredBalanceChangesForLeaveRequestPastDates($leaveRequest);
    }
  }
}
