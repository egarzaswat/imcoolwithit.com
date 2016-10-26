<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/controllers/member.php' );

JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');

JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

if(isset($_POST['username']) && ($_POST['username'] != "")){

    $username = $_POST['username'];

    $db = JFactory::getDBO();

    $query = "SELECT COUNT(user_id) "
           . "FROM {$db->quoteName('#__jshopping_users')} "
           . "WHERE u_name = '{$username}' and user_id != {$current_user}";

    $db->setQuery($query);
    $result = $db->loadResult();

    echo $result == 0 ? 'success' : 'exists';
}