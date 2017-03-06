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
$jshopConfig = JSFactory::getConfig();
$conf = new JConfig();

$session = JFactory::getSession();
$get_cook = $session->get('declined_partners');

if(!$get_cook){
    $get_cook = array();
}
if($_POST['id']){
    if(!in_array($_POST['id'], $get_cook)) {
        array_push($get_cook, $_POST['id']);
    }
}
$session->set('declined_partners', $get_cook);
$_SESSION['declined_partners'] = $get_cook;

print 'success';