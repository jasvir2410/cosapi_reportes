
@extends('layout.dashboard')
@section('title', 'Dashboard 2')

@include('layout.plugins.css-bootstrap3_dialog')
@section('content')


<style type="text/css">
    .highcharts-yaxis-grid .highcharts-grid-line {
	    display : none;
    }

    .active{
        background-color: blue;
    }
</style>
  
<div class="col-md-12 bg-green">
    <div class="col-md-1">
        <h4>Dashboard</h4>
    </div>
    <input type="hidden" name="hidReporttype" id="hidReporttype" value="day">
    <div class="col-md-11">
        <ul class="nav  nav-pills " id="ulOptions">
            <li role="tab" class="active" id="day">
                <a href="#panel-report" role="tab" data-toggle="tab" onclick="refresh_information('day')" >
                    Día
                </a>
            </li>
            <li role="tab" id="week">
                <a href="#" role="tab" data-toggle="tab" onclick="refresh_information('week')">
                    Semana
                </a>
            </li>
            <li role="tab" id="month">
                <a href="#" role="tab" data-toggle="tab" onclick="refresh_information('month')">
                    Mes
                </a>
            </li>
        </ul>
    </div>
</div>

<br><br><br>


<div id='detail_kpi'>

</div>

<script type="text/javascript">
    $(document).ready(function() {

        prueba();
    });

    function prueba(){
        var idevento = $('#hidReporttype').val();
        detalle_kpi_dashboard2(idevento);
        setTimeout('prueba()', 60000);
    }
</script>
@endSection


