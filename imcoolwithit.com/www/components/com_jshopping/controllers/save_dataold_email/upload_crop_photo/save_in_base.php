<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

function deleteOldImage($user_id){
    $db = JFactory::getDBO();
    $query = "SELECT `photosite` "
           . "FROM {$db->quoteName('#__jshopping_users')} "
           . "WHERE `user_id` = {$user_id}";
    $db->setQuery($query);
    $photo_name = $db->loadResult();

    $conf = new JConfig();
    if(file_exists(JPATH_BASE . $conf->path_user_image_big . $photo_name)){
        unlink(JPATH_BASE . $conf->path_user_image_big . $photo_name);
    }
    if(file_exists(JPATH_BASE . $conf->path_user_image_medium . $photo_name)){
        unlink(JPATH_BASE . $conf->path_user_image_medium . $photo_name);
    }
    if(file_exists(JPATH_BASE . $conf->path_user_image_small . $photo_name)){
        unlink(JPATH_BASE . $conf->path_user_image_small . $photo_name);
    }
}

function setImage($image_name, $user_id){
    $db = JFactory::getDBO();
    $query = "UPDATE {$db->quoteName('#__jshopping_users')} "
           . "SET `photosite` = '{$image_name}' "
           . "WHERE `user_id` = {$user_id}";
    $db->setQuery($query);
    $db->query();

    return 'success';
}

if (isset($_POST['image_name'])) {
    deleteOldImage($current_user);
    $result = setImage($_POST['image_name'], $current_user);
}

?>