<div class="panel panel-default">
    <div class="panel-body" >
        <button id="Guardar" onclick="Guardar();" class="btn btn-primary pull-right">Guardar</button>
        <br><br>
        <div class="tab-pane fade active in" id="panel-report">
            {!! Form::open([ 'method' => '', 'name'=>'asignar_cola']) !!}
            <input type="hidden" value="{{$list_users}}" id="list_users" name="list_users">
            <table id="table-agents-queue" class="table table-bordered display nowrap table-responsive " cellspacing="0" width="100%">
                <thead >
                    <tr>
                        <th class="col-md-6">
                            USUARIOS
                        </th>
                        @foreach ($Colas as $cola)
                            <th class="col-md-1">
                                <center>
                                    <input type="checkbox" class="checkGeneral{{$cola['id']}}" onclick="mark_all('{{$cola['id']}}');"/>
                                </center>
                                {{$cola['name']}}
                            </th>
                        @endforeach
                        <th class="col-md-1">
                            Prioridad
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($Users as $user)
                    <tr>
                        <td class="col-md-6">
                            {{$user['primer_nombre'].' '.$user['apellido_paterno']}}
                        </td>
                        @foreach ($Colas as $cola)
                            <td class="col-md-1">
                                <center>
                                    <input type="checkbox" name="{{$user['id'].'_'.$cola['id']}}" class="checkboxCola"/>
                                </center>
                            </td>
                        @endforeach
                        <td class="col-md-1">
                            <input type="number" min="1" max="10" value="1" name="{{$user['id']}}" class="numberPrioridad">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){

        loading_mark();

        $('.numberPrioridad').keyup(function(){
            //Valida que el usuario no ingrese un numero mayor a 10 en la prioridad
            var prioridad = $(this).val();
            if(prioridad>10 || prioridad <=0){
                $(this).val(1);
                alert('Por favor de ingresar un valor en el rango del 1 al 10 !!!');
            }
        });

    });

    function loading_mark(){
        //Carga la configuración actual de las colas
        $.ajax({
            type        : 'POST',
            url         : 'agents_queue/mark',
            cache       : false,
            data        :{ _token : $('input[name=_token]').val() },
            success: function(data){
                for(var posicion = 0;posicion < data.length; posicion++){
                    var name_checked = data[posicion]['user_id']+'_'+data[posicion]['queue_id']
                    $('input[name='+name_checked+']').prop("checked", "checked");
                    $('input[name='+data[posicion]['user_id']+']').val(data[posicion]['priority']);
                }
            },
            error : function(data) {
                $("#plantilla_asignar_cola").html ("A ocurrido un Error, porfavor de comunicarse con el personal de TI !!!");
            },
        });
    }

    function buscar(){
        //Busca los usuarios seleccionados, para ser listados en la plantilla de asignaciones
        $.ajax({
            type        : 'POST',
            url         : 'agents_queue/search_users',
            cache       : false,
            data        : $('form[name=buscador]').serialize(),
            success: function(data){
                $('#plantilla_asignar_cola').html(data);
            },
            error : function(data) {
                $("#plantilla_asignar_cola").html ("A ocurrido un Error, porfavor de comunicarse con el personal de TI !!!");
            },
        });
    }

    function mark_all(cola){
        //Marca todos los check debajo de la cola.
        if ($(".checkGeneral"+cola).is(':checked')) {
            $("input[name$=_"+cola+"]").prop("checked", "checked");
        }else{
            $("input[name$=_"+cola+"]").prop("checked", false);
        }
    }

    function Guardar(){
        //Guardar la información marcada
        $.ajax({
            type        : 'POST',
            url         : 'agents_queue/assign_queue',
            cache       : false,
            data        : $('form[name=asignar_cola]').serialize(),
            success: function(data){
                alert('Los datos fueron guardados correctamente !!!')
            },
            error : function(data) {
                $("#plantilla_asignar_cola").html ("A ocurrido un Error, porfavor de comunicarse con el personal de TI !!!");
            },
        });
    }
</script>
