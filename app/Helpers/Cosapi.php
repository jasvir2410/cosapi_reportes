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

function calcularTiempoTrasnc($fechaInicial,$fechaFinal){
    $segundos = strtotime($fechaFinal) - strtotime($fechaInicial);

    return $segundos;
}

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
        if ($horas < 9 )        {   $horas      = '0'.$horas;       }
        if ($minutos < 9 )      {   $minutos    = '0'.$minutos;     }
        if ($segundos < 9 )     {   $segundos   = '0'.$segundos;    }

        $hora_texto = $horas.':'.$minutos.':'.$segundos;
    }

    return $hora_texto;
}

function ceroIzquierda($numero){
    if ($numero <= 9){
        $numero = '0'.$numero;
    }

    return $numero;
}

function conversorEstadoAnexo($status){

    switch ($status) {
        case 'Up'		:	$status='En Llamada';		break;
        case 'Ringing'	:	$status='Timbrando';		break;

        default:	$status='';		break;
    }

    return $status;
}

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
                    //$evento_id_actual    = $filtroEventos[$a][$b-1]['evento_id'];

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
                        //$evento_id_actual    = $filtroEventos[$a][$b]['evento_id'];
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
?>