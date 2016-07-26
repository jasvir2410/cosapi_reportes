<?php

function upper($expression){
    return strtr(strtoupper($expression),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
}



function calcularTiempoLlamadaSaliente($username, $AsteriskCDR){
    foreach($AsteriskCDR as $cdr){
        if ($cdr['username'] == $username){
            return $cdr['TiempoLlamada'];
        }
    }
}


/**
 * [calcularTiempoTrasnc Función para calcular la diferencia entre dos fechas]
 * @param  [date] $fechaInicial [primera fecha eviada]
 * @param  [date] $fechaFinal   [segunda fecha enviada]
 * @return [int]                [Diferencia entre ambas fechas ingresadas]
 */
function calcularTiempoTrasnc($fechaInicial,$fechaFinal){
    $segundos = strtotime($fechaFinal) - strtotime($fechaInicial);
    return $segundos;
}


/**
 * [conversorSegundosHoras Función para cambiar el formato del tiempo que incialmente deb estar en segundos]
 * @param  [int]        $tiempo_en_segundos    [El timepo en segundos.]
 * @param  [Boolean]    $formato               [Dato el cual ns indica en que formato quiere el tiempo: TRUE => 1h:2m:3s  / FALSE => 00:00:00]
 * @return [String]                            [El tiempo con el nuevo formato]
 */
function conversorSegundosHoras($tiempo_en_segundos, $formato) {
    $horas      = floor($tiempo_en_segundos / 3600);
    $minutos    = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
    $segundos   = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);

    $hora_texto = "";
    if ($formato == true){
        if ($horas > 0 )        {   $hora_texto .= $horas . "h ";       }
        if ($minutos > 0 )      {   $hora_texto .= $minutos . "m ";     }
        if ($segundos >= 0 )    {   $hora_texto .= $segundos . "s";     }
    }else{
        if ($horas <=9 )        {   $horas      = '0'.$horas;       }
        if ($minutos <= 9 )      {   $minutos    = '0'.$minutos;     }
        if ($segundos <= 9 )     {   $segundos   = '0'.$segundos;    }

        $hora_texto = $horas.':'.$minutos.':'.$segundos;
    }

    return $hora_texto;
}


/**
 * [ceroIzquierda Función para rellenar con ceros al lado izquierdo del numero ingresado hasta tener un tamaño de 9 digitos]
 * @param  [int] $numero [El número normal]
 * @return [string]         [El número rellenado con ceros]
 */
function ceroIzquierda($numero){
    if ($numero <= 9){
        $numero = '0'.$numero;
    }
    return $numero;
}


/**
 * [modificarFecha Función que Suma o Resta cierta cantidad de días a una fecha]
 * @param  [date] $fecha [Fecha normal]
 * @param  [string] $dias  [Cambio a realizar en la fecha: "+1" <= Incrementa la fecha en 1 día /  "-1" <= Disminuye la fecha en 1 día]
 * @return [date]        [description]
 */
function modificarFecha($fecha,$dias){

    return date("Y-m-d", strtotime("$fecha $dias day"));
}


/**
 * [conversorEstadoAnexo Función que te permite interpretar los diversos estados del Anexo]
 * @param  [string] $status [Ingresa el estado normal del Anexo]
 * @return [string]         [Devuelve el estado interpretado]
 */
function conversorEstadoAnexo($status){
    switch ($status) {
        case 'Up'		:	$status='En Llamada';		break;
        case 'Ringing'	:	$status='Timbrando';		break;
        default:	$status='';		break;
    }
    return $status;
}


/**
 * [ordernarArrayAsociativo Función para ordenar Array]
 * @param  [Array] $array [Array que se va a modifiar el orden]
 * @return [Array]        [Array odenado]
 */
function ordernarArrayAsociativo($array){
    for ($i = 1; $i <= count($array) ; $i++) {
        $new_array[$i] =  array_values($array[$i]);
    }
    return $new_array;
}


