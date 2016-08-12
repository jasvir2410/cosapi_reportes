<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\AsteriskCDR;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Illuminate\Support\Facades\Log;

class OutgoingCallsController extends CosapiController
{

    
    public function index(Request $request)
    {        
        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->list_calls_outgoing($request->fecha_evento);
            }else{
                return view('elements/outgoing_calls/index');
            }
        }
    }


    /**
     * [listar_llamadas_consolidadas Función para listar el consolidado de llamadas]
     * @param  Request   $request        [Dato para identifcar GET O POST]
     * @param  [string]  $fecha_evento   [Recibe el rango de fecha e buscar]
     * @return [Array]                   [Retorna la lista del consolidado de llamadas]
     */
    public function list_calls_outgoing($fecha_evento){
                
        $query_calls_outgoing   = $this->query_calls_outgoing($fecha_evento);
        $builderview            = $this->builderview($query_calls_outgoing);
        $outgoingcollection     = $this->outgoingcollection($builderview);        
        $list_call_outgoing     = $this->FormatDatatable($outgoingcollection);

        return $list_call_outgoing;
    }


    public function export(Request $request){
        $export_outgoing  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_outgoing;
    }

 
    protected function query_calls_outgoing($fecha_evento){
        $days                   = explode(' - ', $fecha_evento);
        $tamano_anexo           = array ('3');
        $tamano_telefono        = array ('7','9');
        $query_calls_outgoing   = AsteriskCDR::Select()
                                    ->whereIn(DB::raw('LENGTH(src)'),$tamano_anexo)
                                    ->whereIn(DB::raw('LENGTH(dst)'),$tamano_telefono)
                                    ->where('src','like','2%')
                                    ->where('dst','not like','*%')
                                    ->where('disposition','=','ANSWERED')
                                    ->filtro_days($days)
                                    ->OrderBy('src')
                                    ->get()
                                    ->toArray();


        return $query_calls_outgoing;
    }


    protected function builderview($query_calls_outgoing){
        $action = '';
        $posicion = 0;
        foreach ($query_calls_outgoing as $query_call) {
            $builderview[$posicion]['date']          = $this->MostrarSoloFecha($query_call['calldate']);
            $builderview[$posicion]['hour']          = $this->MostrarSoloHora($query_call['calldate']);
            $builderview[$posicion]['annexedorigin'] = $query_call['src'];
            $builderview[$posicion]['destination']   = $query_call['dst'];
            $builderview[$posicion]['calltime']      = conversorSegundosHoras($query_call['billsec'],false);
            $posicion ++;
        }
        if(!isset($builderview)){
            $builderview = [];
        }
        return $builderview;
    }


    protected function outgoingcollection($builderview){
        $outgoingcollection                 = new Collector;
        foreach ($builderview as $view) {
            $outgoingcollection->push([
                'date'                      => $view['date'],
                'hour'                      => $view['hour'],
                'annexedorigin'             => $view['annexedorigin'],
                'destination'               => $view['destination'],
                'calltime'                  => $view['calltime']
            ]);
        }

        return $outgoingcollection;
    }


    protected function export_csv($days){

        $builderview = $this->builderview($this->query_calls_outgoing($days));
        $this->BuilderExport($builderview,'outgoing_calls','csv','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/outgoing_calls.csv']
        ];

        return $data;
    }


    protected function export_excel($days){

        $builderview = $this->builderview($this->query_calls_outgoing($days,'outgoing_calls'));
        $this->BuilderExport($builderview,'outgoing_calls','xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/outgoing_calls.xlsx']
        ];

        return $data;
    }

}