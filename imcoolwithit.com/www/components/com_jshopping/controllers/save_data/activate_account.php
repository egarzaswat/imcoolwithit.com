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

// Return HTML
function answer()
{
    global $success;

    if($success){
        echo 'success';
    } else {
        echo 'error';
    }
}

$success = false;
if (isset($_POST['block']) && ($_POST['block'] == 0 || $_POST['block'] == 1 )) {
    $modelUser = JSFactory::getModel('user', 'jshop');
    $success = $modelUser->setUserActive(JSFactory::getUser()->user_id, $_POST['block']);
} else {
    echo 'error block';
    exit;
}

answer();