<?php

namespace Cosapi\Http\Controllers;


use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\AgentesOnline;
use Cosapi\Http\Controllers\Controller;
use Cosapi\Collector\Collector;
use Cosapi\Http\Controllers\CosapiController;

use DB;
use Illuminate\Support\Facades\Log;

class AgentsOnlineController extends CosapiController
{


    /**
     * [index Función que carga las vistas del reporte Agentes Online]
     * @param  Request $request [Variable por la cual se recibe datos enviados por POST]
     * @return [view]           [retorna datos a cargar en la vista]
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->agents_online($request->fecha_evento);
            }else{
                return view('elements/agents_online/index');
            }
        }
        
    }


    /**
     * [agents_online Función que envia los datos del día para su respectica consulta a la base de datos.]
     * @param  [array] $fecha_evento  [Días de consulta]
     * @return [array]                [retorna datos de los agentes en línea obtenidos]
     */
    public function agents_online($fecha_evento){
        $days                   = explode(' - ', $fecha_evento);
        $query_agents_online    = $this->query_agents_online($days);
        return $query_agents_online;
    }


    /**
     * [query_agents_online Función que realiza la consulta a la tabla Agentes Online]
     * @param  [array] $days [Rango de días a realizar la consulta]
     * @return [array]       [Retorna agentes en línea en base a la consulta realizada]
     */
    public function query_agents_online($days){
        $events_ignored         = array (4,15,7);
        $query_agents_online    = AgentesOnline::select_fechamod()
                                        ->filtro_days($days)
                                        ->whereNotIn('evento_id',$events_ignored)
                                        ->groupBy('date_agent','hour_agent')
                                        ->get();
                              
        return $query_agents_online;
    }
    

}
