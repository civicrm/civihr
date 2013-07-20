CRM.HRApp = new Marionette.Application();

CRM.HRApp.addRegions({
  mainRegion: ".hrjob-main-region",
  treeRegion: ".hrjob-tree-region"
});

CRM.HRApp.navigate = function(route,  options){
  options || (options = {});
  Backbone.history.navigate(route, options);
};

CRM.HRApp.getCurrentRoute = function(){
  return Backbone.history.fragment
};

CRM.HRApp.on("initialize:after", function(){
  if(Backbone.history){
    Backbone.history.start();

    CRM.HRApp.JobTabApp.Tree.Controller.show(CRM.jobTabApp.contact_id);

    if(this.getCurrentRoute() === ""){
      CRM.HRApp.trigger("intro:show", CRM.jobTabApp.contact_id);
    }
  }
});

CRM.HRApp.on("ui:block", function(message) {
  // cj('.hrjob-container').block({
  //   message: message
  // });
  cj.blockUI({
    css: { top: '50px', left: '', right: '50px' },
    message: null // disregard: message
  });
});
CRM.HRApp.on("ui:unblock", function() {
  // cj('.hrjob-container').unblock();
  cj.unblockUI();
});