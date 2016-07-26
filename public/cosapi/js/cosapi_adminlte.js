/*!
 * Cosapi v1.0 : JQuery Personalizado
 * Copyright 2015
 * Alan Cornejo
 */

$(document).ready(function() {
    /* script para daterange y agregarle formato aÃ±o-mes-dia */
    $('input[name="fecha_evento"]').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

} );


function dataTables_entrantes(nombreDIV, data){

    $('#'+nombreDIV).dataTable().fnDestroy();

    $('#'+nombreDIV).DataTable({
        "deferRender"       : true,
        "responsive"        : true,
        "processing"        : true,
        "serverSide"        : true,
        "ajax"              : {
            url : 'calls_incoming',
            type: 'POST',
            data: data
        },
        "language"          : dataTables_lang_spanish(),
        "paging"            : true,
        "pageLength"        : 100,
        "lengthMenu"        : [100, 200, 300, 400, 500],
        "scrollY"           : "300px",
        "scrollX"           : true,
        "scrollCollapse"    : true,
        "select"            : true,
        "columns"           : [
            {"data":"fechamod"},
            {"data":"timemod"},
            {"data":"clid"},
            {"data":"agent"},
            {"data":"queue"},
            {"data":"info2"},
            {"data":"event"},
            {"data":"info1"}
        ]
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

