<!--script para el data picker-->
<script src="{{ asset('js/jquery.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/js/moment.js')}}">  </script>
<script src="{{ asset('plugins/daterangepicker/js/moment.min.js')}}">  </script>
<script src="{{ asset('plugins/daterangepicker/js/daterangepicker.js')}}">  </script>
<!--fin script para el data picker-->

<script type="text/javascript">
      $(document).ready(function() {
          /* script para daterange y agregarle formato aÃ±o-mes-dia */
          $('input[name="fecha_evento"]').daterangepicker({
              locale: {
                  format: 'YYYY-MM-DD'
              }
          });
      } );
</script>
