(function ($, _) {

  $(document)
    .on('crmLoad', function(e) {
      $('.crm-inline-edit').one('DOMSubtreeModified', function() {
        var $form = $(this).find('form');

        if ($form.length === 1) {
          $form.find('label').each(function() {
            var $label = $(this);
            var id = $label.attr('for');
            $('#' + id).attr('placeholder', $label.text());
          });
        }
      })
    })
    .on('updateContactHeader', function (e, data) {
      if (typeof data.contract !== 'undefined')  {
        updateContactHeaderContractDetails(data.contract);
      }
    });

  /**
   * Updates the contact header with the given contract details
   *
   * @param  {object} contract
   */
  function updateContactHeaderContractDetails(contract) {
    if (contract)  {
      $('.crm-summary-contactname-block').removeClass('crm-summary-contactname-block-without-contract');

      if (contract.position) {
        $('.crm-contact-detail-position').html('<strong>Position:</strong> '+ contract.position);
      }

      if (contract.location) {
        $('.crm-contact-detail-location').html('<strong>Normal place of work:</strong> '+ contract.location);
      }
    } else {
      $('.crm-summary-contactname-block').addClass('crm-summary-contactname-block-without-contract');
      $('.crm-contact-detail-position').html('');
      $('.crm-contact-detail-location').html('');
    }
  }
}(CRM.$, CRM._));
