<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Cdr;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OutgoingCallsController extends CosapiController
{
    /**
     * [index Función que retorna vista o datos al reporte Outbound Calls]
     * @param  Request $request [Retorna datos enviados por POST]
     * @return [view]           [Vista o Array con datos del reporte Outbound Calls]
     */
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

    /**
     * [export description]
     * @param  Request $request [Dato para identifcar GET O POST]
     * @return [type]           [description]
     */
    public function export(Request $request){
        $export_outgoing  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_outgoing;
    }

    /**
     * [query_calls_outgoing Función donde se realiza la consulta a la base de datos de las llamadas salientes realizadas]
     * @param  [array] $fecha_evento [Rango de consulta, Ejem: '2016-10-22 - 20016-10-25']
     * @return [array]               [Array con los datos obtenidos de la consutla]
     */
    protected function query_calls_outgoing($fecha_evento){
        $days                   = explode(' - ', $fecha_evento);
        $tamano_anexo           = array (getenv('ANEXO_LENGTH'));
        $tamano_telefono        = array ('7','9');
        $query_calls_outgoing   = Cdr::Select()
                                    ->whereIn(DB::raw('LENGTH(src)'),$tamano_anexo)
                                    ->whereIn(DB::raw('LENGTH(dst)'),$tamano_telefono)
                                    ->where('dst','not like','*%')
                                    ->where('disposition','=','ANSWERED')
                                    ->where('lastapp','=','Dial')
                                    ->where(function ($query){
                                            $query->whereBetween('src',array ('4001','4010'));
                                                  //->orWhereBetween('src',array ('248','248'));
                                        })
                                    ->filtro_days($days)
                                    ->OrderBy('src')
                                    ->get()
                                    ->toArray();


        return $query_calls_outgoing;
    }

    /**
     * [builderview Función que ordena los datos visualmente para que sean cargado en el reporte Outbound Calls]
     * @param  [array]  $query_calls_outgoing [Array con datos de las llamadas salientes realizadas]
     * @param  [string] $type                 [Tipo de evento a realizar Exportar o Cargar datos en la tabla de Outbound Calls]
     * @return [array]                        [Array modificado para la correcta visualización en el reporte]
     */
    protected function builderview($query_calls_outgoing,$type=''){
        $action = '';
        $posicion = 0;
        foreach ($query_calls_outgoing as $query_call) {

            if($type == 'export'){
                $builderview[$posicion]['date']          = $this->MostrarSoloFecha($query_call['calldate']);
                $builderview[$posicion]['hour']          = $this->MostrarSoloHora($query_call['calldate']);
                $builderview[$posicion]['annexedorigin'] = $query_call['src'];
                $builderview[$posicion]['destination']   = $query_call['dst'];
                $builderview[$posicion]['username']      = $query_call['accountcode'];
                $builderview[$posicion]['calltime']      = conversorSegundosHoras($query_call['billsec'],false);
            }else{
                $builderview[$posicion]['date']          = $this->MostrarSoloFecha($query_call['calldate']);
                $builderview[$posicion]['hour']          = $this->MostrarSoloHora($query_call['calldate']);
                $builderview[$posicion]['annexedorigin'] = $query_call['src'];
                $builderview[$posicion]['destination']   = $query_call['dst'];
                $builderview[$posicion]['username']      = $query_call['accountcode'];
                $builderview[$posicion]['userfield']     = $query_call['userfield']; // Url para descarga de audio
                $builderview[$posicion]['calltime']      = conversorSegundosHoras($query_call['billsec'],false);

            }
            $posicion ++;
        }
        if(!isset($builderview)){
            $builderview = [];
        }
        return $builderview;
    }

    /**
     * [outgoingcollection Función que pasa los datos de un array a un collection]
     * @param  [array]      $builderview [Datos de las llamadas salientes que se visualizaran en los reportes]
     * @return [collection]              [description]
     */
    protected function outgoingcollection($builderview){
        $outgoingcollection                 = new Collector;
        foreach ($builderview as $view) {

            $day            = Carbon::parse($view['date']);
            $hour           = Carbon::parse($view['hour']);

            $posicion       = strripos($view['userfield'], '/');

            $url            = 'url='.substr($view['userfield'], 0, $posicion).'/';
            $proyecto       = '&proyect=empresas';
            $audio_name     =  '&nameaudio='.$view['destination'].'-'.$view['annexedorigin'].'-'.$day->format('dmY').'-'.$hour->format('His');
            $route_complete = 'http://'.$_SERVER['HTTP_HOST'].'/cosapi/script_php/descargar_audio.php?'.$url.$audio_name.$proyecto;


            $outgoingcollection->push([
                'date'                      => $view['date'],
                'hour'                      => $view['hour'],
                'annexedorigin'             => $view['annexedorigin'],
                'destination'               => $view['destination'],
                'calltime'                  => $view['calltime'],
                'username'                  => $view['username'],
                'audio'                     => '<a target="_blank" href="'.$route_complete.'">Audios <i class="fa fa-rss" aria-hidden="true"></i></a>'
            ]);

        }

        return $outgoingcollection;
    }

    /**
     * [export_csv Function que retorna la ubicación de los datos a exportar en CSV]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en CSV]
     */
    protected function export_csv($days){

        $builderview = $this->builderview($this->query_calls_outgoing($days),'export');
        $this->BuilderExport($builderview,'outgoing_calls','csv','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/outgoing_calls.csv']
        ];

        return $data;
    }

    /**
     * [export_excel Function que retorna la ubicación de los datos a exportar en Excel]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en Excel]
     */
    protected function export_excel($days){

        $builderview = $this->builderview($this->query_calls_outgoing($days,'outgoing_calls'),'export');
        $this->BuilderExport($builderview,'outgoing_calls','xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/outgoing_calls.xlsx']
        ];

        return $data;
    }

}