<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

$m_not = JSFactory::getModel('notifications', 'jshop');
$notifications = $m_not->getNotes();

$id_arr = array();
foreach ($notifications as $key => $value){
    $m_not->sendNotification($value['message'], $value['email'], $value['type']);
    array_push($id_arr, $value['id']);
}

$m_not->deleteNotes($id_arr);

/* ----------- Test Cron ------------ */
$db = JFactory::getDbo();
$query = "INSERT "
       . "INTO `cron` (`date`) "
       . "VALUES ('" . date("Y-m-d H:i:s") . "')";
$db->setQuery($query);
$db->execute();