<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

function rejectUser($my_id, $userId){
    $db = JFactory::getDBO();
    $query = "SELECT * "
           . "FROM {$db->quoteName('#__rejected_users')} "
           . "WHERE `id_user_active` = {$my_id} and `id_user_guest` = {$db->escape($userId)}";
    $db->setQuery($query, 0, 0);
    $result_send = $db->loadAssocList();

    if(count($result_send) == 0){
        $query = "INSERT "
               . "INTO {$db->quoteName('#__rejected_users')} (`id_user_active`, `id_user_guest`, `date`) "
               . "VALUES ({$my_id}, {$db->escape($userId)}, '" . date("Y-m-d H:i:s") . "')";
        $db->setQuery($query);
        $db->query();
    } else {
        $query = "UPDATE {$db->quoteName('#__rejected_users')} "
               . "SET `date` = '" . date("Y-m-d H:i:s") . "' "
               . "WHERE `id` = {$result_send[0]['id']}";
        $db->setQuery($query);
        $db->query();
    }

    $config=new JConfig();
    // Count user tokens
    $query = "UPDATE {$db->quoteName('#__user_tokens')} "
           . "SET `count` = `count` + {$config->count_tokens_add_to_friends} "
           . "WHERE user_id = {$db->escape($userId)}";
    $db->setQuery($query);
    $db->query();

    $query = "DELETE "
           . "FROM {$db->quoteName('#__friends')} "
           . "WHERE `sender` = {$db->escape($userId)} and `reciper` = {$my_id}";
    $db->setQuery($query);
    $db->query();

    if (JSFactory::isUserOffline($userId)){
        $Config = JSFactory::getConfig();
        $modelNotes = JSFactory::getModel('notifications', 'jshop');
        $modelNotes->addNote($my_id, $userId, $my_id, JSFactory::getUser()->u_name, $Config->notifications[5]);
    }

    echo "success";
}

if(isset($_POST['user_id'])){
    $user_id = (int)$_POST['user_id'];
    rejectUser($current_user, $user_id);
} else {
    echo "error";
}