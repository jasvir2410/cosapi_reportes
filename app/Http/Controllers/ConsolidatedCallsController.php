<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Queue_Log;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Excel;

class ConsolidatedCallsController extends CosapiController
{


    /**
     * [index Función que retorna vista y datos del reporte de Calls Consolidated]
     * @param  Request $request  [Retorna datos por POST]
     * @return [view]            [Retorna una vista o array para cargar en el reporte]
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            if ($request->evento){
                return $this->calls_consolidated($request->fecha_evento, $request->evento);
            }else{
                return view('elements/consolidated_calls/index');
            }
        }
    }


    /**
     * [export Función que llama a otra función para exportar en el formato que se desea]
     * @param  Request $request [Variables para la recepción de datos por POST]
     * @return [array]          [Retornar archivo de exportación]
     */
    public function export(Request $request){
        $export_contestated  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_contestated;
    }


    /**
     * [calls_consolidated Función para el proceso de consulta de datos]
     * @param  [date]   $fecha_evento [Fecha de consulta]
     * @param  [string] $evento       [Tipo de consulta: Skill, fecha, hora, agente]
     * @return [array]                [Datos a cargar en la tabla del reporte de Calls Consolidated]
     */
    protected function calls_consolidated($fecha_evento, $evento)
    {
                
        $calls_inbound              = $this->calls_inbound($fecha_evento, $evento);
        $consolidatedcollection     = $this->consolidatedcollection($calls_inbound);
        $CallsConsolidated          = $this->FormatDatatable($consolidatedcollection);        

        return $CallsConsolidated;

    }


    /**
     * [calls_inbound Función que retorna los datos segun el evento (Skill, Date, Hour, Agent)]
     * @param  [date]   $fecha_evento [Fecha de consulta]
     * @param  [string] $evento       [Tipo de consulta: Skill, fecha, hora, agente]
     * @return [array]                [Retorna datos segun el tipo Calls Consolidated que se requiera]
     */
    protected function calls_inbound($fecha_evento, $evento)
    {
        $groupby                            = '';
        $query_calls_inbound                = $this->query_calls_inbound($fecha_evento);

        switch($evento){
            case 'skills_group' :
                $call_group                 = $this->calls_queue($fecha_evento);
                $groupby                    = 'queue';
                break ;
            case 'agent_group'        :
                $call_group                 = $this->calls_agents($fecha_evento);
                $groupby                    = 'agent';
                break ;
            case 'day_group'          :
                $call_group                 = ArrayDays($fecha_evento);
                $groupby                    = 'fechamod';
                break ;
            case 'hour_group'         :
                $call_group                 = listHoursInterval();
                $groupby                    = 'hourmod';
                break ;

        }

        $CallsConsolidated              = $this->CallsConsolidated($query_calls_inbound,$groupby);
        $calls_inbound                  = $this->BuilderCallsConsolidated($CallsConsolidated,$call_group,$groupby);
        return $calls_inbound;        
    }


    /**
     * [query_calls_inbound Función que retorna los datos de la llamadas entrantes]
     * @param  [date] $days  [Fecha de consulta]
     * @return [array]       [Retorna datos de las llamadas entrantes en determinada fecha]
     */
    protected function query_calls_inbound ($fecha_evento)
    {
        $queues_proyect     = $this->queues_proyect();
        $days               = explode(' - ', $fecha_evento);
        $calls_inbound      = Queue_Log::select_fechamod()
                                        ->filtro_days($days)
                                        ->filtro_anexos()
                                        ->whereIn('queue', $queues_proyect)
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();                         
        return $calls_inbound;
    }


