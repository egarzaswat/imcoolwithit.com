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

$conf = new JConfig();

$file_src = $_FILES['file']['tmp_name'];
$img_src_info = getimagesize($file_src);

$filename = $_POST['user_name'] . "_" . md5(microtime() . rand(0, 9999)) . ".jpg";
$path_directory = JPATH_BASE . $_POST['path'];

if (!is_dir($path_directory)){
    mkdir($path_directory);
    copy(JPATH_BASE . $conf->path_albums_image . 'index.html', $path_directory . 'index.html');
}

if (!is_dir($path_directory . "thumb/")){
    mkdir($path_directory . "thumb/");
    copy(JPATH_BASE . $conf->path_albums_image . 'index.html', $path_directory . 'thumb/index.html');
}

switch ($img_src_info['mime']) {
    case 'image/jpeg' :
        $image = imagecreatefromjpeg($file_src);
        imagejpeg($image, $path_directory . $filename, 100);
        imagedestroy($image);
        break;
    case 'image/png' :
        $image = imagecreatefrompng($file_src);
        imagejpeg($image, $path_directory . $filename, 100);
        imagedestroy($image);
        break;
    case 'image/gif' :
        $image = imagecreatefromgif($file_src);
        imagejpeg($image, $path_directory . $filename, 100);
        imagedestroy($image);
        break;
    default :
        echo 'Error format photo!';
}

$config = new JConfig();
img_thumb_resize($path_directory . $filename, $path_directory . "thumb/" . $filename, $config->size_album_photo_small_w, $config->size_album_photo_small_h);
img_resize($path_directory . $filename, $path_directory . $filename, $config->size_album_photo_big_w, $config->size_album_photo_big_h);

$db = jfactory::getdbo();
$query = "insert into `#__users_photos` (`user_id`, `photo`, `private`, `date`) values (" . $_POST['user'] . ", '" . $filename . "', " . $_POST['private'] . ", '" . date("y-m-d h:i:s") . "')";
$db->setquery($query);
$db->query();

?>