/*!
 * Cosapi v1.0 : JQuery Personalizado
 * Copyright 2015
 * Alan Cornejo
 */





    
    function buscar(){

         	$('#reporte-estados1').DataTable().fnDestroy();

            var fecha_evento    = $('#texto').val();
            var nombre_url      = $('#url').val(); 

            url_consolidado = "listar_llamadas_consolidadas/rango_fechas/"+fecha_evento;          



            $('#reporte-estados1').DataTable({
                "deferRender"       : true,
                "responsive"        : true,

                "processing"        : true,
                "serverSide"        : true,
                "ajax"              : url_consolidado,

                "scrollY"           : "300px",
                "scrollX"           : true,
                "scrollCollapse"    : true,

                "select"            : true,

                "dom"               : 'Bfrtip',
                "buttons"           : ['copyHtml5', 'excelHtml5'],

                
                "columns"           : [
                    {"data":"name"},
                    {"data":"recibidas"},
                    {"data":"atendidas"},
                    {"data":"abandonados"},
                    {"data":"transferencias"},
                    {"data":"constestadas"},
                    {"data":"constestadas_10"},
                    {"data":"constestadas_15"},
                    {"data":"constestadas_20"},
                    {"data":"abandonadas_10"},
                    {"data":"abandonadas_15"},
                    {"data":"abandonadas_20"},
                    {"data":"ro10"},
                    {"data":"ro15"},
                    {"data":"ro20"},
                    {"data":"min_espera"},
                    {"data":"duracion"},
                    {"data":"avgw"},
                    {"data":"avgt"},
                    {"data":"answ"},
                    {"data":"unansw"},                  
                ]


            });
        };





