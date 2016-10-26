<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

if(isset($_POST['username_email']) && isset($_POST['password'])){
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];
    $password = JUserHelper::getCryptedPassword($password);

    $db = JFactory::getDBO();
    if (filter_var($username_email, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT `user_id` "
               . "FROM {$db->quoteName('#__jshopping_users')} "
               . "WHERE `email` = '{$db->escape($username_email)}' AND `password` = '{$db->escape($password)}'";
    } else {
        $query = "SELECT `user_id` "
               . "FROM {$db->quoteName('#__jshopping_users')} "
               . "WHERE `u_name` = '{$db->escape($username_email)}' AND `password` = '{$db->escape($password)}'";
    }
    $db->setQuery($query);
    $user_id = $db->loadResult();

    if(!is_null($user_id) && (int)$user_id){
        $instance = JUser::getInstance($user_id);
        $session = JFactory::getSession();
        $session->set('user', $instance);

        echo 'success';
    } else {
        echo 'user does not exist';
    }
} else {
    echo 'error!';
}