<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

function answer()
{
    global $success;
    if ( $success ) echo "Success";
    if ( !$success ) echo "Error!";
}

$sponsor = $_POST['sponsor'];

if ( $sponsor) {
    $message = "Sponsor: $sponsor";
    $subject = 'New Linc Up Occurred';

    $mail = new sendMail();
    $mail->setSubject($subject);
    $mail->setMessage($message);
    $mail->setTo($conf->smtpuser);
    $success = $mail->Send();
}

answer();