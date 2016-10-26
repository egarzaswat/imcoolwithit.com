<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));

$jpeg_quality = 90;

$src = JPATH_BASE . $_POST['image'];
$img_r = imagecreatefromjpeg($src);
$dst_r = ImageCreateTrueColor( $_POST['w'], $_POST['h'] );

imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'], $_POST['w'],$_POST['h'],$_POST['w'],$_POST['h']);

header('Content-type: image/jpeg');
imagejpeg($dst_r,$src,$jpeg_quality);

?>
