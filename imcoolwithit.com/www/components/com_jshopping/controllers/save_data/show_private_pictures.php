<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/mail_send/index.php' );
JFactory::getApplication('site')->initialise();
$my_id = JSFactory::getUser()->user_id;
$my_name = JSFactory::getUser()->u_name;

function takeToken($user_id, $count_tokens){
    $db = JFactory::getDBO();
    $query = "UPDATE {$db->quoteName('#__user_tokens')} "
           . "SET `count` = `count` - {$count_tokens} "
           . "WHERE `user_id` = {$user_id}";
    $db->setQuery($query);
    $db->query();
}

$user_id = (int)$_POST['user_id'];
$adv_email = $_POST['adv_email'];
$adv_name = $_POST['adv_name'];

$senderName = 'Cool';
$senderSubject = 'Private photos';
$senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
$recipientEmail = $adv_email;

if ($senderName) {
    $message = '
        Hi ' . $adv_name . ',
        <a href="https://' .  $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $my_id . '">' . $my_name . '</a> just viewed your private pictures.
        Have a good one!
        - The I`m Cool with it team';
    $conf = new JConfig();

    $mail = new sendMail();
    $mail->setSubject($senderSubject);
    $mail->setMessage($message);
//    $mail->setTo('vanyatsurkan@gmail.com');
    $mail->setTo($recipientEmail);
    $success = $mail->Send();
}

takeToken($my_id, $conf->count_tokens_view_private_photos);