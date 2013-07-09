<script id="hrjob-intro-template" type="text/template">
  {ts}This is the CiviHR Job tab{/ts}
</script>

<script id="hrjob-tree-template" type="text/template">
  <div class="hrjob-tree-items"></div>
</script>

<script id="hrjob-tree-item-template" type="text/template">
  <dl>
    <dt><a href="#<%= cid %>/hrjob/<%= id %>" class="hrjob-nav" data-hrjob-event="hrjob:summary:show"><%= position %></a></dt>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/general" class="hrjob-nav" data-hrjob-event="hrjob:general:edit">{ts}General{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/health" class="hrjob-nav" data-hrjob-event="hrjob:health:edit">{ts}Healthcare{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/hour" class="hrjob-nav" data-hrjob-event="hrjob:hour:edit">{ts}Hours{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/leave" class="hrjob-nav" data-hrjob-event="hrjob:leave:edit">{ts}Leave{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/pay" class="hrjob-nav" data-hrjob-event="hrjob:pay:edit">{ts}Pay{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/pension" class="hrjob-nav" data-hrjob-event="hrjob:pension:edit">{ts}Pension{/ts}</a></dd>
    <dd><a href="#<%= cid %>/hrjob/<%= id %>/role" class="hrjob-nav" data-hrjob-event="hrjob:role:edit">{ts}Roles{/ts}</a></dd>
  </dl>
</script>

<script id="hrjob-summary-template" type="text/template">
  <div>
    <span>{ts}Position{/ts}:</span>
    <span><%- position %></span>
  </div>
  <div>
    <span>{ts}Contract Type{/ts}:</span>
    <span><%- contract_type %></span>
  </div>
</script>

<script id="hrjob-general-template" type="text/template">
  <div>
    <span>{ts}Position{/ts}:</span>
    <input type="text" value="<%- position %>" />
  </div>
  <div>
    <span>{ts}Contract Type{/ts}:</span>
    <input type="text" value="<%- contract_type %>" />
  </div>
</script>

<script id="hrjob-hour-template" type="text/template">
  TODO: Hours
</script>

<script id="hrjob-pay-template" type="text/template">
  TODO: Pay
</script>

<script id="hrjob-health-template" type="text/template">
  TODO: Health
</script>

<script id="hrjob-leave-template" type="text/template">
  TODO: Leave
</script>

<script id="hrjob-pension-template" type="text/template">
  TODO: Pension
</script>

<script id="hrjob-role-template" type="text/template">
  TODO: Roles
</script>
