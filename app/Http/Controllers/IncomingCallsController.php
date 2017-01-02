<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class IncomingCallsController extends CosapiController
{

    /**
     * [index Función que retorna vista o datos al reporte Incoming Calls]
     * @param  Request $request [Retorna datos enviados por POST]
     * @return [view]           [Vista o Array con datos del reporte Incoming Calls]
     */
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


    /**
     * [export Función que permite exportar el reporte de Incoming Calls]
     * @param  Request $request [Retorna datos enviados por POST]
     * @return [array]          [Array con la ubicación de los archivos exportados]
     */
    public function export(Request $request){
        $export_contestated  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_contestated;
    }


    /**
     * [calls_incoming Función que obtiene los datos para las llamadas entrante]
     * @param  [string] $fecha_evento [Fecha de la consulta del reporte]
     * @param  [string] $evento       [Tipo de reporte a consultar (Atendidas, Transferidas o Abandonadas)]
     * @return [array]                [Array con datos de las llamadas entrantes]
     */
    protected function calls_incoming($fecha_evento, $evento){
        $query_calls        = $this->query_calls($fecha_evento,$evento);
        $builderview        = $this->builderview($query_calls);
        $incomingcollection = $this->incomingcollection($builderview);
        $calls_incoming     = $this->FormatDatatable($incomingcollection);
        return $calls_incoming;
    }


    /**
     * [query_calls Funcción que consulta en la base de datos las llamadas de entrantes]
     * @param  [string] $days   [Fecha de consulta]
     * @param  [array] $events  [Eventos de las llamadas]
     * @param  [string] $users  [Id del usuario]
     * @param  [string] $hours  [Hora de de la llamda, Ejem: 18:52]
     * @return [array]          [Array con los datos de llamadas entrantes]
     */
    public function query_calls($days,$events,$users ='', $hours ='')
    {
        $days           = explode(' - ', $days);
        $events         = $this->get_events($events);
        $query_calls    = Queue_empresa::select_fechamod()
                                        ->filtro_users($users)
                                        ->filtro_hours($hours)
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->filtro_anexos()
                                        ->whereNotIn('queue', ['NONE','HD_CE_BackOffice','Pruebas','HD_CE_Calidad'])
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();  
        
        return $query_calls;
    }


    /**
     * [get_events Función que muestra los eventos en base a la acción a realizar]
     * @param  [string] $events [Tipo de reportes de Llamadas]
     * @return [array]          [Eventos que comforman el tipo de reporte]
     */
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
     * [builderview Función que prepara los datos para mostrar en la vista]
     * @param  [array] $query_calls [Array con los datos de llamadas entrantes]
     * @return [array]              [Array modificado para mostrar en el reporte de Incoming Calls]
     */
    protected function builderview($query_calls){
        $action = '';
        $posicion = 0;
        $builderview = [];
        foreach ($query_calls as $query_call) {
            $builderview[$posicion]['date']        = $query_call['fechamod'];
            $builderview[$posicion]['hour']        = $query_call['timemod'];
            $builderview[$posicion]['telephone']   = $query_call['clid'];
            $builderview[$posicion]['agent']       = ExtraerAgente($query_call['agent']);
            $builderview[$posicion]['skill']       = $query_call['queue'];
            $builderview[$posicion]['duration']    = conversorSegundosHoras(abs($query_call['info2']),false);

            switch ($query_call['event']) {
                case 'TRANSFER':
                    $action = 'Transferido a '.$query_call['url'];
                    break;
                case 'ABANDON':
                    $action = 'Abandonada';
                    break;
                case 'COMPLETECALLER':
                    $action = 'Colgo Cliente';
                    break;
                default:
                    $action = 'Colgo Agente';
                    break;
            }

            $builderview[$posicion]['action']      = $action;
            $builderview[$posicion]['waittime']    = conversorSegundosHoras(abs($query_call['info1']),false);
            $posicion ++;
        }

        return $builderview;
    }


    /**
     * [incomingcollection Función que permite pasar de Array a Collection los datos del reporte Incoming Calls]
     * @param  [array]      $builderview [Array con los datos de llamdas entrantes]
     * @return [collection]              [Collection con los datos de llamadas entrantes]
     */
    protected function incomingcollection($builderview){
        $incomingcollection                 = new Collector;
        foreach ($builderview as $view) {
            switch ($view['skill']) {
                case 'HD_CE_Telefonia':
                    $vdn =  79301;
                    break;
                case 'HD_CE_Internet':
                    $vdn =  79302;
                    break;
                case 'HD_CE_Cable':
                    $vdn =  79303;
                    break;
                case 'HD_CE_Operador':
                    $vdn =  79304;
                    break;                
                case 'HD_CE_BackOffice':
                    $vdn =  79999;
                    break;
                case 'HD_CE_Tecnica_Corp':
                    $vdn =  7289706;
                    break;
            }

            $audio = 'No compatible';
            $day   = Carbon::parse($view['date']);
            $hour  = Carbon::parse($view['hour']);

            $url             = 'http://grabaciones.cosapidata.pe/empresas/'.$view['skill'].'/'.$day->format('Y/m/d').'/';
            $audio_name      = $view['telephone'].'-'.$vdn.'-'.$day->format('dmY').'-'.$hour->format('His').'.gsm';
            $route_complete  = $url.$audio_name;
            $bronswer        = detect_bronswer();

            if($bronswer["browser"] == 'FIREFOX'){ 
            $audio           = '<a class="media" href="'.$route_complete.'" target="_blank">Audios <i class="fa fa-rss" aria-hidden="true"></i></a>';
            }

            $incomingcollection->push([
                'date'                      => $view['date'],
                'hour'                      => $view['hour'],
                'telephone'                 => $view['telephone'],
                'agent'                     => $view['agent'],
                'skill'                     => $view['skill'],
                'duration'                  => $view['duration'],
                'action'                    => $view['action'],
                'waittime'                  => $view['waittime'],
                'audio'                     => $audio

            ]);
        }

        return $incomingcollection;
    }


    /**
     * [export_csv Function que retorna la ubicación de los datos a exportar en CSV]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en CSV]
     */
    protected function export_csv($days){

        $events = ['calls_completed','calls_transfer','calls_abandone'];

        for($i=0;$i<count($events);$i++){
            $builderview = $this->builderview($this->query_calls($days,$events[$i]));
            $this->BuilderExport($builderview,$events[$i],'csv','exports');
        }
    
        $data = [
            'succes'    => true,
            'path'      => [
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/calls_completed.csv',
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/calls_transfer.csv',
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/calls_abandone.csv'
                            ]
        ];

        return $data;
    }


    /**
     * [export_excel Function que retorna la ubicación de los datos a exportar en Excel]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en Excel]
     */
    protected function export_excel($days){
        Excel::create('inbound_calls', function($excel) use($days) {

            $excel->sheet('Atendidas', function($sheet) use($days) {
                $sheet->fromArray($this->builderview($this->query_calls($days,'calls_completed')));
            });

            $excel->sheet('Transferidas', function($sheet) use($days) {
                $sheet->fromArray($this->builderview($this->query_calls($days,'calls_transfer')));
            });


            $excel->sheet('Abandonadas', function($sheet) use($days) {
                $sheet->fromArray($this->builderview($this->query_calls($days,'calls_abandone')));
            });


        })->store('xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/inbound_calls.xlsx']
        ];

        return $data;
    }

}