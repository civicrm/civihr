#!groovy

pipeline {
  agent any

  parameters {
    string(name: 'CIVIHR_BUILDNAME', defaultValue: "hr17-dev_$BRANCH_NAME", description: 'CiviHR site name')
    booleanParam(name: 'DESTROY_SITE', defaultValue: true, description: 'Destroy built site after build finish')
  }

  environment {
    WEBROOT = "/opt/buildkit/build/${params.CIVIHR_BUILDNAME}"
    DRUPAL_SITES_ALL = "$WEBROOT/sites/all"
    DRUPAL_MODULES_ROOT = "$DRUPAL_SITES_ALL/modules"
    DRUPAL_THEMES_ROOT = "$DRUPAL_SITES_ALL/themes"
    CIVICRM_EXT_ROOT = "$DRUPAL_MODULES_ROOT/civicrm/tools/extensions"
    WEBURL = "http://jenkins.compucorp.co.uk:8900"
    ADMIN_PASS = credentials('CVHR_ADMIN_PASS')
    KARMA_TESTS_REPORT_FOLDER = "reports/js-karma"
    PHPUNIT_TESTS_REPORT_FOLDER = "reports/phpunit"
  }

  stages {
    stage('Pre-tasks execution') {
      steps {
        hipchatSend color: 'YELLOW', credentialId: 'c09fbb6e-1a52-4ba7-a87e-6f7c64d4173c', message: "Building <a href=\"${env.CHANGE_URL}\">${env.CHANGE_URL}</a>: <a href=\"${env.JOB_URL}\">${env.JOB_URL}</a>", notify: true, room: 'CiviHR', sendAs: 'Jenkins', server: 'api.hipchat.com', v2enabled: false
        currentBuild.result = 'FAILURE'

        // Print all Environment variables
        sh 'printenv | sort'

        // Destroy existing site
        sh "civibuild destroy ${params.CIVIHR_BUILDNAME} || true"

        // Test build tools
        sh 'amp test'

        // Cleanup old Karma test reports
        sh "rm -f $WORKSPACE/$KARMA_TESTS_REPORT_FOLDER/* || true"

        // Cleanup old PHPUnit test reports
        sh "rm -f $WORKSPACE/$PHPUNIT_TESTS_REPORT_FOLDER/* || true"
      }
    }

    stage('Build site') {
      steps {
        script {
          // Build site with CV Buildkit
          sh "civibuild create ${params.CIVIHR_BUILDNAME} --type hr17 --civi-ver 4.7.27 --hr-ver staging --url $WEBURL --admin-pass $ADMIN_PASS"
          sh """
            cd $DRUPAL_MODULES_ROOT/civicrm
            wget -O attachments.patch https://gist.githubusercontent.com/davialexandre/199b3ebb2c69f43c07dde0f51fb02c8b/raw/0f11edad8049c6edddd7f865c801ecba5fa4c052/attachments-4.7.27.patch
            patch -p1 -i attachments.patch
            rm attachments.patch
          """

          // Change git remote of civihr ext to support dev version of Jenkins pipeline
          changeCivihrGitRemote()

          // Get repos & branch name
          def prBranch = env.CHANGE_BRANCH
          def envBranch = env.CHANGE_TARGET
          if (prBranch != null && prBranch.startsWith("hotfix-")) {
            envBranch = 'master'
          }

          if (prBranch) {
            checkoutPrBranchInCiviHRRepos(prBranch)
            mergeEnvBranchInAllRepos(envBranch)
          }

          sh """
              cd $WEBROOT
              drush features-revert civihr_employee_portal_features -y
              drush features-revert civihr_default_permissions -y
              drush updatedb -y
              drush cvapi extension.upgrade -y
              drush cc all
              drush cc civicrm
            """
        }
      }
    }

    /* Testing PHP */
    stage('Test PHP') {
      steps {
        script {
          for (extension in listCivihrExtensions()) {
            if (extension.hasPHPTests) {
              testPHPUnit(extension)
            }
          }
        }
      }
      post {
        always {
          step([
            $class: 'XUnitBuilder',
            thresholds: [
              [
                $class: 'FailedThreshold',
                failureNewThreshold: '1',
                failureThreshold: '1',
                unstableNewThreshold: '1',
                unstableThreshold: '1'
              ],
              [
                $class: 'SkippedThreshold',
                failureNewThreshold: '0',
                failureThreshold: '0',
                unstableNewThreshold: '0',
                unstableThreshold: '0'
              ]
            ],
            tools: [
              [
                $class: 'JUnitType',
                pattern: env.PHPUNIT_TESTS_REPORT_FOLDER + '/*.xml'
              ]
            ]
          ])
        }
      }
    }

    /* Testing JS */
    stage('Testing JS: Install JS packages') {
      steps {
        script {
          for (extension in listCivihrExtensions()) {
            if(extension.hasJSTests) {
              installJSPackages(extension);
            }
          }
        }
      }
    }

    stage('Testing JS: Test JS') {
      steps {
        script {
          // This is necessary to avoid an additional loop
          // in each extension folder to read the XML.
          // After each test we move the reports to this folder
          sh "mkdir -p $WORKSPACE/$KARMA_TESTS_REPORT_FOLDER"

          for (extension in listCivihrExtensions()) {
            if (extension.hasJSTests) {
              testJS(extension)
            }
          }
        }
      }
      post {
        always {
          step([
            $class: 'XUnitBuilder',
            thresholds: [
              [
                $class: 'FailedThreshold',
                failureNewThreshold: '1',
                failureThreshold: '1',
                unstableNewThreshold: '1',
                unstableThreshold: '1'
              ]
            ],
            tools: [
              [
                $class: 'JUnitType',
                pattern: env.KARMA_TESTS_REPORT_FOLDER + '/*.xml'
              ]
            ]
          ])
        }
      }
    }
  }

  post {
    always {
      // Destroy built site
      script {
        if (params.DESTROY_SITE == true) {
          echo 'Destroying built site...'
          sh "civibuild destroy ${params.CIVIHR_BUILDNAME} || true"
        }
      }
    }
    success {
      hipchatSend color: 'GREEN', credentialId: 'c09fbb6e-1a52-4ba7-a87e-6f7c64d4173c', message: "Successful build. Duration: ${BUILD_DURATION} ${env.JOB_URL}", notify: true, room: 'CiviHR', sendAs: 'Jenkins', server: 'api.hipchat.com', v2enabled: false
    }
    failure {
      hipchatSend color: 'RED', credentialId: 'c09fbb6e-1a52-4ba7-a87e-6f7c64d4173c', message: "Failed build. Duration: ${BUILD_DURATION}. Failed Tests: ${FAILED_TESTS} ${env.JOB_URL}", notify: true, room: 'CiviHR', sendAs: 'Jenkins', server: 'api.hipchat.com', v2enabled: false
    }
  }
}

