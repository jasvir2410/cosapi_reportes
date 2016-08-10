<?php

namespace Cosapi\Http\Controllers;

use Cosapi\Collector\Collector;
use Illuminate\Http\Request;

use Cosapi\Models\User;
use Cosapi\Models\Eventos;
use Cosapi\Models\AsteriskCDR;
use Cosapi\Models\DetalleEventos;
use Cosapi\Http\Controllers\CosapiController;

use Cosapi\Http\Requests;
use Illuminate\Support\Facades\DB;
use Cosapi\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use Cosapi\Http\Controllers\IncomingCallsController;


class EventsAgentController extends CosapiController
{
    public function index(){
        return view('elements/events_detail/detail_events');
    }

    public function events_consolidated(Request $request){

        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->list_state_agents($request->fecha_evento);
            }else{
                return view('elements/events_consolidated/index');
            }
        }
    }


    protected function list_state_agents($fecha_evento){

        $query_estado_agentes   = $this->query_estado_agentes($fecha_evento);
        $estateagentcollection  = $this->collection_state_agent($query_estado_agentes);
        $list_state_agents      = $this->FormatDatatable($estateagentcollection);
        return $list_state_agents;

    }


    protected function query_estado_agentes ($fecha_evento)
    {
        list($fecha_inicial,$fecha_final) = explode(' - ', $fecha_evento);

        $AsteriskCDR        =  AsteriskCDR::Select('accountcode as username' , DB::raw('SUM(billsec) as TiempoLlamada') )
                                    ->whereBetween(DB::raw("DATE(calldate)"),[$fecha_inicial, $fecha_final])
                                    ->where('disposition','=','ANSWERED')
                                    ->WHERE('lastapp','=','Dial')
                                    ->groupBy('accountcode')
                                    ->get()->toArray();

        $Usuarios           = User::get()->toArray();
        $Eventos            = Eventos::Select()->where('estado_id','=','1')->get();
        $Eventos_Auxiliares = Eventos::Select('id')->where('eventos_auxiliares','=','1' )->get();
        $Cosapi_Eventos     = Eventos::Select('id')->where('cosapi_eventos','=','1' )->get();
        $Claro_Eventos      = Eventos::Select('id')->where('claro_eventos','=','1' )->get();

        $DetalleEventos     = DetalleEventos::Select()
                                    ->with('evento')
                                    ->whereBetween(DB::raw("DATE(fecha_evento)"),[$fecha_inicial, $fecha_final])
                                    ->OrderBy('user_id', 'asc')
                                    ->OrderBy('fecha_evento', 'asc')
                                    ->get()->toArray();
        
        $BuilderViewEstateAgent = $this->BuilderViewEstateAgent($Usuarios,$Eventos,$DetalleEventos,$AsteriskCDR,false,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$fecha_evento);
        return $BuilderViewEstateAgent;
    }

    protected function BuilderViewEstateAgent($usuarios, $eventos,$detalleEventos,$AsteriskCDR,$formato,$eventos_auxiliares,$cosapi_eventos,$claro_eventos,$fecha_evento){
        $BuilderViewEstateAgent = [];
        $posicion = 0;
        $resumenEventos = calcularTiempoEntreEstados($usuarios,$eventos,$detalleEventos);
        
        foreach ($resumenEventos as $key => $resumenEvento){
            $totalTiemposTrabajadosCosapi = 0;
            $totalTiemposTrabajadosClaro  = 0;
            $totalTiemposAuxiliares       = 0;
            $tiempoTotalLogueado          = 0;
            $totalACD                     = 0;
            $tiempoLlamadaEntrante        = 0;
            $tiempoLlamadaSaliente        = 0;
            $totaltiempoatendida          = 0;
            foreach ($resumenEvento as $Evento){
                $BuilderViewEstateAgent[$posicion]['agent']                 = $usuarios[$key-1]['primer_nombre'].' '.$usuarios[$key-1]['apellido_paterno']; 
                $BuilderViewEstateAgent[$posicion][$Evento['evento_id']]    = conversorSegundosHoras($Evento['tiempo'], false);                                
                $tiempoTotalLogueado                                        = $tiempoTotalLogueado + $Evento['tiempo'];
                $evento_id                                                  = $Evento['evento_id'];
                
                //  Calculo para Tiempos Auxiliares-->
                foreach ($eventos_auxiliares as $eventos_auxiliar){

                    $id_evento_auxiliar = $eventos_auxiliar['id'];

                    if ( $evento_id == $id_evento_auxiliar  ){
                        $totalTiemposAuxiliares += $Evento['tiempo'];
                    }
                }    

                 
               
                // Calculo para totalTiemposTrabajados para cosapi-->
                foreach ($cosapi_eventos as $cosapi_evento){

                    $id_cosapi_evento = $cosapi_evento['id'];

                    if ( $evento_id == $id_cosapi_evento  ){
                        $totalTiemposTrabajadosCosapi += $Evento['tiempo'];
                    }
                    

                }

                // Calculo para totalTiemposTrabajados para Claro-->
                foreach ($claro_eventos as $claro_evento){

                    $id_claro_evento = $claro_evento['id'];
                
                    if ($evento_id == $id_claro_evento){
                        $totalTiemposTrabajadosClaro += $Evento['tiempo'];
                    }
                    
                }   
                   

                // Calculo para tiempo ACD -->
                if ( $evento_id == 2 ){
                    $totalACD = $Evento['tiempo'];
                }
               
                // Calculo para tiempoLlamadaEntrante-->
                if ( $evento_id == 9 ){
                    $tiempoLlamadaEntrante = $Evento['tiempo'];
                }
                
                                     
            }
            $comingcaller = new IncomingCallsController();
            $result_tiempoatendida  = $comingcaller->query_calls($fecha_evento,'calls_completed',$usuarios[$key-1]['username']);
            foreach ($result_tiempoatendida as $tiempoatendida) {
                $totaltiempoatendida+=$tiempoatendida['info2'];
            }
            $tiempoLlamadaSaliente                              = calcularTiempoLlamadaSaliente($usuarios[$key-1]['username'], $AsteriskCDR);
            $tiempoTotalHablado                                 = $tiempoLlamadaEntrante + $tiempoLlamadaSaliente;

            $BuilderViewEstateAgent[$posicion]['logueado']      = conversorSegundosHoras($tiempoTotalLogueado, false) ;
            $BuilderViewEstateAgent[$posicion]['auxiliares']    = conversorSegundosHoras($totalTiemposAuxiliares, false) ;
            $BuilderViewEstateAgent[$posicion]['talk']          = conversorSegundosHoras($tiempoTotalHablado, false) ;
            $BuilderViewEstateAgent[$posicion]['saliente']      = conversorSegundosHoras($tiempoLlamadaSaliente, false) ;

            if($tiempoTotalLogueado != $totalTiemposAuxiliares){
                $BuilderViewEstateAgent[$posicion]['ocupation_claro']  = round(($totalTiemposTrabajadosClaro/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
                $BuilderViewEstateAgent[$posicion]['ocupation_cosapi'] = round(($totalTiemposTrabajadosCosapi/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
            }else{
                $BuilderViewEstateAgent[$posicion]['ocupation_claro']  = 0;
                $BuilderViewEstateAgent[$posicion]['ocupation_cosapi'] = 0; 
            }           
            
            $BuilderViewEstateAgent[$posicion]['time_attended']        = conversorSegundosHoras($totaltiempoatendida, false); 

            $posicion++;
        }
        return $BuilderViewEstateAgent;
    }


    protected function collection_state_agent($BuilderViewEstateAgent){
        $estateagentcollection                 = new Collector;
        foreach ($BuilderViewEstateAgent as $view) {
            $estateagentcollection->push([
                'agent'              => $view['agent'],
                'acd'                => $view['1'],
                'break'              => $view['2'],
                'sshh'               => $view['3'],
                'refrigerio'         => $view['4'],
                'feeedback'          => $view['5'],
                'capacitacion'       => $view['6'],
                'backoffice'         => $view['7'],
                'indbound'           => $view['time_attended'],
                'outbound'           => $view['9'],
                'acw'                => $view['10'],
                'desconectado'       => $view['15'],
                'logueado'           => $view['logueado'],
                'auxiliares'         => $view['auxiliares'],
                'talk'               => $view['talk'],
                'saliente'           => $view['saliente'],
                'ocupation_claro'    => $view['ocupation_claro'],
                'ocupation_cosapi'   => $view['ocupation_cosapi'],
            ]);
        }

        return $estateagentcollection;
    }


    public function events_detail (Request $request){
        if ($request->ajax()){
            if ($request->fecha_evento){

                $days                   = explode(' - ',$request->fecha_evento);
                $query_events           = $this->query_events($days);
                $array_detail_events    = detailEvents($query_events);
                $objCollection          = $this->convertCollection($array_detail_events);
                $detail_events          = $this->format_datatable($objCollection);
                return $detail_events;

            }else{
                return view('elements/events_detail/index');
            }
        }
    }

    protected function query_events($days){

        $detail_events     = DetalleEventos::Select()
                                ->with('evento','user')
                                ->filtro_days($days)
                                ->OrderBy(DB::raw('user_id'), 'asc')
                                ->OrderBy(DB::raw('DATE(fecha_evento)'), 'asc')
                                ->OrderBy(DB::raw('TIME(fecha_evento)'), 'asc')
                                ->get();

        return $detail_events;
    }

    protected function format_datatable($arrays){
        $format_datatable  = Datatables::of($arrays)->make(true);
        return $format_datatable;
    }

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

}
