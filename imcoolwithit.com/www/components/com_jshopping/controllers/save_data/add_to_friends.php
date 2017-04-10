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

function addUserToFriends($myId, $userId){
    $config = new JConfig();
    $count_tokens = $config->count_tokens_add_to_friends;

    // Count my tokens
    $db = JFactory::getDBO();
    $query = "SELECT `count` "
           . "FROM {$db->quoteName('#__user_tokens')} "
           . "WHERE user_id = {$myId}";
    $db->setQuery($query, 0, 0);
    $count_my_tokens = $db->loadObjectList();
    $count_my_tokens = (int)$count_my_tokens[0]->count;

    if ($count_my_tokens - $count_tokens >= 0) {
        // You have sent a request ?
        $query = "SELECT * "
               . "FROM {$db->quoteName('#__friends')} "
               . "WHERE `sender` = {$myId} and `reciper` = {$db->escape($userId)} and `confirmation` = 0";
        $db->setQuery($query, 0, 0);
        $result_send = $db->loadAssocList();
        if(count($result_send) >= 1){
            return "confirm exists";
        }

        // you came request ?
        $query = "SELECT * "
               . "FROM {$db->quoteName('#__friends')} "
               . "WHERE `sender` = {$db->escape($userId)} and `reciper` = {$myId}  and `confirmation` = 0";
        $db->setQuery($query, 0, 0);
        $result_rec = $db->loadObjectList();
        if(count($result_rec) >= 1){

            $query = "UPDATE {$db->quoteName('#__user_tokens')} "
                   . "SET `count` = " . ($count_my_tokens - $count_tokens) . " "
                   . "WHERE user_id = {$myId}";
            $db->setQuery($query);
            $db->query();

            $query = "SELECT referrer "
                   . "FROM {$db->quoteName('#__friends_refer')} "
                   . "WHERE ((referrer = {$db->escape($userId)} AND recipient = {$myId}) OR (referrer = {$myId} AND recipient = {$db->escape($userId)}))";
            $db->setQuery($query);
            $referrer = $db->loadResult();
            if(isset($referrer) && $referrer != 0){
                $query = "UPDATE {$db->quoteName('#__user_tokens')} "
                       . "SET `count` = `count` + {$config->refer_friend_tokens_count} "
                       . "WHERE `user_id` = {$referrer}";
                $db->setQuery($query);
                $db->query();
            }

            $query = "UPDATE {$db->quoteName('#__friends')} "
                   . "SET `date_confirm` = '" . date("Y-m-d H:i:s") . "', `confirmation` = 1 "
                   . "WHERE id = {$result_rec[0]->id}";
            $db->setQuery($query);
            $db->query();

            if (JSFactory::isUserOffline($userId)){
                $Config = JSFactory::getConfig();
                $modelNotes = JSFactory::getModel('notifications', 'jshop');
                $modelNotes->addNote($myId, $userId, $myId, JSFactory::getUser()->u_name, $Config->notifications[4]);
            }

            return 'request confirmed';
        }

        $query = "UPDATE {$db->quoteName('#__user_tokens')} "
               . "SET `count`= " . ($count_my_tokens - $count_tokens) . " "
               . "WHERE user_id = {$myId}";
        $db->setQuery($query);
        $db->query();

        $query = "INSERT "
               . "INTO {$db->quoteName('#__friends')} (sender, reciper, date_send) "
               . "VALUES ({$myId}, {$db->escape($userId)}, '" . date("Y-m-d H:i:s") . "')";
        $db->setQuery($query);
        $db->query();

        if (JSFactory::isUserOffline($userId)){
            $Config = JSFactory::getConfig();
            $modelNotes = JSFactory::getModel('notifications', 'jshop');
            $modelNotes->addNote($myId, $userId, $myId, JSFactory::getUser()->u_name, $Config->notifications[3]);
        }

        return "request sent";
    } else {
        return "error";
    }
}

if (isset($_POST['user_id'])) {

     $user_id = (int)$_POST['user_id'];

    $db = JFactory::getDBO();
    $query = "SELECT `photosite` FROM `jproject_jshopping_users` WHERE `user_id` = " . $current_user;
    $db->setQuery($query);
    $result = $db->loadAssocList();
    $result = $result[0]['photosite'];

    if(!isset($result) || is_null($result) || $result == ''){
        echo JSFactory::getPopup("Sorry, you have to add a picture to send a friend request.");
        die;
    }
    echo addUserToFriends($current_user, $user_id);
}