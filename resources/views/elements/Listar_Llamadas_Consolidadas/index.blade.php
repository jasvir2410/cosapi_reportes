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
		      	
				<input type="hidden" id="url" value='{{$evento}}'>

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

	    $('#reporte-estados1').dataTable();

	    buscar=function (){

            $('#reporte-estados1').dataTable().fnDestroy();   
            

            $('#reporte-estados1').DataTable({
                "deferRender"       : true,

                "processing"        : true,
                "serverSide"        : true,
                "ajax"              : {
                    url : 'calls_consolidated/consulta',
                    type: 'POST',
                    data:
                    {
                        _token : $('input[name=_token]').val(),
                        fecha_evento : $('#texto').val(),
                        url : $('#url').val()
                    }
                },

                "paging"            : false,
                "scrollY"           : "300px",
                "scrollX"           : true,
                "scrollCollapse"    : true,

                "select"            : true,

                "responsive"        : false,
                "dom"               : 'Bfrtip',
                "buttons"           : ['copyHtml5', 'excelHtml5'],

                
                "columns"           : [
                    {"data":"name"},
                    {"data":"recibidas"},
                    {"data":"atendidas"},
                    {"data":"abandonados"},
                    {"data":"transferencias"},
                    {"data":"constestadas"},
                    {"data":"constestadas_10"},
                    {"data":"constestadas_15"},
                    {"data":"constestadas_20"},
                    {"data":"abandonadas_10"},
                    {"data":"abandonadas_15"},
                    {"data":"abandonadas_20"},
                    {"data":"ro10"},
                    {"data":"ro15"},
                    {"data":"ro20"},
                    {"data":"min_espera"},
                    {"data":"duracion"},
                    {"data":"avgw"},
                    {"data":"avgt"},
                    {"data":"answ"},
                    {"data":"unansw"},
                    {"data":"ns10"},
                    {"data":"ns15"},
                    {"data":"ns20"},
                    {"data":"avh210"},
                    {"data":"avh215"},
                    {"data":"avh220"},                  
                ]
            });
        };

         	
	});
</script>