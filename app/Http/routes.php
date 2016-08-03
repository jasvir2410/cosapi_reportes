<?php
Route::get('/', ['as' => 'admin', 'uses'=>'AdminController@index']);

// Reportes de Eventos
Route::post('events_detail'         , ['uses'=>'EventsAgentController@events_detail']);
Route::post('events_consolidated'   , ['uses'=>'EventsAgentController@events_consolidated']);

// Reportes CALLS
Route::post('incoming_calls'		, ['uses'=>'IncomingCallsController@index']);
Route::post('outgoing_calls'		, ['uses'=>'OutgoingCallsController@index']);
Route::post('consolidated_calls'	, ['uses'=>'ConsolidatedCallsController@index']);

Route::get('prueba'		            , ['uses'=>'IncomingCallsController@prueba']);