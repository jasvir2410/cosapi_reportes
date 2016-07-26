@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')

<div  class="col-md-12" id="container">
	{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><b>Lista de LLamadas Entrantes</b></h3>
			</div>
			<input type="hidden" id="url" value='listar_llamadas_contestadas'>
			<input type="hidden" name="_token" value="{!! csrf_token() !!}">

			@include('elements.filtros.filtro-fecha')
		</div>
	{!! Form::close() !!}

	@include('elements.calls_incoming.tabs_calls_inbound')
</div>

<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		mostrar_tab_entrantes('calls_completed')
	})

	function mostrar_tab_entrantes (evento){
		var data = {
			_token       : $('input[name=_token]').val(),
			fecha_evento : $('#texto').val(),
			evento       : evento
		};

		dataTables_entrantes('detail-calls', data);
	}
</script>


	
