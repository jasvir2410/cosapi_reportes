@extends('layout.principal')

@section('title', 'Administrador')

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
					type : "GET",
					url : url,
					datatype: "html", 
					success : function(data) {
						$("#container").html (data);
					},
					error : function(data) {
						$("#container").html ("problemas para actualizar");
					}
				});
		});
	});
</script>
@endsection



