<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Collector\Collector;
use Illuminate\Http\Request;

use Cosapi\Models\User;
use Cosapi\Models\Eventos;
use Cosapi\Models\DetalleEventosHistory;
use Cosapi\Models\DetalleEventos;
use Cosapi\Http\Controllers\CosapiController;

use Cosapi\Http\Requests;
use Illuminate\Support\Facades\DB;
use Cosapi\Http\Controllers\Controller;
use Excel;
use Yajra\Datatables\Facades\Datatables;
use Cosapi\Http\Controllers\IncomingCallsController;


class EventsAgentController extends CosapiController
{

    /**
     * [events_consolidated Función que retorna la vista o datos para el reporte de Consolidated Events]
     * @param  Request $request  [Recepciona datos enviado por POST]
     * @return [view]            [Vista o Datos para cargar en el reporte consolidated]
     */
    public function events_consolidated(Request $request){

        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->list_state_agents($request->fecha_evento);
            }else{
                return view('elements/events_consolidated/index');
            }
        }

    }



    /**
     * [events_detail Función que carga los datos y vista del Details Events]
     * @param  Request $request [Recepciona los datos por POST]
     * @return [view]           [Retorna una vista y datos del reporte Details Events]
     */
    public function events_detail (Request $request){
        if ($request->ajax()){
            if ($request->fecha_evento){

                $days                   = explode(' - ',$request->fecha_evento);
                $query_events           = $this->query_events($days);
                $array_detail_events    = detailEvents($query_events);
                $objCollection          = $this->convertCollection($array_detail_events);
                $detail_events          = $this->FormatDatatable($objCollection);
                return $detail_events;

            }else{
                return view('elements/events_detail/index');
            }
        }
    }


    /**
     * [export Función que permite exportar los datos de la tabla]
     * @param  Request $request [Recepciona datos enviado por POST]
     * @return [array]          [Ubicaciones de los archivos a exportar]
     */
    public function export(Request $request){
        $export_contestated  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_contestated;
    }


    /**
     * [list_state_agents Función que obtiene los datos a cargar en el reporte de Consolidated Events]
     * @param  [date]  $fecha_evento [Fecha de la consulta]
     * @return [array]               [Array con datos del consolidado de eventos]
     */
    protected function list_state_agents($fecha_evento){

        $query_estado_agentes   = $this->query_estado_agentes($fecha_evento);
        $estateagentcollection  = $this->collection_state_agent($query_estado_agentes);
        $list_state_agents      = $this->FormatDatatable($estateagentcollection);
        return $list_state_agents;

    }


    /**
     * [query_estado_agentes Función que consulta los datos de Consolidated Events]
     * @param  [date]  $fecha_evento [Fecha de la consulta]
     * @return [array]               [Datos extraidos del Consolidated Events desde la Base de Datos]
     */
    protected function query_estado_agentes ($fecha_evento)
    {
        list($fecha_inicial,$fecha_final) = explode(' - ', $fecha_evento);

        $query_estado_agentes = DB::select('CALL sp_get_consolidated_events ("'.$fecha_inicial.'","'.$fecha_final.'")');

        return $query_estado_agentes;
    }


    /**
     * [collection_state_agent Función que transforma un Array en Collection]
     * @param  [array]      $query_estado_agentes [Datos del Consolidated Events]
     * @return [collection]                       [Datos en formato Colecction del reporte Consolidated Events]
     */
    protected function collection_state_agent($query_estado_agentes){
        $estateagentcollection                 = new Collector;
        foreach ($query_estado_agentes as $view) {
            $estateagentcollection->push([
                'agent'              => $view->agente,
                'acd'                => conversorSegundosHoras($view->acd,false),
                'break'              => conversorSegundosHoras($view->break,false),
                'sshh'               => conversorSegundosHoras($view->sshh,false),
                'refrigerio'         => conversorSegundosHoras($view->refrigerio,false),
                'feeedback'          => conversorSegundosHoras($view->feedback,false),
                'capacitacion'       => conversorSegundosHoras($view->capacitacion,false),
                'backoffice'         => conversorSegundosHoras($view->backoffice,false),
                'indbound'           => conversorSegundosHoras($view->inbound,false),
                'outbound'           => conversorSegundosHoras($view->outbound,false),
                'acw'                => conversorSegundosHoras($view->acw,false),
                'login'              => conversorSegundosHoras($view->login,false),
                'desconectado'       => conversorSegundosHoras($view->desconectado,false),
                'logueado'           => conversorSegundosHoras($view->logueado,false),
                'auxiliares'         => conversorSegundosHoras($view->auxiliar,false),
                'talk'               => conversorSegundosHoras($view->talk_time,false),
                'saliente'           => conversorSegundosHoras($view->saliente_hablado,false)
            ]);
        }
        return $estateagentcollection;
    }


    /**
     * [query_events Función en donde se encunetra el query que consulta los datos del Details Event Empresa]
     * @param  [array] $days [Fecha de la consulta]
     * @return [array]       [Retorna datos para el Details Events]
     */
    protected function query_events($days){

        if($days[0] == date('Y-m-d') && $days[1] == date('Y-m-d')){

            $detail_events   = $this->events_presents($days);

        }else if($days[0] != date('Y-m-d') && $days[1] != date('Y-m-d')){

            $detail_events   = $this->events_history($days);
        }else{

            $events_presents = $this->events_presents($days);
            $events_history  = $this->events_history($days);
            $detail_events   = array_merge($events_presents, $events_history);
            //dd($detail_events);
        }


        return $detail_events;
    }


    /**
     * [events_presents description]
     * @return [type] [description]
     */
    protected function events_presents ($days){
        $events_presents  = DetalleEventos::Select('user_id','evento_id','fecha_evento','observaciones')
                            ->with('evento','user')
                            ->filtro_days($days)
                            ->OrderBy(DB::raw('user_id'), 'asc')
                            ->OrderBy(DB::raw('DATE(fecha_evento)'), 'asc')
                            ->OrderBy(DB::raw('TIME(fecha_evento)'), 'asc')
                            ->get()
                            ->toArray();
        return $events_presents;
    } 

    protected function events_history($days){
        $events_history  = DetalleEventosHistory::Select('user_id','evento_id','fecha_evento','observaciones')
                            ->with('evento','user')
                            ->filtro_days($days)
                            ->OrderBy(DB::raw('user_id'), 'asc')
                            ->OrderBy(DB::raw('DATE(fecha_evento)'), 'asc')
                            ->OrderBy(DB::raw('TIME(fecha_evento)'), 'asc')
                            ->get()
                            ->toArray();
        return $events_history;
    }

    /**
     * [convertCollection Función que permite pasar de un Array a Collection]
     * @param  [array]      $array [Datos con la información de Event Details]
     * @return [colection]         [Collection con los datos de Event Details]
     */
    function convertCollection($array){

        $objCollection = New Collector();
        foreach ($array as $data) {
            $objCollection->push([
                'nombre_agente'     => $data['full_name_user'],
                'fecha'             => $this->MostrarSoloFecha($data['fecha_evento']),
                'hora'              => $this->MostrarSoloHora($data['fecha_evento']),
                'evento'            => $data['name_evento'],
                'accion'            => $data['accion'],
            ]);
        }

        return $objCollection;
    }


    /**
     * [builderview Función que prepara los datos para el envío de Detail Events]
     * @param  [array] $detail_events [Array con datos de la consulta a la base de datos]
     * @return [array]                [Array con los datos modificados para la vista]
     */
    protected function builderview ($detail_events){
        $array_detail = [];
        $posicion = 0;
        foreach($detail_events as $events){
            $array_detail[$posicion]['NOMBRE COMPLETO']   = $events['full_name_user'];
            $array_detail[$posicion]['FECHA']             = $this->MostrarSoloFecha($events['fecha_evento']);
            $array_detail[$posicion]['HORA']              = $this->MostrarSoloHora($events['fecha_evento']);
            $array_detail[$posicion]['NOMBRE DEL EVENTO'] = $events['name_evento'];
            $array_detail[$posicion]['REALIZADO POR']     = $events['accion'];
            $posicion++;
        }

        return $array_detail;
    }


    /**
     * [export_csv Function que retorna la ubicación de los datos a exportar en CSV]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en CSV]
     */
    protected function export_csv($days){
            $days                   = explode(' - ',$days);

            $builderview = $this->builderview(detailEvents($this->query_events($days)));
            $this->BuilderExport($builderview,'detail_events','csv','exports');


        $data = [
            'succes'    => true,
            'path'      => [
                'http://'.$_SERVER['HTTP_HOST'].'/exports/detail_events.csv'
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
        $days                   = explode(' - ',$days);
        Excel::create('detail_events', function($excel) use($days) {


            $excel->sheet('Detail Events', function($sheet) use($days) {
                $sheet->fromArray($this->builderview(detailEvents($this->query_events($days))));
            });


        })->store('xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/detail_events.xlsx']
        ];

        return $data;
    }
}
