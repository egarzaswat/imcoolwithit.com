<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

function img_thumb_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);
    if ($size === false) return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio       = max($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    if($use_x_ratio){
        $new_width   = $width;
        $new_height  = floor($size[1] * $ratio);
    } else {
        $new_width   = floor($size[0] * $ratio);
        $new_height  = $height;
    }

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;
}

$jpeg_quality = 90;
$config = new JConfig();

$src = JPATH_BASE . $_POST['image'];

$src_seg = explode('/', $src);
$thumb_url = '';
for($i=0;$i<count($src_seg)-1;$i++){
    $thumb_url .= $src_seg[$i] . '/';
}
$thumb_url .= 'thumb/';
$thumb_url .= $src_seg[count($src_seg)-1];

$img_r = imagecreatefromjpeg($src);
$dst_r = ImageCreateTrueColor( $_POST['w'], $_POST['h'] );

imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'], $_POST['w'],$_POST['h'],$_POST['w'],$_POST['h']);
imagejpeg($dst_r,$thumb_url,$jpeg_quality);

img_thumb_resize($thumb_url, $thumb_url, $config->size_album_photo_small_w, $config->size_album_photo_small_h);

?>
