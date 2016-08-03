/*!
 * Cosapi v1.0 : JQuery Personalizado
 * Copyright 2015
 * Alan Cornejo
 */

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    /* script para daterange y agregarle formato aÃ±o-mes-dia */
    $('input[name="fecha_evento"]').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

} );


function dataTables_entrantes(nombreDIV, data, route){

    $('#'+nombreDIV).dataTable().fnDestroy();

    $('#'+nombreDIV).DataTable({
        "deferRender"       : true,
        "responsive"        : false,
        "processing"        : true,
        "serverSide"        : true,
        "ajax"              : {
            url     : route,
            type    : 'POST',
            data    : data
        },
        dom                 : 'Bfrtip',
        buttons             : [
            'copyHtml5', 'csvHtml5', 'excelHtml5'
        ],

        "paging"            : true,
        "pageLength"        : 100,
        "lengthMenu"        : [100, 200, 300, 400, 500],
        "scrollY"           : "300px",
        "scrollX"           : true,
        "scrollCollapse"    : true,
        "select"            : true,
        "language"          : dataTables_lang_spanish(),
        "columns"           : columns_datatable(route)
    });
}

function dataTables_lang_spanish(){
    var lang = {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }

    return lang;
}

function columns_datatable(route){
    if(route == 'incoming_calls'){
        var columns =   [
            {"data" : "fechamod"},
            {"data" : "timemod"},
            {"data" : "clid"},
            {"data" : "agent"},
            {"data" : "queue"},
            {"data" : "info2"},
            {"data" : "event"},
            {"data" : "info1"}
        ];
    }

    if(route == 'calls_outgoing'){
        var columns =   [
            {"data" : "date"},
            {"data" : "hour"},
            {"data" : "src"},
            {"data" : "dst"},
            {"data" : "billsec"}
        ];
    }

    if(route == 'consolidated_calls'){
        var columns =   [
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
            {"data":"min_espera"},
            {"data":"duracion"},
            {"data":"avgw"},
            {"data":"avgt"},
            {"data":"answ"},
            {"data":"unansw"},
            {"data":"ro10"},
            {"data":"ro15"},
            {"data":"ro20"},
            {"data":"ns10"},
            {"data":"ns15"},
            {"data":"ns20"},
            {"data":"avh210"},
            {"data":"avh215"},
            {"data":"avh220"}
        ];
    }

    if(route == 'events_detail'){
        var columns =   [
            {"data" : "nombre_agente"},
            {"data" : "fecha"},
            {"data" : "hora"},
            {"data" : "evento"},
            {"data" : "accion"}
        ];
    }

    if(route == 'outgoing_calls'){
        var columns =   [
            {"data" : "date"},
            {"data" : "hour"},
            {"data" : "src"},
            {"data" : "dst"},
            {"data" : "duration"}
        ];
    }


    return columns;
}

function show_tab_incoming (evento){
    dataTables_entrantes('table-incoming', get_data_filters(evento), 'incoming_calls');
}

function show_tab_consolidated (evento){
    dataTables_entrantes('table-consolidated', get_data_filters(evento), 'consolidated_calls');
}

function show_tab_detail_events (evento){
    dataTables_entrantes('table-detail-events', get_data_filters(evento), 'events_detail');
}

function show_tab_outgoing (evento){
    dataTables_entrantes('table-outgoing', get_data_filters(evento), 'outgoing_calls');
}

function get_data_filters(evento){
    var data = {
        _token       : $('input[name=_token]').val(),
        fecha_evento : $('input[name=fecha_evento]').val(),
        evento       : evento
    };

    return data;
}


function exportar() {
    cargar_ajax('POST', 'prueba');
}

function cargar_ajax(type,url){

    var token =  $('input[name=_token]').val();

    $.ajax({
        type        : type,
        url         : url,
        cache       : false,
        data        : '_token='+token,

        success: function(data){
            location.href = data.path;
        },
        error: function(data){
            //$('#'+nameDiv).html(msjError);
        }
    });
}
