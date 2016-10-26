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

function existsLincUp($lincup_id, $user_id, $friend_id){
    $db = JFactory::getDBO();
    $query = "SELECT COUNT(id) "
           . "FROM {$db->quoteName('#__meet_up')} "
           . "WHERE ((sender = {$db->escape($user_id)} and recipient = {$db->escape($friend_id)}) or (sender = {$db->escape($friend_id)} and recipient = {$db->escape($user_id)})) and id = {$db->escape($lincup_id)} and occurred = 1";
    $db->setQuery($query);
    $result = $db->loadResult();
    return $result == 1 ? true : false;
}

function isAnswered($lincup_id, $user_id){
    $db = JFactory::getDBO();
    $query = "SELECT COUNT(id) "
           . "FROM {$db->quoteName('#__users_reviews')} "
           . "WHERE meet = {$db->escape($lincup_id)} and sender = {$db->escape($user_id)}";
    $db->setQuery($query);
    $result = $db->loadResult();
    return $result == 1 ? true : false;
}

function setReview($my_id, $user_id, $meet_id, $type){
    $db = JFactory::getDBO();

    if ($type == 1) {
        $query = "UPDATE {$db->quoteName('#__jshopping_users')} "
               . "SET `user_reviews` = `user_reviews` + 1 "
               . "WHERE `user_id` = {$db->escape($user_id)}";
        $db->setQuery($query);
        $db->query();
    }

    $query = "INSERT "
           . "INTO {$db->quoteName('#__users_reviews')} (`sender`, `recipient`, `meet`, `type`) "
           . "VALUES ({$my_id}, {$db->escape($user_id)}, {$db->escape($meet_id)}, {$type})";
    $db->setQuery($query);
    $db->query();

    if (JSFactory::isUserOffline($user_id)){
        $Config = JSFactory::getConfig();
        $modelNotes = JSFactory::getModel('notifications', 'jshop');
        $modelNotes->addNote($my_id, $user_id, $my_id, JSFactory::getUser()->u_name, $Config->notifications[6]);
    }

    $conf = new JConfig();
    $tokens_count = $conf->count_tokens_honest_review;
    $query = "UPDATE {$db->quoteName('#__user_tokens')} "
           . "SET `count` = `count` + {$tokens_count} "
           . "WHERE `user_id` = {$my_id}";
    $db->setQuery($query);
    $db->query();

    echo 'success';
}

if (isset($_POST['answers']) && existsLincUp((int)$_POST['meet_up_id'], $current_user, (int)$_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $meet_id = (int)$_POST['meet_up_id'];

    $answers = array();
    parse_str($_POST['answers'], $answers);

    if (!isAnswered($meet_id, $current_user)) {
        $type = 1;
        foreach ($answers as $temp) {
            if($temp == 0){
                $type = 0;
                break;
            }
        }
        setReview($current_user, $user_id, $meet_id, $type);
    }
}