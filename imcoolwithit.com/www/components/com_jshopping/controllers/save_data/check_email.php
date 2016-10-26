<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );


if(isset($_POST['email']) && ($_POST['email'] != "")){

    $email = $_POST['email'];

    $db = JFactory::getDBO();

    $query = "SELECT COUNT(user_id) FROM `#__jshopping_users` WHERE `email` = '" . $email . "'";

    $db->setQuery($query);
    $result = $db->loadResult();

    echo $result == 0 ? 'success' : 'exists';
}
?>
