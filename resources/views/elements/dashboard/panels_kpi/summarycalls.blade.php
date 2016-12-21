<div class="box box-success box-solid">
	<div class="box-header with-border">
			<h3 class="box-title">Summary Calls</h3>
	</div>
	<!-- /.box-header -->

	<div class="box-body">
		<div class="col-md-4">
			<div id="divAtendidas" style="width: 300px; height: 200px; float: left"></div>
		</div>
		<div class="col-md-4">
			<div id="divAbandonadas"   style="width: 300px; height: 200px; float: left"></div>
		</div>
		<div class="col-md-4">
			<div id="divSla"  style="width: 300px; height: 200px; float: left"></div>
		</div>
	</div>
<!-- /.box-body -->
</div>

<script type="text/javascript">
	function LoadGraphicsSummary(){
		var key;
            var id_divs       = ['#divAtendidas','#divAbandonadas','#divSla']; //Id de los divs de los graficos
            var function_name = ['gaugeAtendidas','gaugeAbandonadas','gaugeSla']; //Nombre de la funcion que define las caracteristicas del grafico
            var title_divs    = ['% Answered','% Abandoned','% SLA']; //Titulos de los graficos
            var series_name   = ['Percentage','Percentage','Percentage']; //Unidad de medidas
            var data_graficos = [{{$Atendidas_porcentaje}},{{$Abandonadas_porcentaje}},{{$Sla}}]; //Datos de los graficos (DINAMICO BLADE)
            var max_data      = [100,100,100]; //Maxima cantidad de grafico (DINAMICO BLADE)
            var colores       = [['#DF5353','#E1E11D','#55BF3B'],['#55BF3B','#E1E11D','#DF5353'],['#DF5353','#E1E11D','#55BF3B']]; //Colores para colocar segun los rangos
            var rangos        = [[0.3,0.6,1],[0.3,0.6,1],[0.84,0.89,1]]; //Rangos para el cambio de color

            for(key in id_divs ){
                var functionaname = function_name[key];
                var functionaname = {
                    chart   : { type: 'solidgauge' },
                    title   : null,
                    pane    : {
                        center      : ['50%', '85%'],
                        size        : '140%',
                        startAngle  : -90,
                        endAngle    : 90,
                        background  : {
                            backgroundColor : (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius     : '60%',
                            outerRadius     : '100%',
                            shape           : 'arc'
                        }
                    },
                    tooltip : { enabled: false },
                    yAxis   : {
                        stops               : [
                            [rangos[key][0]    , colores[key][0]],   // red
                            [rangos[key][1]    , colores[key][1]],   // yellow
                            [rangos[key][2]    , colores[key][2]]    // green
                        ],
                        lineWidth           : 0,
                        minorTickInterval   : null,
                        tickAmount          : 2,
                        title               : { y : -70 },
                        labels              : { y : 16 }
                    },
                    plotOptions: {
                        solidgauge  : {
                            dataLabels      : {
                                y               : 5,
                                borderWidth     : 0,
                                useHTML         : true
                            }
                        }
                    }
                };

                $(id_divs[key]).highcharts(Highcharts.merge(functionaname, {
                    yAxis   : {
                        min        : 0,
                        max        : max_data[key],
                        title      : { text : title_divs[key] }
                    },
                    credits : { enabled : false },
                    series  : [{
                        name       : series_name[key],
                        data       : [data_graficos[key]],
                        dataLabels : {
                            format     : '<div style="text-align:center"><span style="font-size:25px;color:'+'">{y}</span><br/>'+'<span style="font-size:12px;color:silver">'+series_name[key]+'</span></div>'
                        }
                    }]
                }));
            }
	}
</script>