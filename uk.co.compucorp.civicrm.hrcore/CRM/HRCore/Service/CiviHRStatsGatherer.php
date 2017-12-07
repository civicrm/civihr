<?php

use CRM_HRCore_Model_CiviHRStatistics as CiviHRStatistics;
use CRM_HRCore_Model_ReportConfiguration as ReportConfiguration;
use CRM_HRCore_Model_ReportConfigurationAgeGroup as AgeGroup;

/**
 * Responsible for gathering all required site statistics that will be sent to
 * monitor site usage.
 */
class CRM_HRCore_Service_CiviHRStatsGatherer {

  /**
   * Fetch and set all required statistics.
   *
   * @return CiviHRStatistics
   */
  public function gather() {
    $stats = new CiviHRStatistics();
    $stats->setGenerationDate(new \DateTime());
    $stats->setSiteName(variable_get('site_name', 'Undefined Name'));
    $this->setBaseUrl($stats);
    $this->setEntityCounts($stats);
    $this->setContactSubtypes($stats);
    $this->setReportConfigurations($stats);
    $this->setAgeGroups($stats);

    return $stats;
  }

  /**
   * Sets the site base URL
   *
   * @param CiviHRStatistics $stats
   */
  private function setBaseUrl(CiviHRStatistics $stats) {
    global $base_url;
    $stats->setSiteUrl($base_url);
  }

  /**
   * Fetches counts and sets them for all required entities
   *
   * @param CiviHRStatistics $stats
   * @throws CiviCRM_API3_Exception
   */
  private function setEntityCounts(CiviHRStatistics $stats) {
    $entities = [
      'assignment',
      'task',
      'document',
      'leaveRequest',
      'vacancy',
      'contact'
    ];

    // standard entities
    foreach ($entities as $entity) {
      $stats->setEntityCount($entity, (int) civicrm_api3($entity, 'getcount'));
    }

    // drupal users
    $userCount = (int)civicrm_api3('UFMatch', 'getcount');
    $stats->setEntityCount('drupalUser', $userCount);

    // leave request in last 100 days
    $format = 'Y-m-d H:i:s';
    $oneHundredDaysAgo = (new \DateTime('today - 100 days'))->format($format);
    $params = ['from_date' => ['>=' => $oneHundredDaysAgo]];
    $last100DaysCount = (int) civicrm_api3('LeaveRequest', 'getcount', $params);
    $stats->setEntityCount('leaveRequestInLast100Days', $last100DaysCount);
  }

  /**
   * Fetches contact subtypes
   *
   * @param CiviHRStatistics $stats
   * @throws CiviCRM_API3_Exception
   */
  private function setContactSubtypes(CiviHRStatistics $stats) {
    $params = ['parent_id' => ['IS NULL' => 1]];
    $contactTypes = civicrm_api3('ContactType', 'get', $params)['values'];
    foreach ($contactTypes as $contactType) {
      $name = $contactType['name'];
      $count = (int)civicrm_api3('Contact', 'getcount', ['type' => $name]);
      $stats->setContactSubtypeCount($name, $count);
    }
  }

  /**
   * Fetches report configurations
   *
   * @param CiviHRStatistics $stats
   */
  private function setReportConfigurations(CiviHRStatistics $stats) {
    $query = db_select('reports_configuration', 'rc')->fields('rc');
    $result = $query->execute();

    while ($row = $result->fetchAssoc()) {
      $config = new ReportConfiguration();
      $config
        ->setId($row['id'])
        ->setLabel($row['label'])
        ->setName($row['name'])
        ->setJsonConfig($row['json_config']);
      $stats->addReportConfiguration($config);
    }
  }

  /**
   * Sets age group settings
   *
   * @param CiviHRStatistics $stats
   */
  private function setAgeGroups(CiviHRStatistics $stats) {
    $query = db_select('reports_settings_age_group', 'ag')->fields('ag');
    $result = $query->execute();

    while ($row = $result->fetchAssoc()) {
      $group = new AgeGroup();
      $group
        ->setId($row['id'])
        ->setLabel($row['label'])
        ->setAgeFrom($row['age_from'])
        ->setAgeTo($row['age_to']);
      $stats->addReportConfigurationAgeGroup($group);
    }
  }

}
