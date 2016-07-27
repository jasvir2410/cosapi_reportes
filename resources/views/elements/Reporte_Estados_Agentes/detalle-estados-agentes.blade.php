@include('layout.plugins.css-dateTables')

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
<div>
<input name="buscar_agente" id="buscar_agente" aria-controls="example" placeholder="Buscar Agente" class="form-control" type="search">
</div>
<div class="box box-primary table-responsive">
    <p></p>
    <table id="reporte-estados" class="display nowrap table-responsive" cellspacing="0" width="100%">
        <thead>
            <tr class="tr-a">
                <th colspan="2"> </th>
                <th class="th-centro th-1" colspan="11"><b>DETALLES<b></th>
                <th class="th-centro th-2" colspan="6"><b>RESUMEN<b></th>
            </tr>
            <tr>


                <th >#</th>
                <th  >AGENTE</th>
                @foreach ($eventos as $evento)
                    <th > {{ upper($evento->name) }} </th>
                @endforeach
                <th >LOGUEADO</th>
                <th >TIEMPOS AUXILIARES</th>
                <th >TALK TIME</th>
                <th >SALIENTE HABLADO</th>
                <th >NIVEL DE OCUPACION CLARO </th>
                 <th >NIVEL DE OCUPACION COSAPI</th>
            </tr>
        </thead>
        <tbody>

            @define $resumenEventos = calcularTiempoEntreEstados($usuarios,$eventos,$detalleEventos)
            @foreach ($resumenEventos as $key => $resumenEvento)
                <tr>
                    <td>{{ $usuarios[$key-1]['id'] }}</td>
                    <td>{{ $usuarios[$key-1]['primer_nombre'].' '.$usuarios[$key-1]['apellido_paterno'] }}</td>
                            <!--tiempoTotalLogueado = staftime-->
                            @define $tiempoTotalLogueado  = 0
                            <!--totalTiemposAuxiliares = BREAK + SS.HH. + REFRIGERIO + FEEDBACK + CAPACITACION -->
                            @define $totalTiemposAuxiliares   = 0
                            <!--totalTiemposTrabajados = acdIn + acdOut + ACW + GestionBackoffice para cosapi-->
                            @define $totalTiemposTrabajados = 0
                            <!--totalTiemposTrabajados = acdIn + acdOut ESTO PARA CLARO-->
                            @define $totalTiemposTrabajadosClaro = 0

                            @define $totalACD = 0

                        @foreach ($resumenEvento as $resumenEvent)

                                @define $evento_id              = $resumenEvent['evento_id']
                                @define $tiempoTotalLogueado    += $resumenEvent['tiempo']

                                <!-- Calculo para Tiempos Auxiliares-->
                                @foreach ($eventos_auxiliares as $eventos_auxiliar)

                                    @define $id_evento_auxiliar = $eventos_auxiliar['id'];

                                    @if ( $evento_id == $id_evento_auxiliar  )
                                        @define $totalTiemposAuxiliares += $resumenEvent['tiempo']
                                        @endif
                                @endforeach

                                <!--Calculo para totalTiemposTrabajados para cosapi-->
                                @foreach ($cosapi_eventos as $cosapi_evento)

                                    @define $id_cosapi_evento = $cosapi_evento['id'];

                                    @if ( $evento_id == $id_cosapi_evento  )
                                            @define $totalTiemposTrabajados += $resumenEvent['tiempo']
                                    @endif

                                @endforeach

                                <!--Calculo para totalTiemposTrabajados para Claro-->
                                @foreach ($claro_eventos as $claro_evento)

                                    @define $id_claro_evento = $claro_evento['id']

                                    @if ($evento_id == $id_claro_evento)
                                            @define $totalTiemposTrabajadosClaro += $resumenEvent['tiempo']
                                    @endif

                                @endforeach

                            <!--Calculo para tiempo ACD -->
                            @if ( $evento_id == 1 )
                                @define $totalACD = $resumenEvent['tiempo']
                            @endif
                            <!--Calculo para tiempoLlamadaEntrante-->
                            @if ( $evento_id == 8 )
                                @define $tiempoLlamadaEntrante = $resumenEvent['tiempo']
                            @endif

                            <td  class="td-b3" >{{ conversorSegundosHoras($resumenEvent['tiempo'], $formato) }}</td>

                        @endforeach

                        @define $tiempoLlamadaSaliente = calcularTiempoLlamadaSaliente($usuarios[$key-1]['username'], $AsteriskCDR)
                        @define $tiempoTotalHablado    = $tiempoLlamadaEntrante + $tiempoLlamadaSaliente
                        

                          <!--Calculo para Porcentaje de Ocupación Claro-->
                        @if($totalTiemposTrabajadosClaro != 0 )
                          @define $porcentajeOcupacionClaro= round(($totalTiemposTrabajadosClaro/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
                        @else
                          @define $porcentajeOcupacionClaro= 0
                        @endif

                        <!--Calculo para Porcentaje de Ocupación cosapi-->
                        @if($totalTiemposTrabajados != 0 )
                          @define $porcentajeOcupacionCosapi= round(($totalTiemposTrabajados/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
                        @else
                          @define $porcentajeOcupacionCosapi= 0
                        @endif



                    <td class="td-b3">{{ conversorSegundosHoras($tiempoTotalLogueado, $formato)       }}</td>
                    <td class="td-b3">{{ conversorSegundosHoras($totalTiemposAuxiliares, $formato)    }}</td>
                    <td class="td-b3">{{ conversorSegundosHoras($tiempoTotalHablado, $formato)        }}</td>
                    <td class="td-b3">{{ conversorSegundosHoras($tiempoLlamadaSaliente, $formato)     }}</td>
                    <!--Porcentaje de Ocupacion-->
                    <td  class="td-b3">{{ $porcentajeOcupacionClaro }} %</td>
                    <td  class="td-b3">{{ $porcentajeOcupacionCosapi }} %</td>

                </tr>
            @endforeach

        </tbody>
    </table>

</div>

@include('layout.plugins.js-dateTables')

<script>
   $(document).ready(function() {
        var table_reporte = $('#reporte-estados').DataTable( {

            dom: 'Bfrtip',
            paging:false,
            fixedHeader: true,
            scrollY:        "300px",
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns:   {
                leftColumns: 2
            },
            buttons: [
            'copyHtml5', 'csvHtml5', 'excelHtml5'
            ]
        } );

        $('#buscar_agente').on( 'keyup', function () {
            console.log(this.value);
            table_reporte.search( this.value ).draw();
        } );
        
    } );
</script>

