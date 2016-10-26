<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/controllers/bookmarks.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

$current_user = JSFactory::getUser()->user_id;
$db = JFactory::getDBO();
$conf = new JConfig();

$query = "UPDATE `#__user_tokens` SET `count`  = `count` - " . $conf->count_tokens_set_invisible . " WHERE `user_id` = " . $current_user;
$db->setQuery($query);
$db->query();

$date_invisible = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . " +1days"));
$query = "UPDATE `#__jshopping_users` SET `invisible_to` = '" . $date_invisible . "' WHERE `user_id` = " . $current_user;
$db->setQuery($query);
$db->query();

echo "success";