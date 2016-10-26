<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

/*function acceptMeetUp($meet_id, $confirmation, $count_tokens, $my_id, $friend_id, $sponsor_id){
    $db = JFactory::getDBO();
    $query = "SELECT `count` "
           . "FROM {$db->quoteName('#__user_tokens')} "
           . "WHERE user_id = {$my_id}";
    $db->setQuery($query, 0, 0);
    $count_my_tokens = $db->loadObjectList();
    $count_my_tokens = (int)$count_my_tokens[0]->count;

    if ($count_my_tokens - $count_tokens >= 0) {
        $query = "UPDATE {$db->quoteName('#__meet_up')} "
               . "SET `confirmation` = {$confirmation}, `date_confirm` = '" . date("Y-m-d H:i:s") . "' "
               . "WHERE `id` = {$meet_id}";
        $db->setQuery($query);
        $db->query();

        $query = "DELETE "
               . "FROM {$db->quoteName('#__messages_meet_up')} "
               . "WHERE `meet_up_id` = {$meet_id}";
        $db->setQuery($query);
        $db->query();

        $query = "INSERT "
               . "INTO {$db->quoteName('#__messages_meet_up')} (`sender`, `recipient`, `meet_up_id`, `sponsor`, `confirmation`, `new_meet_up`, `date`) "
               . "VALUES ({$my_id}, {$friend_id}, {$meet_id}, {$sponsor_id}, {$confirmation}, 0, '" . date("Y-m-d H:i:s") . "')";
        $db->setQuery($query);
        $db->query();

        if($confirmation == 0){
            $query = "DELETE "
                   . "FROM {$db->quoteName('#__meet_up')} "
                   . "WHERE `id` = {$meet_id}";
            $db->setQuery($query);
            $db->query();

            $query = "UPDATE {$db->quoteName('#__user_tokens')} "
                   . "SET `count` = `count` + {$count_tokens} "
                   . "WHERE `user_id` = {$friend_id}";
            $db->setQuery($query);
            $db->query();
        } else {
            $query = "UPDATE {$db->quoteName('#__user_tokens')} "
                   . "SET `count` = `count` - {$count_tokens} "
                   . "WHERE `user_id` = {$my_id}";
            $db->setQuery($query);
            $db->query();
        }

        return 'confirmed';
    } else {
        return "error";
    }

}*/

function acceptMeetUp($meet_id, $confirmation, $count_tokens, $my_id, $friend_id, $sponsor_id){
    $db = JFactory::getDBO();
    $query = "UPDATE {$db->quoteName('#__meet_up')} "
        . "SET `confirmation` = {$confirmation}, `date_confirm` = '" . date("Y-m-d H:i:s") . "' "
        . "WHERE `id` = {$meet_id}";
    $db->setQuery($query);
    $db->query();

    $query = "DELETE "
        . "FROM {$db->quoteName('#__messages_meet_up')} "
        . "WHERE `meet_up_id` = {$meet_id}";
    $db->setQuery($query);
    $db->query();

    $query = "INSERT "
        . "INTO {$db->quoteName('#__messages_meet_up')} (`sender`, `recipient`, `meet_up_id`, `sponsor`, `confirmation`, `new_meet_up`, `date`) "
        . "VALUES ({$my_id}, {$friend_id}, {$meet_id}, {$sponsor_id}, {$confirmation}, 0, '" . date("Y-m-d H:i:s") . "')";
    $db->setQuery($query);
    $db->query();

    if($confirmation == 0){
        $query = "DELETE "
            . "FROM {$db->quoteName('#__meet_up')} "
            . "WHERE `id` = {$meet_id}";
        $db->setQuery($query);
        $db->query();

        $query = "UPDATE {$db->quoteName('#__user_tokens')} "
            . "SET `count` = `count` + {$count_tokens} "
            . "WHERE `user_id` = {$friend_id}";
        $db->setQuery($query);
        $db->query();
    } else {
        $Config = JSFactory::getConfig();
        $modelNotes = JSFactory::getModel('notifications', 'jshop');
        $modelNotes->addNote($my_id, $friend_id, $meet_id, JSFactory::getUser()->u_name, $Config->notifications[10]);
    }

    return 'confirmed';
}

function existsLincUp($lincup_id, $user_id, $friend_id){
    $db = JFactory::getDBO();
    $query = "SELECT COUNT(id) "
        . "FROM {$db->quoteName('#__meet_up')} "
        . "WHERE (sender = {$friend_id} and recipient = {$user_id}) and id = {$db->escape($lincup_id)} and confirmation = 0";
    $db->setQuery($query);
    $result = $db->loadResult();
    return $result == 1 ? true : false;
}

if(isset($_POST['accept']) && existsLincUp((int)$_POST['meet'], $current_user, (int)$_POST['friend'])){
    $accept = (int)$_POST['accept'];
    $meet_id = (int)$_POST['meet'];
    $friend = (int)$_POST['friend'];
    $modelMeeting = JSFactory::getModel('meeting', 'jshop');
    $meet_info = $modelMeeting->getMeetUpInfo($meet_id);
    $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
    $sponsor_tokens = $modelSponsors->getSponsorData($meet_info->sponsor, array('tokens'));
    $sponsor_tokens = $sponsor_tokens[0]['tokens'];
    echo acceptMeetUp($meet_id, $accept, $sponsor_tokens, $current_user, $friend, $meet_info->sponsor);
} else {
    echo "error";
}