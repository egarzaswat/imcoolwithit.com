<?php
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../../'));
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$user = JSFactory::getUser()->user_id;

$conf = new JConfig();

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

function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);
    if ($size === false) return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    if( ($width < $size[0]) || ($height < $size[1]) ){
        if ($size[1] >= $size[0]){
            $base_size = $height;
        } else {
            $base_size = $width;
        }

        if($base_size == $width){
            $new_width   = $width;
            $new_height  = $width*($size[1]/$size[0]);
        } else {
            $new_height  = $height;
            $new_width   = $height*($size[0]/$size[1]);
        }
    } else {
        $new_width = $size[0];
        $new_height = $size[1];
    }

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($new_width, $new_height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;
}

$path = JPATH_BASE . $conf->path_albums_image . "user_" . $user . "/";
$path_thumb = JPATH_BASE . $conf->path_albums_image . "user_" . $user . "/thumb/";

if (!is_dir($path)){
    mkdir($path);
    copy(JPATH_BASE . $conf->path_albums_image . 'index.html', $path . 'index.html');
}

if (!is_dir($path_thumb)){
    mkdir($path_thumb);
    copy(JPATH_BASE . $conf->path_albums_image . 'index.html', $path_thumb . 'index.html');
}

if(isset($_POST)){
    foreach($_POST['fb_photos'] as $photo){
        $filename = 'facebook_' . md5(microtime() . rand(0, 9999)) . '.jpg';

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $data = @file_get_contents(urldecode($photo), false, stream_context_create($arrContextOptions));
        $file = fopen($path . $filename, 'w+');
        fputs($file, $data);
        fclose($file);

        img_resize($path . $filename, $path . $filename, $conf->size_album_photo_big_w, $conf->size_album_photo_big_h);
        img_thumb_resize($path . $filename, $path_thumb . $filename, $conf->size_album_photo_small_w, $conf->size_album_photo_small_h);

        $db = JFactory::getdbo();
        $query = "INSERT "
               . "INTO `#__users_photos` (`user_id`, `photo`, `private`, `date`) "
               . "VALUES ({$user}, '{$filename}', 0, '" . date("y-m-d h:i:s") . "')";
        $db->setquery($query);
        $db->query();
    }
}