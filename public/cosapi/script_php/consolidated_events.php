<?php
date_default_timezone_set('America/Lima');
$host='192.167.99.252';
$user='raul.dominguez';
$pass='guitarhero1';
/*$host='localhost';
$user='root';
$pass='';*/
$conn=mysql_connect($host,$user,$pass);

//$claro_networking  = array ('EMPRESAS', 'CORPORAIVO', 'CALIDAD','BACKOFFICE');
$claro_networking    = array ('EMPRESAS');
$listDayInterval     = ArrayDays('2016-08-01 - 2016-08-31');
//$listDayInterval     = ArrayDays('2016-08-24 - 2016-08-25');

foreach ($listDayInterval as $listDay) {
    $fecha_inicial = $listDay['fechamod'];
    $fecha_final   = $listDay['fechamod'];
    $bd_history    = 'cosapi_history';

    foreach ($claro_networking as  $claro) {
        $AsteriskCDR        = [];
        $Usuarios           = [];
        $Eventos            = [];
        $Eventos_Auxiliares = [];
        $Cosapi_Eventos     = [];
        $Claro_Eventos      = [];
        $DetalleEventos     = [];
        $BuilderViewEstateAgent = [];

        switch($claro){
            case 'EMPRESAS' :
                $bd_cdr     = 'empresas_cdr';
                $bd_laravel = 'empresas_laravel';
                $bd_qstats  = 'empresas_qstats';
                $tbl_cdr    = 'cdr_empresas';
                $tbl_history_event = 'detalle_eventos_emp';
                break;
            case 'CORPORAIVO' :
                $bd_cdr     = 'corporativo_cdr';
                $bd_laravel = 'corporativo_laravel';
                $bd_qstats  = 'corporativo_qstats';
                $tbl_cdr    = 'cdr_corporativo';
                $tbl_history_event = 'detalle_eventos_corp';
                break;
            case 'CALIDAD' :
                $bd_cdr     = 'empresas_cdr';
                $bd_laravel = 'calidad_laravel';
                $bd_qstats  = 'empresas_qstats';
                $tbl_cdr    = 'cdr_empresas';
                $tbl_history_event = 'detalle_eventos_cal';
                break;
            case 'BACKOFFICE' :
                $bd_cdr     = 'empresas_cdr';
                $bd_laravel = 'backoffice_laravel';
                $bd_qstats  = 'empresas_qstats';
                $tbl_cdr    = 'cdr_empresas';
                $tbl_history_event = 'detalle_eventos_back';
                break;
        }

        /*$sql_insert_history = 'insert into cosapi_history.'.$tbl_history_event.' (evento_id,user_id,fecha_evento,ip_cliente,observaciones) 
        select evento_id,user_id,fecha_evento,ip_cliente,observaciones FROM '.$bd_laravel.'.detalle_eventos 
        where DATE(fecha_evento) = "'.$fecha_inicial.'" ';*/
        $sql_insert_history = 'insert into detalle_eventos_history (evento_id,user_id,fecha_evento,ip_cliente,observaciones) 
        select evento_id,user_id,fecha_evento,ip_cliente,observaciones FROM detalle_eventos 
        where DATE(fecha_evento) = "'.$fecha_inicial.'" ';
        //mysql_select_db($bd_laravel,$conn);
        mysql_select_db('pruebas',$conn);        
        mysql_query($sql_insert_history,$conn);


        $query_AsteriskCDR 	   = 'select accountcode as username, SUM(billsec) as TiempoLlamada from '.$tbl_cdr.' where DATE(calldate) between "'.$fecha_inicial.'" and "'.$fecha_final.'" and disposition = "ANSWERED" and lastapp = "Dial" group by accountcode';

        mysql_select_db($bd_cdr,$conn);
        $Asterisk_CDR = mysql_query($query_AsteriskCDR,$conn);

        while($row_AsteriskCDR = mysql_fetch_array($Asterisk_CDR)) {
           $AsteriskCDR[] 	   = $row_AsteriskCDR;
        }

        $query_Usuarios 	   = 'select * from users';
        mysql_select_db($bd_laravel,$conn);
        $usuario = mysql_query($query_Usuarios,$conn);

        while($row_Usuarios    = mysql_fetch_array($usuario)) {
           $Usuarios [] 	   = $row_Usuarios;
        }

        $query_Eventos 			= 'select * from eventos where estado_id = "1"';
        mysql_select_db($bd_laravel,$conn);
        $evento 				= mysql_query($query_Eventos,$conn);
        while($row_evento 		= mysql_fetch_array($evento)) {
           $Eventos []          = $row_evento;
        }

        $query_Eventos_Auxiliares 	  = 'select id from eventos where eventos_auxiliares = "1"';
        mysql_select_db($bd_laravel,$conn);
        $EventosAuxiliares 			  = mysql_query($query_Eventos_Auxiliares,$conn);
        while($row_Eventos_Auxiliares = mysql_fetch_array($EventosAuxiliares)) {
           $Eventos_Auxiliares []     = $row_Eventos_Auxiliares;
        }

        $query_Cosapi_Eventos 		= 'select id from eventos where cosapi_eventos = "1"';
        mysql_select_db($bd_laravel,$conn);
        $CosapiEventos 				= mysql_query($query_Cosapi_Eventos,$conn);
        while($row_Cosapi_Eventos 	= mysql_fetch_array($CosapiEventos)) {
           $Cosapi_Eventos [] 		= $row_Cosapi_Eventos;
        }

        $query_Claro_Eventos 			= 'select id from eventos where claro_eventos = "1"';
        mysql_select_db($bd_laravel,$conn);
        $ClaroEventos  					= mysql_query($query_Claro_Eventos,$conn);
        while($row_Claro_Eventos 		= mysql_fetch_array($ClaroEventos)) {
           $Claro_Eventos [] 			= $row_Claro_Eventos;
        }

        $query_DetalleEventos 	   = 'select * from detalle_eventos where DATE(fecha_evento) between "'.$fecha_inicial.'" and "'.$fecha_final.'" order by user_id asc, fecha_evento asc';
        //mysql_select_db($bd_laravel,$conn);
        mysql_select_db('pruebas',$conn);
        $Detalle_Eventos  		   = mysql_query($query_DetalleEventos,$conn);

        while($row_DetalleEventos  = mysql_fetch_array($Detalle_Eventos)) {
           $DetalleEventos [] 	   = $row_DetalleEventos;
        }

        $query_delete_events       = 'delete from detalle_eventos where DATE(fecha_evento) between "'.$fecha_inicial.'" and "'.$fecha_final.'" ';
        //mysql_select_db($bd_laravel,$conn);
        mysql_select_db('pruebas',$conn);
        $Detalle_Eventos           = mysql_query($query_delete_events,$conn);
        
        $BuilderViewEstateAgent    = BuilderViewEstateAgent($Usuarios,$Eventos,$DetalleEventos,$AsteriskCDR,false,$Eventos_Auxiliares,$Cosapi_Eventos,$Claro_Eventos,$fecha_inicial,$fecha_final,$conn,$bd_qstats);

        foreach ($BuilderViewEstateAgent as $ViewEstateAgent) {
            if($ViewEstateAgent['logueado'] != '00:00:00'){
            	$query_consolidated_events = 'insert into consolidated_events values ("","'.$fecha_inicial.'","'.$ViewEstateAgent['agent'].'","'.$ViewEstateAgent['11'].'","'.$ViewEstateAgent['1'].'","'.$ViewEstateAgent['2'].'","'.$ViewEstateAgent['3'].'","'.$ViewEstateAgent['4'].'","'.$ViewEstateAgent['5'].'","'.$ViewEstateAgent['6'].'","'.$ViewEstateAgent['7'].'","'.$ViewEstateAgent['time_attended'].'","'.$ViewEstateAgent['9'].'","'.$ViewEstateAgent['10'].'","'.$ViewEstateAgent['15'].'","'.$ViewEstateAgent['logueado'].'","'.$ViewEstateAgent['auxiliares'].'","'.$ViewEstateAgent['talk'].'","'.$ViewEstateAgent['saliente'].'","'.$ViewEstateAgent['ocupation_claro'].'","'.$ViewEstateAgent['ocupation_cosapi'].'")';
            	//mysql_select_db($bd_laravel,$conn);
                mysql_select_db('pruebas',$conn);
            	mysql_query($query_consolidated_events,$conn);
            }
        }

    }
}

