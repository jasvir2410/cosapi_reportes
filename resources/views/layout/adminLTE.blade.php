<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="{{ asset('images/cosapi.ico') }}"  rel="shortcut icon">
    <title>Reportes | @yield('title')</title>
    <!-- para que la web sea responsive -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css')}}">
    <!-- para los iconos Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/adminLTE/css/font-awesome.css')}}">
    <!-- para el css de Adminlte -->
    <link rel="stylesheet" href="{{ asset('plugins/adminLTE/css/AdminLTE.min.css')}}">
    <!-- para cambiar el color del panel de adminlte -->
    <link rel="stylesheet" href="{{ asset('plugins/adminLTE/css/skins/_all-skins.min.css') }}">
     <!--para incluir otros css-->
     @yield('css')
  </head>

  <body class="hold-transition skin-blue sidebar-mini">
    <!--para que se muestre la pagina en donde estas -->
    <?php
      $crumbs = explode("/",$_SERVER["REQUEST_URI"]);
      foreach($crumbs as $crumb){
          $d=ucfirst(str_replace(array(".html","_"),array(""," "),$crumb) . ' ');
      }
    ?>
    <div class="wrapper">
      <!--<clase header="para el header principal">-->
      @include('elements.recursos.header')
      <!-- para la columna de la izquierda -->
      @include('elements.recursos.left_colum')

      <!-- Content Wrapper -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
              Dashboard
              <small>Panel de Control</small>
            </h1>
            <ol class="breadcrumb">
              <li><a href="/"><i class="fa fa-dashboard"></i>Inicio</a></li>
              <li class="active"><?php echo $d;?></li>
            </ol>
          </section>

          <!-- inicio contenido dinamico-->
          <section class="content">

            @yield('content')

          </section><!-- fin contenido dinamico -->

      </div><!-- /.content-wrapper -->

      <!--<para el footer>-->
        @include('elements.recursos.footer')
      <!-- Control Sidebar -->
     <!-- <para la columna de la derecha">-->
        @include('elements.recursos.right_colum')

      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <!--Todos los javascript del layout principal-->
    <!-- jQuery -->
    <script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <script src="{{ asset('plugins/jQueryUI/jquery-ui.js')}}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('plugins/adminLTE/js/app.min.js')}}"></script>
    <script src="{{ asset('plugins/adminLTE/js/funcionalidades.js')}}"></script>
    <!--para incluir todos los demas script que se necesite-->
    @yield('scripts')
  </body>
</html>
