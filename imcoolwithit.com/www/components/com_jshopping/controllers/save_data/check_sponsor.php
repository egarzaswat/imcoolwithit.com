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
$current_user = JSFactory::getUser()->user_id;

function checkSponsor($sponsor, $code){
    $db = JFactory::getDBO();
    $query = "SELECT `product_ean` FROM `jproject_jshopping_products` WHERE `product_id` = " . $sponsor;
    $db->setQuery($query);
    $result = $db->loadAssocList();
    $result = $result[0]['product_ean'];

    if($result == $code){
        return 'success';
    } else {
        return 'error';
    }
}

if(isset($_POST['sponsor']) && isset($_POST['code'])){
    $sponsor = $_POST['sponsor'];
    $code = $_POST['code'];
}

if (isset($sponsor) && isset($code)) {
    echo checkSponsor($sponsor, $code);
} else {
    echo "error";
}