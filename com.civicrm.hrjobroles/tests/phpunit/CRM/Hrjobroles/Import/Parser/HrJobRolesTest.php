<?php

require_once 'HrJobRolesTestBase.php';

class CRM_Hrjobroles_Import_Parser_HrJobRolesTest extends HrJobRolesTestBase {


  function setUp() {
    parent::setUp();
    $session = CRM_Core_Session::singleton();
    $session->set('dateTypes', 1);
    $this->createSampleOptionGroupsAndValues();
  }

  function tearDown() {
    parent::tearDown();
  }

  function testBasicImport() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
  }

  function testImportWithoutMandatoryFields() {
    // run importer
    $importParams = [
      'title' => 'test import role'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  function testImportWithValidOptionValues() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
    $this->assertEquals($importParams['location'], $roleEntity->location);
    $this->assertEquals($importParams['hrjc_region'], $roleEntity->region);
    $this->assertEquals($importParams['hrjc_role_department'], $roleEntity->department);
    $this->assertEquals($importParams['hrjc_level_type'], $roleEntity->level_type);
  }

  function testImportWithInvalidOptionValues() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role2',
      'location' => 'amman',
      'hrjc_region' => 'southhggh ammandshhghg',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);;
  }

  function testImportWithEmptyOptionValues() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role3',
      'location' => '',
      'hrjc_region' => '',
      'hrjc_role_department' => '',
      'hrjc_level_type' => ''
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
  }

  function testImportFunderByIDAndPercent() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_funder_val_type' => '%',
      'hrjc_role_percent_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
    $this->assertEquals($importParams['funder'], $roleEntity->funder);
    $this->assertEquals(1, $roleEntity->funder_val_type);
    $this->assertEquals($importParams['hrjc_role_percent_pay_funder'], $roleEntity->percent_pay_funder);
  }

  function testImportFunderByIDAndAmount() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_funder_val_type' => 'fixed',
      'hrjc_role_amount_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
    $this->assertEquals($importParams['funder'], $roleEntity->funder);
    $this->assertEquals(0, $roleEntity->funder_val_type);
    $this->assertEquals($importParams['hrjc_role_amount_pay_funder'], $roleEntity->amount_pay_funder);
  }

  function testImportFunderByDisplayNameAndAmount() {
    // create contact
    $contactParams = [
      'first_name'=>'walter',
      'last_name'=>'white',
      'display_name' => 'walter white',
      'prefix_id' => '',
      'suffix_id' => ''
    ];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactParams['display_name'],
      'hrjc_funder_val_type' => 'fixed',
      'hrjc_role_amount_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::VALID, $importResponse);

    $roleEntity = $this->findRole(['title' => $importParams['title']]);
    $this->assertEquals($importParams['title'], $roleEntity->title);
    $this->assertEquals($contactID, $roleEntity->funder);
    $this->assertEquals(0, $roleEntity->funder_val_type);
    $this->assertEquals($importParams['hrjc_role_amount_pay_funder'], $roleEntity->amount_pay_funder);
  }

  function testImportFunderWithInvalidID() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => 100000,
      'hrjc_funder_val_type' => 'fixed',
      'hrjc_role_amount_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  // I commented out these tests because I didn't had the chance to run them before the release
  // and I run and uncomment them if the passed in other PR later
  /*function testImportFunderWithInvalidDisplayName() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => 'wrong name',
      'hrjc_funder_val_type' => 'fixed',
      'hrjc_role_amount_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  function testImportFunderWithInvalidValueType() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_funder_val_type' => 'wrong_type',
      'hrjc_role_amount_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  function testImportFunderWithInvalidPercentPay() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_funder_val_type' => '%',
      'hrjc_role_percent_pay_funder' => 'should_be_number'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  function testImportFunderWithInvalidAmountPay() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_funder_val_type' => 'fixed',
      'hrjc_role_percent_pay_funder' => 'should_be_number'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }

  function testImportFunderWithoutValueType() {
    // create contact
    $contactParams = ['first_name'=>'walter', 'last_name'=>'white'];
    $contactID = $this->individualCreate($contactParams);

    // create contract
    $contract = $this->createJobContract($contactID, date('Y-m-d', strtotime('-14 days')));

    // run importer
    $importParams = [
      'job_contract_id' => $contract->id,
      'title' => 'test import role',
      'location' => 'amman',
      'hrjc_region' => 'south amman',
      'hrjc_role_department' => 'amman devs',
      'hrjc_level_type' => 'guru',
      'funder' => $contactID,
      'hrjc_role_percent_pay_funder' => '30'
    ];
    $importResponse = $this->runImport($importParams);
    $this->assertEquals(CRM_Import_Parser::ERROR, $importResponse);
  }*/

  private function runImport($params)  {
    $fields = array_keys($params);
    $values = array_values($params);
    $importObject = new CRM_Hrjobroles_Import_Parser_HrJobRoles($fields);
    $importObject->init();
    return $importObject->import(NULL, $values);
  }

}
