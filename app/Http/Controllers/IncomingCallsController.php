<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Excel;
use Illuminate\Support\Facades\Log;


class IncomingCallsController extends CosapiController
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


    public function export(Request $request){
        $export_contestated  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_contestated;
    }


    protected function calls_incoming($fecha_evento, $evento){
        $query_calls        = $this->query_calls($fecha_evento,$evento);
        $builderview        = $this->builderview($query_calls);
        $incomingcollection = $this->incomingcollection($builderview);
        $calls_incoming     = $this->FormatDatatable($incomingcollection);

        return $calls_incoming;
    }


    public function query_calls($days,$events,$users ='')
    {
        $days           = explode(' - ', $days);
        $events         = $this->get_events($events);
        $query_calls    = Queue_empresa::select_fechamod()
                                        ->filtro_users($users)
                                        ->filtro_days($days)
                                        ->filtro_events($events)
                                        ->whereNotIn('queue', ['NONE','HD_CE_BackOffice','Pruebas'])
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();  
        
        return $query_calls;
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
            $builderview[$posicion]['duration']    = conversorSegundosHoras($query_call['info2'],false);

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
            $builderview[$posicion]['waittime']    = conversorSegundosHoras($query_call['info1'],false);
            $posicion ++;
        }

        return $builderview;
    }


    protected function incomingcollection($builderview){
        $incomingcollection                 = new Collector;
        foreach ($builderview as $view) {
            $incomingcollection->push([
                'date'                      => $view['date'],
                'hour'                      => $view['hour'],
                'telephone'                 => $view['telephone'],
                'agent'                     => $view['agent'],
                'skill'                     => $view['skill'],
                'duration'                  => $view['duration'],
                'action'                    => $view['action'],
                'waittime'                  => $view['waittime']
            ]);
        }

        return $incomingcollection;
    }


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