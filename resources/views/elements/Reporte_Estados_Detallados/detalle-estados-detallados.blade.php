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
                <th>NOMBRE COMPLETO</th>
                <th>FECHA/HORA</th>
                <th>NOMBRE DEL EVENTO</th>
                <th>REALIZADO POR</th>
                <!--<th>OBSERVACIONES</th>-->
            </tr>
        </thead>
        <tbody>
            @foreach ($detalleEventos as $key => $detalleEvento)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $detalleEvento['user']['primer_nombre'].'  '.$detalleEvento['user']['segundo_nombre'].'  '.$detalleEvento['user']['apellido_paterno'].'  '.$detalleEvento['user']['apellido_materno'] }}</td>
                    <td>{{ $detalleEvento['fecha_evento'] }}</td>
                    <td>{{ $detalleEvento['evento']['name'] }}</td>
                    <!--PARA DECLARAR UNA VARIABLE EN BLADE-->
                    @define $observaciones = $detalleEvento['observaciones']

                    @if ($observaciones=='')
                        @define $evento_realizado='Evento del Agente'
                    @else
                        @define $evento_realizado=$observaciones
                    @endif
                    
                    <td>{{ $evento_realizado }}</td>
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
            'copyHtml5', 'csvHtml5', 'excelHtml5'
            ]
        } );

        $('#buscar_agente').on( 'keyup', function () {
            table_reporte.search( this.value ).draw();
        } );
        
    } );
</script>

