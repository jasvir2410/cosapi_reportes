<div class="panel panel-default">
    <ul class="nav nav-tabs nav-pills ">
        <li role="tab" class="active">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_incoming('calls_completed')">
                <icon class="glyphicon glyphicon-earphone"></icon>Atendidas
            </a>
        </li>
        <li role="tab">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_incoming('calls_transfer')">
                <icon class="glyphicon glyphicon-random"></icon>Transferidas
            </a>
        </li>
        <li role="tab">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_incoming('calls_abandone')">
                <icon class="glyphicon glyphicon-alert"></icon>Abandonadas
            </a>
        </li>
    </ul>
    <input type="hidden" id="hidDefaultEvent" value="calls_completed">
    <div class="panel-body" >
        <div class="tab-pane fade active in" id="panel-report">
            @include('filtros.export')
            <table id="table-incoming" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Hour</th>
                    <th>Telephone</th>
                    <th>Agent</th>
                    <th>Skill</th>
                    <th>Duration</th>
                    <th>Action</th>
                    <th>Wait Time</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        prueba();
        
    })

    function prueba(){
        show_tab_incoming('calls_completed');
    }

</script>