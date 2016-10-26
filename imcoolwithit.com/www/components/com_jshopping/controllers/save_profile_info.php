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
$query = "insert into `#__user_info` ";

$keys = "";
$values = "";
$dublicate = "";

$i = 0;
foreach($_POST as $key => $value){
    if($i==0){
        $keys .= '`' . $key . '`';
        $values .= "'" . $value . "'";
        $dublicate .= "`" . $key . "` = '" . $value . "'";
    } else {
        $keys .= ', `' . $key . '`';
        $values .= ", '" . $value . "'";
        $dublicate .= ", `" . $key . "` = '" . $value . "'";
    }
    $i++;
}
$query .= '( `user_id`, ' . $keys . ') values (' . JSFactory::getUser()->user_id . ', ' . $values . ') on duplicate key update ' .$dublicate;

$db->setQuery($query);
$db->query();

echo 'success';

?>
