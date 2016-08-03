<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Controllers\Controller;

use DB;
use Excel;
use Datatables;
use Illuminate\Support\Facades\Log;


class IncomingCallsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            if ($request->evento){
                return $this->calls_incoming($request->fecha_evento, $request->evento);
            }else{
                return view('elements/incoming_calls/index');
            }
        }
    }

    protected function calls_incoming($fecha_evento, $evento){
        $days           = explode(' - ', $fecha_evento);
        $calls_incoming = $this->query_calls($days,$evento);
        $calls_incoming = $this->format_datatable($calls_incoming);
        return $calls_incoming;
    }


    protected function get_events($events){

        switch($events){
            case 'calls_completed' :
                $events             = array ('COMPLETECALLER', 'COMPLETEAGENT', 'TRANSFER');
            break;
            case 'calls_transfer' :
                $events             = array ('TRANSFER');
            break;
            case 'calls_abandone' :
                $events             = array ('ABANDON');
            break;
        }
        return $events;
    }

    /**
     * [query_calls_transfer Función para listar las llamadas transferidas]
     * @param  [Array] $days [Recibe las fechas para establecer el rango de busqueda]
     * @return [Array]       [Array con los datos de las llamadas tranferidas]
     */
    protected function query_calls($days,$events)
    {
        $events             = $this->get_events($events);
        $calls_abandoned    = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->OrderBy('id')
                                        ->get();                           
        return $calls_abandoned;
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

    public function prueba(){
        $days                = explode(' - ', '2016-07-01 - 2016-07-01');
        $calls_incoming      = $this->query_calls($days,'calls_completed');
        $export_contestated  = $this->export_excel($calls_incoming);
        return $export_contestated;
    }

    protected function export_excel($array){
        Excel::create('Filename', function($excel) use($array) {           
            $excel->sheet('prueba', function($sheet) use($array) {               
                $sheet->fromArray($array);
            });
        })->export('csv');
        return $array;
    }

}