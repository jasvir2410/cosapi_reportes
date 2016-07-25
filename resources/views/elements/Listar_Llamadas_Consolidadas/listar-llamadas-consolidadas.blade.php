<input type="hidden" name="_token" value="{!! csrf_token() !!}">

<div class="box box-primary table-responsive" id="cuerpo1">
    <table id="reporte-estados1" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Queue</th>
                <th>Received</th>
                <th>Answered</th>
                <th>Abandoned   </th>
                <th>Transferred</th>
                <th>Catered</th>
                <th>Answ 10s</th>
                <th>Answ 15s</th>
                <th>Answ 20s</th>
                <th>Aband 10s</th>
                <th>Aband 15s</th>
                <th>Aband 20s</th>
                <th>Ro10%</th>
                <th>Ro15%</th>
                <th>Ro20%</th>
                <th>Wait Time</th>
                <th>Talk Time</th>
                <th>Avg Wait</th>
                <th>Avg Talk</th>
                <th>% Answ</th>
                <th>% Unansw</th>
            </tr>
        </thead>
    </table>
</div>