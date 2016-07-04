<?php
/*use Illuminate\Http\Request;*/
use Illuminate\Support\Facades\Request; /*con esta linea se muestra el request del ajax*/

//ruta para ver logs de laravel
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');


//ruta para acceder al panel de administrador
Route::get('/', ['as' => 'admin', 'uses'=>'AdminController@index']);


// ruta para realizar busqueda de registros reporte estados-agentes.
Route::get('listar_estado_agentes'									, ['uses'=>'ReporteEstadosAgentesController@index']);
Route::get('listar_estado_agentes/rango_fechas/{fecha_evento}'		, ['uses'=>'ReporteEstadosAgentesController@listar_estado_agentes']);

// reporte de estados detallado
Route::get('listar_estado_detallados'								, ['uses'=>'ReporteEstadosDetalladosController@index']);
Route::get('listar_estado_detallados/rango_fechas/{fecha_evento}'	, ['uses'=>'ReporteEstadosDetalladosController@listar_estado_detallados']);

//ruta para reporte de llamadas.
//Route::get('reportes-llamadas', ['as' => 'reportes-llamadas', 'uses'=>'ReporteController@reportes_llamadas']);
//ruta para reporte de llamadas atendidas.
//Route::get('reportes-llamadas/reportes-atendidas/{fecha_evento2}', ['as' => 'reportes-atendidas', 'uses'=>'ReporteController@reportes_atendidas']);
//ruta para reporte de llamadas atendidas.
//Route::get('reportes-llamadas/reportes-transferidas/{fecha_evento2}', ['as' => 'reportes-transferidas', 'uses'=>'ReporteController@reportes_transferidas']);
//ruta para reporte de llamadas atendidas.
//Route::get('reportes-llamadas/reportes-abandonadas/{fecha_evento2}', ['as' => 'reportes-abandonadas', 'uses'=>'ReporteController@reportes_abandonadas']);






?>