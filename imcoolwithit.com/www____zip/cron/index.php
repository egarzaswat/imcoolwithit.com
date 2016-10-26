<?php
date_default_timezone_set('America/New_York');

define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
include_once JPATH_BASE . "/configuration.php";
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

$config = new JConfig();

define('HOST', $config->host);
define('USER', $config->user);
define('PASSWORD', $config->password);
define('NAME_DB', $config->db);

$connect = mysql_connect(HOST, USER, PASSWORD) or die("Unable to connect" .mysql_error( ));

mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");

mysql_select_db(NAME_DB, $connect) or die ("Can’t select the specified database" .mysql_error());

/* ---------- Friends ---------- */
$query = "SELECT `sender`, COUNT(`sender`) FROM `jproject_friends` WHERE `confirmation` = 0 AND `date_send` < NOW() - INTERVAL " . $config->day_expires_add_to_friends . " DAY GROUP BY `sender`";
$result = mysql_query($query);

$query_update = "UPDATE `jproject_user_tokens` SET `count` = CASE ";

$mass = array();
while($tmp = mysql_fetch_row($result)){
    array_push($mass, $tmp);
    $query_update .= "WHEN `user_id` = " . $tmp[0] . " THEN (`count` + " . $config->count_tokens_add_to_friends*$tmp[1] . ") ";
}
$query_update .= "END WHERE `user_id` IN (";
foreach($mass as $k=>$v){
    if($k == 0){
        $query_update .= $v[0];
    } else {
        $query_update .= ", " . $v[0];
    }
}
$query_update .= ")";
mysql_query($query_update);

$query_delete = "DELETE FROM `jproject_friends` WHERE `confirmation` = 0 AND `date_send` < NOW() - INTERVAL " . $config->day_expires_add_to_friends . " DAY";
mysql_query($query_delete);

/* ---------- Meet Up ---------- */
$query = "SELECT MEET.`sender`, COUNT(MEET.`sender`), PROD.`tokens` FROM `jproject_meet_up` AS MEET LEFT JOIN `jproject_jshopping_products` AS PROD ON MEET.`sponsor` = PROD.`product_id` WHERE MEET.`confirmation` = 0 AND MEET.`date_sent` < NOW() - INTERVAL " . $config->day_expires_meet_up . " DAY GROUP BY MEET.`sender`, PROD.`product_id` ";
$result = mysql_query($query);

$meet_update = array();
while($tmp = mysql_fetch_row($result)){
    if( !isset($meet_update[$tmp[0]]) ){
        $meet_update[$tmp[0]] = $tmp[1]*$tmp[2];
    } else {
        $meet_update[$tmp[0]] = $meet_update[$tmp[0]] + $tmp[1]*$tmp[2];
    }
}

$query_update = "UPDATE `jproject_user_tokens` SET `count` = CASE ";
$mass = array();
foreach($meet_update as $k=>$v){
    $query_update .= "WHEN `user_id` = " . $k . " THEN (`count` + " . $v . ") ";
}
$query_update .= "END WHERE `user_id` IN (";
$tmp = 0;
foreach($meet_update as $k=>$v){
    if($tmp == 0){
        $query_update .= $k;
    } else {
        $query_update .= ", " . $k;
    }
    $tmp++;
}
$query_update .= ")";
mysql_query($query_update);

$query_delete_meet = "DELETE M, MESS FROM `jproject_meet_up` AS M LEFT JOIN `jproject_messages_meet_up` AS MESS ON M.id = MESS.meet_up_id WHERE M.`confirmation` = 0 AND M.`date_sent` < NOW() - INTERVAL " . $config->day_expires_meet_up . " DAY";
mysql_query($query_delete_meet);

/* -------- Not occurred Meet Up -------- */
$query = "SELECT MEET.`sender`, COUNT(MEET.`sender`), PROD.`tokens` FROM `jproject_meet_up` AS MEET LEFT JOIN `jproject_jshopping_products` AS PROD ON MEET.`sponsor` = PROD.`product_id` WHERE MEET.`confirmation` = 1 AND MEET.`occurred` = 0 AND MEET.`date_confirm` < NOW() - INTERVAL " . $config->day_expires_meet_up . " DAY GROUP BY MEET.`sender`, PROD.`product_id` ";
$result = mysql_query($query);

$update_array = array();
while($tmp = mysql_fetch_row($result)){
    $update_array[$tmp[0]] += $tmp[1]*$tmp[2];
}

$query_update = "UPDATE `jproject_user_tokens` SET `count` = CASE ";
$mass = array();
foreach($update_array as $k=>$v){
    $query_update .= "WHEN `user_id` = " . $k . " THEN (`count` + " . $v . ") ";
}
$query_update .= "END WHERE `user_id` IN (";
$tmp = 0;
foreach($update_array as $k=>$v){
    if($tmp == 0){
        $query_update .= $k;
    } else {
        $query_update .= ", " . $k;
    }
    $tmp++;
}
$query_update .= ")";
mysql_query($query_update);

$query_delete_meet = "DELETE M, MESS FROM `jproject_meet_up` AS M LEFT JOIN `jproject_messages_meet_up` AS MESS ON M.id = MESS.meet_up_id WHERE M.`confirmation` = 1 AND M.`occurred` = 0 AND M.`date_confirm` < NOW() - INTERVAL " . $config->day_expires_meet_up . " DAY";
mysql_query($query_delete_meet);

/* ---------- Complete Profile ---------- */
$query = "UPDATE `jproject_user_tokens` AS T LEFT JOIN `jproject_jshopping_users` AS U ON U.user_id = T.user_id SET T.count = (T.count + " . $config->count_tokens_complete_profile . "), U.date_complete = '" . date("Y-m-d H:i:s") . "' WHERE U.`complete_profile` = 1 AND U.`date_complete` < NOW() - INTERVAL 1 MONTH";
mysql_query($query);

/* ----------- Verify Email ------------ */
$query = "UPDATE `jproject_user_tokens` AS T LEFT JOIN `jproject_verification` AS V ON V.user_id = T.user_id SET T.count = (T.count + " . $config->verify_email_tokens_count . "), V.date = '" . date("Y-m-d H:i:s") . "' WHERE V.`date` < NOW() - INTERVAL 1 MONTH";
mysql_query($query);

/* ----------- Test Cron ------------ */
$query = "INSERT INTO `cron` (`date`) VALUES ('" . date("Y-m-d H:i:s") . "')";
mysql_query($query);