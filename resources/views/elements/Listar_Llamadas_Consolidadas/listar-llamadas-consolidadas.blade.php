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

<script type="text/javascript">
    
    $(document).on('ready',function(){



        buscar=function (){

                console.log($('form[name=buscador]').serialize());
                
                var data = $('#frmBuscador').serialize().split('&');

                $('#reporte-estados1').dataTable().fnDestroy();   

                

                $('#reporte-estados1').DataTable({
                    "deferRender"       : true,

                    "processing"        : true,
                    "serverSide"        : true,
                    "ajax"              : {
                        url : 'listar_llamadas_consolidadas/consulta',
                        type: 'POST',
                        data: data
                    },

                    "paging"            : false,
                    "scrollY"           : "300px",
                    "scrollX"           : true,
                    "scrollCollapse"    : true,

                    "select"            : true,

                    "responsive"        : true,
                    "dom"               : 'Bfrtip',
                    "buttons"           : ['copyHtml5', 'excelHtml5'],

                    
                    "columns"           : [
                        {"data":"name"},
                        {"data":"recibidas"},
                        {"data":"atendidas"},
                        {"data":"abandonados"},
                        {"data":"transferencias"},
                        {"data":"constestadas"},
                        {"data":"constestadas_10"},
                        {"data":"constestadas_15"},
                        {"data":"constestadas_20"},
                        {"data":"abandonadas_10"},
                        {"data":"abandonadas_15"},
                        {"data":"abandonadas_20"},
                        {"data":"ro10"},
                        {"data":"ro15"},
                        {"data":"ro20"},
                        {"data":"min_espera"},
                        {"data":"duracion"},
                        {"data":"avgw"},
                        {"data":"avgt"},
                        {"data":"answ"},
                        {"data":"unansw"},                  
                    ]
                });
                };

    });

</script>