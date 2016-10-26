<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$img = explode('/', $_POST['image']);
$img = $img[count($img)-1];

setPrivate($img);

function setPrivate($img){
    $db = JFactory::getDBO();
    $query = "UPDATE `#__users_photos` SET `private` = (`private` + 1) % 2 WHERE `photo` = '" . $img . "'";
        $db->setQuery($query);
    $db->query();
}

print "success";

?>