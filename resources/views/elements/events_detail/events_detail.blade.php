<div class="panel panel-default">
    <div class="panel-body" >
        <div class="tab-pane fade active in" id="panel-report">
            <table id="table-detail-events" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>NOMBRE COMPLETO</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>NOMBRE DEL EVENTO</th>
                    <th>REALIZADO POR</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        buscar();
    })

    function buscar(){
        show_tab_detail_events('events_detail'); }

</script>