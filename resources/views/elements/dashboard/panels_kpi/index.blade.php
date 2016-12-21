<div class="col-md-9">  @include('elements.dashboard.panels_kpi.summarycalls')          </div>
<div class="col-md-3">  @include('elements.dashboard.panels_kpi.groupcalls')            </div>
<div class="col-md-9"> 	@include('elements.dashboard.panels_kpi.groupstatistics')       </div>
<div class="col-md-3"> 	@include('elements.dashboard.panels_kpi.agentactivitysummary')  </div>
<script type="text/javascript">
    $(document).ready(function() {

        LoadGraphicsPie();
        LoadGraphicsSummary();

    });
</script>

