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
$my_id = JSFactory::getUser()->user_id;
$friend_id = (int)$_POST['friend_id'];
$modelFriends = JSFactory::getModel('friends', 'jshop');
$isFriends = $modelFriends->getIsFrieds($friend_id) && !$modelFriends->getIsAccept($friend_id) && ($friend_id != $my_id);
if (!$isFriends) { exit; }
$modelMeessaging = JSFactory::getModel('messaging', 'jshop');
print $modelMeessaging->getCountChatMessages($my_id, $friend_id);
?>