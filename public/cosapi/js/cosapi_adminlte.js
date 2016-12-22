/*!
 * Cosapi v1.0 : JQuery Personalizado
 * Copyright 2015
 * Alan Cornejo
 */

/**
 * [dataTables_entrantes Funcion para cargar datos en la tablas de los reportes]
 * @param  {String} nombreDIV [Nombre del div donde esta la tabla para agregar los datos]
 * @param  {String} data      [Nombre del tipo de porte a cargar]
 * @param  {String} route     [Ruta a la cual va a consultar los datos a cargar]
 */
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


/**
 * [dataTables_lang_spanish Función que permite colocar el Datable en español]
 */
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


/**
 * [columns_datatable description]
 * @param  {String} route [Nombre del tipo de reporte]
 * @return {Array}        [Array con nombre de cada parametro que ira en las columnas de la tabla dl reporte]
 */
function columns_datatable(route){
    if(route == 'incoming_calls'){
        var columns =   [
            {"data" : "date"},
            {"data" : "hour"},
            {"data" : "telephone"},
            {"data" : "agent"},
            {"data" : "skill"},
            {"data" : "duration"},
            {"data" : "action"},
            {"data" : "waittime"},
            {"data" : "audio"}
        ];
    }

    if(route == 'surveys'){
        var columns =   [
            {"data" : "Tipo Encuesta"},
            {"data" : "Date"},
            {"data" : "Hour"},
            {"data" : "Username"},
            {"data" : "Anexo"},
            {"data" : "Telephone"},
            {"data" : "Skill"},
            {"data" : "Duration"},
            {"data" : "Question_01"},
            {"data" : "Answer_01"},
            {"data" : "Question_02"},
            {"data" : "Answer_02"},
            {"data" : "Action"}
        ];
    }

    if(route == 'consolidated_calls'){
        var columns =   [
            {"data":"Name"},
            {"data":"Received"},
            {"data":"Answered"},
            {"data":"Abandoned"},
            {"data":"Transferred"},
            {"data":"Attended"},
            {"data":"Answ 10s"},
            {"data":"Answ 15s"},
            {"data":"Answ 20s"},
            {"data":"Answ 30s"},
            {"data":"Aband 10s"},
            {"data":"Aband 15s"},
            {"data":"Aband 20s"},
            {"data":"Aband 30s"},
            {"data":"Wait Time"},
            {"data":"Talk Time"},
            {"data":"Avg Wait"},
            {"data":"Avg Talk"},
            {"data":"Answ"},
            {"data":"Unansw"},
            {"data":"Ro10"},
            {"data":"Ro15"},
            {"data":"Ro20"},
            {"data":"Ro30"},
            {"data":"Ns10"},
            {"data":"Ns15"},
            {"data":"Ns20"},
            {"data":"Ns30"},
            {"data":"Avh2 10"},
            {"data":"Avh2 15"},
            {"data":"Avh2 20"},
            {"data":"Avh2 30"}
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
            {"data" : "annexedorigin"},
            {"data" : "username"},
            {"data" : "destination"},
            {"data" : "calltime"}
        ];
    }


    return columns;
}

/**
 * [show_tab_incoming Función que carga Llamadas Entrantes en el reporte]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 */
function show_tab_incoming (evento){
    dataTables_entrantes('table-incoming', get_data_filters(evento), 'incoming_calls');
}

/**
 * [show_tab_surveys Función que carga los datos de las Encuenstas]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 */
function show_tab_surveys (evento){
    dataTables_entrantes('table-surveys', get_data_filters(evento), 'surveys');
}

/**
 * [show_tab_consolidated Función que carga los datos del Consolidado]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 */
function show_tab_consolidated (evento){
    dataTables_entrantes('table-consolidated', get_data_filters(evento), 'consolidated_calls');
}

/**
 * [show_tab_detail_events Función que carga los datos detallados de los Eventos del Agente]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 */
function show_tab_detail_events (evento){
    dataTables_entrantes('table-detail-events', get_data_filters(evento), 'events_detail');
}

/**
 * [show_tab_outgoing Función que carga los datos de las Llamadas Salientes]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 */
function show_tab_outgoing (evento){
    dataTables_entrantes('table-outgoing', get_data_filters(evento), 'outgoing_calls');
}

