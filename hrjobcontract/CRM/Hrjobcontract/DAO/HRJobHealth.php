<?php
/*
+--------------------------------------------------------------------+
| CiviHR version 1.4                                                 |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2014                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * Generated from xml/schema/CRM/HRJob/HRJobHealth.xml
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Hrjobcontract_DAO_HRJobHealth extends CRM_Hrjobcontract_DAO_Base
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_hrjobcontract_health';
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   * @static
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   * @static
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   * @static
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   * @static
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = true;
  /**
   * Unique HRJobHealth ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * FK to Contact ID for the organization or company which manages healthcare service
   *
   * @var int unsigned
   */
  public $provider;
  /**
   * .
   *
   * @var string
   */
  public $plan_type;
  /**
   *
   * @var text
   */
  public $description;
  /**
   *
   * @var text
   */
  public $dependents;
  /**
   * FK to Contact ID for the organization or company which manages life insurance service
   *
   * @var int unsigned
   */
  public $provider_life_insurance;
  /**
   * .
   *
   * @var string
   */
  public $plan_type_life_insurance;
  /**
   *
   * @var text
   */
  public $description_life_insurance;
  /**
   *
   * @var text
   */
  public $dependents_life_insurance;

  /**
   * class constructor
   *
   * @access public
   */
  function __construct()
  {
    $this->__table = 'civicrm_hrjobcontract_health';
    parent::__construct();
  }
  /**
   * return foreign keys and entity references
   *
   * @static
   * @access public
   * @return array of CRM_Core_Reference_Interface
   */
  static function getReferenceColumns()
  {
    if (!self::$_links) {
      self::$_links = array(
        new CRM_Core_Reference_Basic(self::getTableName() , 'provider', 'civicrm_contact', 'id') ,
        new CRM_Core_Reference_Basic(self::getTableName() , 'provider_life_insurance', 'civicrm_contact', 'id') ,
        new CRM_Core_Reference_Basic(self::getTableName() , 'jobcontract_revision_id', 'civicrm_hrjobcontract_revision', 'id') ,
      );
    }
    return self::$_links;
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
        self::$_fields = self::setFields(
            array(
              'id' => array(
                'name' => 'id',
                'type' => CRM_Utils_Type::T_INT,
                'title' => ts('Job Contract Health Insurance ID') ,
                'required' => true,
                'export' => false,
                'import' => false,
              ) ,
              'hrjobcontract_health_health_provider' => array(
                'name' => 'provider',
                'type' => CRM_Utils_Type::T_INT,
                'title' => ts('Health Insurance Provider') ,
                'export' => true,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.provider',
                'headerPattern' => '',
                'dataPattern' => '',
                'FKClassName' => 'CRM_Contact_DAO_Contact',
                'headerPattern' => '/^health\s?insurance\s?provider/i',
              ) ,
              'hrjobcontract_health_health_plan_type' => array(
                'name' => 'plan_type',
                'type' => CRM_Utils_Type::T_STRING,
                'title' => ts('Health Insurance Plan Type') ,
                'export' => true,
                'maxlength' => 63,
                'size' => CRM_Utils_Type::BIG,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.plan_type',
                'headerPattern' => '',
                'dataPattern' => '',
                'pseudoconstant' => array(
                  'optionGroupName' => 'hrjc_insurance_plantype',
                ),
                'headerPattern' => '/^health\s?insurance\s?plan\s?type/i',
              ) ,
              'hrjobcontract_health_description' => array(
                'name' => 'description',
                'type' => CRM_Utils_Type::T_TEXT,
                'title' => ts('Health Insurance Description') ,
                'export' => true,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.description',
                'headerPattern' => '/^health\s?insurance\s?description/i',
              ) ,
              'hrjobcontract_health_dependents' => array(
                'name' => 'dependents',
                'type' => CRM_Utils_Type::T_TEXT,
                'title' => ts('Health Insurance Dependents') ,
                'export' => true,
                'import' => true,
                'headerPattern' => '/^healthcare\s?dependents/i',
                'where' => 'civicrm_hrjobcontract_health.dependents'
              ) ,
              'hrjobcontract_health_health_provider_life_insurance' => array(
                'name' => 'provider_life_insurance',
                'type' => CRM_Utils_Type::T_INT,
                'title' => ts('Life insurance Provider') ,
                'export' => true,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.provider_life_insurance',
                'headerPattern' => '',
                'dataPattern' => '',
                'FKClassName' => 'CRM_Contact_DAO_Contact',
                'headerPattern' => '/^life\s?insurance\s?provider/i',
              ) ,
              'hrjobcontract_health_life_insurance_plan_type' => array(
                'name' => 'plan_type_life_insurance',
                'type' => CRM_Utils_Type::T_STRING,
                'title' => ts('Life insurance Plan Type') ,
                'export' => true,
                'maxlength' => 63,
                'size' => CRM_Utils_Type::BIG,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.plan_type_life_insurance',
                'headerPattern' => '',
                'dataPattern' => '',
                'pseudoconstant' => array(
                  'optionGroupName' => 'hrjc_insurance_plantype',
                ),
                'headerPattern' => '/^life\s?insurance\s?plan\s?type/i',
              ) ,
              'hrjobcontract_health_description_life_insurance' => array(
                'name' => 'description_life_insurance',
                'type' => CRM_Utils_Type::T_TEXT,
                'title' => ts('Life Insurance Description') ,
                'export' => true,
                'import' => true,
                'where' => 'civicrm_hrjobcontract_health.description_life_insurance',
                'headerPattern' => '/^description\s?life\s?insurance/i',
              ) ,
              'hrjobcontract_health_dependents_life_insurance' => array(
                'name' => 'dependents_life_insurance',
                'type' => CRM_Utils_Type::T_TEXT,
                'title' => ts('Life Insurance Dependents') ,
                'export' => true,
                'import' => true,
                'headerPattern' => '/^life\s?insurance\s?dependents/i',
                'where' => 'civicrm_hrjobcontract_health.dependents_life_insurance'
              ) ,
            )
        );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
        self::$_fieldKeys = self::setFieldKeys(
            array(
                'id' => 'id',
                'provider' => 'hrjobcontract_health_health_provider',
                'plan_type' => 'hrjobcontract_health_health_plan_type',
                'description' => 'hrjobcontract_health_description',
                'dependents' => 'hrjobcontract_health_dependents',
                'provider_life_insurance' => 'hrjobcontract_health_health_provider_life_insurance',
                'plan_type_life_insurance' => 'hrjobcontract_health_life_insurance_plan_type',
                'description_life_insurance' => 'hrjobcontract_health_description_life_insurance',
                'dependents_life_insurance' => 'hrjobcontract_health_dependents_life_insurance',
            )
        );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the names of this table
   *
   * @access public
   * @static
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * returns the list of fields that can be imported
   *
   * @access public
   * return array
   * @static
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (!empty($field['import'])) {
          if ($prefix) {
            self::$_import['hrjobcontract_health'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (!empty($field['export'])) {
          if ($prefix) {
            self::$_export['hrjobcontract_health'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}
