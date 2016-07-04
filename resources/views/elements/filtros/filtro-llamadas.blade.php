{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
<div >
  <div class="box box-primary">
  	<div class="box-header">
      <h3 class="box-title"><b>Reportes de Llamadas</b></h4>
    </div>
    <div class="box-body">
    	<!-- Date range -->
      <div class="form-group col-md-5">
      	<label>Seleccione rango de fechas:</label>
      	<div class="input-group ">
      	  <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" id="fecha" name="fecha_evento2" class="form-control pull-right">
      	</div>	
      </div>

      <!--***********botones atendidas, ranferidas, abandonadas*************-->

      <div class="form-group col-md-6 col-offset-xs-3">
       <div class="btn btn-atendidas"> 
          <span class="badge bg-teal">121</span>
            <i class="fa fa-phone"></i>
          <a  href="#" id="reportes-atendidas" class="reportes-llamadas a-1">Atendidas</a>
       </div> 
       <div class="btn btn-app btn-transferidas"> 
          <span class="badge bg-teal">101</span>
            <i class="fa fa-reply-all"></i>
          <a href="#" id="reportes-transferidas" class="reportes-llamadas a-1">Tranferidas</a>
       </div> 
       <div class="btn btn-abandonadas"> 
          <span class="badge bg-teal">13</span>
            <i class="fa fa-power-off"></i>
          <a href="#" id="reportes-abandonadas" class="reportes-llamadas a-1">Abandonadas</a>
       </div> 
      </div>	
    
      <!--************************-->
    </div>	
  </div>	        
</div>    

{!! Form::close() !!}