/*
 *  Change URL Git remote of civihr main repositry to the URL where configured by Jenkins project
 */
def changeCivihrGitRemote() {
  def pulledCvhrRepo = sh(returnStdout: true, script: "cd $WORKSPACE; git remote -v | grep fetch | awk '{print \$2}'").trim()

  echo 'Changing Civihr git URL..'

  sh """
    cd $CIVICRM_EXT_ROOT/civihr
    git remote set-url origin ${pulledCvhrRepo}
    git fetch --all
  """
}

def checkoutPrBranchInCiviHRRepos(String branch) {
  echo 'Checking out CiviHR repos..'

  for (repo in listCivihrGitRepoPath()) {
    try {
        sh """
          cd ${repo}
          git checkout ${branch}
        """
    } catch (err) {}
  }
}

def mergeEnvBranchInAllRepos(String envBranch) {
  echo 'Merging env branch'

  for (repo in listCivihrGitRepoPath()) {
    try {
        sh """
          cd ${repo}
          git merge origin/${envBranch} --no-edit
        """
    } catch (err) {}
  }
}

/*
 * Execute PHPUnit testing
 * params: extension
 */
def testPHPUnit(java.util.LinkedHashMap extension) {
  echo "PHPUnit testing: ${extension.name}"

  sh """
    cd $CIVICRM_EXT_ROOT/civihr/${extension.folder}
    phpunit4 --testsuite="Unit Tests" --log-junit $WORKSPACE/$PHPUNIT_TESTS_REPORT_FOLDER/result-phpunit_${extension
    .shortName}.xml
  """
}

