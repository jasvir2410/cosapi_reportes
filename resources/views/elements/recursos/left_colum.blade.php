<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="plugins/adminLTE/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p>Cosapi Data</p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- search form -->
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          <!-- /.search form -->
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MENU PRINCIPAL</li>
            <li class="active treeview">
              <a href="#">
                <i class="fa fa-dashboard"></i> <span>ESTADO CALLS</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">              
                  <li ><a href="#" id="listar_estado_agentes" class="reportes"><i class="fa fa-circle-o"></i> Consolidado de Estados</a></li>
                  <li ><a href="#" id="listar_estado_detallados" class="reportes"><i class="fa fa-circle-o"></i> Estados Detallado</a></li>
              </ul>
            </li>
            <li >
              <a href="#">
                <i class="fa fa-phone-square"></i> <span>DETALLE CALLS</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
              <li ><a tabindex="-1" href="#" id="calls_inbound" class="reportes"><i class="fa fa-circle-o text-green"></i> Llamadas Entrantes</a></li>
              <li ><a tabindex="-1" href="#" id="listar_llamadas_salientes" class="reportes"><i class="fa fa-circle-o text-yellow"></i> Llamadas Salientes</a></li>
              <li ><a tabindex="-1" href="#" id="excel" class="reportes"><i class="fa fa-circle-o text-green"></i> Pruebas Excel</a></li></ul>
            </li>
            <li >
              <a href="#">
                <i class="fa fa-book"></i> <span>CONSOLIDADO CALLS</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li ><a href="#" id="calls_consolidated" class="reportes"><i class="fa fa-circle-o"></i> Consolidado por Skill</a></li>
                <li ><a href="#" id="calls_agent" class="reportes"><i class="fa fa-circle-o"></i> Consolidado por Agentes</a></li>
                <li ><a href="#" id="calls_day" class="reportes"><i class="fa fa-circle-o"></i> Consolidado por Día</a></li>
                <li ><a href="#" id="calls_hour" class="reportes"><i class="fa fa-circle-o"></i> Consolidado por Hora</a></li>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
      