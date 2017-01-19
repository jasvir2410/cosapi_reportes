<!--script para el data select2-->
<script src="{{ asset('plugins/select2/select2.js')}}">  </script>
<!--fin script para el data select2-->

<script type="text/javascript">
    $(document).ready(function() {
        $("#select_Users").select2({
            tags: true,
            tokenSeparators: [','],
            ajax: {
                url: 'agents_queue/users',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        name: params.term
                    }
                },
                processResults: function (data, page) {
                    return {
                        results: data
                    };
                },
            }
        });
    } );
</script>