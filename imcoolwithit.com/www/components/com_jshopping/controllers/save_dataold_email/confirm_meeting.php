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

function confirmMeeting($code, $meet, $count_tokens){
    $db = JFactory::getDBO();
    $query = "SELECT `code` "
           . "FROM {$db->quoteName('#__meet_up')} "
           . "WHERE `id` = {$db->escape($meet)}";
    $db->setQuery($query);
    $result = $db->loadAssocList();
    $result = $result[0]['code'];

    if($result == $code){
        $query = "UPDATE {$db->quoteName('#__meet_up')} "
               . "SET `occurred` = 1, `occurred_date` = '" . date("Y-m-d H:i:s") . "' "
               . "WHERE `id` = {$db->escape($meet)}";
        $db->setQuery($query);
        $db->query();

        $query = "UPDATE {$db->quoteName('#__user_tokens')} U "
               . "SET U.`count` = U.`count` + {$count_tokens} "
               . "WHERE U.`user_id` = "
               . "(SELECT `sender` FROM {$db->quoteName('#__meet_up')} WHERE `id` = {$db->escape($meet)}) OR  U.`user_id` = (SELECT `recipient` FROM {$db->quoteName('#__meet_up')} WHERE `id` = {$db->escape($meet)})";
        /*$query = "UPDATE {$db->quoteName('#__user_tokens')} U "
            . "SET U.`count` = U.`count` + {$count_tokens} "
            . "WHERE U.`user_id` = (SELECT `sender` FROM {$db->quoteName('#__meet_up')} WHERE `id` = {$db->escape($meet)})";*/
        $db->setQuery($query);
        $db->query();

        $query = "DELETE "
               . "FROM {$db->quoteName('#__messages_meet_up')} "
               . "WHERE `meet_up_id` = {$db->escape($meet)}";
        $db->setQuery($query);
        $db->query();

        return 'confirmed';
    } else {
        return 'error';
    }
}

if(isset($_POST['code'])){
    $code = $_POST['code'];
}
if(isset($_POST['meet'])){
    $meet = (int)$_POST['meet'];
}

$modelMeeting = JSFactory::getModel('meeting', 'jshop');
$meet_info = $modelMeeting->getMeetUpInfo($meet);
$modelSponsors = JSFactory::getModel('sponsors', 'jshop');
$sponsor_tokens = $modelSponsors->getSponsorData($meet_info->sponsor, array('tokens'));

if (isset($code) && isset($meet)) {
    echo confirmMeeting($code, $meet, $sponsor_tokens[0]['tokens']);
} else {
    echo "error";
}