function calcularTiempoEntreEstados($usuarios,$eventos,$detalleEventos){

    $resumenEventos =array();
    $totalLogueado=0;
    foreach ($usuarios as $key => $usuario) {
        foreach ($eventos as $key => $evento) {
            $resumenEventos[$usuario['id']][$evento->id] = array('cantidad' => 0, 'tiempo' => '0', 'evento_id' => $evento['id']);
        }

    }

    foreach ($usuarios as $key => $usuario) {
        $user_id = $usuario['id'];
        $filtroEventos[$user_id]   =  array_filter($detalleEventos,
            function($v) use ($user_id) {
                return $v['user_id'] === intval($user_id);
            }
        );
    }

    $filtroEventos=ordernarArrayAsociativo($filtroEventos);


    for ($a = 1; $a <= count($filtroEventos) ; $a++) {

        $totalRegistros = count($filtroEventos[$a]);
        $cantidad       = 0;

        if ($totalRegistros>0){
            for ($b = 0; $b <= $totalRegistros ; $b++) {

                /**
                 *  Si estamos en el ultimo registro del detalle de Eventos.
                 */
                if ($b == $totalRegistros){
                    $user_id             = $filtroEventos[$a][$b-1]['user_id'];
                    $evento_id_anterior  = $filtroEventos[$a][$b-1]['evento_id'];
                    $fechaInicial        = $filtroEventos[$a][$b-1]['fecha_evento'];

                    /**
                     * Si el ultimo evento del Dia es igual a Desconectado no se debe considerar en la suma del detalle de eventos
                     */
                    if ($evento_id_anterior != 15){
                        $fechaFinal = date('Y-m-d H:i:s');
                    }else{
                        $fechaFinal = $fechaInicial;
                    }

                    $cantidad            = 1;
                }else{

                    if ($b=='0'){
                        $user_id             = $filtroEventos[$a][$b]['user_id'];
                        $evento_id_anterior  = $filtroEventos[$a][$b]['evento_id'];

                        $fechaInicial        = $filtroEventos[$a][$b]['fecha_evento'];
                        $fechaFinal          = $filtroEventos[$a][$b]['fecha_evento'];

                        $cantidad            = 0;

                    }else{

                        $user_id             = $filtroEventos[$a][$b-1]['user_id'];
                        $evento_id_anterior  = $filtroEventos[$a][$b-1]['evento_id'];
                        $fechaInicial        = $filtroEventos[$a][$b-1]['fecha_evento'];
                        $fechaFinal          = $filtroEventos[$a][$b]['fecha_evento'];
                        $cantidad            = 1;
                    }

                }

                /**
                 * Obtener Campo de Fecha para evaluadar si estamos en el mismo dia.
                 */
                list($soloFechaFinal, $soloHoraFinal)       = explode(' ', $fechaFinal);
                list($soloFechaInicial, $SoloHoraInicial)   = explode(' ', $fechaInicial);

                /**
                 * Para controlar si el evento pertenece al mismo dia
                 */
                if($soloFechaFinal == $soloFechaInicial){

                    /**
                    Validamos si la hora es 23:59:00 para modificar por 24:00:00
                    De esta manera no faltaria un segundo en el momento de la resta
                    */
                    if ( $soloHoraFinal == '23:59:59') {
                        $fechaFinal = $soloFechaFinal.' 24:00:00';
                    }

                    $tiempoTranscurrido  = calcularTiempoTrasnc($fechaInicial,$fechaFinal);

                    if ($evento_id_anterior != '15' and  $tiempoTranscurrido<'36000')
                    {
                        $resumenEventos[$user_id][$evento_id_anterior]['cantidad']  += $cantidad;
                        $resumenEventos[$user_id][$evento_id_anterior]['tiempo']    += $tiempoTranscurrido;
                    }
                     

                }else{
                    /**
                     * Se realiza para los agentes que trabajan de madrugada y se registra
                     * Hora Inicio  23:00:00
                     * Hora Fin     06:00:00 del dia siguiente
                     */
                    if ($evento_id_anterior == '1'){

                        $tiempoTranscurrido   = calcularTiempoTrasnc($fechaInicial,$soloFechaInicial.' 24:00:00');
                        $tiempoTranscurrido  += calcularTiempoTrasnc($soloFechaFinal.' 00:00:00',$fechaFinal);
                        $resumenEventos[$user_id][$evento_id_anterior]['cantidad']  += $cantidad;
                        $resumenEventos[$user_id][$evento_id_anterior]['tiempo']    += $tiempoTranscurrido;
                    }
                }
            }
            $totalLogueado+=$tiempoTranscurrido;
        }
    }


    return $resumenEventos;
}