    /**
     * [calls_queue Función la cual permite obtener la lista de nombre de los Skills]
     * @param  [date] $days  [Fecha de consulta]
     * @return [Array]       [Retorna un Array con la lista de nombre de los Skills]
     */
    protected function calls_queue ($fecha_evento)
    {
        $queues_proyect = $this->queues_proyect();
        $days           = explode(' - ', $fecha_evento);
        $calls_queue    = Queue_Log::Select('queue')
                                    ->filtro_days($days)
                                    ->filtro_anexos()
                                    ->whereIn('queue', $queues_proyect)
                                    ->groupBy('queue')
                                    ->get()
                                    ->toArray();
                                           
        return $calls_queue;
    }

    /**
     * [calls_agents Función la cual permite obtener la lista de nombre de los Agentes]
     * @param  [date] $days  [Fecha de consulta]
     * @return [Array]       [Retorna un Array con la lista de nombre de los Agentes]
     */
    protected function calls_agents ($fecha_evento)
    {
        $queues_proyect = $this->queues_proyect();
        $days           = explode(' - ', $fecha_evento);
        $calls_agents   = Queue_Log::Select('agent')
                                    ->filtro_days($days)
                                    ->filtro_anexos()
                                    ->whereNotIn('agent', ['NONE'])
                                    ->whereIn('queue', $queues_proyect)
                                    ->groupBy('agent')
                                    ->get()
                                    ->toArray();
    
        return $calls_agents;
    }

    
    /**
     * [ListCallsConsolidated Función que permite obtener los datos del consolidado de llamadas]
     * @param  [Array]  $calls_consolidated [Array con toda la información de las llamadas en una determinada fecha]
     * @param  [String] $indice             [Nombre de la columna por las cual va a agrupar]
     * @return [Array]                      [Array con los datos del consolidados de llamdas]
     */
    protected function CallsConsolidated($calls_inbound,$groupby){

        $time_standar = array(10,15,20,30);
        $Consolidated = [];
        foreach ($calls_inbound as $calls) {
            
            
                if(isset($Consolidated[$calls[$groupby]][$calls['event']])){

                    $Consolidated[$calls[$groupby]][$calls['event']]=$Consolidated[$calls[$groupby]][$calls['event']]+1;

                    if(isset($Consolidated[$calls[$groupby]]['min_espera'])){

                            $Consolidated[$calls[$groupby]]['min_espera']=abs($calls['info1'])+$Consolidated[$calls[$groupby]]['min_espera'];

                    }else{

                        $Consolidated[$calls[$groupby]]['min_espera']=abs($calls['info1']);

                    }

                    if(isset($Consolidated[$calls[$groupby]]['duracion'])){

                        $Consolidated[$calls[$groupby]]['duracion']=abs($calls['info2'])+$Consolidated[$calls[$groupby]]['duracion'];

                    }else{

                        $Consolidated[$calls[$groupby]]['duracion']=abs($calls['info2']);

                    }


                    for($i=0;$i<count($time_standar);$i++){

                        if(abs($calls['info1'])<=$time_standar[$i]){

                            /*
                             * Contador de tiempo en cola < 10 y < 20
                            */
                            $Consolidated[$calls[$groupby]][$calls['event'].$time_standar[$i]]=$Consolidated[$calls[$groupby]][$calls['event'].$time_standar[$i]]+1;
                            
                        }

                    }

                }else{

                    /**
                     * Entra cuando encuentra un evento que aun no a sid contabilizado, para asì inicializar los contadores
                     */

                    $Consolidated[$calls[$groupby]]['name']=$calls[$groupby];

                    $Consolidated[$calls[$groupby]][$calls['event']]=1;


                    if(isset($Consolidated[$calls[$groupby]]['min_espera'])){

                        $Consolidated[$calls[$groupby]]['min_espera']=abs($calls['info1'])+$Consolidated[$calls[$groupby]]['min_espera'];

                    }else{
                        $Consolidated[$calls[$groupby]]['min_espera']=abs($calls['info1']);
                    }

                    if(isset($Consolidated[$calls[$groupby]]['duracion'])){

                        $Consolidated[$calls[$groupby]]['duracion']=abs($calls['info2'])+$Consolidated[$calls[$groupby]]['duracion'];

                    }else{
                        $Consolidated[$calls[$groupby]]['duracion']=abs($calls['info2']);
                    }
                    
                    for($i=0;$i<count($time_standar);$i++){

                        /*
                         * Contador de tiempo en cola < 10 y < 20
                         */

                        if(abs($calls['info1'])<=$time_standar[$i]){
                          
                            $Consolidated[$calls[$groupby]][$calls['event'].$time_standar[$i]]=1;
                            
                        }else{

                            $Consolidated[$calls[$groupby]][$calls['event'].$time_standar[$i]]=0;

                        }

                    }

                }
            
        
        }

        return $Consolidated ;

    }


