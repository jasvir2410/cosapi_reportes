<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Http\Requests;
use Illuminate\Http\Request;
use Cosapi\Models\Queue_Empresa;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Collector\Collector;

use DB;
use Excel;

class ConsolidatedCallsController extends CosapiController
{

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

    public function export(Request $request){
        $export_contestated  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_contestated;
    }

    protected function calls_consolidated($fecha_evento, $evento)
    {
                
        $calls_inbound              = $this->calls_inbound($fecha_evento, $evento);
        $consolidatedcollection     = $this->consolidatedcollection($calls_inbound);
        $CallsConsolidated          = $this->FormatDatatable($consolidatedcollection);        

        return $CallsConsolidated;

    }


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
     * [query_calls_inbound description]
     * @param  [type] $days [description]
     * @return [type]       [description]
     */
    protected function query_calls_inbound ($fecha_evento)
    {
        $days               = explode(' - ', $fecha_evento);
        $calls_inbound      = Queue_empresa::select_fechamod()
                                        ->filtro_days($days)
                                        ->whereNotIn('queue', ['NONE','HD_CE_BackOffice','Pruebas'])
                                        ->OrderBy('id')
                                        ->get()
                                        ->toArray();                         
        return $calls_inbound;
    }


    /**
     * [calls_queue Función la cual permite obtener la lista de nombre de los Skills]
     * @return [Array] [Retorna un Array con la lista de nombre de los Skills]
     */
    protected function calls_queue ($fecha_evento)
    {
        $days           = explode(' - ', $fecha_evento);
        $calls_queue    = Queue_empresa::Select('queue')
                                    ->filtro_days($days)
                                    ->whereNotIn('queue', ['NONE','HD_CE_BackOffice','Pruebas'])
                                    ->groupBy('queue')
                                    ->get()
                                    ->toArray();
                                           
        return $calls_queue;
    }

    
    protected function calls_agents ($fecha_evento)
    {
        $days           = explode(' - ', $fecha_evento);
        $calls_agents   = Queue_empresa::Select('agent')
                                    ->filtro_days($days)
                                    ->whereNotIn('agent', ['NONE'])
                                    ->whereNotIn('queue', ['Pruebas','NONE','HD_CE_BackOffice'])
                                    ->groupBy('agent')
                                    ->get()
                                    ->toArray();
    
        return $calls_agents;
    }

    
    /**
     * [ListCallsConsolidated Función que permite obtener los datos del consolidado de llamadas]
     * @param  [Array] $calls_consolidated [Array con toda la información de las llamadas en una determinada fecha]
     * @param  [String] $indice            [Nombre de la columna por las cual va a agrupar]
     * @return [Array]                     [Array con los datos del consolidados de llamdas]
     */
    protected function CallsConsolidated($calls_inbound,$groupby){

        $time_standar = array(10,15,20);
        
        foreach ($calls_inbound as $calls) {
            
            
                if(isset($Consolidated[$calls[$groupby]][$calls['event']])){

                    $Consolidated[$calls[$groupby]][$calls['event']]=$Consolidated[$calls[$groupby]][$calls['event']]+1;

                    if(isset($Consolidated[$calls[$groupby]]['min_espera'])){

                            $Consolidated[$calls[$groupby]]['min_espera']=$calls['info1']+$Consolidated[$calls[$groupby]]['min_espera'];

                    }else{

                        $Consolidated[$calls[$groupby]]['min_espera']=$calls['info1'];

                    }

                    if(isset($Consolidated[$calls[$groupby]]['duracion'])){

                        $Consolidated[$calls[$groupby]]['duracion']=$calls['info2']+$Consolidated[$calls[$groupby]]['duracion'];

                    }else{

                        $Consolidated[$calls[$groupby]]['duracion']=$calls['info2'];

                    }


                    for($i=0;$i<count($time_standar);$i++){

                        if($calls['info1']<=$time_standar[$i]){

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

                        $Consolidated[$calls[$groupby]]['min_espera']=$calls['info1']+$Consolidated[$calls[$groupby]]['min_espera'];

                    }else{
                        $Consolidated[$calls[$groupby]]['min_espera']=$calls['info1'];
                    }

                    if(isset($Consolidated[$calls[$groupby]]['duracion'])){

                        $Consolidated[$calls[$groupby]]['duracion']=$calls['info2']+$Consolidated[$calls[$groupby]]['duracion'];

                    }else{
                        $Consolidated[$calls[$groupby]]['duracion']=$calls['info2'];
                    }
                    
                    for($i=0;$i<count($time_standar);$i++){

                        /*
                         * Contador de tiempo en cola < 10 y < 20
                         */

                        if($calls['info1']<=$time_standar[$i]){
                          
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
            $completeagent_20       =   0;
            $min_espera             =   0;
            $duracion               =   0;
            $abandon                =   0;
            $abandon_10             =   0;
            $abandon_15             =   0;
            $abandon_20             =   0;
            $transfer_10            =   0;
            $transfer_15            =   0;
            $transfer_20            =   0;


            if(isset($CallsConsolidated[$call_group[$j][$groupby]])){
                 
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER'])){         $transfer            =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER'])){   $complete_caller     =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT'])){    $complete_agent      =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON'])){          $abandon             =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER10'])){ $completecaller_10   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER15'])){ $completecaller_15   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER20'])){ $completecaller_20   =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETECALLER20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT10'])){  $completeagent_10    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT15'])){  $completeagent_15    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT20'])){  $completeagent_20    =   intval($CallsConsolidated[$call_group[$j][$groupby]]['COMPLETEAGENT20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON10'])){        $abandon_10          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON15'])){        $abandon_15          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON20'])){        $abandon_20          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['ABANDON20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER10'])){       $transfer_10         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER10']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER15'])){       $transfer_15         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER15']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER20'])){       $transfer_20         =   intval($CallsConsolidated[$call_group[$j][$groupby]]['TRANSFER20']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['min_espera'])){       $min_espera          =   intval($CallsConsolidated[$call_group[$j][$groupby]]['min_espera']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['duracion'])){         $duracion            =   intval($CallsConsolidated[$call_group[$j][$groupby]]['duracion']); }
                if(isset($CallsConsolidated[$call_group[$j][$groupby]]['name'])){             $name                =   $CallsConsolidated[$call_group[$j][$groupby]]['name']; }

                
                if($groupby == 'hourmod'){
                    $name = $call_group[$j]['name'];
                }

                if($groupby == 'agent'){
                    $name =   ExtraerAgente($name,$list_user);
                }

                $Consolidateds[$posicion]['name']                                                                  =   $name; 
                $Consolidateds[$posicion]['recibidas']                                                             =   $complete_caller + $complete_agent + $abandon + $transfer;
                $Consolidateds[$posicion]['atendidas']                                                             =   $complete_caller + $complete_agent;
                $Consolidateds[$posicion]['abandonados']                                                           =   $abandon;
                $Consolidateds[$posicion]['transferencias']                                                        =   $transfer;
                $Consolidateds[$posicion]['constestadas']                                                          =   $complete_caller + $complete_agent + $transfer;
                $Consolidateds[$posicion]['constestadas_10']                                                       =   $completecaller_10 + $completeagent_10 + $transfer_10;
                $Consolidateds[$posicion]['constestadas_15']                                                       =   $completecaller_15 + $completeagent_15 + $transfer_15;
                $Consolidateds[$posicion]['constestadas_20']                                                       =   $completecaller_20 + $completeagent_20 + $transfer_20;
                $Consolidateds[$posicion]['abandonadas_10']                                                        =   $abandon_10 ;
                $Consolidateds[$posicion]['abandonadas_15']                                                        =   $abandon_15 ;
                $Consolidateds[$posicion]['abandonadas_20']                                                        =   $abandon_20 ;
                $Consolidateds[$posicion]['min_espera']                                                            =   conversorSegundosHoras($min_espera,false) ;
                $Consolidateds[$posicion]['duracion']                                                              =   conversorSegundosHoras($duracion,false) ;

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['answ']   =   convertDecimales(($Consolidateds[$posicion]['atendidas']/ $Consolidateds[$posicion]['recibidas'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['answ']   = convertDecimales(0,2);
                }
                
                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['unansw'] =   convertDecimales((($Consolidateds[$posicion]['abandonados'] )/ $Consolidateds[$posicion]['recibidas'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['unansw']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['avgw']   =   conversorSegundosHoras(intval($min_espera/$Consolidateds[$posicion]['recibidas']),false);
                }else{
                    $Consolidateds[$posicion]['avgw']   = conversorSegundosHoras(0,false);
                }

                if($Consolidateds[$posicion]['constestadas']!=0){
                    $Consolidateds[$posicion]['avgt']   =   conversorSegundosHoras(intval($duracion/$Consolidateds[$posicion]['constestadas']),false);
                }else{
                    $Consolidateds[$posicion]['avgt']   = conversorSegundosHoras(0,false);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ro10']   =   convertDecimales((($Consolidateds[$posicion]['constestadas_10']+$Consolidateds[$posicion]['abandonadas_10'])/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ro10']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ro15']   =   convertDecimales((($Consolidateds[$posicion]['constestadas_15']+$Consolidateds[$posicion]['abandonadas_15'])/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ro15']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ro20']   =   convertDecimales((($Consolidateds[$posicion]['constestadas_20']+$Consolidateds[$posicion]['abandonadas_20'])/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ro20']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ns10']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_10']/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ns10']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ns15']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_15']/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ns15']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['recibidas']!=0){
                    $Consolidateds[$posicion]['ns20']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_20']/$Consolidateds[$posicion]['recibidas'])*100,2);
                }else{
                    $Consolidateds[$posicion]['ns20']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['constestadas'] !=0){
                    $Consolidateds[$posicion]['avh210']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_10']/$Consolidateds[$posicion]['constestadas'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['avh210']   = convertDecimales(0,2);
                }

                if($Consolidateds[$posicion]['constestadas'] !=0){
                    $Consolidateds[$posicion]['avh215']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_15']/$Consolidateds[$posicion]['constestadas'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['avh215']   = convertDecimales(0,2);
                } 

                if($Consolidateds[$posicion]['constestadas'] !=0){
                    $Consolidateds[$posicion]['avh220']   =   convertDecimales(($Consolidateds[$posicion]['constestadas_20']/$Consolidateds[$posicion]['constestadas'] )*100,2);
                }else{
                    $Consolidateds[$posicion]['avh220']   = convertDecimales(0,2);
                } 


                $posicion                               =   $posicion + 1;
                
            }else{
                $name = $call_group[$j][$groupby];
                if($groupby == 'hourmod'){
                    $name = $call_group[$j]['name'];
                }

                $Consolidateds[$posicion]['name']                                           =   $name;
                $Consolidateds[$posicion]['recibidas']                                      =   0 ;
                $Consolidateds[$posicion]['atendidas']                                      =   0 ;
                $Consolidateds[$posicion]['abandonados']                                    =   0 ;
                $Consolidateds[$posicion]['transferencias']                                 =   0 ;
                $Consolidateds[$posicion]['constestadas']                                   =   0 ;
                $Consolidateds[$posicion]['constestadas_10']                                =   0 ;
                $Consolidateds[$posicion]['constestadas_15']                                =   0 ;
                $Consolidateds[$posicion]['constestadas_20']                                =   0 ;
                $Consolidateds[$posicion]['abandonadas_10']                                 =   0 ;
                $Consolidateds[$posicion]['abandonadas_15']                                 =   0 ;
                $Consolidateds[$posicion]['abandonadas_20']                                 =   0 ;
                $Consolidateds[$posicion]['min_espera']                                     =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['duracion']                                       =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['answ']                                           =   0 ;
                $Consolidateds[$posicion]['unansw']                                         =   0 ;
                $Consolidateds[$posicion]['avgw']                                           =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['avgt']                                           =   conversorSegundosHoras(0,false) ;
                $Consolidateds[$posicion]['ro10']                                           =   0 ;
                $Consolidateds[$posicion]['ro15']                                           =   0 ;
                $Consolidateds[$posicion]['ro20']                                           =   0 ;
                $Consolidateds[$posicion]['ns10']                                           =   0 ;
                $Consolidateds[$posicion]['ns15']                                           =   0 ;
                $Consolidateds[$posicion]['ns20']                                           =   0 ;
                $Consolidateds[$posicion]['avh210']                                         =   0 ;
                $Consolidateds[$posicion]['avh215']                                         =   0 ;
                $Consolidateds[$posicion]['avh220']                                         =   0 ;
                $posicion                               =   $posicion + 1;
            }
        }

        return $Consolidateds;

    }


    protected function consolidatedcollection($BuilderCallsConsolidated){

        $consolidatedcollection                      = new Collector;
        foreach ($BuilderCallsConsolidated as $CallsConsolidated) {
            $consolidatedcollection->push([
                            'name'              => $CallsConsolidated['name'],
                            'recibidas'         => $CallsConsolidated['recibidas'],
                            'atendidas'         => $CallsConsolidated['atendidas'],
                            'abandonados'       => $CallsConsolidated['abandonados'],
                            'transferencias'    => $CallsConsolidated['transferencias'],
                            'constestadas'      => $CallsConsolidated['constestadas'],
                            'constestadas_10'   => $CallsConsolidated['constestadas_10'],
                            'constestadas_15'   => $CallsConsolidated['constestadas_15'],
                            'constestadas_20'   => $CallsConsolidated['constestadas_20'],
                            'abandonadas_10'    => $CallsConsolidated['abandonadas_10'],
                            'abandonadas_15'    => $CallsConsolidated['abandonadas_15'],
                            'abandonadas_20'    => $CallsConsolidated['abandonadas_20'],
                            'ro10'              => $CallsConsolidated['ro10'],
                            'ro15'              => $CallsConsolidated['ro15'],
                            'ro20'              => $CallsConsolidated['ro20'],
                            'min_espera'        => $CallsConsolidated['min_espera'],
                            'duracion'          => $CallsConsolidated['duracion'],
                            'avgw'              => $CallsConsolidated['avgw'],
                            'avgt'              => $CallsConsolidated['avgt'],
                            'answ'              => $CallsConsolidated['answ'],
                            'unansw'            => $CallsConsolidated['unansw'],
                            'ns10'              => $CallsConsolidated['ns10'],
                            'ns15'              => $CallsConsolidated['ns15'],
                            'ns20'              => $CallsConsolidated['ns20'],
                            'avh210'            => $CallsConsolidated['avh210'],
                            'avh215'            => $CallsConsolidated['avh215'],
                            'avh220'            => $CallsConsolidated['avh220'],  
                        ]);
        }

        return $consolidatedcollection;
    
    }


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
