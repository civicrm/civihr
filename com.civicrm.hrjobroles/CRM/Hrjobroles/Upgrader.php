<?php

/**
 * Collection of upgrade steps
 */
class CRM_Hrjobroles_Upgrader extends CRM_Hrjobroles_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed
   */
  public function install() {
//    $this->executeSqlFile('sql/myinstall.sql');
    $this->installCostCentreTypes();
  }

  /**
   * Example: Add start and and date for job roles
   *
   * @return TRUE on success
   * @throws Exception
   *
   */
  public function upgrade_1001() {
    $this->ctx->log->info('Applying update for job role start and end dates');
    CRM_Core_DAO::executeQuery("ALTER TABLE  `civicrm_hrjobroles` ADD COLUMN  `start_date` timestamp DEFAULT 0 COMMENT 'Start Date of the job role'");
    CRM_Core_DAO::executeQuery("ALTER TABLE  `civicrm_hrjobroles` ADD COLUMN  `end_date` timestamp DEFAULT 0 COMMENT 'End Date of the job role'");
    return TRUE;
  }

  public function upgrade_1002(){
    $this->installCostCentreTypes();

    return TRUE;
  }

  public function upgrade_1004() {
    CRM_Core_DAO::executeQuery('ALTER TABLE `civicrm_hrjobroles` MODIFY COLUMN `start_date` DATETIME DEFAULT NULL');
    CRM_Core_DAO::executeQuery('ALTER TABLE `civicrm_hrjobroles` MODIFY COLUMN `end_date` DATETIME DEFAULT NULL');
    //Update any end_date using "0000-00-00 00:00:00" as an empty value to NULL
    CRM_Core_DAO::executeQuery("UPDATE `civicrm_hrjobroles` SET `end_date` = NULL WHERE end_date = '0000-00-00 00:00:00'");

    return TRUE;
  }

  /**
   * 1- Drops (functional_area, hours & role_hours_unit) columns.
   * 2- Removes foreign key from job_contract_id field and adds
   * index to it instead due to some issues with generating DAOs from XML.
   * @ToDo: Fix DAO generation script and regenerate the DAOs.
   *
   * @return bool
   */
  public function upgrade_1005() {
    CRM_Core_DAO::executeQuery(
      'ALTER TABLE `civicrm_hrjobroles`
         DROP COLUMN functional_area,
         DROP COLUMN hours,
         DROP COLUMN role_hours_unit
         DROP FOREIGN KEY FK_civicrm_hrjobroles_job_contract_id
         ADD INDEX `job_contract_id` (`job_contract_id`)'
    );

    return TRUE;
  }

  /**
   * Creates new Option Group for Cost Centres
   */
  public function installCostCentreTypes() {

    function save($val, $key, $id){
      civicrm_api3('OptionValue', 'create', array(
          'sequential' => 1,
          'option_group_id' => $id,
          'label' => $val,
          'value' => $val,
          'name' => $val,
      ));
    }

    try{
      $result = civicrm_api3('OptionGroup', 'create', array(
          'sequential' => 1,
          'name' => "cost_centres",
          'title' => "Cost Centres",
          'is_active' => 1
      ));

      $id = $result['id'];

      $options = array(
          'Other' => 'Other'
      );

      array_walk($options, 'save', $id);

    } catch(Exception $e){
      // OptionGroup already exists
      // Skip this
    }
  }
}
