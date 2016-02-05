require.config({
    urlArgs: 'bust=' + (new Date()).getTime(),
    paths: {
        'contact-summary': CRM.vars.contactsummary.baseURL + '/js/src/contact-summary'
    }
});

require(['contact-summary/app'], function () {
    document.addEventListener('contactsummaryLoad', function () {
        angular.bootstrap(document.getElementById('contactsummary'), ['contactsummary']);
    });
});
