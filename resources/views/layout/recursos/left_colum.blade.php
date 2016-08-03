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
                <i class="fa fa-dashboard"></i> <span>REPORTING EVENTS</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#" id="events_detail"        class="reportes"><i class="fa fa-circle-o text-blue"></i> Detail Events</a></li>
                <li><a href="#" id="events_consolidated"  class="reportes"><i class="fa fa-circle-o text-blue"></i> Consolidated Events</a></li>
              </ul>
            </li>
            <li>
              <a href="#">
                <i class="fa fa-phone-square"></i> <span>REPORTING CALLS</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#" id="incoming_calls"     class="reportes"><i class="fa fa-circle-o text-green"></i> Inbound Calls</a></li>
                <li><a href="#" id="outgoing_calls"     class="reportes"><i class="fa fa-circle-o text-yellow"></i> Outbound Calls</a></li>
                <li><a href="#" id="consolidated_calls" class="reportes"><i class="fa fa-circle-o text-blue"></i> Consolidated Calls</a></li>
              </ul>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
      