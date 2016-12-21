
<?php
//Script desarrollado por Eder Vela
//date_default_timezone_set('America/Lima');  
function Conectarse()
{
	if (!($con=mysql_connect("balanceo_basedatos_empresas","script_corpo","script_corpo")))
	{
		echo "Error conectando a la base de datos.";
		exit();
	}
	if (!mysql_select_db("corporativo_laravel",$con))
	{
		echo "Error seleccionando la base de datos.";
		exit();
	}
	return $con;
}


$con = Conectarse();

$consulta = "SELECT id, evento_id, fecha_evento  FROM (
	SELECT u.*,
	IFNULL(
		(SELECT evento_id
		FROM detalle_eventos
		WHERE DATE(fecha_evento)=DATE(NOW())
		AND user_id = u.id
		ORDER BY fecha_evento DESC
		LIMIT 1)
	, 0) AS evento_id,
	IFNULL(
		(SELECT fecha_evento
		FROM detalle_eventos
		WHERE DATE(fecha_evento)=DATE(NOW())
		AND user_id = u.id
		ORDER BY fecha_evento DESC
		LIMIT 1)
	, 0) AS fecha_evento
	FROM users AS u
) AS listado WHERE evento_id NOT IN ('0','15') ";

$result = mysql_query($consulta, $con);

 //echo $consulta;

 while ($row = mysql_fetch_array ($result)){

 	//para hacer el insert de la madrugada
 	$user_id			=	$row['id'];
	$evento_id			=	$row['evento_id'];
	$fecha_actual		=	date('Y-m-d').' 23:59:59';
	$fecha_siguiente	=	date('Y-m-d',strtotime("+1 day")).' 00:00:00';
	$fecha_siguiente2	=	date('Y-m-d',strtotime("+1 day")).' 00:00:01';

 	//para RESTAR LAS HORA Y VALIDAR que se desloguearon mal
 	$hora_evento_bd		=	date('H:i:s',strtotime($row['fecha_evento']));
 	$hora_actual		=	date('H:i:s');

	$hora_bd 			= 	convertir_segundos($hora_evento_bd);
	$hora_actual		= 	convertir_segundos($hora_actual);

	$resta 				= 	$hora_actual-$hora_bd;
 	

 	if ($resta>=3600) {
 		//echo "se desconecto mal ".$row['id'];
 		//echo "</br>";
 		//$query_logout  ="INSERT INTO `corporativo_laravel`.detalle_eventos (evento_id, user_id, fecha_evento, ip_cliente, observaciones) ";
		//$query_logout .="VALUES ('15', '".$user_id."', '".$fecha_actual."', '10.200.74.237','Se deslogueo MAL') ";
		//mysql_query($query_logout,$con);
 	}
 	else{

 		// insertar logout a la media noche
	    $query_logout  ="INSERT INTO `corporativo_laravel`.detalle_eventos (evento_id, user_id, fecha_evento, ip_cliente, observaciones) ";
		$query_logout .="VALUES ('15', '".$user_id."', '".$fecha_actual."', '10.200.74.238','Evento WebServer Logout') ";
		mysql_query($query_logout,$con);

		// // insertar login a la media noche por cambios en el panel del agente para agente de madrugada -- 14/10/2016 -- Jasvir Vela
	    $query_login  ="INSERT INTO `corporativo_laravel`.detalle_eventos (evento_id, user_id, fecha_evento, ip_cliente, observaciones) ";
		$query_login .="VALUES ('15', '".$user_id."', '".$fecha_siguiente."', '10.200.74.238','Evento WebServer Login') ";
		mysql_query($query_login,$con);

		// regresar el agente conectado a su estado orginal
		$query_estado_actual  ="INSERT INTO `corporativo_laravel`.detalle_eventos (evento_id, user_id, fecha_evento, ip_cliente, observaciones) ";
		$query_estado_actual .="VALUES ('".$evento_id."', '".$user_id."', '".$fecha_siguiente2."', '10.200.74.238','Evento WebServer Restaurar Estado Inicial') ";
		mysql_query($query_estado_actual,$con);
 	}

 }

 function convertir_segundos ($time){

	$array_time=explode(':', $time);

	$hour_time=$array_time[0] *3600;
	$minute_time=$array_time[1]*60;
	$second_time=$hour_time+$minute_time+$array_time[2];
	return $second_time;
}



?>
