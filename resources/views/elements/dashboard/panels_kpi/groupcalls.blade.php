<div class="box box-success box-solid">
  <div class="box-header with-border">
		<h3 class="box-title">Group Calls</h3>
  </div>
  <div class="box-body">
  	<div class="col-md-12">
      <canvas id="myChart" height="230px"></canvas>
    </div>
  </div>
<!-- /.box-body -->
</div>

<script type="text/javascript">
	function LoadGraphicsPie(){
		//Script para el gráfico pastel del panel Group Calls
		var ctx = document.getElementById("myChart");
		var myChart = new Chart(ctx, {
		    type         : 'pie',
		    responsive   : true,
		    data         : {
		        labels       : ["Incoming","Transfer","Abandon","Outbound"],
		        datasets     : [{
		            label            : '# Llamadas',
		            data             : [{{$Incoming}}, {{$Transferidas}},{{$Abandonadas}}, {{$Salientes}}],
		            backgroundColor  : [
		                'rgba(38, 181, 119, 0.9)',
		                'rgba(034, 113, 179, 0.9)',
		                'rgba(231,41,43, 0.9)',
		                'rgba(204, 217, 44, 0.9)'
		            ],
		            borderColor      : [
		                'rgba(38, 181, 119,1)',
		                'rgba(034, 113, 179, 1)',
		                'rgba(231,41,43, 1)',
                        'rgba(204, 217, 44, 0.9)'
		            ],
		            borderWidth      : 1
		        }]
		    },
		    options:{
		    	legend: {
		    		position : 'right',
        			display  : true,
        			fullWidth: true
		    	}
		    }

		});
	}
</script>