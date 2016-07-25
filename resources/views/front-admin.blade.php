@extends('layout.principal')

@section('title', 'Empresas')

@section('content')
	<div  id="container"><br>
	</div>
@endsection

@section('scripts')
<script>
	$(function(){
		$(".reportes").on("click", function(e){
				var url = e.target.id;//para capturar el atributo id en ajax
				//comparamos si el id es igual al id que se capturo
			
				$.ajax({
					type : "POST",
					url : url,
					datatype: "html",
					data:{
						_token : $('input[name=_token]').val(),
						url : url
					}, 
					success : function(data) {
						$('#container').html (data);

						// DEFINICION DEL EVENTO
						

						// ARBOL UBICADO A LA DERECHA EN LA PARTE SUPERIOR
						$('#urlsistema a').remove();						
						$('#urlsistema').append('<a href="#" id="'+url+'" class="reportes">'+$('#'+url).text()+'</a>');
					},
					error : function(data) {
						$("#container").html ("problemas para actualizar");
					}
				});
		});
	});
</script>
@endsection



