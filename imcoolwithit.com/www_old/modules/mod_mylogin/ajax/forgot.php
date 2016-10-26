<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

$conf = new JConfig();
$config['smtp_username'] = $conf->smtpuser;
$config['smtp_port']     = $conf->smtpport;
$config['smtp_host']     = $conf->smtphost;
$config['smtp_password'] = $conf->smtppass;
$config['smtp_debug']   = true;
$config['smtp_charset']  = 'UTF-8';
$config['smtp_from']     = 'Cool';

if(!isset($_POST['action_type'])){
    echo 'error';
    exit;
}

if($_POST['action_type'] == 'send_email' ) {
    if (!isset($_POST['email'])) {
        echo 'error';
        exit;
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            echo 'invalid email';
            exit;
        } else {
            $db = JFactory::getDBO();
            $query = "SELECT `user_id` "
                   . "FROM {$db->quoteName('#__jshopping_users')} "
                   . "WHERE `email` = '{$_POST['email']}'";
            $db->setQuery($query);
            $user_id = $db->loadResult();

            if (is_null($user_id) || (int)$user_id == 0) {
                echo 'unverified email';
                exit;
            }

            $hash = md5(rand(0, 1000));

            $query = "INSERT "
                   . "INTO {$db->quoteName('#__forgot')} (`user_id`, `hash`) "
                   . "VALUES ({$user_id}, '{$hash}')";
            $db->setQuery($query);
            $db->query();

            $success = false;
            $senderName = 'Cool';
            $senderSubject = 'Forgot Password';
            $senderEmail = isset($conf->mailfrom) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom) : "";
            $recipientEmails = array();
            $headers = "Content-Type: text/html;\r\n";
            $headers .= "From: " . '=?utf-8?B?'.base64_encode($senderName).'?=' . " <" . $senderEmail . ">\r\n";
            $headers .= "To: '" . $_POST['email'] . "'<" . $_POST['email'] . ">\r\n";
            $subject = $senderSubject;
            $message = '
            Hi, please click this &nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_LOGIN') . '?u=' . $user_id . '&h=' . $hash . '">link</a> to reset your password.<br>
            Thanks,<br>
            The Cool with it team<br>
            Thank you';
            $success = smtpmail($_POST['email'], $subject, $message, $headers);
            echo "success";
            exit;
        }
    }
}

if($_POST['action_type'] == 'update_password' ){
    if(!isset($_POST['password1']) || !isset($_POST['password2']) || !isset($_POST['user']) || !isset($_POST['hash'])){
        echo 'error';
        exit;
    } else {
        if(strlen($_POST['password1']) < 6 || strlen($_POST['password2']) < 6 || ($_POST['password1'] != $_POST['password2'])){
            echo 'password error';
            exit;
        } else {
            $user = (int)$_POST['user'];
            $hash = $_POST['hash'];
            $password = JUserHelper::getCryptedPassword($_POST['password1']);
            $db = JFactory::getDBO();
            $query = "SELECT COUNT(`id`) "
                   . "FROM {$db->quoteName('#__forgot')} "
                   . "WHERE `user_id` = {$user} and `hash` = '{$db->escape($hash)}'";
            $db->setQuery($query);
            $count = $db->loadResult();

            if ($count > 0) {
                $query = "DELETE "
                       . "FROM {$db->quoteName('#__forgot')} "
                       . "WHERE `user_id` = {$user}";
                $db->setQuery($query);
                $db->query();

                $query = "UPDATE {$db->quoteName('#__jshopping_users')} "
                       . "SET `password` = '{$password}' "
                       . "WHERE `user_id` = {$user}";
                $db->setQuery($query);
                $db->query();

                $query = "UPDATE {$db->quoteName('#__users')} "
                       . "SET `password` = '{$password}' "
                       . "WHERE `id` = {$user}";
                $db->setQuery($query);
                $db->query();

                echo "success";
                exit;
            } else {
                echo 'hash error';
                exit;
            }
        }
    }
}

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