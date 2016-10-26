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
$jshopConfig = JSFactory::getConfig();

$message = $_POST['class'];
$message = explode(' ', $message);
$message = $message[0];
$modelMessaging = JSFactory::getModel('messaging', 'jshop');
$smile = $modelMessaging->getSmile($message);
print $smile;

?>
