<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();

$session = JFactory::getSession();


if ($current_user != -1 && $_POST['location_save'] && ($session->get('latitude') != $_POST['latitude'] && $session->get('longitude') != $_POST['longitude'])) {

    $session->set('latitude', $_POST['latitude']);
    $session->set('longitude', $_POST['longitude']);
    $session->set('location_message', $_POST['message']);

    $db = JFactory::getDBO();
    $query = "UPDATE `#__jshopping_users` "
           . "SET  longitude=" . $_POST['longitude'] . ", latitude=" . $_POST['latitude']
           . "WHERE user_id = {$current_user}";
    $db->setQuery($query);
    $db->query();
    print 'success';
} else {
    print 'error';
}