function BuilderViewEstateAgent($usuarios, $eventos,$detalleEventos,$AsteriskCDR,$formato,$eventos_auxiliares,$cosapi_eventos,$claro_eventos,$fecha_inicial,$fecha_final,$conn,$bd_qstats){
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
            $BuilderViewEstateAgent[$posicion][$Evento['evento_id']]    = $Evento['tiempo'];  
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
            if ( $evento_id == 1 ){
                $totalACD = $Evento['tiempo'];
            }
           
            // Calculo para tiempoLlamadaEntrante-->
            if ( $evento_id == 8 ){
                $tiempoLlamadaEntrante = $Evento['tiempo'];
            }
            
            if($evento_id != 8){
                $tiempoTotalLogueado             = $tiempoTotalLogueado + $Evento['tiempo'];
            }

        }

        
    	$result_tiempoatendida  		= [];
        $query_Comingcaller 			= 'select *,DATE(datetime) as fechamod, TIME(datetime) AS timemod,  DATE_FORMAT((DATE_SUB(DATETIME, INTERVAL ( MINUTE(DATETIME)%30 )MINUTE)), "%H:%i") AS hourmod, DATE_FORMAT((DATE_SUB(DATE_FORMAT(DATE_ADD(DATETIME, INTERVAL info2 SECOND), "%Y-%m-%d %H:%i:%s"),INTERVAL ( MINUTE( DATE_FORMAT(DATE_ADD(DATETIME, INTERVAL info2 SECOND), "%Y-%m-%d %H:%i:%s"))%30) MINUTE)), "%H:%i") AS hour_final  from queue_stats_mv where agent like "%'.$usuarios[$key-1]['username'].'%" and DATE(datetime) between "'.$fecha_inicial.'" and "'.$fecha_final.'" and event in ("COMPLETECALLER", "COMPLETEAGENT", "TRANSFER") and queue not in ("NONE", "HD_CE_BackOffice", "Pruebas", "HD_CE_Calidad") order by id asc';
        mysql_select_db($bd_qstats,$conn);
		$Comingcaller   				= mysql_query($query_Comingcaller,$conn);

		while($row_Comingcaller = mysql_fetch_array($Comingcaller)) {
		   $result_tiempoatendida[] = $row_Comingcaller;
		}


        foreach ($result_tiempoatendida as $tiempoatendida) {
            $totaltiempoatendida+=abs($tiempoatendida['info2']);
        }
        
        
        $tiempoTotalLogueado                                = $tiempoTotalLogueado + $totaltiempoatendida;

        $tiempoLlamadaSaliente                              = calcularTiempoLlamadaSaliente($usuarios[$key-1]['username'], $AsteriskCDR);
        $tiempoTotalHablado                                 = $totaltiempoatendida + $tiempoLlamadaSaliente;

        $BuilderViewEstateAgent[$posicion]['logueado']      = $tiempoTotalLogueado;
        $BuilderViewEstateAgent[$posicion]['auxiliares']    = $totalTiemposAuxiliares;
        $BuilderViewEstateAgent[$posicion]['talk']          = $tiempoTotalHablado;
        $BuilderViewEstateAgent[$posicion]['saliente']      = $tiempoLlamadaSaliente;

        if($tiempoTotalLogueado != $totalTiemposAuxiliares){
            $BuilderViewEstateAgent[$posicion]['ocupation_claro']  = round(($totalTiemposTrabajadosClaro/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
            $BuilderViewEstateAgent[$posicion]['ocupation_cosapi'] = round(($totalTiemposTrabajadosCosapi/($tiempoTotalLogueado - $totalTiemposAuxiliares))*100,2);
        }else{
            $BuilderViewEstateAgent[$posicion]['ocupation_claro']  = 0;
            $BuilderViewEstateAgent[$posicion]['ocupation_cosapi'] = 0; 
        }           
        
        $BuilderViewEstateAgent[$posicion]['time_attended']  = $totaltiempoatendida; 

        $posicion++;
    }
    return $BuilderViewEstateAgent;
}


