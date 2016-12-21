<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="{{ asset('images/cosapi.ico') }}"  rel="shortcut icon">
    <title>Monitor Resumen | @yield('title')</title>
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
    {!!Html::style('cosapi/css/preloader.css')!!}
     <!--para incluir otros css-->
     @yield('css')


    <!--Todos los javascript del layout principal-->
    <!-- jQuery -->
    {!!Html::script('plugins/jQuery/jQuery-2.1.4.min.js')!!}
    {!!Html::script('plugins/jQueryUI/jquery-ui.js')!!}
    {!!Html::script('bootstrap/js/bootstrap.min.js')!!}
    {!!Html::script('extras/bootstrap3-dialog/js/bootstrap-dialog.min.js')!!}
    {!!Html::script('plugins/adminLTE/js/app.min.js')!!}
    {!!Html::script('plugins/adminLTE/js/funcionalidades.js')!!}
    {!!Html::script('cosapi/js/cosapi_adminlte.js')!!}

    {!!Html::script('plugins/chartjs/Chart.bundle.js')!!}
    {!!Html::script('plugins/highcharts/highcharts.js')!!}
    {!!Html::script('plugins/highcharts/highcharts-more.js')!!}
    {!!Html::script('plugins/highcharts/modules/solid-gauge.js')!!}

    <!--para incluir todos los demas script que se necesite-->
    @yield('scripts')
  </head>

  <body >

     @yield('content')
    
  </body>
</html>