/**
 * [searcharray Función que busca la posición de un valor en específico en la columna de un array]
 * @param  [string] $value  [Valor a buscar en la columna del Array]
 * @param  [string] $column [Nombre de la columna en donde se va a buscar el valor]
 * @param  [Array]  $array  [Array en donde se va a realizarce la busqueda]
 * @return [int]            [Posición del valor en el Array]
 */
function searcharray($value, $column, $array) {
    foreach ($array as $key => $val) {
        if ($val[$column] === $value) {
            return $key;
        }
        unset($val);
    }    
    return -1;
}


/**
 * [ExtraerFecha Funcion que extrae la fecha en formato Y-m-d]
 * @param  [date] $day [Valor en bruto de la fecha]
 * @return [date]      [Valor extraido en el formati solicitado]
 */
function ExtraerFecha($day){
    return date('Y-m-d',$day); 
}


/**
 * [ExtraerFecha Funcion que extrae la fecha en formato H:i:s]
 * @param  [date] $day [Valor en bruto de la fecha]
 * @return [date]      [Valor extraido en el formati solicitado]
 */
function ExtraerHora($day){
    return date('H:i:s',$day); 
}


/**
 * [ListCallsConsolidated Función que permite obtener los datos del consolidado de llamadas]
 * @param  [Array] $calls_consolidated [Array con toda la información de las llamadas en una determinada fecha]
 * @param  [String] $indice            [Nombre de la columna por las cual va a agrupar]
 * @return [Array]                     [Array con los datos del consolidados de llamdas]
 */
