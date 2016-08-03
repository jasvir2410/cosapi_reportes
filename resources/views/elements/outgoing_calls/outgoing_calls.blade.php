<div class="panel panel-default">
    <div class="panel-body" >
        <div class="tab-pane fade active in" id="panel-report">
            <table id="table-outgoing" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Hour</th>
                    <th>Annexed Origin</th>
                    <th>Number Destination</th>
                    <th>Call Time</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        //show_tab_incoming('calls_completed')
        buscar();
    })

    function buscar(){
        show_tab_outgoing('outgoing_calls');
    }
</script>

