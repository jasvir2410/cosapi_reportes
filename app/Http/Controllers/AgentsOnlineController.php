<?php

namespace Cosapi\Http\Controllers;


use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\AgentesOnline;
use Cosapi\Http\Controllers\Controller;

use DB;
use Datatables;
use Illuminate\Support\Facades\Log;

class AgentsOnlineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

    public function agents_online($fecha_evento){
        $days                = explode(' - ', $fecha_evento);
        $query_agents_online = $this->query_agents_online($days);
        $agents_online       = $this->format_datatable($query_agents_online);
        return $query_agents_online;
    }

    public function query_agents_online($days){
        $events_ignored         = array (4,15,7);
        $query_agents_online    = AgentesOnline::select_fechamod()
                                        ->filtro_days($days)
                                        ->whereNotIn('evento_id',$events_ignored)
                                        ->groupBy('date_agent','hour_agent')
                                        ->get();
                              
        return $query_agents_online;
    }

    protected function format_datatable($arrays)
    {
        $format_datatable  = Datatables::of($arrays)->make(true);
        return $format_datatable;
    }

}
