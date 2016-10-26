<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

$m_not = JSFactory::getModel('notifications', 'jshop');

$db = JFactory::getDbo();
$query = "SELECT P.product_id, P.`name_" . JSFactory::getLang()->lang . "` AS name, P.`longitude_" . JSFactory::getLang()->lang . "` as longitude, P.`latitude_" . JSFactory::getLang()->lang . "` as latitude, EX.distance "
       . "FROM {$db->quoteName('#__jshopping_products')} AS P "
       . "LEFT JOIN {$db->quoteName('#__jshopping_products_to_categories')} AS C ON P.product_id = C.product_id "
       . "LEFT JOIN {$db->quoteName('#__products_offer_extra_options')} AS EX ON P.product_id = EX.product_id "
       . "WHERE C.category_id = 1 and `product_date_added` > NOW() - INTERVAL 1 DAY";
$db->setQuery($query);
$surveys = $db->loadAssocList();

foreach ($surveys as $key => $value){
    $query = "SELECT user_id "
           . "FROM {$db->quoteName('#__jshopping_users')} "
           . "WHERE (3958.0*acos( sin(" . ($value['latitude'] / 57.29577951) . ")*sin(`latitude`/57.29577951) + cos(" . ($value['latitude'] / 57.29577951) . ")*cos(`latitude`/57.29577951)*cos(" . $value['longitude'] / 57.29577951 . " - `longitude`/57.29577951)) < {$value['distance']})";
    $db->setQuery($query);
    $users = $db->loadAssocList();
    foreach ($users as $user_key => $user_value){
        $Config = JSFactory::getConfig();
        $m_not = JSFactory::getModel('notifications', 'jshop');
        $m_not->addNote($value['product_id'], $user_value['user_id'], $value['product_id'], $value['name'], $Config->notifications[8]);
    }
}

$query = "SELECT P.product_id, P.`name_" . JSFactory::getLang()->lang . "` AS name, P.`longitude_" . JSFactory::getLang()->lang . "` as longitude, P.`latitude_" . JSFactory::getLang()->lang . "` as latitude "
    . "FROM {$db->quoteName('#__jshopping_products')} AS P "
    . "LEFT JOIN {$db->quoteName('#__jshopping_products_to_categories')} AS C ON P.product_id = C.product_id "
    . "WHERE C.category_id != 1 and `product_date_added` > NOW() - INTERVAL 1 DAY";
$db->setQuery($query);
$lincups = $db->loadAssocList();

foreach ($lincups as $key => $value){
    $query = "SELECT user_id "
        . "FROM {$db->quoteName('#__jshopping_users')} "
        . "WHERE (3958.0*acos( sin(" . ($value['latitude'] / 57.29577951) . ")*sin(`latitude`/57.29577951) + cos(" . ($value['latitude'] / 57.29577951) . ")*cos(`latitude`/57.29577951)*cos(" . $value['longitude'] / 57.29577951 . " - `longitude`/57.29577951)) < `distance`)";
    $db->setQuery($query);
    $users = $db->loadAssocList();
    foreach ($users as $user_key => $user_value){
        $Config = JSFactory::getConfig();
        $m_not = JSFactory::getModel('notifications', 'jshop');
        $m_not->addNote($value['product_id'], $user_value['user_id'], $value['product_id'], $value['name'], $Config->notifications[9]);
    }
}

/* -------- Meet Up expires message-------- */
$config = new JConfig();
$query = "SELECT MU.`id`, MU.`sender`, MU.`recipient`, S.`u_name` as sender_name, R.`u_name` as recipient_name "
       . "FROM `jproject_meet_up` AS MU "
       . "LEFT JOIN `jproject_jshopping_users` AS S ON S.user_id = MU.sender "
       . "LEFT JOIN `jproject_jshopping_users` AS R ON R.user_id = MU.recipient "
       . "WHERE `confirmation` = 1 AND `occurred` = 0 AND `date_confirm` < NOW() - INTERVAL " . ($config->day_expires_meet_up - 1) . " DAY GROUP BY `sender` ";
$db->setQuery($query);
$result = $db->loadAssocList();
$Config = JSFactory::getConfig();
$m_not = JSFactory::getModel('notifications', 'jshop');
foreach ($result as $key => $value){
    $m_not->addNote($value['sender'], $value['recipient'], $value['id'], $value['sender_name'], $Config->notifications[2]);
}

/* ----------- Test Cron ------------ */
$db = JFactory::getDbo();
$query = "INSERT "
       . "INTO `cron` (`date`) "
       . "VALUES ('" . date("Y-m-d H:i:s") . "')";
$db->setQuery($query);
$db->execute();