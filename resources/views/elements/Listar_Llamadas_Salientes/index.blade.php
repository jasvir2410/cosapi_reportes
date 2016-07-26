@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')
<div  class="col-md-12" id="container">
		
		{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
		<div >
		  <div class="box box-primary">

		    <div class="box-header">
		      <h3 class="box-title"><b>Lista de LLamadas Salientes</b>
		      </div>
				<input type="hidden" id="url" value='listar_llamadas_salientes'>

				@include('elements.filtros.filtro-fecha')

		      </div>
	
		    </div>
		</div>
		{!! Form::close() !!}

	@include('elements.Listar_Llamadas_Salientes.listar-llamadas-salientes')
</div>

<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>

<script type="text/javascript">
	$(document).on('ready',function(){
		buscar();
	})

	function buscar (){
		var data = {
			_token       : $('input[name=_token]').val(),
			fecha_evento : $('#texto').val()
		};

		dataTables_entrantes('reporte-estados1', data, 'calls_outgoing');
	}
</script>
