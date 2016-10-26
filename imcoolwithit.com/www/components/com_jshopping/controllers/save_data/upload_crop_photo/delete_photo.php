<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

$img = explode('/', $_POST['image']);
$img_name = $img[count($img)-1];
deleteImage($img_name);

$path = "";
for( $i=0; $i<count($img)-1; $i++){
    if($i != 0){
        $path .= "/" . $img[$i];
    } else {
        $path .= $img[$i];
    }
}

unlink(JPATH_BASE . $path . '/' . $img_name);
unlink(JPATH_BASE . $path . '/thumb/' . $img_name);

function deleteImage($img){
    $db = JFactory::getDBO();
    $query = "SELECT `avatar` FROM `#__users_photos` WHERE `photo` = '" . $img . "'";
    $db->setQuery($query);
    if($db->loadResult() == 1){
        $query = "SELECT `photosite` FROM `#__jshopping_users` WHERE `user_id` = '" . JSFactory::getUser()->user_id . "'";
        $db->setQuery($query);
        $conf = new JConfig();
        $img_name = $db->loadResult();

        unlink(JPATH_BASE . $conf->path_user_image_big . '/' . $img_name);
        unlink(JPATH_BASE . $conf->path_user_image_small . '/' . $img_name);
        unlink(JPATH_BASE . $conf->path_user_image_medium . '/' . $img_name);

        $query = "UPDATE `#__jshopping_users` SET `photosite` = '' WHERE `user_id` = " . JSFactory::getUser()->user_id;
        $db->setQuery($query);
        $db->query();
    }

    $db = JFactory::getDBO();
    $query = "DELETE FROM `#__users_photos` WHERE `photo` = '" . $img . "'";
    $db->setQuery($query);
    $db->query();
}
print "success";

?>