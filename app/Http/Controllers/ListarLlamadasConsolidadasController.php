<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;
use Cosapi\Collector\Collector;
use Datatables;
use DB;

class ListarLlamadasConsolidadasController extends Controller
{
    

    /**
     * [index Función que muestra la ventana principal del módulo ListarLlamadasConsolidadas]
     * @return [Array] [retorna los datos para la vista]
     */
    public function index($evento)
    {

        $evento=$evento;
        return view('elements/Listar_Llamadas_Consolidadas/index')->with(array(
                'evento'   => $evento
            ));   
    }

    public function consolidadas_skills(){

        dd('hola');

    }

    /**
     * [listar_llamadas_consolidadas Función para listar el consolidado de llamadas]
     * @param  Request   $request        [Dato para identifcar GET O POST]
     * @param  [string]  $fecha_evento   [Recibe el rango de fecha e buscar]
     * @return [Array]                   [Retorna la lista del consolidado de llamadas]
     */
    public function listar_llamadas_consolidadas(Request $request){

        $objCollection                      = new Collector;
        $days                               = explode(' - ', $request->fecha_evento);      
        $calls_inbound                      = $this->calls_inbound($days);

        // DATOS PARA ORDENAR
        switch($request->url){
            case 'calls_consolidated' :
                $call_group                 = $this->calls_queue();
                $groupby                      = 'queue';
            case 'agent' :
                $call_group                 = $this->calls_agents();
            case 'hourmod' :
                $call_group                 = $this->calls_hours($days);
            case 'fechamod' :
                $call_group                 = ArrayDays($days);
        }

        // CONSTRUYENDO LOS DATOS DE VISTA
        $ListCallsConsolidated              = ListCallsConsolidated($calls_inbound,$groupby);
        $BuilderCallsConsolidated           = BuilderCallsConsolidated($ListCallsConsolidated,$call_group,$groupby);
        $objCollection                      = convertCollection($BuilderCallsConsolidated, $objCollection );

        // CAMIBANDO A FORMATO DATABALE
        $CallsConsolidated                  = Datatables::of($objCollection)
                                                        ->editColumn('name', function ($objCollection) {

                                                            if($groupby == 'hourmod'){
                                                                return conversorSegundosHoras(((intval($objCollection['name']))*3600),false).' - '.conversorSegundosHoras(convertHourExactToSeconds($objCollection['name'],3540),false);
                                                            }

                                                            return $objCollection['name'];
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
                                    ->whereNotIn('queue', ['NONE'])
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
                                    ->whereNotIn('queue', ['Pruebas','NONE'])
                                    ->groupBy('agent')
                                    ->get()
                                    ->toArray();
        return $calls_agents;
    }

    
    protected function calls_hours($days)
    {
        
        $calls_hours  = Queue_empresa::Select(DB::raw('HOUR(datetime) AS hourmod ' ))
                                        ->filtro_days($days)
                                        ->groupBy('hourmod')
                                        ->get()
                                        ->toArray();        
        return $calls_hours;
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
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();                         
        return $calls_inbound;
    }


}
