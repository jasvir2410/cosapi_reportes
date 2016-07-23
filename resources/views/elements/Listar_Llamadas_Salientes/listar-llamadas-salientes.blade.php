
@include('layout.plugins.css-dateTables')

<div>
<input name="buscar_agente" id="buscar_agente"aria-controls="example" placeholder="Buscar Agente" class="form-control" type="search">
</div>
<div class="box box-primary table-responsive">
    <p></p>
    <table id="reporte-estados1" class="display nowrap table-responsive" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Hour</th>
                <th>Annexed Origin</th>
                <th>Outgoing Number</th>
                <th>Call Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($llamadasSalientes as $key => $llamadaSaliente)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $llamadaSaliente['fecha']}}</td>
                    <td>{{ $llamadaSaliente['hora']}}</td>
                    <td>{{ $llamadaSaliente['anexo']}}</td>
                    <td>{{ $llamadaSaliente['saliente']}}</td>
                    <td>{{ conversorSegundosHoras($llamadaSaliente['tiempo'],false)}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@include('layout.plugins.js-dateTables')

<script>
   $(document).ready(function() {
        var table_reporte = $('#reporte-estados1').DataTable( {

            dom             : 'Bfrtip',
            paging          : false,
            fixedHeader     : true,
            scrollY         : "300px",
            scrollX         : true,
            scrollCollapse  : true,
            deferRender     : true,
            fixedColumns: {
                leftColumns: 1
            },
            buttons: [
            'copyHtml5', 'csvHtml5', 'excel'
            ]
        } );

        $('#buscar_agente').on( 'keyup', function () {
            table_reporte.search( this.value ).draw();
        } );
        
    } );
</script>

