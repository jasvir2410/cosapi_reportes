{!! Form::open([ 'method' => '', 'name'=>'buscador']) !!}
<div >
  <div class="box box-primary">

    <div class="box-header">
      <h3 class="box-title"><b>Reportes de Estado por Agente</b>
      </div>

      <label>&nbsp;&nbsp;&nbsp; Seleccione rango de fechas:</label>

      <div class="box-body">
        <!-- Date range -->
        <div class="form-group col-md-5">
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" id="texto" name="fecha_evento" class="form-control pull-right">
          </div>        
          <!-- /.input group -->

          <div class="checkbox">
            <label>
              <input type="checkbox" id="formato"> Mostrar valores en Formato Excel (hh:mm:ss)
            </label>
          </div>

        </div>
        <!-- /.form group -->
        <div class="form-group col-xs-0 col-xs-offset-0">
          <a onclick="buscar();" value="Get Element By Id"  class="btn btn-primary">Buscar</a>
        </div>  
        
      </div>

    </div>
</div>
{!! Form::close() !!}
