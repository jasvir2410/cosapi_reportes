<div class="row">

  <div class="col-md-6">
    <label>&nbsp;&nbsp;&nbsp; Seleccione rango de fechas:</label>
    <div class="box-body">
      <!-- Date range -->
      <div class="form-group col-md-12">
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" id="texto" name="fecha_evento" class="form-control pull-right" >
        </div>        
      </div>       
    </div>
  </div>
  
  <div class="col-md-6">  
    <label>&nbsp;&nbsp;&nbsp; Seleccione Hora:</label>
    <div class="box-body">
      <!-- Date range -->
      <div class="form-group col-md-10">
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
          </div>
          <select class="form-control" name="hora_evento">
            <option value="900">15minutos</option>
            <option value="1800">30 minutos</option>
            <option value="2700">45 minutos</option>
            <option value="3600">1 hora</option>
          </select>
        </div>        
      </div>
      <!-- /.form group -->
      <div class="form-group col-xs-0 col-xs-offset-0 col-md-2">
        <a onclick="buscar();" value="Get Element By Id"  class="btn btn-primary">Buscar</a>
      </div>
    </div>
  </div>

</div>
