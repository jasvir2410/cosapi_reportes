<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;

use Cosapi\Models\Queue_Empresa;
use Cosapi\Models\User;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\Controller;

use Datatables;
use Excel;
use DB;
use Carbon\Carbon;

class ListarLlamadasEntrantesController extends Controller
{


    /**
     * [index Función principal para cargar la vista por defecto del Modulo de ListarLlamadasContestadas]
     * @return [Array] [retorna datos para la vista]
     */
    public function index()
    {
        return view('elements/Listar_Llamadas_Entrantes/index');
    }


    /**
     * [listar_llamadas_contestadas Fución para listar las llamadas contestadas]
     * @param  Request   $request      [Dato para identifcar GET O POST]
     * @param  [Strign]  $fecha_evento [Recibe las fechas para establecer el rango de busqueda]
     * @return [Array]                 [Array con los datos de las llamadas contestadas en un determinado time]
     */
    public function listar_llamadas_contestadas(Request $request, $fecha_evento)
    {
        $days                   = explode(' - ', $fecha_evento);        
        $LlamadasEntrantes      = $this->calls_contestated($days);
        return $LlamadasEntrantes;

    }


    /**
     * [listar_llamadas_abandonadas description]
     * @param  Request $request      [description]
     * @param  [type]  $fecha_evento [description]
     * @return [type]                [description]
     */
    public function listar_llamadas_abandonadas(Request $request, $fecha_evento)
    {
        $days                   = explode(' - ', $fecha_evento);        
        $LlamadasEntrantes      = $this->calls_abandoned($days);
        return $LlamadasEntrantes;

    }


    /**
     * [listar_llamadas_transferidas description]
     * @param  Request $request      [description]
     * @param  [type]  $fecha_evento [description]
     * @return [type]                [description]
     */
    public function listar_llamadas_transferidas(Request $request, $fecha_evento)
    {
        $days                   = explode(' - ', $fecha_evento);        
        $LlamadasEntrantes      = $this->calls_transfer($days);
        return $LlamadasEntrantes;

    }


    /**
     * [query_calls_abandoned Función para listar las llamdas abandonadas]
     * @param  [Array] $days   [Recibe las fechas para establecer el rango de busqueda]
     * @param  string $filtro  [Id_cdr de llamadas perteneciente a los dios no establecidos]
     * @return [Array]         [Array con los datos de las llamadas abandonadas]
     */
    protected function query_calls_abandoned($days)    
    {
        $events             = array ('ABANDON');
        $calls_abandoned    = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->OrderBy('id')
                                        ->get();                           
        return $calls_abandoned;
    }


    /**
     * [query_calls_transfer Función para listar las llamadas transferidas]
     * @param  [Array] $days [Recibe las fechas para establecer el rango de busqueda]
     * @return [Array]       [Array con los datos de las llamadas tranferidas]
     */
    protected function query_calls_transfer($days)
    {
        $events             = array ('TRANSFER');
        $calls_transfer     = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->OrderBy('id')
                                        ->get();                           
        return $calls_transfer;
    }


    /**
     * [query_calls_completed Función para listar las llamadas completadas]
     * @param  [type] $days [Recibe las fechas para establecer el rango de busqueda]
     * @return [type]       [Array con la lista de las llamadas completadas]
     */
    protected function query_calls_completed($days)
    {
        $events             = array ('COMPLETECALLER', 'COMPLETEAGENT', 'TRANSFER');
        $calls_completed    = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->OrderBy('id')
                                        ->get();                           
        return $calls_completed;
    }




    /**
     * [calls_contestated description]
     * @param  [type] $days [description]
     * @return [type]       [description]
     */
    protected function calls_contestated($days)
    {
        $LlamadasEntrantes  = $this->query_calls_completed($days);

        /*$document=Excel::create('Filename', function($excel) use($LlamadasEntrantes) {
            foreach($LlamadasEntrantes as $LlamadasEntrante){                 
                 $date= Carbon::now();
                 $main_arr[] =  $date->toTimeString();                
            }
            $excel->sheet('prueba', function($sheet) use($main_arr) {
                
                

                $sheet->fromArray($main_arr);

            });

        })->export('xls');*/

        //$LlamadasEntrantes  = Datatables::of($LlamadasEntrantes)->make(true);
        
        $LlamadasEntrantes= $this->format_datatable($LlamadasEntrantes);
        return $LlamadasEntrantes;        
    }


    protected function calls_transfer($days)
    {
        $LlamadasEntrantes  = $this->query_calls_transfer($days);
        $LlamadasEntrantes  = $this->format_datatable($LlamadasEntrantes);
        return $LlamadasEntrantes;        
    }


    protected function calls_abandoned($days)
    {
        $LlamadasEntrantes  = $this->query_calls_abandoned($days);
        $LlamadasEntrantes  = $this->format_datatable($LlamadasEntrantes);
        return $LlamadasEntrantes;        
    }    





    protected function format_datatable($arrays)
    {
        $format_datatable  = Datatables::of($arrays)
                                    ->editColumn('agent', function ($array) {
                                            return ExtraerAgente($array->agent);
                                        })
                                    ->editColumn('info1', function ($array) {
                                            return conversorSegundosHoras($array->info1,false);
                                        })
                                    ->editColumn('info2', function ($array) {
                                            return conversorSegundosHoras($array->info2,false);
                                        })
                                    ->editColumn('event', function ($array) {
                                            if($array->event == 'TRANSFER'){
                                                return 'Transferido a '.$array->url;
                                            }else if($array->event == 'ABANDON'){
                                                return 'Abandonada';
                                            }else if($array->event == 'COMPLETECALLER'){
                                                return 'Colgo Cliente';
                                            }else{
                                                return 'Colgo Agente';
                                            }
                                        })
                                    ->make(true);
        return $format_datatable;
    }


    protected function export_contestated($days)
    {
        $days                = explode(' - ', $days); 
        $export_contestated  = $this->query_calls_completed($days);
        $export_contestated  = $this->export_excel($export_contestated);
        return $export_contestated;
    }


    protected function export_excel($array){
        Excel::create('Filename', function($excel) use($array) {           
            $excel->sheet('prueba', function($sheet) use($array) {               
                $sheet->fromArray($array);
            });
        })->export('xls');
        return $array;
    }

}