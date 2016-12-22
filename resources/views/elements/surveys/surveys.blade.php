
<div class="panel panel-default">
    <div class="panel-body" >
        <div class="tab-pane fade active in" id="panel-report">
            @include('filtros.export')
            <table id="table-surveys" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Tipo Encuesta</th>
                    <th>Date</th>
                    <th>Hour</th>
                    <th>Username</th>
                    <th>Anexo</th>
                    <th>Telephone</th>
                    <th>Skill</th>
                    <th>Duration</th>
                    <th>Question_01</th>
                    <th>Answer_01</th>
                    <th>Question_02</th>
                    <th>Answer_02</th>
                    <th>Action</th>
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
        show_tab_surveys('surveys'); }
</script>

