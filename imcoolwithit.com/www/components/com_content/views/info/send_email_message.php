<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/mail_send/index.php' );

function answer()
{
    global $success;
    if ( $success ) echo "Success";
    if ( !$success ) echo "Error!";
}

$success = false;
$senderName = $_POST['name'];
$senderSubject = $_POST['subject'];
$senderEmail = isset( $_POST['email'] )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['email'] ) : "";
$message = isset( $_POST['message'] )? preg_replace( "/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/", "", $_POST['message'] ) : "";

$search = array ("'<script[^>]*?>.*?</script>'si",
    "'<[\/\!]\S[^<>]*?>'si",
);

$replace = array ("",
    ""
);

$message_s = preg_replace($search, $replace, $message);
if($message_s != $message) {
    $success = true;
    answer();
    exit;
}

if ( $senderName && $message) {
    $message = "Sender: " . $senderName . "\r\nE mail: " . $senderEmail . "\r\nMessage: ". $message;
    $conf = new JConfig();

    $mail = new sendMail();
    $mail->setSubject($senderSubject);
    $mail->setMessage($message);
//    $mail->setTo('vanyatsurkan@gmail.com');
    $mail->setTo($conf->smtpuser);
    $success = $mail->Send();
}

answer();