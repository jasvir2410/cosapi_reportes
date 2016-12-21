#!/usr/bin/php -q
<?php
date_default_timezone_set('America/Lima');
$tiempo_limite=20;
//$baseasterisk="empresas_queuelog";
$baseccskills="buenaventura_reportes";
$bdQstats="buenaventura_qstats";
$host="balanceo_basedatos_buenaventura";
$user="script_buenaven";
$pass="script_buenaven";
$conn=mysql_connect($host,$user,$pass);


// ==================================== RAUL DOMINGUEZ ROSALES 14-08-2016 =======================================================


mysql_select_db($baseccskills,$conn);
$skills = "SELECT * FROM buenaventura_reportes.CC_COLAS ";
$result_skills = mysql_query($skills,$conn);
while($list_skills=mysql_fetch_array($result_skills)){
	$query_incoming 	= "SELECT * FROM buenaventura_qstats.queue_stats_mv WHERE DATE(DATETIME)=DATE(NOW()) AND queue='".$list_skills['CC_NOMBRE']."'";

	$result_incoming 			= mysql_query($query_incoming,$conn);
	$telefonia_abandon  = 0;
	$telefonia_atendida = 0;
	$info2_abandon 		= 0;
	$info2_atendida		= 0;

	while($list_incoming = mysql_fetch_array($result_incoming)){
		if($list_incoming['event'] == 'ABANDON'){			
			if(intval($list_incoming['info1']) >= 15){
				$info2_abandon = $info2_abandon + 1;
			}else{
				$telefonia_abandon = $telefonia_abandon + 1;
			}
		}else{
			$telefonia_atendida = $telefonia_atendida + 1;
			if(intval($list_incoming['info1']) >= 15){
				$info2_atendida = $info2_atendida + 1;
			}
		}
	}
	
	$query_refresh = "UPDATE buenaventura_reportes.CC_CONSOLIDADO SET CC_ATENDIDAS=".$telefonia_atendida." ,CC_ABANDONADAS_MEN_LIM=".$telefonia_abandon." ,CC_ABANDONADAS=".$info2_abandon." ,CC_ATENDIDAS_MAY_LIM=".$info2_atendida." WHERE CC_NOMBRE='".$list_skills['CC_NOMBRE']."'";
	
	mysql_query($query_refresh,$conn);
}

//===================================================================================================================

mysql_close($conn);
?>
