@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')

<div  class="col-md-12" id="container">
	<div  id="filtro-fecha">
		
		{!! Form::open([ 'method' => '', 'name'=>'buscador', 'id'=>'frmBuscador']) !!}
		<div >
		  <div class="box box-primary">

		    <div class="box-header">
		      <h3 class="box-title"><b>Consolidado de Llamadas</b></h3>
		      </div>
		      	
				<input type="text" id="url" value='{{$evento}}'>

				@include('elements.filtros.filtro-fecha')
				
		       
		      </div>
	
		    </div>
		</div>
		{!! Form::close() !!}
		

	</div>
	<div id="resumen">

		@include('elements.Listar_Llamadas_Consolidadas.listar-llamadas-consolidadas')
		
	</div>
</div>

<script type="text/javascript">
	$(document).on('ready',function(){

		/* script para daterange y agregarle formato aÃ±o-mes-dia */
	    $('input[name="fecha_evento"]').daterangepicker(
	        {
	            locale: {
	                format: 'YYYY-MM-DD'
	            }
	        }
	    );

	    
         	
	});
</script>