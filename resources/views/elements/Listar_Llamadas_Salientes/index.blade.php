@include('layout.plugins.css-datepicker')
@include('layout.plugins.js-datepicker')

<div  class="col-md-12" id="container">
	<div  id="filtro-fecha">
		
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

	</div>
	<div id="resumen">

	</div>
</div>

<script src="{{ asset('cosapi/js/cosapi_adminlte.js')}}"></script>