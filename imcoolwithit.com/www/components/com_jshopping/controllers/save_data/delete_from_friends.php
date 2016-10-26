<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

function DeleteUserFormFriends($userId, $my_id){
    $db = JFactory::getDBO();
    $query = "DELETE "
           . "FROM {$db->quoteName('#__friends')} "
           . "WHERE (reciper = {$db->escape($userId)} AND sender = {$my_id}) OR (reciper = {$my_id} AND sender = {$db->escape($userId)})";
    $db->setQuery($query);
    $db->query();

    echo "success";
}

if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    DeleteUserFormFriends($user_id, $current_user);
} else {
    echo "error";
}