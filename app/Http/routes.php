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

// reporte de llamadas contestadas
Route::post('listar_llamadas_entrantes'									, ['uses'=>'ListarLlamadasEntrantesController@index']);
Route::get('listar_llamadas_contestadas/rango_fechas/{fecha_evento}'	, ['uses'=>'ListarLlamadasEntrantesController@listar_llamadas_contestadas']);
Route::get('listar_llamadas_abandonadas/rango_fechas/{fecha_evento}'	, ['uses'=>'ListarLlamadasEntrantesController@listar_llamadas_abandonadas']);
Route::get('listar_llamadas_transferidas/rango_fechas/{fecha_evento}'	, ['uses'=>'ListarLlamadasEntrantesController@listar_llamadas_transferidas']);

// reporte de consolidaod de llamadas
Route::get('calls_consolidated/{evento}'					, ['uses'=>'ListarLlamadasConsolidadasController@index']);
Route::get('calls_agent/{evento}'							, ['uses'=>'ListarLlamadasConsolidadasController@index']);
Route::get('calls_day/{evento}'								, ['uses'=>'ListarLlamadasConsolidadasController@index']);
Route::get('calls_hour/{evento}'							, ['uses'=>'ListarLlamadasConsolidadasController@index']);


Route::ajax('calls_consolidated/consulta'					, ['uses'=>'ListarLlamadasConsolidadasController@calls_consolidated']);




// reporte de llamadas salienes
Route::get('listar_llamadas_salientes'								, ['uses'=>'ListarLlamadasSalientesController@index']);
Route::get('listar_llamadas_salientes/rango_fechas/{fecha_evento}'	, ['uses'=>'ListarLlamadasSalientesController@listar_llamadas_salientes']);

// exportar A excel
Route::get('export_contestated/rango_fechas/{fecha_evento}'	, ['uses'=>'ListarLlamadasEntrantesController@export_contestated']);


// Importar datos desde Excel
Route::get('excel',['uses'=>'ExcelController@index']);
Route::post('excel/resultado',['uses'=>'ExcelController@resultado']);
Route::get('excel/getImport',['uses'=>'ExcelController@getImport']);


/**
 * Arreglar Bug de Debugbar, se tuvo que agregar las rutas manualmente.
 */
$router->get('open',                ['uses' => '\Barryvdh\Debugbar\Controllers\OpenHandlerController@handle',   'as' => 'debugbar.openhandler'  ]);
$router->get('assets/stylesheets',  ['uses' => '\Barryvdh\Debugbar\Controllers\AssetController@css',            'as' => 'debugbar.assets.css'   ]);
$router->get('assets/javascript',   ['uses' => '\Barryvdh\Debugbar\Controllers\AssetController@js',             'as' => 'debugbar.assets.js'    ]);

?>