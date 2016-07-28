<?php
/*
+--------------------------------------------------------------------+
| CiviCRM version 4.7                                                |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2016                                |
+--------------------------------------------------------------------+
| This file is a part of CiviCRM.                                    |
|                                                                    |
| CiviCRM is free software; you can copy, modify, and distribute it  |
| under the terms of the GNU Affero General Public License           |
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
*/
/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 *
 * Generated from xml/schema/CRM/HRLeaveAndAbsences/LeaveBalanceChange.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_HRLeaveAndAbsences_DAO_LeaveBalanceChange extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_hrleaveandabsences_leave_balance_change';
  /**
   * static instance to hold the field values
   *
   * @var array
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = true;
  /**
   * Unique LeaveBalanceChange ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * FK to LeavePeriodEntitlement
   *
   * @var int unsigned
   */
  public $entitlement_id;
  /**
   * One of the values of the Leave Balance Type option group
   *
   * @var int unsigned
   */
  public $type_id;
  /**
   * The amount of days this change in balance represents to the entitlement
   *
   * @var float
   */
  public $amount;
  /**
   * Some balance changes can expire. This is the date it will expire.
   *
   * @var date
   */
  public $expiry_date;
  /**
   * FK to LeaveBalanceChange. This is only used for a balance change that represents expired days, and it will be related to the balance change that has expired.
   *
   * @var int unsigned
   */
  public $expired_balance_id;
  /**
   * Some balance changes are originated from an specific source (a leave request date, for example) and this field will have the ID of this source.
   *
   * @var int unsigned
   */
  public $source_id;
  /**
   * class constructor
   *
   * @return civicrm_hrleaveandabsences_leave_balance_change
   */
  function __construct()
  {
    $this->__table = 'civicrm_hrleaveandabsences_leave_balance_change';
    parent::__construct();
  }
  /**
   * Returns foreign keys and entity references
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  static function getReferenceColumns()
  {
    if (!self::$_links) {
      self::$_links = static ::createReferenceColumns(__CLASS__);
      self::$_links[] = new CRM_Core_Reference_Basic(self::getTableName() , 'entitlement_id', 'civicrm_hrleaveandabsences_leave_period_entitlement', 'id');
      self::$_links[] = new CRM_Core_Reference_Basic(self::getTableName() , 'expired_balance_id', 'civicrm_hrleaveandabsences_leave_balance_change', 'id');
    }
    return self::$_links;
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'Unique LeaveBalanceChange ID',
          'required' => true,
        ) ,
        'entitlement_id' => array(
          'name' => 'entitlement_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'FK to LeavePeriodEntitlement',
          'required' => true,
          'FKClassName' => 'CRM_HRLeaveAndAbsences_DAO_LeavePeriodEntitlement',
        ) ,
        'type_id' => array(
          'name' => 'type_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'One of the values of the Leave Balance Type option group',
          'required' => true,
          'pseudoconstant' => array(
            'optionGroupName' => 'hrleaveandabsences_leave_balance_change_type',
            'optionEditPath' => 'civicrm/admin/options/hrleaveandabsences_leave_balance_change_type',
          )
        ) ,
        'amount' => array(
          'name' => 'amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Amount') ,
          'description' => 'The amount of days this change in balance represents to the entitlement',
          'required' => true,
          'precision' => array(
            20,
            2
          ) ,
        ) ,
        'expiry_date' => array(
          'name' => 'expiry_date',
          'type' => CRM_Utils_Type::T_DATE,
          'title' => ts('Expiry Date') ,
          'description' => 'Some balance changes can expire. This is the date it will expire.',
        ) ,
        'expired_balance_id' => array(
          'name' => 'expired_balance_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'FK to LeaveBalanceChange. This is only used for a balance change that represents expired days, and it will be related to the balance change that has expired.',
          'FKClassName' => 'CRM_HRLeaveAndAbsences_DAO_LeaveBalanceChange',
        ) ,
        'source_id' => array(
          'name' => 'source_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'Some balance changes are originated from an specific source (a leave request date, for example) and this field will have the ID of this source.',
        ) ,
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'entitlement_id' => 'entitlement_id',
        'type_id' => 'type_id',
        'amount' => 'amount',
        'expiry_date' => 'expiry_date',
        'expired_balance_id' => 'expired_balance_id',
        'source_id' => 'source_id',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['hrleaveandabsences_leave_balance_change'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['hrleaveandabsences_leave_balance_change'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}
