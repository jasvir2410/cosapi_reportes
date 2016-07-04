<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Models\User;
use Cosapi\Models\DetalleEventos;
use Cosapi\Models\Eventos;

use DB;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

class ReporteEstadosDetalladosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('elements/Reporte_Estados_Detallados/index');
    }

    public function listar_estado_detallados (Request $request, $fecha_evento){

    	list($fecha_inicial,$fecha_final)=explode(' - ', $fecha_evento);

    	/** Query listar detalle eventos*/
    	$DetalleEventos     = DetalleEventos::Select()
                                    ->with('evento','user')
                                    ->whereBetween(DB::raw("DATE(fecha_evento)"),[$fecha_inicial, $fecha_final])
                                    ->where('user_id','<>','1')
                                    ->OrderBy(DB::raw('user_id'), 'asc')
                                    ->OrderBy(DB::raw('DATE(fecha_evento)'), 'asc')
                                    ->OrderBy(DB::raw('TIME(fecha_evento)'), 'asc')
                                    ->get()->toArray();



    	return View('elements/Reporte_Estados_Detallados/detalle-estados-detallados')->with(array(
            'detalleEventos'    =>  $DetalleEventos
            )
        ) ;
    }

    
}
