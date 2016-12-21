<?php
Route::get('/'													, ['as' => 'admin', 'uses'=>'AdminController@index']);

// Reportes de Eventos
Route::get('dashboard_01'         								, ['uses'=>'DashboardController@dashboard_01']);
Route::get('dashboard_01/detail_agents'   						, ['uses'=>'DashboardController@detail_agents']);
Route::get('dashboard_01/detail_encoladas'   					, ['uses'=>'DashboardController@detail_encoladas']);
Route::get('dashboard_01/total_encoladas'   					, ['uses'=>'DashboardController@total_encoladas']);
Route::get('dashboard_01/detail_kpi'   							, ['uses'=>'DashboardController@detail_kpi']);
Route::get('dashboard_01/logoutagent/{anexo}/{username}'   		, ['uses'=>'DashboardController@desconectar_agente']);
Route::get('dashboard_02'   									, ['uses'=>'DashboardController@dashboard_02']);
Route::get('dashboard_02/detail_kpi/{evento}'   				, ['uses'=>'DashboardController@detail_kpi_dashboard_02']);

// Reportes de Eventos
Route::post('events_detail'         							, ['uses'=>'EventsAgentController@events_detail']);
Route::post('events_consolidated'   							, ['uses'=>'EventsAgentController@events_consolidated']);

// Reportes CALLS
Route::post('incoming_calls'									, ['uses'=>'IncomingCallsController@index']);
Route::post('outgoing_calls'									, ['uses'=>'OutgoingCallsController@index']);
Route::post('consolidated_calls'								, ['uses'=>'ConsolidatedCallsController@index']);

Route::post('export_incoming'									, ['uses'=>'IncomingCallsController@export']);
Route::post('export_outgoing'									, ['uses'=>'OutgoingCallsController@export']);
Route::post('export_consolidated'								, ['uses'=>'ConsolidatedCallsController@export']);
Route::post('export_events_detail'								, ['uses'=>'EventsAgentController@export']);
Route::post('export_surveys'	    							, ['uses'=>'SurveysController@export']);

// reporte de llamadas salienes
Route::post('agents_online'										, ['uses'=>'AgentsOnlineController@index']);

// Reporte de nivel de occupacion
Route::post('level_of_occupation'								, ['uses'=>'LeveloccupationController@index']);

// Reporte de encuestaa
Route::post('surveys'											, ['uses'=>'SurveysController@index']);