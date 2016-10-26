<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

$conf = new JConfig();

$img_src = JPATH_BASE . $_POST['image'];
$img_src_info = getimagesize($img_src);

$path_directory = JPATH_BASE . $conf->path_user_image_big;
$img_new_name = JSFactory::getUser()->u_name . "_" . date("YmdHis") . ".jpg";

$image_orig = explode('/', $_POST['image']);
$image_orig = $image_orig[count($image_orig)-1];

$db = JFactory::getDBO();
$query = "UPDATE `#__users_photos` SET `avatar` = 0 WHERE `user_id` = " . JSFactory::getUser()->user_id;
$db->setQuery($query);
$db->query();

$query = "UPDATE `#__users_photos` SET `avatar` = 1 WHERE `photo` = '" . $image_orig . "'";
$db->setQuery($query);
$db->query();

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