/**
 * [get_data_filters Función que configura datos a enviar en las consultas]
 * @param  {String} evento [Tipo de reporte a cargar en la vista]
 * @return {Array}         [Devuelve parametros configurados]
 */
function get_data_filters(evento){
    var data = {
        _token       : $('input[name=_token]').val(),
        fecha_evento : $('input[name=fecha_evento]').val(),
        evento       : evento
    };

    return data;
}

/**
 * [exportar description]
 * @param  {[type]} format_export [description]
 * @return {[type]}               [description]
 */
function exportar(format_export) {
    var days = $('#texto').val();
    var event =$('#hidEvent').val()
    export_ajax('POST',event,format_export,days);
}

/**
 * [export_ajax description]
 * @param  {[type]} type          [Define si el envio de datos es por POST o GET]
 * @param  {String} url           [Ruta a la cual se van a solicitar los datos]
 * @param  {String} format_export [Formato en el cual se va a exportar el archivo]
 * @param  {String} days          [Fecha de consulta de datos]
 */
function export_ajax(type, url, format_export='', days=''){

    var dialog = cargar_dialog('primary','Cosapi Data','Cargando el Excel',false);

    var token =  $('input[name=_token]').val();

    $.ajax({
        type        : type,
        url         : url,
        cache       : false,
        data        : {
            _token : token,
            format_export : format_export,
            days : days,
        },

        beforeSend: function(data) {
            dialog.open();
        },
        success: function(data){
            var array_url = data.path;
            for (var i = 0; i <array_url.length; i++) {
                 downloadURL(array_url[i],i);
            }
        },
        complete: function(){
            dialog.close();
        }
    });
}

/**
 * [downloadURL Iframe creado dinamicamente para realizar la descarga de archivos a la hora de exportar]
 * @param  {String} url   [Dirección en donde se encuentra el archivo descargado]
 * @param  {String} index [Indentificador de los frame, para que no tengan el mismo nombre como ID]
 */
function downloadURL(url,index){
    var hiddenIFrameID = 'hiddenDownloader' + index;
    var iframe = document.createElement('iframe');
    iframe.id = hiddenIFrameID;
    iframe.style.display = 'none';
    document.body.appendChild(iframe);
    iframe.src = url;
}

/**
 * [cargar_dialog Función que muestra ventana emergente indicando que se va a realizar una descargar de algun archivo]
 * @param  {String} new_string [Tipo de ventan emergente a mostrar]
 * @param  {String} title    [Titulo de la ventana emergente]
 * @param  {String} message  [Mensaje que se mostrara en la ventana emergente]
 * @param  {String} closable [Dato que nos indica que si podemos cerrar la ventana]
 * @return {[booleam]}          [description]
 */
function cargar_dialog(new_type,title,message,closable){

    var message = $('<div></div>');
        message.append('<center><b>Porfavor no cerrar la ventana</b></br><img src="../cosapi/img/loading_bar_cicle.gif" /></center>');

    var dialog = new BootstrapDialog({
        size        : BootstrapDialog.SIZE_SMALL,
        type        : 'type-' + new_type,
        title       : title,
        message     : message,
        closable    : closable,

    });

    return dialog;
}

/**
 * [cargar_ajaxDIV Función para cargar los datos en la vista]
 * @param  {String}  type     [Tipo de envío de datos: POST o GET]
 * @param  {String}  url      [Ruta a la cual se consultara los datos]
 * @param  {String}  nameDiv  [El nombre del Div en el cual se cargaran los datos]
 * @param  {String}  msjError [Texto qu se mostrar si ocurre algún tipo de error a la hora de cargar los datos]
 * @param  {Boolean} before   [Datos que nos dira si se ebe mostrar la imagen de cargando]
 */
function cargar_ajaxDIV(type,url,nameDiv,msjError, before = false){

    
    var image_loading = '<div class="loading" id="loading"><li></li><li></li><li></li><li></li><li></li></div>';

    $.ajax({
        type        : type,
        url         : url,
        cache       : false,
        dataType    : 'HTML',
        beforeSend: function() {
            if(before == true){
                $('#'+nameDiv).html(image_loading);
            }
        },
        success: function(data){
            $("#loading").hide();
            $('#'+nameDiv).html(data);
        },
        error: function(data){
            $('#'+nameDiv).html(msjError);
        }
    });
}