function ListCallsConsolidated($calls_consolidated,$indice){

    $time_standar = array(10,15,20);
    
    foreach ($calls_consolidated as $calls) {
        
        
            if(isset($Consolidated[$calls[$indice]][$calls['event']])){

                $Consolidated[$calls[$indice]][$calls['event']]=$Consolidated[$calls[$indice]][$calls['event']]+1;

                if(isset($Consolidated[$calls[$indice]]['min_espera'])){

                        $Consolidated[$calls[$indice]]['min_espera']=$calls['info1']+$Consolidated[$calls[$indice]]['min_espera'];

                }else{

                    $Consolidated[$calls[$indice]]['min_espera']=$calls['info1'];

                }

                if(isset($Consolidated[$calls[$indice]]['duracion'])){

                    $Consolidated[$calls[$indice]]['duracion']=$calls['info2']+$Consolidated[$calls[$indice]]['duracion'];

                }else{

                    $Consolidated[$calls[$indice]]['duracion']=$calls['info2'];

                }


                for($i=0;$i<count($time_standar);$i++){

                    if($calls['info1']<=$time_standar[$i]){

                        /*
                         * Contador de tiempo en cola < 10 y < 20
                        */
                        $Consolidated[$calls[$indice]][$calls['event'].$time_standar[$i]]=$Consolidated[$calls[$indice]][$calls['event'].$time_standar[$i]]+1;
                        
                    }

                }

            }else{

                /**
                 * Entra cuando encuentra un evento que aun no a sid contabilizado, para asì inicializar los contadores
                 */

                $Consolidated[$calls[$indice]]['name']=$calls[$indice];

                $Consolidated[$calls[$indice]][$calls['event']]=1;


                if(isset($Consolidated[$calls[$indice]]['min_espera'])){

                    $Consolidated[$calls[$indice]]['min_espera']=$calls['info1']+$Consolidated[$calls[$indice]]['min_espera'];

                }else{
                    $Consolidated[$calls[$indice]]['min_espera']=$calls['info1'];
                }

                if(isset($Consolidated[$calls[$indice]]['duracion'])){

                    $Consolidated[$calls[$indice]]['duracion']=$calls['info2']+$Consolidated[$calls[$indice]]['duracion'];

                }else{
                    $Consolidated[$calls[$indice]]['duracion']=$calls['info2'];
                }
                
                for($i=0;$i<count($time_standar);$i++){

                    /*
                     * Contador de tiempo en cola < 10 y < 20
                     */

                    if($calls['info1']<=$time_standar[$i]){
                      
                        $Consolidated[$calls[$indice]][$calls['event'].$time_standar[$i]]=1;
                        
                    }else{

                        $Consolidated[$calls[$indice]][$calls['event'].$time_standar[$i]]=0;

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
function BuilderCallsConsolidated($callsConsolidated ,$calls_queue,$indice,$list_user=''){

    $time_standar       = array(10,20);
    $posicion           = 1;

    for($j=0;$j<count($calls_queue);$j++){

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


        if(isset($callsConsolidated[$calls_queue[$j][$indice]])){
             
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER'])){         $transfer            =   intval($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER'])){   $complete_caller     =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT'])){    $complete_agent      =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON'])){          $abandon             =   intval($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER10'])){ $completecaller_10   =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER10']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER15'])){ $completecaller_15   =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER15']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER20'])){ $completecaller_20   =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETECALLER20']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT10'])){  $completeagent_10    =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT10']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT15'])){  $completeagent_15    =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT15']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT20'])){  $completeagent_20    =   intval($callsConsolidated[$calls_queue[$j][$indice]]['COMPLETEAGENT20']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON10'])){        $abandon_10          =   intval($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON10']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON15'])){        $abandon_15          =   intval($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON15']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON20'])){        $abandon_20          =   intval($callsConsolidated[$calls_queue[$j][$indice]]['ABANDON20']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER10'])){       $transfer_10         =   intval($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER10']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER15'])){       $transfer_15         =   intval($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER15']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER20'])){       $transfer_20         =   intval($callsConsolidated[$calls_queue[$j][$indice]]['TRANSFER20']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['min_espera'])){       $min_espera          =   intval($callsConsolidated[$calls_queue[$j][$indice]]['min_espera']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['duracion'])){         $duracion            =   intval($callsConsolidated[$calls_queue[$j][$indice]]['duracion']); }
            if(isset($callsConsolidated[$calls_queue[$j][$indice]]['name'])){             $name                =   $callsConsolidated[$calls_queue[$j][$indice]]['name']; }
            if($list_user!=''){
                $Consolidateds[$posicion]['name']                                                              =   ExtraerAgente($name,$list_user);
            }else{
                $Consolidateds[$posicion]['name']                                                              =   $name;
            }
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



            $posicion                               =   $posicion + 1;
            
        }else{
            $Consolidateds[$posicion]['name']                                           =   $calls_queue[$j][$indice];
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
            $posicion                               =   $posicion + 1;
        }
    }

    return $Consolidateds;

}


/**
 * [ExtraerAgente Funcion que devuelve el nombre completo del Agente si se envia el Username]
 * @param  [String] $agente [Dato en bruto extraida de la columna Agent de la BD : Agent/jchero]
 * @param  [Array]  $agente [Dato de los usuarios : nombre, apellidos, etc]
 * @return [array]          [Dato tratado : Jchero]
 */
function ExtraerAgente($agente,$list_user='')
{   
    if($agente <> 'NONE'){
        $array_agente=explode('/', $agente);
        return $array_agente[1];
    }
    return '-';
}


