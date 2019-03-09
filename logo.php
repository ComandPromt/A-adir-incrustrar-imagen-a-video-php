<?php
require 'vendor/autoload.php';
	$video = 'video.mp4';//Url del video
	$salida = 'ejemplo1.mp4';//Nombre de salida
	
    //Recuperar informacion del video y decodificarla
	exec('ffprobe -i '.$video.' -v quiet -print_format json -show_format -show_streams -hide_banner', $out, $res);
	if($res==1){ echo 'Error al leer informacion del video'; exit;}
	$info = json_decode(implode($out));
	
	//Datos a extraer
	$w = $info->streams[0]->width;//Ancho del video
	$h = $info->streams[0]->height;//Alto del video
	
	//Creamos un lienzo de color negro con la dimenciones del video
	$imagen = imagecreatetruecolor($w, $h);
	
	//Indicamos que la $imagen tendra el canal alfha activo
	imagesavealpha($imagen, true);
	
	//Generamos un color 100% transparente
	$alpha = imagecolorallocatealpha($imagen, 0, 0, 0, 127);
	
	//Rellenamos a $imagen con el color $alpha
	imagefill($imagen, 0, 0, $alpha);

	//Cargando la marca de agua en formato PNG
	$logo = imagecreatefrompng('logo.png');
	
	//Obteniendo el alto y ancho de la imagen logo.png
	$lw = imagesx($logo);
	$lh = imagesy($logo);
	
	//Margen de la marca de agua
	$margen = 10;
	
	//Calculando coordenadas para colocar el logo en la esquina inferior izquierda
	$x = $w - $lw - $margen; 
	$y = $h - $lh - $margen;
	
	//Colocando la marca de agua, en la esquina inferior izquierda
	imagecopy($imagen, $logo, $x, $y, 0, 0, $lw, $lh);
	
	//Guardamos la imagen
	imagepng($imagen, 'marca.png');
	imagedestroy($imagen);
		
	//Fusionar el video con la marca de a guar
	exec('ffmpeg -i '.$video.' -i marca.png -filter_complex overlay '.$salida, $out, $res);
	
	//Veficar si ocurrio un error
	if($res==1){ echo 'Error al procesar video'; exit;}
?>