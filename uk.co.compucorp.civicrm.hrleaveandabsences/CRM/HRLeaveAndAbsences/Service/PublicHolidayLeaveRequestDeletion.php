<?php

use CRM_HRLeaveAndAbsences_BAO_PublicHoliday as PublicHoliday;
use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_BAO_AbsencePeriod as AbsencePeriod;
use CRM_HRLeaveAndAbsences_Service_JobContract as JobContractService;
use CRM_HRLeaveAndAbsences_BAO_WorkPattern as WorkPattern;
use CRM_HRLeaveAndAbsences_BAO_ContactWorkPattern as ContactWorkPattern;
use CRM_HRLeaveAndAbsences_Factory_LeaveDateAmountDeduction as LeaveDateAmountDeductionFactory;
use CRM_HRLeaveAndAbsences_Service_ContactWorkPattern as ContactWorkPatternService;

class CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestDeletion {

  /**
   * @var \CRM_HRLeaveAndAbsences_Service_JobContract
   */
  private $jobContractService;

  public function __construct(JobContractService $jobContractService) {
    $this->jobContractService = $jobContractService;
  }

  /**
   * Deletes all the existing LeaveRequests for the given Public Holiday for
   * all contacts
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   */
  public function deleteForAllContacts(PublicHoliday $publicHoliday) {
    $contracts = $this->jobContractService->getContractsForPeriod(
      new DateTime($publicHoliday->date),
      new DateTime($publicHoliday->date)
    );

    foreach($contracts as $contract) {
      $this->deleteForContact($contract['contact_id'], $publicHoliday);
    }
  }

  /**
   * Deletes the Public Holiday Leave Request for the contact and Public Holiday.
   *
   * If there are LeaveRequestDates overlapping the public holiday, their
   * balance change amount will be updated to not be 0 anymore.
   *
   * @param int $contactID
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   */
  public function deleteForContact($contactID, PublicHoliday $publicHoliday) {
    $leaveRequest = LeaveRequest::findPublicHolidayLeaveRequest($contactID, $publicHoliday);

    if(!$leaveRequest) {
      return;
    }

    $this->deleteDatesWithBalanceChanges($leaveRequest);
    $leaveRequest->delete();
  }

  /**
   * Soft Deletes the Public Holiday Leave Request for the contact and Public Holiday.
   *
   * If there are LeaveRequestDates overlapping the public holiday, their
   * balance change amount will be updated to not be 0 anymore.
   *
   * @param int $contactID
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   */
  public function softDeleteForContact($contactID, PublicHoliday $publicHoliday) {
    $leaveRequest = LeaveRequest::findPublicHolidayLeaveRequest($contactID, $publicHoliday);

    if(!$leaveRequest) {
      return;
    }

    LeaveRequest::softDelete($leaveRequest->id);
    foreach($leaveRequest->getDates() as $date) {
      $this->recalculateDeductionForOverlappingLeaveRequestDate($leaveRequest, new DateTime($date->date));
    }
  }

  /**
   * Deletes the public holiday leave dates and balance changes.
   * It also updates the balance change of any leave request overlapping
   * the public holiday date to not be zero again but the amount calculated
   * from the contact's work pattern for the date.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   */
  private function deleteDatesWithBalanceChanges(LeaveRequest $leaveRequest) {
    foreach($leaveRequest->getDates() as $date) {
      LeaveBalanceChange::deleteForLeaveRequestDate($date);
      $this->recalculateDeductionForOverlappingLeaveRequestDate($leaveRequest, new DateTime($date->date));
      $date->delete();
    }
  }

  /**
   * Deletes all the Public Holiday Leave Requests between the start and end
   * dates of the given contract.
   *
   * @param int $contractID
   */
  public function deleteAllForContract($contractID) {
    $contract = $this->jobContractService->getContractByID($contractID);

    if(!$contract) {
      return;
    }

    $publicHolidays = PublicHoliday::getAllForPeriod(
      $contract['period_start_date'],
      $contract['period_end_date']
    );

    foreach($publicHolidays as $publicHoliday) {
      $this->deleteForContact($contract['contact_id'], $publicHoliday);
    }
  }

  /**
   * Deletes all the Public Holiday Leave Requests for Public Holidays in the
   * future
   *
   * @param array $contactID
   *   If not empty, Public Holiday Leave Requests are deleted for only these contacts
   */
  public function deleteAllInTheFuture(array $contactID = []) {
    $futurePublicHolidays = PublicHoliday::getAllInFuture();
    $lastPublicHoliday = end($futurePublicHolidays);

    $contracts = $this->jobContractService->getContractsForPeriod(
      new DateTime(),
      new DateTime($lastPublicHoliday->date),
      $contactID
    );

    $contactIDs = array_unique(array_column($contracts, 'contact_id'));

    foreach($contactIDs as $contactID) {
      foreach($futurePublicHolidays as $publicHoliday) {
        $this->deleteForContact($contactID, $publicHoliday);
      }
    }
  }

  /**
   * First, searches for an existing balance change for the same contact and absence
   * type of the given $leaveRequest and linked to a LeaveRequestDate with the
   * same date as $date. Next, if such balance change exists, update
   * it's amount to using the Work Pattern assigned to the contact or the default
   * one, if the contact has no work patterns.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   * @param \DateTime $date
   */
  private function recalculateDeductionForOverlappingLeaveRequestDate(LeaveRequest $leaveRequest, DateTime $date) {
    $leaveBalanceChange = LeaveBalanceChange::getExistingBalanceChangeForALeaveRequestDate($leaveRequest, $date);
    $dateDeductionFactory = LeaveDateAmountDeductionFactory::createForAbsenceType($leaveRequest->type_id);
    $contactWorkPatternService = new ContactWorkPatternService();

    if($leaveBalanceChange) {
      $deduction = LeaveBalanceChange::calculateAmountForDate(
        $leaveRequest,
        $date,
        $dateDeductionFactory,
        $contactWorkPatternService
      );

      LeaveBalanceChange::create([
        'id' => $leaveBalanceChange->id,
        'amount' => $deduction
      ]);
    }
  }

  /**
   * Deletes all the Public Holiday Leave Requests for Public Holidays in the
   * future for the contacts using the given workPatternID. If it is the default Work Pattern
   * the Leave Requests are deleted for all contacts.
   *
   * @param int $workPatternID
   */
  public function deleteAllInTheFutureForWorkPatternContacts($workPatternID) {
    $workPattern = WorkPattern::findById($workPatternID);
    $contacts = [];

    if (!$workPattern->is_default) {
      $contacts = ContactWorkPattern::getContactsUsingWorkPatternFromDate(
        new DateTime(),
        $workPatternID
      );
    }

    $this->deleteAllInTheFuture($contacts);
  }

  /**
   * Deletes all the Public Holiday Leave Requests for Public Holidays
   * within the given Absence Period for all contacts with contracts
   * within the period.
   *
   * If contactID is present, will only delete for the contacts in the
   * array.
   *
   * @param AbsencePeriod $absencePeriod
   * @param array $contactID
   */
  public function deleteAllForAbsencePeriod($absencePeriod, array $contactID = []) {
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

    foreach($contactIDs as $contactID) {
      foreach($publicHolidays as $publicHoliday) {
        $this->deleteForContact($contactID, $publicHoliday);
      }
    }
  }
}
