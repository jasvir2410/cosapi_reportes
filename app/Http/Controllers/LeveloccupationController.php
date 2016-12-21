<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;
use Cosapi\Http\Requests;
use Cosapi\Collector\Collector;
use Cosapi\Models\DetalleEventosHistory;
use Cosapi\Models\DetalleEventos;
use Cosapi\Models\Eventos;
use Cosapi\Models\User;
use Cosapi\Http\Controllers\AgentsOnlineController;
use Illuminate\Support\Facades\DB;

class LeveloccupationController extends CosapiController
{

    /**
     * [index Función que retorna vista y datos del Level Of Occupation]
     * @param  Request $request [Parametro que recepcion datos enviados por POST]
     * @return [view]           [View o Array con datos del Level Of Occupation]
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->NivelOcupationHour($request->fecha_evento);
            }else{
                return view('elements/level_of_occupation/index');
            }
        }
    }


    /**
     * [NivelOcupationHour Función que calcula la acantida de Tiempo entre horas para poder realiza el calculo del Level Of Occupation]
     * @param [string] $day_consult [Fecha la cual se va a realizar la consulta]
     */
    protected function NivelOcupationHour($day_consult){
        $array_day_consult              = explode(' - ', $day_consult);
        $before_date                    = 0;
        $before_event                   = 0;
        $before_hour                    = 0;
        $before_time                    = 0;
        $before_user                    = 0;

        $Listhour                       = listHoursInterval();

        $query_detalle_eventos          = DetalleEventosHistory::Select_fechamod()
                                                    ->with('evento')
                                                    ->whereBetween(DB::raw("DATE(fecha_evento)"),$array_day_consult)
                                                    ->OrderBy('user_id', 'asc')
                                                    ->OrderBy('fecha_evento', 'asc')
                                                    ->get()->toArray();

        $Usuarios                       = User::get()->toArray();
        $Eventos                        = Eventos::Select()->where('estado_id','=','1')->get();
        $Eventos_Auxiliares             = Eventos::Select('id')->where('eventos_auxiliares','=','1' )->get();
        $Cosapi_Eventos                 = Eventos::Select('id')->where('cosapi_eventos','=','1' )->get();
        $Claro_Eventos                  = Eventos::Select('id')->where('claro_eventos','=','1' )->get();
        $Array_days                     = ArrayDays($day_consult);
        $cantidad_detalles              = count($query_detalle_eventos);

        foreach ( $query_detalle_eventos as $key => $detalle_eventos) {
            $second_timemod    =  convertHourExactToSeconds($detalle_eventos['timemod']);
            $array_hour        =  explode(':',$detalle_eventos['hourmod']);
            $hour              =  $array_hour[0]*3600;
            $minute            =  $array_hour[1]*60;
            $second            =  $hour + $minute;

            if(!isset($new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']])){

                if(isset($new_detalle)){
                    $new_detalle = $this->nivel_ocupacion($day_consult,$new_detalle,$Usuarios,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$Eventos,$before_hour,$before_user,$before_date);
                }

                $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']]['evento_ini']=$detalle_eventos['evento_id'] ;

                $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']][1] = 0;

                if($query_detalle_eventos[$key+1]['user_id'] != $detalle_eventos['user_id'] or $query_detalle_eventos[$key+1]['fechamod'] != $detalle_eventos['fechamod'] ){
                    if($query_detalle_eventos[$key-1]['evento_id'] != 15 ){
                        $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']][1] = 1800;
                    }
                }


            }else{

                if(!isset($new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']])){
                    $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']]['evento_ini']=$detalle_eventos['evento_id'] ;
                    $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']][1] = $second_timemod - $second;
                    $before_second_timemod                                                    =  convertHourExactToSeconds($before_hour.':00',1800);

                    if(!isset($new_detalle[$before_date][$before_user][$before_hour][$before_event])){
                        $new_detalle[$before_date][$before_user][$before_hour][$before_event] = 0;
                    }

                    $new_detalle[$before_date][$before_user][$before_hour][$before_event] = $new_detalle[$before_date][$before_user][$before_hour][$before_event] + ($before_second_timemod - $before_time);

                    $new_detalle = $this->nivel_ocupacion($day_consult,$new_detalle,$Usuarios,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$Eventos,$before_hour,$before_user,$before_date);


                }else{
                    if(!isset($new_detalle[$before_date][$before_user][$before_hour][$before_event])){
                        $new_detalle[$before_date][$before_user][$before_hour][$before_event] = 0;
                    }

                    $new_detalle[$before_date][$before_user][$before_hour][$before_event] = $new_detalle[$before_date][$before_user][$before_hour][$before_event] + ($second_timemod - $before_time);

                }
            }

            if(($key+1)==$cantidad_detalles){
                $new_detalle = $this->nivel_ocupacion($day_consult,$new_detalle,$Usuarios,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$Eventos,$detalle_eventos['hourmod'],$detalle_eventos['user_id'],$detalle_eventos['fechamod']);
            }

            $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']]['evento_fin'] = $detalle_eventos['evento_id'];
            $new_detalle[$detalle_eventos['fechamod']][$detalle_eventos['user_id']][$detalle_eventos['hourmod']]['hour'] = $detalle_eventos['hourmod'];
            $before_event = $detalle_eventos['evento_id'];
            $before_time  = $second_timemod;
            $before_date  = $detalle_eventos['fechamod'];
            $before_hour  = $detalle_eventos['hourmod'];
            $before_user  = $detalle_eventos['user_id'];

        }


        foreach($Array_days as $keyd => $days){
            foreach($Usuarios as $keyu => $usuario){
                $posicion = 0;
                foreach($Listhour as $keyh => $hour){
                    //EXISTE REGISTRO
                    if(isset($new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']])){
                        //COMIENZA CON UNA HORA DIFERENTE A 00:00
                        if($hour['hourmod'] != '00:00' and $posicion==0){
                            $evento_ini = $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['evento_ini'];
                            $listarentreHoras = listarentreHoras('00:00',$hour['hourmod']);

                            //EVENTO INICIAL DEL REGISTRO EXISTENTE ES DIFERENTE A "LOGUIN"
                            if($evento_ini != 11){
                                foreach($listarentreHoras as $hour_faltante){
                                    foreach ($Eventos as $evento){
                                        $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']][$evento['id']]= 0;
                                    }
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']][1]= 1800;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['evento_ini']= 1;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['evento_fin']= 1;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['logueo']= 1800;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['outbound'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['acw'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['indbound'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['auxiliares'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['hour'] = $hour_faltante['hour'];
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['88'] = 0;
                                }
                            }else{
                            // EVENTO INICIAL DEL REGISTRO ES IGUAL A "LOGIN"
                                foreach($listarentreHoras as $hour_faltante){
                                    foreach ($Eventos as $evento){
                                        $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']][$evento['id']]= 0;
                                    }
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']][15] = 1800;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['evento_ini']= 15;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['evento_fin']= 15;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['logueo']= 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['outbound'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['acw'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['indbound'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['auxiliares'] = 0;
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['hour'] = $hour_faltante['hour'];
                                    $new_detalle[$days['fechamod']][$usuario['id']][$hour_faltante['hour']]['88'] = 0;
                                }
                            }
                        }else{

                            //REGISTRO EXISTENTE CON HORA DIFERENTE A 23:30
                            if ($hour['hourmod'] != '23:30'){

                                //SIGUIENTE REGISTRO NO EXISTE
                                if(!isset($new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]) ){
                                   
                                        $evento_fin = $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['evento_fin'];

                                        //EL ULTIMO EVENTO DEL REGISTRO EXISTENTE ES "DESCONECTADO"
                                        if($evento_fin == 15){

                                            $datetime = $days['fechamod'].' '.$Listhour[$keyh+1]['hourmod'].':00';

                                            if($datetime <= date("Y-m-d H:i:s")){

                                                foreach ($Eventos as $evento){
                                                    $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']][$evento['id']]= 0;
                                                }
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']][15] = 1800;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['evento_ini']= 15;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['evento_fin']= 15;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['logueo']= 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['outbound'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['acw'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['indbound'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['auxiliares'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['hour'] = $Listhour[$keyh+1]['hourmod'];
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['88'] = 0;
                                            }
                                            
                                        }else{
                                        //EL ULTIMO EVENTO DEL REGISTRO EXISTENTE ES DIFERENTE A "DESCONECTADO"

                                            $datetime = $days['fechamod'].' '.$Listhour[$keyh+1]['hourmod'].':00';

                                            if($datetime <= date("Y-m-d H:i:s")){

                                                foreach ($Eventos as $evento){
                                                    $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']][$evento['id']]= 0;
                                                }
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']][$evento_fin] = 1800;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['evento_ini']= $evento_fin;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['evento_fin']= $evento_fin;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['logueo']= 1800;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['outbound'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['acw'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['indbound'] = 0;
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['auxiliares'] = 0;

                                                foreach ($Eventos_Auxiliares as $eventos_auxiliar){
                                                    if($eventos_auxiliar['id']==$evento_fin){
                                                        $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['auxiliares'] = 1800;
                                                    }
                                                }

                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['hour'] = $Listhour[$keyh+1]['hourmod'];
                                                $new_detalle[$days['fechamod']][$usuario['id']][$Listhour[$keyh+1]['hourmod']]['88'] = 0;
                                            }
                                        }
                                }
                            }
                        }
                        $posicion++;
                    }
                }
            }
        }

        $ocupacion = [];
        foreach($Array_days as $days){
            foreach($Listhour as $hour){

                $datetime = $days['fechamod'].' '.$hour['hourmod'].':00';

                if($datetime <= date("Y-m-d H:i:s")){

                    foreach($Usuarios as $usuario){
                        if(isset($new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']])){
                            if(!isset($ocupacion [$days['fechamod']][$hour['hourmod']])){
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['cantidad']           = 1;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_indbound']     = 0;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound']     = 0;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_acw']          = 0;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound']     = 0;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_auxiliares']   = 0;
                                $ocupacion[$days['fechamod']][$hour['hourmod']]['total_logueo']       = 0;
                            }
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['cantidad']         = $ocupacion [$days['fechamod']][$hour['hourmod']]['cantidad']        + 1;
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['total_indbound']   = $ocupacion[$days['fechamod']][$hour['hourmod']]['total_indbound']   + $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['indbound'];
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['total_acw']        = $ocupacion[$days['fechamod']][$hour['hourmod']]['total_acw']        + $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['acw'];
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound']   = $ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound']   + $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['outbound'];
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['total_auxiliares'] = $ocupacion[$days['fechamod']][$hour['hourmod']]['total_auxiliares'] + $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['auxiliares'];
                            $ocupacion[$days['fechamod']][$hour['hourmod']]['total_logueo']     = $ocupacion[$days['fechamod']][$hour['hourmod']]['total_logueo']     + $new_detalle[$days['fechamod']][$usuario['id']][$hour['hourmod']]['logueo'];
                        }
                    }
                    
                    $formula_inferior = ($ocupacion[$days['fechamod']][$hour['hourmod']]['total_logueo']-$ocupacion[$days['fechamod']][$hour['hourmod']]['total_auxiliares']);


                    if($formula_inferior != 0){

                        $ocupacion[$days['fechamod']][$hour['hourmod']]['total_cosapi']     = round(($ocupacion[$days['fechamod']][$hour['hourmod']]['total_indbound'] + $ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound'] + $ocupacion[$days['fechamod']][$hour['hourmod']]['total_acw'])/($formula_inferior)*100,2);
                    }else{
                        $ocupacion[$days['fechamod']][$hour['hourmod']]['total_cosapi']     = round(0,2);
                    }
                    $ocupacion[$days['fechamod']][$hour['hourmod']]['hora']             = $hour['hourmod'];
                    $ocupacion[$days['fechamod']][$hour['hourmod']]['fecha']            = $days['fechamod'];
                }    
            }
        }

        $nivelocupacion = $this->nivelocupacionCollection($ocupacion,$Array_days,$Listhour);

        return $nivelocupacion;
    }



    /**
     * [nivel_ocupacion Función que calcula el Level Of Occupation]
     * @param  [string] $day_consult        [Fecha de consulta, Ejem: '2016-10-22 - 2016-10-25']
     * @param  [string] $new_detalle        [Array con los tiempo acumulados por evento del agente]
     * @param  [int]    $Usuarios           [Id del Usuario]
     * @param  [string] $Eventos_Auxiliares [Id de los eventos auxiliares]
     * @param  [string] $Cosapi_Eventos     [Id de los eventos que considera Cosapi para el nivel de ocupacion] 
     * @param  [string] $Claro_Eventos      [Id de los eventos que considera Claro para el nivel de ocupacion ]
     * @param  [string] $Eventos            [Id de todos los eventos activos]
     * @param  [string] $before_hour        [Hora anterior]
     * @param  [string] $before_user        [Usuario anterior]
     * @param  [string] $before_date        [Fecha anterior]
     * @return [array]                      [Array con datos de Level Of Occupation]
     */
    protected function nivel_ocupacion($day_consult,$new_detalle,$Usuarios,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$Eventos,$before_hour,$before_user,$before_date){
       $indbound   = 0;
       $auxiliares = 0;
       $cosapi     = 0;
       $claro      = 0;
       $logueo     = 0;
       $acw        = 0;
       $outbound   = 0;

       //CALCULANDO TIEMPOS DE LLAMADAS ENTRANTES
       $comingcaller           = new IncomingCallsController();
       $result_tiempoatendida  = $comingcaller->query_calls($day_consult,'calls_completed',$Usuarios[$before_user-1]['username'],$before_hour);

       foreach ($result_tiempoatendida as $tiempoatendida) {
           if($tiempoatendida['hourmod'] == $tiempoatendida['hour_final']){
               $indbound = $indbound + abs($tiempoatendida['info2']);
           }else if($tiempoatendida['hourmod'] != $tiempoatendida['hour_final'] and $tiempoatendida['hour_final'] > $tiempoatendida['hourmod']){
               $second_registro = convertHourExactToSeconds($tiempoatendida['timemod']);
               if($tiempoatendida['hour_final'] != '00:00') {
                   $second_tope = convertHourExactToSeconds($tiempoatendida['hour_final'] . ':00');
               }else{
                   $second_tope = 86400;
               }
               $second_duracion=$second_tope-$second_registro;
               $indbound = $indbound + abs($second_duracion);
           }
       }

       // 88 = Inbound sacado de la tabla "Queue_stats_mv"
       $new_detalle[$before_date][$before_user][$before_hour]['88'] = $indbound;

       //  CALCULANDO TIEMPOS AUXILIARES
       foreach ($Eventos_Auxiliares as $eventos_auxiliar){
           if(!isset($new_detalle[$before_date][$before_user][$before_hour][$eventos_auxiliar['id']])){
               $new_detalle[$before_date][$before_user][$before_hour][$eventos_auxiliar['id']] = 0;
           }
           $auxiliares = $auxiliares + $new_detalle[$before_date][$before_user][$before_hour][$eventos_auxiliar['id']];
       }

       // CALCULANDO TIEMPOS COSAPI
       foreach ($Cosapi_Eventos as $cosapi_evento){
           if(!isset($new_detalle[$before_date][$before_user][$before_hour][$cosapi_evento['id']])){
               $new_detalle[$before_date][$before_user][$before_hour][$cosapi_evento['id']] = 0;
           }

           if($cosapi_evento['id'] != 8){
                $cosapi = $cosapi + $new_detalle[$before_date][$before_user][$before_hour][$cosapi_evento['id']];
           }
       }

       // CALCULANDO TIEMPOS CLARO
       foreach ($Claro_Eventos as $claro_evento){
           if(!isset($new_detalle[$before_date][$before_user][$before_hour][$claro_evento['id']])){
               $new_detalle[$before_date][$before_user][$before_hour][$claro_evento['id']] = 0;
           }

           if($claro_evento['id'] != 8){
               $claro = $claro + $new_detalle[$before_date][$before_user][$before_hour][$claro_evento['id']];
           }
       }

       // CALCULANDO TIEMPOS LOGUEO
       foreach ($Eventos as $evento){
           if($evento['id'] != 8){
               if(!isset($new_detalle[$before_date][$before_user][$before_hour][$evento['id']])){
                   $new_detalle[$before_date][$before_user][$before_hour][$evento['id']] = 0;
               }
               $logueo = $logueo + $new_detalle[$before_date][$before_user][$before_hour][$evento['id']];
           }

           if($evento['id'] == 10){
               if(!isset($new_detalle[$before_date][$before_user][$before_hour][$evento['id']])){
                   $new_detalle[$before_date][$before_user][$before_hour][$evento['id']] = 0;
               }
               $acw = $acw + $new_detalle[$before_date][$before_user][$before_hour][$evento['id']];
           }


           if($evento['id'] == 9){
               if(!isset($new_detalle[$before_date][$before_user][$before_hour][$evento['id']])){
                   $new_detalle[$before_date][$before_user][$before_hour][$evento['id']] = 0;
               }
               $outbound = $outbound + $new_detalle[$before_date][$before_user][$before_hour][$evento['id']];
           }

       }

       $new_detalle[$before_date][$before_user][$before_hour]['indbound']     = $indbound;
       $new_detalle[$before_date][$before_user][$before_hour]['acw']          = $acw;
       $new_detalle[$before_date][$before_user][$before_hour]['outbound']     = $outbound;
       $new_detalle[$before_date][$before_user][$before_hour]['auxiliares']   = $auxiliares;
       $new_detalle[$before_date][$before_user][$before_hour]['logueo']       = $logueo + $indbound;

        return $new_detalle;
    }


    /**
     * [nivelocupacionCollection Función que transforma el array en collection]
     * @param  [array]          $ocupacion  [Array con los datos del reporte de Level Of Occupation]
     * @param  [array]          $Array_days [Array con todos los días que conforman el rango de fecha en consulta]
     * @param  [array]          $Listhour   [Array con rango de hora cada 30 minutos desde las 0 horas hasta las  23:30]
     * @return [colllection]                [Collection con datos para el reporte de Level Of Ocupation]
     */
    protected function nivelocupacionCollection($ocupacion,$Array_days,$Listhour){

        $nivelocupacionCollection = new Collector();
        foreach($Array_days as $days){
            foreach($Listhour as $hour){
                $datetime = $days['fechamod'].' '.$hour['hourmod'].':00';

                if($datetime <= date("Y-m-d H:i:s")){    
                    $nivelocupacionCollection->push([
                        'date'              => $ocupacion[$days['fechamod']][$hour['hourmod']]['fecha'],
                        'hour'              => $hour['name'],
                        'indbound'          => conversorSegundosHoras($ocupacion[$days['fechamod']][$hour['hourmod']]['total_indbound'], false),
                        'acw'               => conversorSegundosHoras($ocupacion[$days['fechamod']][$hour['hourmod']]['total_acw'], false),
                        'outbound'          => conversorSegundosHoras($ocupacion[$days['fechamod']][$hour['hourmod']]['total_outbound'], false),
                        'auxiliares'        => conversorSegundosHoras($ocupacion[$days['fechamod']][$hour['hourmod']]['total_auxiliares'], false),
                        'logueo'            => conversorSegundosHoras($ocupacion[$days['fechamod']][$hour['hourmod']]['total_logueo'], false),
                        'occupation_cosapi' => $ocupacion[$days['fechamod']][$hour['hourmod']]['total_cosapi'],
                    ]);
                }else{
                    $nivelocupacionCollection->push([
                        'date'              => $days['fechamod'],
                        'hour'              => $hour['name'],
                        'indbound'          => conversorSegundosHoras(0, false),
                        'acw'               => conversorSegundosHoras(0, false),
                        'outbound'          => conversorSegundosHoras(0, false),
                        'auxiliares'        => conversorSegundosHoras(0, false),
                        'logueo'            => conversorSegundosHoras(0, false),
                        'occupation_cosapi' => 0,
                    ]);
                }
            }
        }

        return $nivelocupacionCollection;
    }

}
