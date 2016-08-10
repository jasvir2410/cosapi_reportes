<div class="panel panel-default">
    <ul class="nav nav-tabs nav-pills ">
        <li role="tab" class="active">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_consolidated('skills_group')">
                <icon class="glyphicon glyphicon-earphone"></icon>Agrupado por Skills
            </a>
        </li>
        <li role="tab">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_consolidated('agent_group')">
                <icon class="glyphicon glyphicon-random"></icon>Agrupado por Agentes
            </a>
        </li>
        <li role="tab">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_consolidated('day_group')">
                <icon class="glyphicon glyphicon-alert"></icon>Agrupado por Dia
            </a>
        </li>
        <li role="tab">
            <a href="#panel-report" role="tab" data-toggle="tab" onclick="show_tab_consolidated('hour_group')">
                <icon class="glyphicon glyphicon-alert"></icon>Agrupado por Hora
            </a>
        </li>
    </ul>
    <input type="hidden" id="hidDefaultEvent" value="skills_group">
    <div class="panel-body" >
        <div class="tab-pane fade active in" id="panel-report">
            <div style="width:100%; background-color:#3c8dbc; padding: 5px;" ><button onclick="exportar('csv','export_consolidated');">Csv</button><button onclick="exportar('excel','export_consolidated');">Excel</button></div>
            <table id="table-consolidated" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Queue</th>
                        <th>Received</th>
                        <th>Answered</th>
                        <th>Abandoned   </th>
                        <th>Transferred</th>
                        <th>Attended</th>
                        <th>Answ 10s</th>
                        <th>Answ 15s</th>
                        <th>Answ 20s</th>
                        <th>Aband 10s</th>
                        <th>Aband 15s</th>
                        <th>Aband 20s</th>
                        <th>Wait Time</th>
                        <th>Talk Time</th>
                        <th>Avg Wait</th>
                        <th>Avg Talk</th>
                        <th>% Answ</th>
                        <th>% Unansw</th>
                        <th>Ro10%</th>
                        <th>Ro15%</th>
                        <th>Ro20%</th>
                        <th>Ns10%</th>
                        <th>Ns15%</th>
                        <th>Ns20%</th>
                        <th>Avh2 10%</th>
                        <th>Avh2 15%</th>
                        <th>Avh2 20%</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        show_tab_consolidated('skills_group')
    })

</script>