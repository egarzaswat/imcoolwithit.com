<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

function img_resize_old($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);

    if ($size === false) return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio       = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
    $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;

}

function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
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

$image_segments = explode('/', $_POST['image']);
$image_name = $image_segments[count($image_segments)-1];

$img_info = getimagesize(JPATH_BASE . $_POST['image']);

$config = new JConfig();

if( $img_info[0] >= $config->size_avatar_big_w && $img_info[1] >= $config->size_avatar_big_h ){
    img_resize(JPATH_BASE . $_POST['image'], JPATH_BASE . "/images/user_photo/" . $image_name, $config->size_avatar_big_w, $config->size_avatar_big_h);
} else {
    img_resize(JPATH_BASE . $_POST['image'], JPATH_BASE . "/images/user_photo/" . $image_name, $config->size_avatar_medium_w, $config->size_avatar_medium_h);
}
img_resize(JPATH_BASE . $_POST['image'], JPATH_BASE . "/images/user_photo/medium/" . $image_name, $config->size_avatar_medium_w, $config->size_avatar_medium_h);
img_resize(JPATH_BASE . $_POST['image'], JPATH_BASE . "/images/user_photo/small/" . $image_name, $config->size_avatar_small_w, $config->size_avatar_small_h);

print $image_name;

?>
