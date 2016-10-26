<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$conf = new JConfig();

$img_src = $_POST['image'];
$img_src_info = getimagesize($img_src);

$path_directory = JPATH_BASE . $_POST['path'];
$img_new_name = $_POST['user'] . "_" . date("YmdHis") . ".jpg";

$config = new JConfig();
if( ($img_src_info[0] < $config->size_avatar_big_w) || ($img_src_info[1] < $config->size_avatar_big_h) ){
    echo 'Error size photo!';
    exit;
}

switch ($img_src_info['mime']) {
    case 'image/jpeg' :
        $image = imagecreatefromjpeg($img_src);
        imagejpeg($image, $path_directory . $img_new_name, 100);
        imagedestroy($image);
        echo $img_new_name;
        break;
    case 'image/png' :
        $image = imagecreatefrompng($img_src);
        imagejpeg($image, $path_directory . $img_new_name, 100);
        imagedestroy($image);
        echo $img_new_name;
        break;
    case 'image/gif' :
        $image = imagecreatefromgif($img_src);
        imagejpeg($image, $path_directory . $img_new_name, 100);
        imagedestroy($image);
        echo $img_new_name;
        break;
    default :
        echo 'Error format photo!';
}