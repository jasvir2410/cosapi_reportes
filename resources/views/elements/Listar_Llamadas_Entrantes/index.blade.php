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

        

    var table1 =$('#reporte-estados1').DataTable({
        "deferRender"       : true,
        "responsive"        : true,

        "paging"            : true,
        "pageLength"        : 100,
        "lengthMenu"        : [100, 200, 300, 400, 500],
        "fixedHeader"       : true,
        "scrollY"           : "300px",
        "scrollX"           : true,
        "scrollCollapse"    : true,

        "select"            : true,

        "dom"               : 'Bfrtip',
        "buttons"           : ['copyHtml5', 'excelHtml5']
    });

    var table2 =$('#reporte-estados2').DataTable({
        "deferRender"       : true,
        "responsive"        : true,

        "paging"            : true,
        "pageLength"        : 100,
        "lengthMenu"        : [100, 200, 300, 400, 500],
        "fixedHeader"       : true,
        "scrollY"           : "300px",
        "scrollX"           : true,
        "scrollCollapse"    : true,

        "select"            : true,

        "dom"               : 'Bfrtip',
        "buttons"           : ['copyHtml5', 'excelHtml5']
    });

    var table3 =$('#reporte-estados3').DataTable({
        "deferRender"       : true,
        "responsive"        : true,

        "paging"            : true,
        "pageLength"        : 100,
        "lengthMenu"        : [100, 200, 300, 400, 500],
        "fixedHeader"       : true,
        "scrollY"           : "300px",
        "scrollX"           : true,
        "scrollCollapse"    : true,

        "select"            : true,

        "dom"               : 'Bfrtip',
        "buttons"           : ['copyHtml5', 'excelHtml5']
    });



    buscar = function (){

        $('#cuerpo1 div').remove();
        $('#cuerpo1').append('<table id="reporte-estados1" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th>Date</th>'+
                                        '<th>Hour</th>'+
                                        '<th>Telephone</th>'+
                                        '<th>Agent</th>'+
                                        '<th>Skill</th>'+
                                        '<th>Duration</th>'+
                                        '<th>Action</th>'+
                                        '<th>Wait Time</th>'+
                                    '</tr>'+
                                '</thead>'+
                            '</table>');

        $('#cuerpo2 div').remove();
        $('#cuerpo2').append('<table id="reporte-estados2" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th>Date</th>'+
                                        '<th>Hour</th>'+
                                        '<th>Telephone</th>'+
                                        '<th>Agent</th>'+
                                        '<th>Skill</th>'+
                                        '<th>Duration</th>'+
                                        '<th>Action</th>'+
                                        '<th>Wait Time</th>'+
                                    '</tr>'+
                                '</thead>'+
                            '</table>');

        $('#cuerpo3 div').remove();
        $('#cuerpo3').append('<table id="reporte-estados3" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th>Date</th>'+
                                        '<th>Hour</th>'+
                                        '<th>Telephone</th>'+
                                        '<th>Agent</th>'+
                                        '<th>Skill</th>'+
                                        '<th>Duration</th>'+
                                        '<th>Action</th>'+
                                        '<th>Wait Time</th>'+
                                    '</tr>'+
                                '</thead>'+
                            '</table>');

        var fecha_evento    = $('#texto').val();
        var nombre_url      = $('#url').val(); 

        url1 = "listar_llamadas_contestadas/rango_fechas/"+fecha_evento;
        url2 = "listar_llamadas_abandonadas/rango_fechas/"+fecha_evento;            
        url3 = "listar_llamadas_transferidas/rango_fechas/"+fecha_evento;           



        table1 = $('#reporte-estados1').DataTable({
            "deferRender"       : true,
            "responsive"        : true,

            "processing"        : true,
            "serverSide"        : true,
            "ajax"              : url1,

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

        table2 = $('#reporte-estados2').DataTable({
            "deferRender"       : true,
            "responsive"        : true,

            "processing"        : true,
            "serverSide"        : true,
            "ajax"              : url3,

            "paging"            : true,
            "pageLength"        : 100,
            "lengthMenu"        : [100, 200, 300, 400, 500],
            "fixedHeader"       : true,
            "scrollY"           : "300px",
            "scrollX"           : true,
            "scrollCollapse"    : true,

            "select"            : true,

            //"dom"               : 'Bfrtip',
            //"buttons"           : ['copyHtml5', 'excelHtml5'],

            
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

        table3 = $('#reporte-estados3').DataTable({
            "deferRender"       : true,
            "responsive"        : true,

            "processing"        : true,
            "serverSide"        : true,
            "ajax"              : url2,

            "paging"            : true,
            "pageLength"        : 100,
            "lengthMenu"        : [100, 200, 300, 400, 500],
            "fixedHeader"       : true,
            "scrollY"           : "300px",
            "scrollX"           : true,
            "scrollCollapse"    : true,

            "select"            : true,

            //"dom"               : 'Bfrtip',
            //"buttons"           : ['copyHtml5', 'excelHtml5'],

            
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

    };   

} );


</script>


	
