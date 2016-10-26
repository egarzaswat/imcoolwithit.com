<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/mail_send/index.php' );
JFactory::getApplication('site')->initialise();

function answer()
{
    global $success;
    if ( $success ) echo "Success";
    if ( !$success ) echo "Error!";
}

$email = isset($_POST['email']) ? $_POST['email'] : '' ;
$config = new JConfig();
$count_tokens = $config->refer_friend_tokens_count;

$success = false;

$senderName = 'Cool';
$senderSubject = 'Refer Credits';
$senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";


if ($senderName) {
    $message = 'We are pleased to inform that you or your friend have received a request to friends. In this regard, we add you ' . $count_tokens .  ' credits. Invite more friends for more tokens.' . $email;
    $mail = new sendMail();
//    $mail->setTo('vanyatsurkan@gmail.com');
    $mail->setSubject($senderSubject);
    $mail->setMessage($message);
    $mail->setTo($email);
    $success = $mail->Send();
}

answer();