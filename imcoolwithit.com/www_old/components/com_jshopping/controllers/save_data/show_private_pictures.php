<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
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

$conf = new JConfig();
$config['smtp_username'] = $conf->smtpuser;
$config['smtp_port']     = $conf->smtpport;
$config['smtp_host']     = $conf->smtphost;
$config['smtp_password'] = $conf->smtppass;
$config['smtp_debug']   = true;
$config['smtp_charset']  = 'UTF-8';
$config['smtp_from']     = 'Cool';

function smtpmail($mail_to, $subject, $message, $headers='') {
    global $config;
    $SEND = "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
    $SEND .= "Subject: =?" . $config['smtp_charset'].'?B?' . base64_encode($subject)."=?=\r\n";
    if ($headers) $SEND .= $headers."\r\n\r\n";
    else
    {
        $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
        $SEND .= "MIME-Version: 1.0\r\n";
        $SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
        $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
        $SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
        $SEND .= "To: $mail_to <$mail_to>\r\n";
        $SEND .= "X-Priority: 3\r\n\r\n";
    }
    $SEND .=  $message."\r\n";
    if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
        if ($config['smtp_debug']) echo $errno."&lt;br&gt;".$errstr;
        return false;
    }

    if (!server_parse($socket, "220", __LINE__)) return false;

    fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>I can not send HELO!</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, "AUTH LOGIN\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>I can not find the answer to an authorization request.</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Login authorization was not accepted by the server!</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
    if (!server_parse($socket, "235", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Password was not accepted by the server as a faithful! Authorization Error!</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>I can not send command MAIL FROM:</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>I can not send command RCPT TO:</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, "DATA\r\n");

    if (!server_parse($socket, "354", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>I can not send command DATA</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, $SEND."\r\n.\r\n");

    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Unable to send the message body. The letter has been sent not!</p>';
        fclose($socket);
        return false;
    }
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    return TRUE;
}

function server_parse($socket, $response, $line = __LINE__) {
    global $config;
    while (@substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            if ($config['smtp_debug']) echo "<p>Problems sending mail!</p>$response<br>$line<br>";
            return false;
        }
    }
    if (substr($server_response, 0, 3) != $response) {
        if ($config['smtp_debug']) echo "<p>Problems sending mail!</p>$server_response вместо $response<br>$line<br>";
        return false;
    }
    return true;
}

$user_id = (int)$_POST['user_id'];
$adv_email = $_POST['adv_email'];
$adv_name = $_POST['adv_name'];

$senderName = 'Cool';
$senderSubject = 'Private photos';
$senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
$recipientEmail = $adv_email;

if ($senderName) {
    $headers = "Content-Type: text/html; From: " . '=?utf-8?B?'.base64_encode($senderName).'?=' . " <" . $senderEmail . ">";
    $subject = $senderSubject;
    $message = '
Hi ' . $adv_name . ',
<a href="http://' .  $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $my_id . '">' . $my_name . '</a> just viewed your private pictures.
Have a good one!
- The Cool With It™ team.';
    $success = smtpmail($recipientEmail, $subject, $message, $headers);
}

takeToken($my_id, $conf->count_tokens_view_private_photos);