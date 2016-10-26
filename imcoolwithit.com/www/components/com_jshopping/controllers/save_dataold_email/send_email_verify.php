<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/mail_send/index.php' );
JFactory::getApplication('site')->initialise();
$user_id = JSFactory::getUser()->user_id;

function answer()
{
    global $success;
    if ( $success ) echo "Success";
    if ( !$success ) echo "Error!";
}

$email = isset($_POST['email']) ? $_POST['email'] : '' ;
$hash = md5(rand(0,1000));

$success = false;

$db = JFactory::getDBO();
$query = "SELECT active " . "FROM {$db->quoteName('#__verification')} WHERE user_id = {$user_id}";
$db->setQuery($query);
$result = $db->loadResult();

$active = $result == 1 ? true : false;

if(!$active) {

    $db = JFactory::getDBO();
    $query = "SELECT COUNT(*) " . "FROM {$db->quoteName('#__verification')} WHERE user_id = {$user_id}";
    $db->setQuery($query);
    $result = $db->loadResult();

    $exists = $result == 1 ? true : false;

    if(!$exists){
        $db = JFactory::getDBO();

        $query = "INSERT "
               . "INTO {$db->quoteName('#__verification')} (user_id, email, hash, active) "
               . "VALUES ({$user_id}, '{$db->escape($email)}', '{$hash}', 0)";

        $db->setQuery($query);
        $db->query();
    } else {
        $db = JFactory::getDBO();

        $query = "UPDATE {$db->quoteName('#__verification')} "
               . "SET email = '{$db->escape($email)}', hash = '{$hash}' "
               . "WHERE user_id = {$user_id}";

        $db->setQuery($query);
        $db->query();
    }

    $conf = new JConfig();
    $senderName = 'Cool';
    $senderSubject = 'Verification';
    $senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
    $recipientEmail = isset( $email )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $email ) : "";

    if ($senderName) {
        $message = '

Hello!
We need to verify your email before you get some extra tokens.

Please click on the link below to complete verification:
http://' . $_SERVER['SERVER_NAME'] . '/earntokens/verify_status?user=' . $user_id . '&email=' . $email . '&hash=' . $hash . '';

        $mail = new sendMail();
        $mail->setSubject($senderSubject);
        $mail->setMessage($message);
        $mail->setTo('vanyatsurkan@gmail.com');
        $mail->setTo($recipientEmail);
        $success = $mail->Send();
    }

}

answer();