<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
ini_set ('memory_limit', '-1');
$file_src = $_FILES['file']['tmp_name'];
$img_src_info = getimagesize($file_src);


$config = new JConfig();
if( ($img_src_info['mime'] == 'image/jpeg') || ($img_src_info['mime'] == 'image/png') || ($img_src_info['mime'] == 'image/gif') ){
    if( ($img_src_info[0] < $config->size_avatar_big_w) || ($img_src_info[1] < $config->size_avatar_big_h) ){
        echo 'Error size photo!';
        exit;
    }

    $path_directory = JPATH_BASE . $_POST['path'];
    $img_new_name = $_POST['user'] . "_" . date("YmdHis") . ".jpg";

    switch ($img_src_info['mime']) {
        case 'image/jpeg' :
            $image = imagecreatefromjpeg($file_src);
            imagejpeg($image, $path_directory . $img_new_name, 100);
            imagedestroy($image);
            echo $img_new_name;
            break;
        case 'image/png' :
            $image = imagecreatefrompng($file_src);
            imagejpeg($image, $path_directory . $img_new_name, 100);
            imagedestroy($image);
            echo $img_new_name;
            break;
        case 'image/gif' :
            $image = imagecreatefromgif($file_src);
            imagejpeg($image, $path_directory . $img_new_name, 100);
            imagedestroy($image);
            echo $img_new_name;
            break;
        default :
            echo 'Error format photo!';
    }

} else {
    echo 'Error format photo!';
}
?>