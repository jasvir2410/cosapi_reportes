<style>
    .td-b3{
        text-align: center;
    }
    .th-centro {
        text-align: center;
    }
    .th-1{

        background-color: #86bee3;
    }
    .th-2{
        background-color: #c1f5ff;
    }

    .tr-a{
        line-height: 12px;
    }
</style>

<div class="box box-primary table-responsive">
    <p></p>
    <table id="reporte-estados" class="display  table table-bordered nowrap table-responsive" cellspacing="0" width="100%">
        <thead>
            <tr class="tr-a">
                <th colspan="2"> </th>
                <th class="th-centro th-1" colspan="11"><b>DETALLES<b></th>
                <th class="th-centro th-2" colspan="6"><b>RESUMEN<b></th>
            </tr>
            <tr>
                <th >AGENTE</th>
                <th >ACD</th>
                <th >BREAK</th>
                <th >SSHH</th>
                <th >REFRIGERIO</th>
                <th >FEEDBACK</th>
                <th >CAPACITACIÓN</th>
                <th >GESTIÓN BACKOFFICE</th>
                <th >INBOUND</th>
                <th >OUTBOUND</th>
                <th >ACW</th>
                <th >DESCONECTADO</th>
                <th >LOGUEADO</th>
                <th >TIEMPOS AUXILIARES</th>
                <th >TALK TIME</th>
                <th >SALIENTE HABLADO</th>
                <th >NIVEL DE OCUPACION CLARO </th>
                <th >NIVEL DE OCUPACION COSAPI</th>
            </tr>
        </thead>
    </table>

</div>
<script type="text/javascript">
    $(document).ready(function(){

        /* script para daterange y agregarle formato aÃ±o-mes-dia */
        $('input[name="fecha_evento"]').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
        
        buscar();
    });

    function buscar(){
        var fecha =$("#texto").val();
        $("#reporte-estados").dataTable().fnDestroy();
        $("#reporte-estados").DataTable({
            "ajax"              : {
                url     : "events_consolidated",
                type    : "POST",
                dataSrc : "data",
                data :{
                    _token       : $('input[name=_token]').val(),
                    fecha_evento : fecha
                }
            },
            "columns"    : [
                {"data":"agent"},
                {"data":"acd"},
                {"data":"break"},
                {"data":"sshh"},
                {"data":"refrigerio"},
                {"data":"feeedback"},
                {"data":"capacitacion"},
                {"data":"backoffice"},
                {"data":"indbound"},
                {"data":"outbound"},
                {"data":"acw"},
                {"data":"desconectado"},
                {"data":"logueado"},
                {"data":"auxiliares"},
                {"data":"talk"},
                {"data":"saliente"},
                {"data":"ocupation_claro"},
                {"data":"ocupation_cosapi"}
            ],
            "paging"            : true,
            "pageLength"        : 100,
            "lengthMenu"        : [100, 200, 300, 400, 500],
            "scrollY"           : "300px",
            "scrollX"           : true,
            "scrollCollapse"    : true,
            "select"            : true,
            "dom": 'Bfrtip',
            "buttons": [
                'copy',
                'csv',
                'excel'
            ],
            fixedColumns:   {
                leftColumns: 1
            },
        });
    }

</script>
