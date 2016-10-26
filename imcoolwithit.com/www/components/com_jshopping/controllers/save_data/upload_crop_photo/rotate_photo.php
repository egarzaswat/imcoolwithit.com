<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));

$img = explode('/', $_POST['image']);
$img_name = $img[count($img)-1];

$path = "";
for( $i=0; $i<count($img)-1; $i++){
    if($i != 0){
        $path .= "/" . $img[$i];
    } else {
        $path .= $img[$i];
    }
}

$img_big = JPATH_BASE . $path . '/' . $img_name;
$img_thumb = JPATH_BASE . $path . '/thumb/' . $img_name;

$src = $img_big;
$new_image = $img_big;
$img = imagecreatefromjpeg($src);
$degrees = 90;
$imgRotated = imagerotate($img, $degrees, 0);
imagejpeg($imgRotated, $new_image, 90);

$src = $img_thumb;
$new_image = $img_thumb;
$img = imagecreatefromjpeg($src);
$imgRotated = imagerotate($img, $degrees, 0);
imagejpeg($imgRotated, $new_image, 90);

print 'success';
