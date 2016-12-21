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
    {!!Html::style('bootstrap/css/bootstrap.min.css')!!}
    <!-- para los iconos Font Awesome -->
    {!!Html::style('plugins/adminLTE/css/font-awesome.css')!!}
    <!-- para el css de Adminlte -->
    {!!Html::style('plugins/adminLTE/css/AdminLTE.min.css')!!}
    <!-- para cambiar el color del panel de adminlte -->
    {!!Html::style('plugins/adminLTE/css/skins/_all-skins.min.css')!!}
    <!-- estilos para la imagen de cargando -->
    {!!Html::style('cosapi/css/preloader.css')!!}
     <!--para incluir otros css-->
     @yield('css')
  </head>

  <body class="hold-transition skin-red-light sidebar-mini sidebar-collapse ">
    <!--para que se muestre la pagina en donde estas -->
    <?php
      $crumbs = explode("/",$_SERVER["REQUEST_URI"]);
      foreach($crumbs as $crumb){
          echo $d=ucfirst(str_replace(array(".html","_"),array(""," "),$crumb) . ' ');
      }
    ?>
    <div class="wrapper">
      <!--<clase header="para el header principal">-->
      @include('layout.recursos.header')
      <!-- para la columna de la izquierda -->
      @include('layout.recursos.left_colum')

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

              <li class="active" id='urlsistema'></li>
            </ol>
          </section>

          <!-- inicio contenido dinamico-->
          <section class="content">

            @yield('content')

          </section><!-- fin contenido dinamico -->

      </div><!-- /.content-wrapper -->

      <!--<para el footer>-->
        @include('layout.recursos.footer')
      <!-- Control Sidebar -->
     <!-- <para la columna de la derecha">-->
        @include('layout.recursos.right_colum')

      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <!--Todos los javascript del layout principal-->
    <!-- jQuery -->
    {!!Html::script('plugins/jQuery/jQuery-2.1.4.min.js')!!}
    {!!Html::script('plugins/jQueryUI/jquery-ui.js')!!}
    {!!Html::script('bootstrap/js/bootstrap.min.js')!!}
    {!!Html::script('extras/bootstrap3-dialog/js/bootstrap-dialog.min.js')!!}
    {!!Html::script('plugins/adminLTE/js/app.min.js')!!}
    {!!Html::script('plugins/adminLTE/js/funcionalidades.js')!!}

    <script type="text/javascript">
      var AdminLTEOptions = {
        sidebarExpandOnHover: true,
        enableBSToppltip: true
      };

      $(document).ready(function() {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
      } );

    </script>
    @yield('scripts')
  </body>
</html>
