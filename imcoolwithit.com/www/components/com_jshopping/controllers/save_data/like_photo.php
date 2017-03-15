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

if (isset($_POST['photo_id'])) {
    $photo_id = (int)$_POST['photo_id'];

    $modelUser = JSFactory::getModel('user', 'jshop');
    $modelUser->setLikePhoto($photo_id);

    if(isset($_POST['user_id']) && (int)$_POST['user_id']  > 0){
        $Config = JSFactory::getConfig();
        $modelNotes = JSFactory::getModel('notifications', 'jshop');
        $modelNotes->addNote(JSFactory::getUser()->user_id, (int)$_POST['user_id'], JSFactory::getUser()->user_id, JSFactory::getUser()->u_name, $Config->notifications[13]);
    }
}