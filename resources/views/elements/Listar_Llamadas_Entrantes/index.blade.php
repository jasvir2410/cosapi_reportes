@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-dateTables')
@include('layout.plugins.js-datepicker')
@include('layout.plugins.js-dateTables')

<div  class="col-md-12" id="container">
	<div  id="filtro-fecha">
		
		{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
		<div >
			<div class="box box-primary">
			    <div class="box-header">
			    	<h3 class="box-title"><b>Lista de LLamadas Entrantes</b></h3>
		      	</div>
				<input type="hidden" id="url" value='listar_llamadas_contestadas'>

				@include('elements.filtros.filtro-fecha')
				
		    </div>
	
		</div>
	</div>
	{!! Form::close() !!}		

</div>


<div id="resumen">
	@include('elements.Listar_Llamadas_Entrantes.listar-llamadas-entrantes')
</div>

	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {

     /* script para daterange y agregarle formato aÃ±o-mes-dia */
    $('input[name="fecha_evento"]').daterangepicker(
        {
            locale: {
                format: 'YYYY-MM-DD'
            }
        }
    );

    //CONTROL DE PESTAÑAS
    $('#tblTransfer').hide();
    $('#tblAbandonadas').hide();

    $('#btnAtendidas').on('click',function(){
        $('#btnTransferidas').removeClass('active');
        $('#btnAbandonadas').removeClass('active');
        $('#btnAtendidas').addClass('active');
        $('#tblAtendidas').show();
        $('#tblTransfer').hide();
        $('#tblAbandonadas').hide();
    });

    $('#btnTransferidas').on('click',function(){
        $('#btnAtendidas').removeClass('active');
        $('#btnAbandonadas').removeClass('active');
        $('#btnTransferidas').addClass('active');
        $('#tblAtendidas').hide();
        $('#tblTransfer').show();
        $('#tblAbandonadas').hide();
    });

    $('#btnAbandonadas').on('click',function(){
        $('#btnTransferidas').removeClass('active');
        $('#btnAtendidas').removeClass('active');
        $('#btnAtendidas').removeClass('active');
        $('#btnAbandonadas').addClass('active');
        $('#tblAtendidas').hide();
        $('#tblTransfer').hide();
        $('#tblAbandonadas').show();
    });

    
    var eventos =['calls_completed','calls_transfer','calls_abandone'];
    $('table display').dataTable();

    buscar = function (){
        $('#reporte-estados0').dataTable().fnDestroy(); 
        $('#reporte-estados1').dataTable().fnDestroy(); 
        $('#reporte-estados2').dataTable().fnDestroy(); 
        //CONSTRUCCION Y CARGA DE DATOS A LAS TABLAS 
        for(var j=0; j<eventos.length; j++){

            

            //CREACION DE DATATABLES
            $('#reporte-estados'+j).DataTable({
                "deferRender"       : true,
                "responsive"        : true,
                "processing"        : true,
                "serverSide"        : true,
                "ajax"              : {
                    url : 'calls_inbound/consulta',
                    type: 'POST',
                    data:
                    {
                        _token       : $('input[name=_token]').val(),
                        fecha_evento : $('#texto').val(),
                        evento       : eventos[j]
                    }
                },
                "paging"            : true,
                "pageLength"        : 100,
                "lengthMenu"        : [100, 200, 300, 400, 500],
                "fixedHeader"       : true,
                "scrollY"           : "300px",
                "scrollX"           : true,
                "scrollCollapse"    : true,
                "select"            : true,
                "dom"               : 'Bfrtip',
                "buttons"           : 
                    [
                        'copyHtml5', {
                            text: 'Excel',
                            action: function ( e, dt, node, config ) {


                                $.ajax({
                                    type:'GET',
                                    dataType:'JSON',
                                    url:'export_contestated/rango_fechas/2016-07-03%20-%202016-07-03',
                                    success: function(data){
                                        return data;
                                    }
                                });
                            }
                        }
                    ],             
                "columns"           : [
                    {"data":"fechamod"},
                    {"data":"timemod"},
                    {"data":"clid"},
                    {"data":"agent"},
                    {"data":"queue"},
                    {"data":"info2"},
                    {"data":"event"},
                    {"data":"info1"}
                ]
            });
        }
    };   
} );

</script>


	
