<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/controllers/bookmarks.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

function addUserToBookmark($userId, $userIdAddToBookmarks){
    $db = JFactory::getDBO();
    $date = date("Y-m-d H:i:s");

    $query = "INSERT "
           . "INTO {$db->quoteName('#__bookmarks')} "
           . "SET `sender` = '{$userId}', `reciper` = '{$db->escape($userIdAddToBookmarks)}', `date` = '{$date}'";

    $db->setQuery($query);
    $db->query();

    echo "success";
}

if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    addUserToBookmark($current_user, $user_id);
}