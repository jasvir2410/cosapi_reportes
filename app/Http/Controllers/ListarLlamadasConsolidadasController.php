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
    public function index(Request $request)
    {

        $evento=$request->url;
        return view('elements/Listar_Llamadas_Consolidadas/index')->with(array(
                'evento'   => $evento
            ));   
    }


    /**
     * [listar_llamadas_consolidadas Función para listar el consolidado de llamadas]
     * @param  Request   $request        [Dato para identifcar GET O POST]
     * @param  [string]  $fecha_evento   [Recibe el rango de fecha e buscar]
     * @return [Array]                   [Retorna la lista del consolidado de llamadas]
     */
    public function calls_consolidated(Request $request){

        $objCollection                      = new Collector;
        $groupby                            = '';
        $days                               = explode(' - ', $request->fecha_evento);      
        $calls_inbound                      = $this->calls_inbound($days);


        //dd($request->url);

        // DATOS PARA ORDENAR
        switch(trim((string)$request->url)){
            case 'calls_consolidated' :
                $call_group                 = $this->calls_queue($days);
                $groupby                    = 'queue';
                break ;
            case 'calls_agent'        :
                $call_group                 = $this->calls_agents($days);
                $groupby                    = 'agent';
                break ;
            case 'calls_hour'         :
                $call_group                 = $this->calls_hours($days);
                $groupby                    = 'hourmod';
                break ;
            case 'calls_day'          :
                $call_group                 = ArrayDays($days);
                $groupby                    = 'fechamod';
                break ;
        }


        //dd($groupby);
        // CONSTRUYENDO LOS DATOS DE VISTA
        $ListCallsConsolidated              = ListCallsConsolidated($calls_inbound,$groupby);
        $BuilderCallsConsolidated           = BuilderCallsConsolidated($ListCallsConsolidated,$call_group,$groupby);
        $objCollection                      = convertCollection($BuilderCallsConsolidated, $objCollection );

        // CAMIBANDO A FORMATO DATABALE
        $CallsConsolidated                  = Datatables::of($objCollection)
                                                        ->editColumn('name', function ($objCollection) {
                                                            $posicion = strpos($objCollection['name'], 'Agent/');
                                                            /*if(is_numeric($objCollection['name'])){
                                                                return conversorSegundosHoras(((intval($objCollection['name']))*3600),false).' - '.conversorSegundosHoras(convertHourExactToSeconds($objCollection['name'],3540),false);
                                                            }else{*/
                                                                if($posicion === false){
                                                                    return $objCollection['name'];
                                                                }else{
                                                                    return ExtraerAgente($objCollection['name']);
                                                                }                                                               
                                                            //}
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
        
        $calls_hours = [];
        for($i = 0;$i<24;$i++){
            if($i<=9){
                $hora='0'.$i;
            }else{
                $hora=$i;
            }
            array_push($calls_hours, ["hourmod" => $hora.':00']);
            array_push($calls_hours, ["hourmod" => $hora.':30']);
        }
       
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