/**
 * [detalle_agentes_dashboard_01 Función que refresca la información del estado de los agentes del Dashboard1]
 */
function detalle_agentes_dashboard_01(){
    cargar_ajaxDIV('GET', 'dashboard_01/detail_agents', 'detail_agents', 'Problema para actualizar el detalle de agentes');
    setTimeout('detalle_agentes_dashboard_01()', 6000);
}

/**
 * [detalle_kpi_dashboard_01 Función que carga los KPI del Dashboard1]
 */
function detalle_kpi_dashboard_01(){
    cargar_ajaxDIV('GET', 'dashboard_01/detail_kpi', 'detail_kpi', 'Problema para actualizar el KPI de agentes');
    setTimeout('detalle_kpi_dashboard_01()', 6000);
}

/**
 * [detail_encoladas_dashboard_01 Función que carga datos de las llamadas en cola del Dashboard1]
 */
function detail_encoladas_dashboard_01(){
    cargar_ajaxDIV('GET', 'dashboard_01/detail_encoladas', 'detail_encoladas', 'Problema para actualizar el panel de encoladas');
    setTimeout('detail_encoladas_dashboard_01()', 1500);
}

function total_encoladas_dashboard_01(){
    cargar_ajaxDIV('GET', 'dashboard_01/total_encoladas', 'total_encoladas', 'Problema para actualizar el panel de encoladas');
    setTimeout('total_encoladas_dashboard_01()', 1500);
}

/**
 * [refresh_information Función que refresca la información del dahsboard 2]
 * @param  {String} evento [Tipo de reporte a visualizar: day, week, mounth]
 */
function refresh_information (evento){
    BootstrapDialog.confirm('¿Está seguro que quieres actualizar los datos del monitor?', function(result){
        if(result) {
            $('#hidReporttype').val(evento);
            detalle_kpi_dashboard_02(evento);
        }else{
            $( '#ulOptions li' ).removeClass( 'active' );
            var idevento = $('#hidReporttype').val();
            $( '#'+idevento ).addClass( 'active' );
        }
    });
}

/**
 * [detalle_kpi_dashboard_02 Función que carga los ddatos del KPI ubicados en el Dashboard2]
 * @param  {string} evento [Tipo de reporte que se quiere cargar: day, week, mounth]
 * @return {[view]}          [Vista con los datos dl reporte]
 */
function detalle_kpi_dashboard_02(evento){
    
    return cargar_ajaxDIV('GET', 'dashboard_02/detail_kpi/'+evento, 'detail_kpi', 'Problema para actualizar el KPI de agentes',true);
}

/**
 * [create_sound_bite Función que reproduce el audio para llamadas en cola]
 * @param  {String} sound [Ruta donde se encuentra ubicado nuestro audio]
 */
function create_sound_bite(sound){
    var html5_audiotypes={
        "mp3": "audio/mpeg",
        "mp4": "audio/mp4",
        "ogg": "audio/ogg",
        "wav": "audio/wav"
    }

    var html5audio=document.createElement('audio')
    if (html5audio.canPlayType){ //Comprobar soporte para audio HTML5
        for (var i=0; i<arguments.length; i++){
            var sourceel=document.createElement('source')
            sourceel.setAttribute('src', arguments[i])
            if (arguments[i].match(/.(w+)$/i))
                sourceel.setAttribute('type', html5_audiotypes[RegExp.$1])
            html5audio.appendChild(sourceel)
        }
        html5audio.load()
        html5audio.playclip=function(){
            html5audio.pause()
            html5audio.currentTime=0
            html5audio.play()
        }
        return html5audio
    }
    else{
        return {playclip:function(){throw new Error('Su navegador no soporta audio HTML5')}}
    }
}

/**
 * [validar_sonido Funcion que valida la cantidad de llamadas en cola par activar el sonido del monitor]
 */
function validar_sonido(){

    var click2      = create_sound_bite('/cosapi/sonidos/alerta_principal.mp3');
    var encoladas   = $('#count_encoladas').text();

    if (encoladas >= 2){
        click2.playclip();
    }

    setTimeout('validar_sonido()', 4000);
}

