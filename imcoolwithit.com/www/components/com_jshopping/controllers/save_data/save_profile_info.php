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

$db = JFactory::getDBO();

if(isset($_POST['looking_for']) && $_POST['looking_for'] !== null){
    $query = "UPDATE `#__jshopping_users` SET `looking_for` = " . $_POST['looking_for'] ." WHERE `user_id` = " . JSFactory::getUser()->user_id;
    $db->setQuery($query);
    $db->query();
    unset($_POST['looking_for']);
}


$query = "insert into `#__user_info` ";

$keys = "";
$values = "";
$duplicate = "";

$i = 0;
foreach($_POST as $key => $value){
    $value = htmlspecialchars($value);
    $value = addslashes($value);
    if($i==0){
        $keys .= '`' . $key . '`';
        $values .= "'" . $value . "'";
        $duplicate .= "`" . $key . "` = '" . $value . "'";
    } else {
        $keys .= ', `' . $key . '`';
        $values .= ", '" . $value . "'";
        $duplicate .= ", `" . $key . "` = '" . $value . "'";
    }
    $i++;
}
$query .= '( `user_id`, ' . $keys . ') values (' . JSFactory::getUser()->user_id . ', ' . $values . ') on duplicate key update ' .$duplicate;

$db->setQuery($query);
$db->query();

echo 'success';

?>
