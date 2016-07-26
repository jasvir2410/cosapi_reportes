<div class="panel panel-default">
    <ul class="nav nav-tabs nav-pills ">
        <li role="tab" class="active" id="btnAtendidas" >
            <a href="#calls" role="tab" data-toggle="tab" onclick="mostrar_tab_entrantes('calls_completed')">
                <icon class="glyphicon glyphicon-earphone"></icon>Atendidas
            </a>
        </li>
        <li role="tab" id="btnTransferidas">
            <a href="#calls" role="tab" data-toggle="tab" onclick="mostrar_tab_entrantes('calls_transfer')">
                <icon class="glyphicon glyphicon-random"></icon>Transferidas
            </a>
        </li>
        <li role="tab" id="btnAbandonadas">
            <a href="#calls" role="tab" data-toggle="tab" onclick="mostrar_tab_entrantes('calls_abandone')">
                <icon class="glyphicon glyphicon-alert"></icon>Abandonadas
            </a>
        </li>
    </ul>

    <div class="panel-body" >
        <div class="tab-pane fade active in" id="calls">
            <table id="detail-calls" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
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