function calcularTiempoEntreEstados($usuarios,$eventos,$detalleEventos){
   
    $resumenEventos = array();
    $totalLogueado=0;
    foreach ($usuarios as $key => $usuario) {
        foreach ($eventos as $key => $evento) {
            $resumenEventos[$usuario['id']][$evento['id']] = array('cantidad' => 0, 'tiempo' => '0', 'evento_id' => $evento['id']);
        }

    }



    foreach ($usuarios as $key => $usuario) {
        $user_id = $usuario['id'];

        $filtroEventos[$user_id]   =  array_filter($detalleEventos,
            function($v) use ($user_id) {
                return $v['user_id'] == $user_id;
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
                    if ($evento_id_anterior == '2'){

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

function ordernarArrayAsociativo($array){
    for ($i = 1; $i <= count($array) ; $i++) {
        $new_array[$i] =  array_values($array[$i]);
    }
    return $new_array;
}

function calcularTiempoTrasnc($fechaInicial,$fechaFinal){
    $segundos = strtotime($fechaFinal) - strtotime($fechaInicial);
    return $segundos;
}


function calcularTiempoLlamadaSaliente($username, $AsteriskCDR){
    foreach($AsteriskCDR as $cdr){
        if ($cdr['username'] == $username){
            return $cdr['TiempoLlamada'];
        }
    }
}

function ArrayDays ($fecha_evento){
    $days               = explode(' - ', $fecha_evento);
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

function modificarFecha($fecha,$dias){

    return date("Y-m-d", strtotime("$fecha $dias day"));
}

mysql_close($conn);

?>