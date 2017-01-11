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

$emails = isset($_POST['emails']) ? $_POST['emails'] : '' ;
$emails = str_replace(' ','',$emails);
$emails = explode(',', $emails);

$success = false;

$senderName = 'Cool';
$senderSubject = 'Refer';
$senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
$recipientEmails = array();
foreach($emails as $temp) {
    array_push($recipientEmails, isset($temp) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $temp) : "");
}

if ($senderName) {
    $message = '

        Hello!
        Your friend wants to invite you to be cool with him.

        Please click on the link below to move to the site:
        https://' . $_SERVER['SERVER_NAME'] . '/index.php?referrer=' . $user_id;

    $mail = new sendMail();
    $mail->setSubject($senderSubject);
    $mail->setMessage($message);
    $mail->setTo('vanyatsurkan@gmail.com');
    $mail->setTo($recipientEmail);
    $success = $mail->Send();

    foreach($recipientEmails as $email){
        $mail->setTo('vanyatsurkan@gmail.com');
        $mail->setTo($email);
    }
}

answer();