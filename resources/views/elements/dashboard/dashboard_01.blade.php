@include('layout.plugins.css-valores-cuadros')
@extends('layout.dashboard')
@section('title', 'Dashboard')
@section('content')

	<p>

	<div class="row-fluid">
		<div id='detail_kpi'></div>
	    <div id='total_encoladas'></div>
	</div>

	<!-- Panel de Llamadas en Cola -->
	<div class="row-fluid">
		<div class="col-md-12">
			<div class="box box-primary box-solid collapsed-box">
		    	<div class="box-header with-border ">
		    		<h3 class="box-title"><b>Details Encoladas</b></h3>
		      		<div class="box-tools pull-right">
		        		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
		        		</button>
		      		</div>
		    	</div>
    			<div id='detail_encoladas' class="box-body"></div>
		  	</div>
		</div>
	</div>

	<!-- Panel de Estado de los Agentes -->
	<div class="row-fluid">
		<div class="col-md-12">
			<div class="box box-primary box-solid ">
		    	<div class="box-header with-border ">
		    		<h3 class="box-title"><b>Details Calls</b></h3>
		      		<div class="box-tools pull-right">
		        		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		        		</button>
		      		</div>
		    	</div>
    			<div id='detail_agents' class="box-body"></div>
		  	</div>
		</div>
	</div>

@endsection

@section('scripts')
 	<script type="text/javascript">
      $(document).ready(function() {

        detalle_kpi_dashboard_01();
        total_encoladas_dashboard_01();
        detalle_agentes_dashboard_01();
        detail_encoladas_dashboard_01();
        validar_sonido();

        desloguear_agente = function ($anexo, $username){

		    $.ajax({
		        type        : 'GET',
		        url         : 'dashboard_01/logoutagent/'+$anexo+'/'+$username,
		        success: function (data){
		        	detalle_agentes_dashboard_01();
		        }
		    });
		}
      } );
    </script>

@endsection