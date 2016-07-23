@include('layout.plugins.css-preloader')
@include('layout.plugins.css-datepicker')
@include('layout.plugins.js-datepicker')
<html>
	<head>
		
	</head>
	<body>
		<form id="frmPrueba">
			<input type="hidden" name="_token" value="{!! csrf_token() !!}">
			Nombre : <input type="text" name="txtNombre" id="idNombre"><br>
			Paterno : <input type="text" name="txtPaterno" id="idPaterno"><br>
			Materno : <input type="text" name="txtMaterno" id="idMaterno">			
		</form>
		<br>
		<button id="btnEnviar">Enviar</button>
	</body>
</html>

<script type="text/javascript">
	
	$(document).on('ready',function(){

		
		$('#btnEnviar').on('click',function(){
			console.log($('#frmPrueba').serialize());
/*
			$.ajax({
				type:'POST',
	            dataType:'JSON',
	            url:'excel/resultado',
	            data:$('#frmPrueba').serialize(),
	            success:function(data){

	            }
			});*/
		});
		
	});

</script>