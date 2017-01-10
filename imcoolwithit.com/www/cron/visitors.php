<?php
date_default_timezone_set('Etc/GMT-4');

define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
include_once JPATH_BASE . "/configuration.php";
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

$m_not = JSFactory::getModel('notifications', 'jshop');
$notifications = $m_not->getVisitorsNotes();

$user_not = array();
$id_arr = array();
foreach($notifications as $value){
    array_push($id_arr, $value['id']);
    if(!$user_not[$value['recipient']] || $user_not[$value['recipient']] === NULL){
        $user_not[$value['recipient']]['count'] = 1;
        $email = $user_not[$value['recipient']]['email'] = $value['email'];
        $user_id = $user_not[$value['recipient']]['user_id'] = $value['recipient'];
    } else {
        $user_not[$value['recipient']]['count'] += 1;
    }
}

foreach($user_not as $value){
    $message = $m_not->getMessage('New Visitor', $value['user_id'], $value['count']);
    $m_not->sendNotification($message, $value['email'], 'New Visitors');
}

if(count($id_arr)){
    $m_not->deleteNotes($id_arr);
}