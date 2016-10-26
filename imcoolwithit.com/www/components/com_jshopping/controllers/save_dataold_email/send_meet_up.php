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

function sendMeetUp($my_id, $friend_id, $sponsor_id, $count_tokens, $meet_code){
    // Count my tokens
    $db = JFactory::getDBO();
    $query = "SELECT `count` "
           . "FROM {$db->quoteName('#__user_tokens')} "
           . "WHERE user_id = {$my_id}";
    $db->setQuery($query, 0, 0);
    $count_my_tokens = $db->loadResult();

    if($count_my_tokens - $count_tokens >= 0){
        $query = "UPDATE {$db->quoteName('#__user_tokens')} "
               . "SET `count` = " . ($count_my_tokens - $count_tokens) . " "
               . "WHERE user_id = {$my_id}";
        $db->setQuery($query);
        $db->query();

        $query = "INSERT "
               . "INTO {$db->quoteName('#__meet_up')} (`sender`, `recipient`, `sponsor`, `code`, `date_sent`) "
               . "VALUES ({$my_id}, {$friend_id}, {$sponsor_id}, '{$meet_code}', '" . date("Y-m-d H:i:s") . "')";
        $db->setQuery($query);
        $db->query();
        $id_new_meet_up = $db->insertid();

        $query = "INSERT "
               . "INTO {$db->quoteName('#__messages_meet_up')} (`sender`, `recipient`, `meet_up_id`, `sponsor`, `new_meet_up`, `date`) "
               . "VALUES ({$my_id}, {$friend_id}, {$id_new_meet_up}, {$sponsor_id}, 1, '" . date("Y-m-d H:i:s") . "')";
        $db->setQuery($query);
        $db->query();

        if (JSFactory::isUserOffline($friend_id)){
            $Config = JSFactory::getConfig();
            $modelNotes = JSFactory::getModel('notifications', 'jshop');
            $modelNotes->addNote($my_id, $friend_id, $id_new_meet_up, JSFactory::getUser()->u_name, $Config->notifications[1]);
        }

        $array = array(
            'confirmed' => 'confirmed',
            'meet_up' => $id_new_meet_up
        );
        return json_encode($array);
    } else {
        $array = array(
            'confirmed' => 'error',
            'meet_up' => ''
        );
        return json_encode($array);
    }
}

if (isset($_POST['friend']) && isset($_POST['sponsor'])) {
    $friend_id = (int)$_POST['friend'];
    $modelFriends = JSFactory::getModel('friends', 'jshop');
    $isFriends = $modelFriends->getIsFrieds($friend_id) && ($friend_id != $my_id);
    if (!$isFriends) { exit; }
    $sponsor_id = (int)$_POST['sponsor'];
    $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
    if (!$modelSponsors->existSponsor($sponsor_id)) { exit; }
    $sponsor_data = $modelSponsors->getSponsorData($sponsor_id, array('tokens', 'product_ean'));
    echo sendMeetUp($current_user, $friend_id, $sponsor_id, $sponsor_data[0]['tokens'], $sponsor_data[0]['product_ean']);
} else {
    $array = array(
        'confirmed' => 'error',
        'meet_up' => ''
    );
    echo json_encode($array);
}