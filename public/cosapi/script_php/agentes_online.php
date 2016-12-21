#!/usr/bin/php -q
<?php
date_default_timezone_set('America/Lima');
$host="balanceo_basedatos_empresas";
$user="empresas_web";
$pass="empresas_web";
$conn=mysql_connect($host,$user,$pass);


$agentes_online_bo = " CALL backoffice_laravel.show_agent_online() ";
$result_skills = mysql_query($agentes_online_bo,$conn);

$agentes_online_ca = " CALL calidad_laravel.show_agent_online() ";
$result_skills = mysql_query($agentes_online_ca,$conn);

$agentes_online_ce = " CALL empresas_laravel.show_agent_online() ";
$result_skills = mysql_query($agentes_online_ce,$conn);

$agentes_online_co = " CALL corporativo_laravel.show_agent_online() ";
$result_skills = mysql_query($agentes_online_co,$conn);

mysql_close($conn);

?>