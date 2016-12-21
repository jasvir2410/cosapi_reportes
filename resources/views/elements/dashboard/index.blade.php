@include('layout.plugins.css-valores-cuadros')
@extends('layout.dashboard')
@section('title', 'Dashboard')
@section('content')

	<p>

	<div class="row-fluid">
		<div id='detail_kpi'>
	    	<div class="loading" id="loading">
	            <li></li>
	            <li></li>
	            <li></li>
	            <li></li>
	            <li></li>
	        </div>
	    </div>
	</div>

	<div class="row-fluid">
	    <div id='detail_agents'>
	    	<div class="loading" id="loading">
	            <li></li>
	            <li></li>
	            <li></li>
	            <li></li>
	            <li></li>
	        </div>
	    </div>
	</div>

@endsection

@section('scripts')
 	<script type="text/javascript">
      $(document).ready(function() {

        detalle_agentes_dashboard();
        detalle_kpi_dashboard();

        desloguear_agente = function ($anexo, $username){

		    $.ajax({
		        type        : 'GET',
		        url         : 'dashboard/logoutagent/'+$anexo+'/'+$username,
		        success: function (data){
		        	detalle_agentes_dashboard();
		        }
		    });
		}
      } );
    </script>

@endsection