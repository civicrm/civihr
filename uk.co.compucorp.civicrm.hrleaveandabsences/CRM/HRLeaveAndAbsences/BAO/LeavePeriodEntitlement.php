<?php
use CRM_HRLeaveAndAbsences_EntitlementCalculation as EntitlementCalculation;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequestDate as LeaveRequestDate;
use CRM_HRLeaveAndAbsences_Exception_InvalidLeavePeriodEntitlementException as InvalidPeriodEntitlementException;
use CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement as LeavePeriodEntitlement;

/**
 * Class CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement
 */
class CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement extends CRM_HRLeaveAndAbsences_DAO_LeavePeriodEntitlement {

  /**
   * Create a new LeavePeriodEntitlement based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement|NULL
   **/
  public static function create($params) {
    $entityName = 'LeavePeriodEntitlement';
    $hook = empty($params['id']) ? 'create' : 'edit';

    self::validateParams($params);

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new self();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Validates the $params passed to the create method
   *
   * @param array $params
   *
   * @throws \CRM_HRLeaveAndAbsences_Exception_InvalidEntitlementException
   */
  private static function validateParams($params) {
    self::validateComment($params);
  }

  /**
   * Validates the comment fields on the $params array.
   *
   * If the comment is not empty, then the comment author and date are required.
   * Otherwise, the author and the date should be empty.
   *
   * @param array $params
   *
   * @throws \CRM_HRLeaveAndAbsences_Exception_InvalidLeavePeriodEntitlementException
   */
  private static function validateComment($params) {
    $hasComment = !empty($params['comment']);
    $hasCommentAuthor = !empty($params['comment_author_id']);
    $hasCommentDate = !empty($params['comment_date']);

    if($hasComment) {
      if(!$hasCommentAuthor) {
        throw new InvalidPeriodEntitlementException(
          ts('The author of the comment cannot be null')
        );
      }

      if(!$hasCommentDate) {
        throw new InvalidPeriodEntitlementException(
          ts('The date of the comment cannot be null')
        );
      }
    }

    if(!$hasComment && $hasCommentAuthor) {
      throw new InvalidPeriodEntitlementException(
        ts('The author of the comment should be null if the comment is empty')
      );
    }

    if(!$hasComment && $hasCommentDate) {
      throw new InvalidPeriodEntitlementException(
        ts('The date of the comment should be null if the comment is empty')
      );
    }
  }

  /**
   * Returns the calculated entitlement for a JobContract,
   * AbsencePeriod and AbsenceType with the given IDs
   *
   * @param int $contractId The ID of the JobContract
   * @param int $periodId The ID of the Absence Period
   * @param int $absenceTypeId The ID of the AbsenceType
   *
   * @return \CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement|null
   *    If there's no entitlement for the given arguments, null will be returned
   *
   * @throws \InvalidArgumentException
   */
  public static function getPeriodEntitlementForContract($contractId, $periodId, $absenceTypeId) {
    if(!$contractId) {
      throw new InvalidArgumentException("You must inform the Contract ID");
    }
    if(!$periodId) {
      throw new InvalidArgumentException("You must inform the AbsencePeriod ID");
    }
    if(!$absenceTypeId) {
      throw new InvalidArgumentException("You must inform the AbsenceType ID");
    }

    $entitlement = new self();
    $entitlement->contract_id = (int)$contractId;
    $entitlement->period_id = (int)$periodId;
    $entitlement->type_id = (int)$absenceTypeId;
    $entitlement->find(true);
    if($entitlement->id) {
      return $entitlement;
    }

    return null;
  }

  /**
   * This method saves a new LeavePeriodEntitlement and the respective
   * LeaveBalanceChanges based on the given EntitlementCalculation.
   *
   * If there's already an LeavePeriodEntitlement for the calculation's Absence
   * Period, Absence Type, and Contract, it will be replaced by a new one.
   *
   * If an overridden entitlement is given, the created Entitlement will be marked
   * as overridden.
   *
   * If a calculation comment is given, the current logged in user will be stored
   * as the comment's author.
   *
   * @param \CRM_HRLeaveAndAbsences_EntitlementCalculation $calculation
   * @param float|null $overriddenEntitlement
   *  A value to override the calculation's proposed entitlement
   * @param string|null $calculationComment
   *  A comment describing the calculation
   */
  public static function saveFromCalculation(EntitlementCalculation $calculation, $overriddenEntitlement = null, $calculationComment = null) {
    $transaction = new CRM_Core_Transaction();
    try {
      $absencePeriodID = $calculation->getAbsencePeriod()->id;
      $absenceTypeID = $calculation->getAbsenceType()->id;
      $contractID = $calculation->getContract()['id'];
      self::deleteLeavePeriodEntitlement($absencePeriodID, $absenceTypeID, $contractID);

      $periodEntitlement = self::create(self::buildLeavePeriodParamsFromCalculation(
        $calculation,
        $overriddenEntitlement,
        $calculationComment
      ));

      self::saveLeaveBalanceChange($calculation, $periodEntitlement, $overriddenEntitlement);

      if(!$periodEntitlement->overridden) {
        self::saveBroughtForwardBalanceChange($calculation, $periodEntitlement);
        self::savePublicHolidaysBalanceChanges($calculation, $periodEntitlement);
      }

      $transaction->commit();
    } catch(\Exception $ex) {
      $transaction->rollback();
    }
  }

  /**
   * @param \CRM_HRLeaveAndAbsences_EntitlementCalculation $calculation
   * @param boolean $overriddenEntitlement
   * @param string $calculationComment
   *
   * @return array
   */
  private static function buildLeavePeriodParamsFromCalculation(
    EntitlementCalculation $calculation,
    $overriddenEntitlement,
    $calculationComment
  ) {
    global $user;
    $absenceTypeID   = $calculation->getAbsenceType()->id;
    $contractID      = $calculation->getContract()['id'];
    $absencePeriodID = $calculation->getAbsencePeriod()->id;

    $params = [
      'type_id'     => $absenceTypeID,
      'contract_id' => $contractID,
      'period_id'   => $absencePeriodID,
      'overridden'  => (boolean)$overriddenEntitlement,
    ];

    if ($calculationComment) {
      $params['comment']            = $calculationComment;
      $params['comment_author_id']  = $user->uid;
      $params['comment_date'] = date('YmdHis');
    }

    return $params;
  }

  /**
   * Saves the Entitlement Calculation Pro Rata as a Balance Change of the "Leave
   * Type".
   *
   * @param \CRM_HRLeaveAndAbsences_EntitlementCalculation $calculation
   * @param \CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement $periodEntitlement
   * @param int $overriddenEntitlement
   */
  private static function saveLeaveBalanceChange(
    EntitlementCalculation $calculation,
    LeavePeriodEntitlement $periodEntitlement,
    $overriddenEntitlement = null
  ) {
    $balanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id'));

    if($periodEntitlement->overridden && !is_null($overriddenEntitlement)) {
      $amount = (float)$overriddenEntitlement;
    } else {
      $amount = $calculation->getProRata();
    }

    LeaveBalanceChange::create([
      'type_id' => $balanceChangeTypes['Leave'],
      'source_id' => $periodEntitlement->id,
      'source_type' => LeaveBalanceChange::SOURCE_ENTITLEMENT,
      'amount' => $amount
    ]);
  }

  /**
   * Saves the Entitlement Calculation Brought Forward as a Balance Change of the
   * "Brought Forward" type.
   *
   * @param \CRM_HRLeaveAndAbsences_EntitlementCalculation $calculation
   * @param \CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement $periodEntitlement
   */
  private static function saveBroughtForwardBalanceChange(
    EntitlementCalculation $calculation,
    LeavePeriodEntitlement $periodEntitlement
  ) {
    $balanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id'));

    $broughtForward = $calculation->getBroughtForward();

    if ($broughtForward) {
      $broughtForwardExpirationDate = $calculation->getBroughtForwardExpirationDate();

      LeaveBalanceChange::create([
        'type_id' => $balanceChangeTypes['Brought Forward'],
        'source_id' => $periodEntitlement->id,
        'source_type' => LeaveBalanceChange::SOURCE_ENTITLEMENT,
        'amount' => $broughtForward,
        'expiry_date' => CRM_Utils_Date::processDate($broughtForwardExpirationDate)
      ]);
    }
  }

  /**
   * Saves the Entitlement Calculation Public Holiday as Leave Requests and
   * Balance Changes.
   *
   * On Balance Change, of type "Public Holiday", will be created with the amount
   * equals to the number of Public Holidays in the entitlement. Next, for each of
   * the Public Holidays, a LeaveRequest will be created, including it's respective
   * LeaveRequestDates and LeaveBalanceChanges.
   *
   * @param \CRM_HRLeaveAndAbsences_EntitlementCalculation $calculation
   * @param \CRM_HRLeaveAndAbsences_BAO_LeavePeriodEntitlement $periodEntitlement
   *
   * @TODO Once we get a way to related a job contract to a work pattern, we'll
   *       need to take that in consideration to calculate the amount added/deducted
   *       by public holidays
   *
   * @throws \Exception
   */
  private static function savePublicHolidaysBalanceChanges(
    EntitlementCalculation $calculation,
    LeavePeriodEntitlement $periodEntitlement
  ) {
    $balanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id'));
    $leaveRequestStatuses = array_flip(LeaveRequest::buildOptions('status_id'));
    $leaveRequestDateTypes  = array_flip(LeaveRequest::buildOptions('from_date_type'));

    $publicHolidays = $calculation->getPublicHolidaysInEntitlement();

    if (!empty($publicHolidays)) {
      LeaveBalanceChange::create([
        'type_id'     => $balanceChangeTypes['Public Holiday'],
        'source_id'   => $periodEntitlement->id,
        'source_type' => LeaveBalanceChange::SOURCE_ENTITLEMENT,
        'amount'      => count($publicHolidays)
      ]);

      foreach ($publicHolidays as $publicHoliday) {
        $leaveRequest = LeaveRequest::create([
          'entitlement_id' => $periodEntitlement->id,
          'status_id'      => $leaveRequestStatuses['Admin Approved'],
          'from_date'      => CRM_Utils_Date::processDate($publicHoliday->date),
          'from_date_type' => $leaveRequestDateTypes['All Day']
        ]);

        $requestDate  = LeaveRequestDate::getDatesForLeaveRequest($leaveRequest->id)[0];

        LeaveBalanceChange::create([
          'type_id'        => $balanceChangeTypes['Debit'],
          'amount'         => -1,
          'source_id'      => $requestDate->id,
          'source_type'    => LeaveBalanceChange::SOURCE_LEAVE_REQUEST_DAY
        ]);
      }
    }
  }

  /**
   * Deletes the LeavePeriodEntitlement with the given Absence Period ID, Absence Type ID
   * and Contract ID
   *
   * @param int $absencePeriodID
   * @param int $absenceTypeID
   * @param int $contractID
   */
  private static function deleteLeavePeriodEntitlement($absencePeriodID, $absenceTypeID, $contractID) {
    $tableName = self::getTableName();
    $query     = "
      DELETE FROM {$tableName}
      WHERE period_id = %1 AND type_id = %2 AND contract_id = %3
    ";
    $params    = [
      1 => [$absencePeriodID, 'Positive'],
      2 => [$absenceTypeID, 'Positive'],
      3 => [$contractID, 'Positive'],
    ];

    CRM_Core_DAO::executeQuery($query, $params);
  }

  /**
   * Returns the current balance for this LeavePeriodEntitlement.
   *
   * The balance only includes:
   * - Brought Forward
   * - Public Holidays
   * - Expired Balance Changes
   * - Approved Leave Requests
   *
   * @return float
   */
  public function getBalance() {
    $leaveRequestStatus = array_flip(LeaveRequest::buildOptions('status_id'));
    $filterStatuses = [
      $leaveRequestStatus['Approved'],
      $leaveRequestStatus['Admin Approved'],
    ];
    return LeaveBalanceChange::getBalanceForEntitlement($this->id, $filterStatuses);
  }

  /**
   * Returns the entitlement (number of days) for this LeavePeriodEntitlement.
   *
   * This is basic the sum of the amounts of the LeaveBalanceChanges that are
   * part of the entitlement Breakdown. That is balance changes of the Leave,
   * Brought Forward and Public Holidays types, without a source_id.
   *
   * @see CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange::getBreakdownBalanceChangesForEntitlement()
   *
   * @return float
   */
  public function getEntitlement() {
    return LeaveBalanceChange::getBreakdownBalanceForEntitlement($this->id);
  }

  /**
   * Returns the current LeaveRequest balance for this LeavePeriodEntitlement. That
   * is, a balance that sums up only the balance changes caused by Leave Requests.
   *
   * Since LeaveRequests generates negative balance changes, the returned number
   * will be negative as well.
   *
   * This method only accounts for Approved LeaveRequests.
   *
   * @return float
   */
  public function getLeaveRequestBalance() {
    $leaveRequestStatus = array_flip(LeaveRequest::buildOptions('status_id'));
    $filterStatuses = [
      $leaveRequestStatus['Approved'],
      $leaveRequestStatus['Admin Approved'],
    ];

    return LeaveBalanceChange::getLeaveRequestBalanceForEntitlement($this->id, $filterStatuses);
  }
}
