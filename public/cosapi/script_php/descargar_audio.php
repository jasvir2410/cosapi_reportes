<?php

/**
 * $RutaGrabaciones Sirve para enviar la url donde se encuentra el audio(grabaciones/nombre del skills/año/mes/dia)
 * @var [proyect] envia el nombre del proyecto que hace referencia a la carpeta
 * @var [url] envia el nombre de la Ruta de URL : Ejemplo : eje. HD_CE_Internet/2016/08/18/
 */
$RutaGrabaciones	= $_GET["proyect"].'/'.$_GET["url"];


/**
 * $NombreAudio Sirve para capturar el nombre del auido (numero de telefono-vdn-fecha-hora.gsm)
 * @var [nameaudio] Ejemplo 989066831-79999-17082016-100435.gsm
 */
$NombreAudio 		= $_GET["nameaudio"];
$hour				= $_GET["hour"];
$newHour			= date("His",strtotime($hour));
/**
 * $destino Concatena las variable para armar la URL completa para descargar el audio.
 * @var string
 */
//$destino 			= "http://grabaciones.cosapidata.pe/".$RutaGrabaciones.$NombreAudio;
$destino 			= "../grabaciones_asterisk/".$RutaGrabaciones.$NombreAudio.$newHour.".gsm";

if(file_exists($destino)){
	$nuevaHora=date("His",strtotime($hour));
	$destino 			= "../grabaciones_asterisk/".$RutaGrabaciones.$NombreAudio.$nuevaHora.".gsm";
	$NombreAudio		= $NombreAudio.$nuevaHora.".gsm";

}else{
	$nuevaHora=date("His",strtotime($hour)-1);
	$destino 			= "../grabaciones_asterisk/".$RutaGrabaciones.$NombreAudio.$nuevaHora.".gsm";
	if(file_exists($destino)){
		$destino 			= "../grabaciones_asterisk/".$RutaGrabaciones.$NombreAudio.$nuevaHora.".gsm";
		$NombreAudio		= $NombreAudio.$nuevaHora.".gsm";
	}else{
		$nuevaHora=date("His",strtotime($hour)+1);
		$destino 			= "../grabaciones_asterisk/".$RutaGrabaciones.$NombreAudio.$nuevaHora.".gsm";
		$NombreAudio		= $NombreAudio.$nuevaHora.".gsm";
	}
}


header("Pragma: public");
header("Expires: 0");
//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

/**
 * Sirve para forzar a descargar el archivo
 */
header("Content-Type: application/force-download");

/**
 * Sara indicar que nombre del archivo se va descargar
 */
header("Content-Disposition: attachment; filename=".basename($NombreAudio));

header("Content-type: application/octet-stream");
header("Content-Description: File Transfer");
readfile($destino);


?>