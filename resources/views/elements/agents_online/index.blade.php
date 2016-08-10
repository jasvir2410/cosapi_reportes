@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')

<div  class="col-md-12" id="container">
	{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><b>Lista de Agentes Online</b></h3>
			</div>
			<input type="hidden" id="url" value='agents_onlie'>

			@include('filtros.filtro-fecha')
			@include('filtros.button-search')
			</div>
		</div>
	{!! Form::close() !!}

	@include('elements.agents_online.agents-online')
</div>

<script type="text/javascript">
	$(document).ready(function(){

		/* script para daterange y agregarle formato aÃ±o-mes-dia */
	    $('input[name="fecha_evento"]').daterangepicker(
	        {
	            locale: {
	                format: 'YYYY-MM-DD'
	            }
	        }
	    );

		buscar = function(){

			$('#reporte-estados1').dataTable().fnDestroy();
			
			$('#reporte-estados1').DataTable({
		        "responsive"        : true,
		        "processing"        : true,
		        "ajax"              : {
		            url : 'agents_online',
		            dataSrc : '',
		            type: 'POST',
		            data: {
						_token       : $('input[name=_token]').val(),
						fecha_evento : $('#texto').val()
		            }
		        },
		        "paging"            : true,
		        "pageLength"        : 100,
		        "lengthMenu"        : [100, 200, 300, 400, 500],
		        "scrollY"           : "300px",
		        "scrollX"           : true,
		        "scrollCollapse"    : true,
		        "select"            : true,
		        "dom"             : 'Bfrtip',
                "buttons"           : ['copyHtml5', 'excelHtml5'],		        
		        "columns"			: [    
				                        {"data":"date_agent"},
				                        {"data":"hour_agent"},
				                        {"data":"quantity"}
				                    ]
		    });

		}
		
	})	



</script>


	
