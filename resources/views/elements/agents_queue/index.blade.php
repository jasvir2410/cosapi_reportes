@include('layout.plugins.css-select2')
@include('layout.plugins.js-select2')

<div  class="col-md-12" id="container">
    {!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><b>Asignación de Colas</b></h3>
        </div>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        @include('filtros.filtro-usuario')
        @include('filtros.button-search')
        </div>
    </div>
    {!! Form::close() !!}

    <div id="plantilla_asignar_cola">
        @include('elements.agents_queue.agents_queue')
    </div>

</div>

