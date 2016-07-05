/*!
 * Cosapi v1.0 : JQuery Personalizado
 * Copyright 2015
 * Alan Cornejo
 */

$(document).ready(function(){

    /* script para daterange y agregarle formato año-mes-dia */
    $('input[name="fecha_evento"]').daterangepicker(
        {
            locale: {
                format: 'YYYY-MM-DD'
            }
        }
    );
});


function buscar(){
    
    var fecha_evento    = document.getElementById('texto').value;
    var nombre_url      = document.getElementById('url').value; 

    url = nombre_url+"/rango_fechas/"+fecha_evento;
    console.log(url);
    cargar_ajaxDIV('GET', url, 'resumen', 'Problemas para actualizar Reporte de Estados');
}


function cargar_ajaxDIV(type,url,nameDiv,msjError){

    $.ajax({
        type        : type,
        url         : url,
        cache       : false,
        dataType    : 'HTML',

        success: function(data){
            $('#'+nameDiv).html(data);
        },
        error: function(data){
            $('#'+nameDiv).html(msjError);
        }
    });
}