/*
 * Install JS Testing
 * params: extension
 */
def installJSPackages(java.util.LinkedHashMap extension) {
  sh """
    cd $CIVICRM_EXT_ROOT/civihr/${extension.folder}
    yarn || true
  """
}

/*
 * Execute JS Testing
 * params: extension
 */
def testJS(java.util.LinkedHashMap extension) {
  echo "JS Testing ${extension.name}"

  // We cannot change, using CLI arguments, the place where
  // karma stores the junit XML report, so the last command
  // here copies the XML from the extension folder to the
  // workspace, where Jenkins will read it
  sh """
    cd $CIVICRM_EXT_ROOT/civihr/${extension.folder}
    gulp test --reporters junit,progress || true
    mv test-reports/*.xml $WORKSPACE/$KARMA_TESTS_REPORT_FOLDER/ || true
  """
}

/*
 * Get a list of CiviHR repository
 * https://compucorp.atlassian.net/wiki/spaces/PCHR/pages/68714502/GitHub+repositories
 */
def listCivihrGitRepoPath() {
  return [
    "$CIVICRM_EXT_ROOT/civihr",
    "$CIVICRM_EXT_ROOT/civihr_tasks",
    "$CIVICRM_EXT_ROOT/org.civicrm.shoreditch",
    "$CIVICRM_EXT_ROOT/org.civicrm.styleguide",
    "$DRUPAL_MODULES_ROOT/civihr-custom",
    "$DRUPAL_THEMES_ROOT/civihr_employee_portal_theme"
  ]
}

/*
 * Get a list of enabled CiviHR extensions
 */
def listCivihrExtensions() {
  return [
    [
      name: 'Job Roles',
      shortName: 'hrjobroles',
      folder: 'com.civicrm.hrjobroles',
      hasJSTests: true,
      hasPHPTests: true
    ],
    [
      name: 'Contacts Access Rights',
      shortName: 'contactaccessrights',
      folder: 'contactaccessrights',
      hasJSTests: true,
      hasPHPTests: true
    ],
    [
      name: 'Contacts Summary',
      shortName: 'contactsummary',
      folder: 'contactsummary',
      hasJSTests: true,
      hasPHPTests: false
    ],
    [
      name: 'Job Contracts',
      shortName: 'hrjobcontract',
      folder: 'hrjobcontract',
      hasJSTests: true,
      hasPHPTests: true
    ],
    [
      name: 'Recruitment',
      shortName: 'hrrecruitment',
      folder: 'hrrecruitment',
      hasJSTests: false,
      hasPHPTests: true
    ],
    [
      name: 'Reports',
      shortName: 'hrreport',
      folder: 'hrreport',
      hasJSTests: false,
      hasPHPTests: false
    ],
    [
      name: 'HR UI',
      shortName: 'hrui',
      folder: 'hrui',
      hasJSTests: false,
      hasPHPTests: false
    ],
    [
      name: 'HR Visa',
      shortName: 'hrvisa',
      folder: 'hrvisa',
      hasJSTests: false,
      hasPHPTests: true
    ],
    [
      name: 'Reqangular',
      shortName: 'reqangular',
      folder: 'org.civicrm.reqangular',
      hasJSTests: true,
      hasPHPTests: false
    ],
    [
      name: 'HRCore',
      shortName: 'hrcore',
      folder: 'uk.co.compucorp.civicrm.hrcore',
      hasJSTests: false,
      hasPHPTests: true
    ],
    [
      name: 'Leave and Absences',
      shortName: 'hrleaveandabsences',
      folder: 'uk.co.compucorp.civicrm.hrleaveandabsences',
      hasJSTests: true,
      hasPHPTests: true
    ],
    [
      name: 'Sample Data',
      shortName: 'hrsampledata',
      folder: 'uk.co.compucorp.civicrm.hrsampledata',
      hasJSTests: false,
      hasPHPTests: true
    ],
    [
      name: 'Emergency Contacts ',
      shortName: 'hremergency',
      folder: 'org.civicrm.hremergency',
      hasJSTests: false,
      hasPHPTests: true
    ]
  ]
}


