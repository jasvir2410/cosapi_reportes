@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')

<div  class="col-md-12" id="container">
    {!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><b>Consolidado de Llamadas</b></h3>
        </div>
        <input type="hidden" id="url" value='listar_llamadas_contestadas'>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        @include('filtros.filtro-fecha')
    </div>
    {!! Form::close() !!}

    @include('elements.consolidated_calls.tabs_consolidated_calls')
</div>