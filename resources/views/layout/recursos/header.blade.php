<header class="main-header">

    <!-- Token de sistemas -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{{getenv('PROYECT_NAME_SHORT')}}</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{getenv('PROYECT_NAME_COMPLETE')}}</b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>

    </nav>
  </header>