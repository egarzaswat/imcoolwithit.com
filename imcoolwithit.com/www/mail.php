<?php
include_once "mail_send/index.php";
$mail = new sendMail();
$mail->setTo('vanyatsurkan@gmail.com');
$mail->setSubject('Test message');
$mail->setMessage('text text text text text text text text text text text text text text text text Vanya text message');
$mail->Send();