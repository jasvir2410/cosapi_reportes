<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;
use Cosapi\Collector\Collector;
use Datatables;
use DB;

class ConsolidatedCallsController extends Controller{

    public function index(Request $request){
        if ($request->ajax()){
            if ($request->evento){
                return $this->calls_consolidated($request->fecha_evento, $request->evento);
            }else{
                return view('elements/consolidated_calls/index');
            }
        }
    }


    protected function calls_consolidated($fecha_evento, $evento){

        $objCollection                      = new Collector;
        $groupby                            = '';
        $days                               = explode(' - ', $fecha_evento);
        $calls_inbound                      = $this->calls_inbound($days);


        switch($evento){
            case 'skills_group' :
                $call_group                 = $this->calls_queue($days);
                $groupby                    = 'queue';
                break ;
            case 'agent_group'        :
                $call_group                 = $this->calls_agents($days);
                $groupby                    = 'agent';
                break ;
            case 'day_group'          :
                $call_group                 = ArrayDays($days);
                $groupby                    = 'fechamod';
                break ;
            case 'hour_group'         :
                $call_group                 = listHoursInterval();
                $groupby                    = 'hourmod';
                break ;

        }

        $CallsConsolidated                  = CallsConsolidated($calls_inbound,$groupby);
        $BuilderCallsConsolidated           = BuilderCallsConsolidated($CallsConsolidated,$call_group,$groupby);
        $objCollection                      = convertCollection($BuilderCallsConsolidated, $objCollection );

        $CallsConsolidated                  = Datatables::of($objCollection)
                                                        ->editColumn('name', function ($objCollection) {
                                                            $posicion = strpos($objCollection['name'], 'Agent/');   
                                                            if($posicion === false){
                                                                return $objCollection['name'];
                                                            }else{
                                                                return ExtraerAgente($objCollection['name']);
                                                            }   
                                                        })
                                                        ->make(true);

        return $CallsConsolidated;

    }

    /**
     * [calls_queue Función la cual permite obtener la lista de nombre de los Skills]
     * @return [Array] [Retorna un Array con la lista de nombre de los Skills]
     */
    protected function calls_queue($days)
    {

        $calls_queue  = Queue_empresa::Select('queue')
                                    ->filtro_days($days)
                                    ->whereNotIn('queue', ['NONE','HD_CE_BackOffice','Pruebas'])
                                    ->groupBy('queue')
                                    ->get()
                                    ->toArray();
                                           
        return $calls_queue;
    }

    
    protected function calls_agents($days)
    {

        $calls_agents  = Queue_empresa::Select('agent')
                                    ->filtro_days($days)
                                    ->whereNotIn('agent', ['NONE'])
                                    ->whereNotIn('queue', ['Pruebas','NONE','HD_CE_BackOffice'])
                                    ->groupBy('agent')
                                    ->get()
                                    ->toArray();
    
        return $calls_agents;
    }

    
    

    protected function calls_inbound($days)
    {
        $LlamadasEntrantes  = $this->query_calls_inbound($days);
        return $LlamadasEntrantes;        
    }

        /**
     * [query_calls_inbound description]
     * @param  [type] $days [description]
     * @return [type]       [description]
     */
    protected function query_calls_inbound($days)
    {
        $calls_inbound      = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->whereNotIn('queue', ['HD_CE_BackOffice','Pruebas'])
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();                         
        return $calls_inbound;
    }


}
