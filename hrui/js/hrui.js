// Copyright CiviCRM LLC 2013. See http://civicrm.org/licensing
(function ($, _) {
  $(document).on('crmLoad', function(e) {
    if (CRM.formName == 'contactForm' || CRM.pageName == 'viewSummary') {
      // Rename "Summary" tab to "Personal Details"
      // Hack to check contact type - This field only appears for individuals
      if ($('.crm-contact-job_title', '.crm-summary-contactinfo-block').length) {
        $('.crm-contact-tabs-list #tab_summary a', e.target).text('Personal Details');
      }
      $("#Extended_Demographics, .Extended_Demographics").addClass("collapsed");
      // Hide current employer and job title
      // Contact summary screen:
      $('div.crm-contact-current_employer, div.crm-contact-job_title', '.crm-summary-contactinfo-block').parent('div.crm-summary-row').hide();
      // Inline edit form
      $('form#ContactInfo input#employer_id, form#ContactInfo input#job_title', e.target).closest('div.crm-summary-row').hide();
      // Contact edit screen
      $('input#employer_id, input#job_title', 'form#Contact').parent('td').hide();

      /* Changes on Add Individual pages and Personal details tab for HR-358 */
      // Move Job summary to top
      $('.HRJob_Summary', e.target).insertBefore($('.crm-summary-contactinfo-block'));
      // changes of email block, remove bulkmail and onhold
      $('div.email-signature, td#Email-Bulkmail-html', 'form#Contact').hide();
      $('#Email-Primary', 'form#Contact').prev('td').prev('td').hide();
      $('td#Email-Bulkmail-html, #Email-Primary', 'form#Contact').prev('td').hide();

      //shift demographic above extended demographic
      $('.crm-demographics-accordion', 'form#Contact').insertAfter($('.crm-contactDetails-accordion'));
      if ($('tr#Phone_Block_2', 'form#Contact').length < 1) {
        $('#addPhone').click();
      }
    }
    //changes of sorce help text
    $('INPUT#contact_source').parent('td').children('a').click(function() {
      $('#crm-notification-container .crm-help .notify-content').remove();
      if ($('#crm-notification-container .crm-help p').length) {
	$('#crm-notification-container .crm-help p').remove();
      }
      $('#crm-notification-container .crm-help').append('<p>Source is a useful field where data has been migrated to CiviHR from one or a number of other legacy systems. The Source field will indicate which legacy system the contact has come from.</p>');
    });
  });
}(CRM.$, CRM._));