    /**
     * [BuilderCallsConsolidated Función para contruir un array para cargar los datos en la vista]
     * @param  [Array]  $ListarLlamadasConsolidadas [Conjunto de Datos de donde extraer la informaciòn requerida para armar la tabla de Consolidados de llamadas]
     * @param  [Array]  $agente                     [Dato de los usuarios : nombre, apellidos, etc]
     * @param  [Array]  $indice                     [Dato que define la columan que servira para agrupar]
     * @param  [Array]  $calls_queue                [Lista de nombres de Skill]
     */
protected function BuilderCallsConsolidated($CallsConsolidated ,$call_group,$groupby,$list_user=''){

        $time_standar       = array(10,20);
        $posicion           = 1;
        $Consolidateds      = [];
        for($j=0;$j<count($call_group);$j++){

            $complete_caller        =   0;
            $complete_agent         =   0;
            $transfer               =   0;
            $name                   =   '-';
            $completecaller_10      =   0;
            $completeagent_10       =   0;
            $completecaller_15      =   0;
            $completeagent_15       =   0;
            $completecaller_20      =   0;
            $completecaller_30      =   0;
            $completeagent_20       =   0;
            $completeagent_30       =   0;
            $min_espera             =   0;
            $duracion               =   0;
            $abandon                =   0;
            $abandon_10             =   0;
            $abandon_15             =   0;
            $abandon_20             =   0;
            $abandon_30             =   0;
            $transfer_10            =   0;
            $transfer_15            =   0;
            $transfer_20            =   0;
            $transfer_30            =   0;


            if(isset($CallsConsolidated[$call_group[$j][$groupby]])){
                 
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER'])){         $transfer            =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER'])){   $complete_caller     =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT'])){    $complete_agent      =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON'])){          $abandon             =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER10'])){ $completecaller_10   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER15'])){ $completecaller_15   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER20'])){ $completecaller_20   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER30'])){ $completecaller_30   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER30']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT10'])){  $completeagent_10    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT15'])){  $completeagent_15    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT20'])){  $completeagent_20    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT30'])){  $completeagent_30    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT30']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON10'])){        $abandon_10          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON15'])){        $abandon_15          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON20'])){        $abandon_20          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON30'])){        $abandon_30          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON30']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER10'])){       $transfer_10         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER15'])){       $transfer_15         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER20'])){       $transfer_20         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER30'])){       $transfer_30         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER30']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['min_espera'])){       $min_espera          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['min_espera']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['duracion'])){         $duracion            =   intval($CallsConsolidated[$call_group[$j][$groupby]]['duracion']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['name'])){             $name                =   $CallsConsolidated[$call_group[$j][$groupby]]['name']; }

                
                if($groupby == 'hourmod'){
                    $name = $call_group[$j]['name'];
                }

                if($groupby == 'agent'){
                    $name =   ExtraerAgente($name,$list_user);
                }

                $Consolidateds[$posicion]['Name']                                                           =   $name; 
                $Consolidateds[$posicion]['Received']                                                       =   $complete_caller + $complete_agent + $abandon + $transfer;
                $Consolidateds[$posicion]['Answered']                                                       =   $complete_caller + $complete_agent;
                $Consolidateds[$posicion]['Abandoned']                                                      =   $abandon;
                $Consolidateds[$posicion]['Transferred']                                                    =   $transfer;
                $Consolidateds[$posicion]['Attended']                                                       =   $complete_caller + $complete_agent + $transfer;
                $Consolidateds[$posicion]['Answ 10s']                                                       =   $completecaller_10 + $completeagent_10 + $transfer_10;
                $Consolidateds[$posicion]['Answ 15s']                                                       =   $completecaller_15 + $completeagent_15 + $transfer_15;
                $Consolidateds[$posicion]['Answ 20s']                                                       =   $completecaller_20 + $completeagent_20 + $transfer_20;
                $Consolidateds[$posicion]['Answ 30s']                                                       =   $completecaller_30 + $completeagent_30 + $transfer_30;
                $Consolidateds[$posicion]['Aband 10s']                                                      =   $abandon_10 ;
                $Consolidateds[$posicion]['Aband 15s']                                                      =   $abandon_15 ;
                $Consolidateds[$posicion]['Aband 20s']                                                      =   $abandon_20 ;
                $Consolidateds[$posicion]['Aband 30s']                                                      =   $abandon_30 ;
                $Consolidateds[$posicion]['Wait Time']                                                      =   conversorSegundosHoras($min_espera,false) ;
                $Consolidateds[$posicion]['Talk Time']                                                      =   conversorSegundosHoras($duracion,false) ;


                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Answ']   =   convertDecimales(($Consolidateds[$posicion]['Answered']/ $Consolidateds[$posicion]['Received'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Answ']   = convertDecimales(0,2);
                }
                
                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Unansw'] =   convertDecimales((($Consolidateds[$posicion]['Abandoned'] )/ $Consolidateds[$posicion]['Received'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Unansw']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Avg Wait']   =   conversorSegundosHoras(intval($min_espera/$Consolidateds[$posicion]['Received']),false);
                }else{
                    $Consolidateds[$posicion]['Avg Wait']   = conversorSegundosHoras(0,false);
                }

                if($Consolidateds[$posicion]['Attended']!=0){
                    $Consolidateds[$posicion]['Avg Talk']   =   conversorSegundosHoras(intval($duracion/$Consolidateds[$posicion]['Attended']),false);
                }else{
                    $Consolidateds[$posicion]['Avg Talk']   = conversorSegundosHoras(0,false);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ro10']   =   convertDecimales((($Consolidateds[$posicion]['Answ 10s']+$Consolidateds[$posicion]['Aband 10s'])/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ro10']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ro15']   =   convertDecimales((($Consolidateds[$posicion]['Answ 15s']+$Consolidateds[$posicion]['Aband 15s'])/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ro15']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ro20']   =   convertDecimales((($Consolidateds[$posicion]['Answ 20s']+$Consolidateds[$posicion]['Aband 20s'])/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ro20']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ro30']   =   convertDecimales((($Consolidateds[$posicion]['Answ 30s']+$Consolidateds[$posicion]['Aband 30s'])/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ro30']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ns10']   =   convertDecimales(($Consolidateds[$posicion]['Answ 10s']/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ns10']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ns15']   =   convertDecimales(($Consolidateds[$posicion]['Answ 15s']/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ns15']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ns20']   =   convertDecimales(($Consolidateds[$posicion]['Answ 20s']/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ns20']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Received']!=0){
                    $Consolidateds[$posicion]['Ns30']   =   convertDecimales(($Consolidateds[$posicion]['Answ 30s']/$Consolidateds[$posicion]['Received'])*100,2);
                }else{
                    $Consolidateds[$posicion]['Ns30']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Attended'] !=0){
                    $Consolidateds[$posicion]['Avh2 10']   =   convertDecimales(($Consolidateds[$posicion]['Answ 10s']/$Consolidateds[$posicion]['Attended'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Avh2 10']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['Attended'] !=0){
                    $Consolidateds[$posicion]['Avh2 15']   =   convertDecimales(($Consolidateds[$posicion]['Answ 15s']/$Consolidateds[$posicion]['Attended'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Avh2 15']   = convertDecimales(0,2);
                } 

                if($Consolidateds[$posicion]['Attended'] !=0){
                    $Consolidateds[$posicion]['Avh2 20']   =   convertDecimales(($Consolidateds[$posicion]['Answ 20s']/$Consolidateds[$posicion]['Attended'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Avh2 20']   = convertDecimales(0,2);
                } 

                if($Consolidateds[$posicion]['Attended'] !=0){
                    $Consolidateds[$posicion]['Avh2 30']   =   convertDecimales(($Consolidateds[$posicion]['Answ 30s']/$Consolidateds[$posicion]['Attended'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['Avh2 30']   = convertDecimales(0,2);
                } 


                $posicion                               =   $posicion + 1;
                
            }else{
                $name = $call_group[$j][$groupby];
                if($groupby == 'hourmod'){
                    $name = $call_group[$j]['name'];
                }

                $Consolidateds[$posicion]['Name']           =   $name;
                $Consolidateds[$posicion]['Received']       =   0 ;
                $Consolidateds[$posicion]['Answered']       =   0 ;
                $Consolidateds[$posicion]['Abandoned']      =   0 ;
                $Consolidateds[$posicion]['Transferred']    =   0 ;
                $Consolidateds[$posicion]['Attended']       =   0 ;
                $Consolidateds[$posicion]['Answ 10s']       =   0 ;
                $Consolidateds[$posicion]['Answ 15s']       =   0 ;
                $Consolidateds[$posicion]['Answ 20s']       =   0 ;
                $Consolidateds[$posicion]['Answ 30s']       =   0 ;
                $Consolidateds[$posicion]['Aband 10s']      =   0 ;
                $Consolidateds[$posicion]['Aband 15s']      =   0 ;
                $Consolidateds[$posicion]['Aband 20s']      =   0 ;
                $Consolidateds[$posicion]['Aband 30s']      =   0 ;
                $Consolidateds[$posicion]['Wait Time']      =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['Talk Time']      =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['Answ']           =   0 ;
                $Consolidateds[$posicion]['Unansw']         =   0 ;
                $Consolidateds[$posicion]['Avg Wait']       =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['Avg Talk']       =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['Ro10']           =   0 ;
                $Consolidateds[$posicion]['Ro15']           =   0 ;
                $Consolidateds[$posicion]['Ro20']           =   0 ;
                $Consolidateds[$posicion]['Ro30']           =   0 ;
                $Consolidateds[$posicion]['Ns10']           =   0 ;
                $Consolidateds[$posicion]['Ns15']           =   0 ;
                $Consolidateds[$posicion]['Ns20']           =   0 ;
                $Consolidateds[$posicion]['Ns30']           =   0 ;
                $Consolidateds[$posicion]['Avh2 10']        =   0 ;
                $Consolidateds[$posicion]['Avh2 15']        =   0 ;
                $Consolidateds[$posicion]['Avh2 20']        =   0 ;
                $Consolidateds[$posicion]['Avh2 30']        =   0 ;
                $posicion                                   =   $posicion + 1;
            }
        }


        return $Consolidateds;

    }


    /**
     * [consolidatedcollection Función que tranforma un array en collection]
     * @param  [array]      $BuilderCallsConsolidated [Datos para transforma los datos de array a consolidated]
     * @return [collection]                           [Datos de llamadas apra el reporte de agentes online]
     */
    protected function consolidatedcollection($BuilderCallsConsolidated){

        $consolidatedcollection                      = new Collector;
        foreach ($BuilderCallsConsolidated as $CallsConsolidated) {
            $consolidatedcollection->push([
                            'Name'              => $CallsConsolidated['Name'],
                            'Received'          => $CallsConsolidated['Received'],
                            'Answered'          => $CallsConsolidated['Answered'],
                            'Abandoned'         => $CallsConsolidated['Abandoned'],
                            'Transferred'       => $CallsConsolidated['Transferred'],
                            'Attended'          => $CallsConsolidated['Attended'],
                            'Answ 10s'          => $CallsConsolidated['Answ 10s'],
                            'Answ 15s'          => $CallsConsolidated['Answ 15s'],
                            'Answ 20s'          => $CallsConsolidated['Answ 20s'],
                            'Answ 30s'          => $CallsConsolidated['Answ 30s'],
                            'Aband 10s'         => $CallsConsolidated['Aband 10s'],
                            'Aband 15s'         => $CallsConsolidated['Aband 15s'],
                            'Aband 20s'         => $CallsConsolidated['Aband 20s'],
                            'Aband 30s'         => $CallsConsolidated['Aband 30s'],
                            'Ro10'              => $CallsConsolidated['Ro10'],
                            'Ro15'              => $CallsConsolidated['Ro15'],
                            'Ro20'              => $CallsConsolidated['Ro20'],
                            'Ro30'              => $CallsConsolidated['Ro30'],
                            'Wait Time'         => $CallsConsolidated['Wait Time'],
                            'Talk Time'         => $CallsConsolidated['Talk Time'],
                            'Avg Wait'          => $CallsConsolidated['Avg Wait'],
                            'Avg Talk'          => $CallsConsolidated['Avg Talk'],
                            'Answ'              => $CallsConsolidated['Answ'],
                            'Unansw'            => $CallsConsolidated['Unansw'],
                            'Ns10'              => $CallsConsolidated['Ns10'],
                            'Ns15'              => $CallsConsolidated['Ns15'],
                            'Ns20'              => $CallsConsolidated['Ns20'],
                            'Ns30'              => $CallsConsolidated['Ns30'],
                            'Avh2 10'           => $CallsConsolidated['Avh2 10'],
                            'Avh2 15'           => $CallsConsolidated['Avh2 15'],
                            'Avh2 20'           => $CallsConsolidated['Avh2 20'],  
                            'Avh2 30'           => $CallsConsolidated['Avh2 30'],  
                        ]);
        }

        return $consolidatedcollection;
    
    }


    /**
     * [export_csv Función que permite exportar la información el formato CSV]
     * @param  [date]  $days [Días de consulta]
     * @return [array]       [Array con datos de las rutas donde estas los CSV generados]
     */
    protected function export_csv($days){

        $events = ['skills_group','agent_group','day_group','hour_group'];

        for($i=0;$i<count($events);$i++){
            $builderview = $this->calls_inbound($days,$events[$i]);
            $this->BuilderExport($builderview,$events[$i],'csv','exports');
        }
    
        $data = [
            'succes'    => true,
            'path'      => [
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/skills_group.csv',
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/agent_group.csv',
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/day_group.csv',
                            'http://'.$_SERVER['HTTP_HOST'].'/exports/hour_group.csv'
                            ]
        ];

        return $data;
    }



    /**
     * [export_excel Función que permite exportar la información el formato Excel]
     * @param  [date]  $days [Días de consulta]
     * @return [array]       [Array con datos de las rutas donde estas los Excel generados]
     */
    protected function export_excel($days){
        Excel::create('consolidated_calls', function($excel) use($days) {

            $excel->sheet('Skills', function($sheet) use($days) {
                $sheet->fromArray($this->calls_inbound($days,'skills_group'));
            });

            $excel->sheet('Agentes', function($sheet) use($days) {
                $sheet->fromArray($this->calls_inbound($days,'agent_group'));
            });

            $excel->sheet('Dias', function($sheet) use($days) {
                $sheet->fromArray($this->calls_inbound($days,'day_group'));
            });

            $excel->sheet('Horas', function($sheet) use($days) {
                $sheet->fromArray($this->calls_inbound($days,'hour_group'));
            });


        })->store('xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/consolidated_calls.xlsx']
        ];

        return $data;
    }

}
