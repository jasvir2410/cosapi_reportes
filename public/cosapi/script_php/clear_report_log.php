<?php
$proyectos = array('empresas','buenaventura','corporativo');
foreach ($proyectos as $key => $proyecto) {
	$carpeta = '/var/www/'.$proyecto.'/'.$proyecto.'/front_end/reportes/storage/logs';
	if(is_dir($carpeta)){
    	if($dir = opendir($carpeta)){
        	while(($archivo = readdir($dir)) !== false){
            if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){
            	unlink($carpeta.'/'.$archivo);
        		}
        	}
    	}
	}
}
?>