/**
 * [ArrayDays Funcion que cre un arrays con los dias que se encunetra entre el rango de dias establecido]
 * @param  [days]     $days [Rango de dias]
 * @return [array]          [Arrays de dias]
 */
function ArrayDays ($days){
    $i=0;
    $fecha_arrays[$i]= array('fechamod' => date('Y-m-d',strtotime($days[0])));
    $fecha_array[$i]=$days[0];
    while($fecha_array[$i]<$days[1]){

        $i=$i+1;
        $fecha_array[$i]= modificarFecha($fecha_array[$i-1],'+1');
        $fecha_arrays[$i]= array('fechamod' => date('Y-m-d',strtotime(modificarFecha($fecha_array[$i-1],'+1'))));
        
    }
    
    return $fecha_arrays;
}


/**
 * [convertHourExactToSeconds Función que convierte las Horas en Segundos]
 * @param  [string or int] $HourExact   [Horas en formato => 00:00:00  or  0]
 * @param  [string or int] $sumar_extra [tiempo extra]
 * @return [int]                        [Devuelve la hora en segundos]
 */
function convertHourExactToSeconds ($HourExact,$sumar_extra = 0){

    $posicion = strpos($HourExact, ':');

    if($posicion === false){
        $secconds=(intval($HourExact)*3600)+$sumar_extra;
    }else{
        $arrray_HourExact=explode(':', $HourExact);
        $hour_second=intval ($arrray_HourExact[0])*3600;
        $minute_second=intval ($arrray_HourExact[1])*60;
        $secconds=$hour_second+$minute_second+(intval ($arrray_HourExact[2]))+$sumar_extra;
    }

    return $secconds;
}


/**
 * [convertDecimales Función que permite modificar la cantidad de decimales de un numero]
 * @param  [floar] $numero             [Numro a modificar cantidad de decimales]
 * @param  [int]   $cantidad_decimales [Cantidad de decimales que debe tener]
 * @return [float]                     [Numero modificado segun la cantidad de decimales que se desea que tenga]
 */
function convertDecimales($numero,$cantidad_decimales){

    $convertDecimales =round($numero, $cantidad_decimales);

    return $convertDecimales;

}

function filtrar_idcdr($array_base, $array_filtro){

    foreach($array_base as $key => $array_01) {

        foreach($array_filtro as $array_02) {

            if ($array_02['id_cdr'] == $array_01['id_cdr']){

                if ($array_02['name'] == $array_01['name']){

                    unset($array_base[$key]);

                }
            }
        }
    }

    return $array_base;

}

function deletearray($value, $column, $array) {

    foreach ($array as $key => $val) {

        if ($val[$column] === $value) {

            unset($array);

        }        

    }

}


function convertCollection($arrays, $collection){

    foreach ($arrays as $array) {
        $collection->push([
                        'name'              => $array['name'],
                        'recibidas'         => $array['recibidas'],
                        'atendidas'         => $array['atendidas'],
                        'abandonados'       => $array['abandonados'],
                        'transferencias'    => $array['transferencias'],
                        'constestadas'      => $array['constestadas'],
                        'constestadas_10'   => $array['constestadas_10'],
                        'constestadas_15'   => $array['constestadas_15'],
                        'constestadas_20'   => $array['constestadas_20'],
                        'abandonadas_10'    => $array['abandonadas_10'],
                        'abandonadas_15'    => $array['abandonadas_15'],
                        'abandonadas_20'    => $array['abandonadas_20'],
                        'ro10'              => $array['ro10'],
                        'ro15'              => $array['ro15'],
                        'ro20'              => $array['ro20'],
                        'min_espera'        => $array['min_espera'],
                        'duracion'          => $array['duracion'],
                        'avgw'              => $array['avgw'],
                        'avgt'              => $array['avgt'],
                        'answ'              => $array['answ'],
                        'unansw'            => $array['unansw'],  
                    ]);
    }

    return $collection;
    
}

?>