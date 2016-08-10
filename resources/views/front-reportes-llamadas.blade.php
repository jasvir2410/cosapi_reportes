<!--el css para el datepicker-->
@include('layout.plugins.css-datepicker')
@include('layout.plugins.css-botones-llamadas')
<!--el js para el datepicker-->
@include('layout.plugins.js-datepicker')

<div  class="col-md-12 " id="container">
	<div  id="filtro-fecha">
		@include('elements.filtro-llamadas')
	</div>
	<div id="resumen2">
		
	</div>
</div>

	<!--script para daterange y agregarle formato año-mes-dia-->
<script >
	$(function() {
		$('input[name="fecha_evento2"]').daterangepicker(

		{
			locale: {
				format: 'YYYY-MM-DD'
			}
		}

		);
	});

	$(function(){
		$(".reportes-llamadas").on("click", function(e){
				var direccion = e.target.id;//para capturar el atributo id en ajax
				var fecha_evento2 =($('input:text[id=fecha]').val());//para capturar valor de textbox mediante el id
				//alert(fecha +" "+"esta es la fecha que elegiste xD ");
				url="reportes-llamadas/"+direccion+"/"+fecha_evento2;

				//url="reporte-estado/buscar-estado/"+fecha_evento; esto es un ejemplooo

				//alert(url);
				$.ajax({
					type: "GET",
					url : url,
					datatype: "html", 
					success : function(data) {
						$("#resumen2").html (direccion + data);
					},
					error : function(data) {
						$("#resumen2").html ("problemas para actualizar");
					}
				});
		});
	});

	
	

	
</script>




