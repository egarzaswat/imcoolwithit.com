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

if (is_numeric($_POST['age']) && $_POST['age'] >= 18) { $birthday = (int)$_POST['age']; } else { echo 'error birthday'; exit; }

if (is_numeric($_POST['zip']) && (strlen($_POST['zip']) == 5)) {
    $postal_code = $_POST['zip'];
    $zip_json = file_get_contents('http://api.zippopotam.us/us/' . $postal_code);
    $zip_array = json_decode($zip_json, true);
    if (count($zip_array) != 0){
        $city = $zip_array['places'][0]['place name'];
        $state = $zip_array['places'][0]['state abbreviation'];
        $longitude = $zip_array['places'][0]['longitude'];
        $latitude = $zip_array['places'][0]['latitude'];

        $session = JFactory::getSession();
        $session->set('latitude', $latitude);
        $session->set('longitude', $longitude);
    } else {
        echo 'error zip';
        exit;
    }
} else {
    echo 'error zip';
    exit;
}

if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];
} else {
    echo 'error email';
    exit;
}

if (strlen($_POST['password']) >= 6) { $password = $_POST['password']; } else { echo 'error password'; exit; }

$creating_message = createUser($birthday, $postal_code, $city, $state, $longitude, $latitude, $email, $password);

if ($creating_message == 'success') {
    $success = false;
    $senderName = 'Support';
    $senderSubject = 'Welcome to Cool With It™';
    $senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
    $recipientEmails = array();
    $headers = "Content-Type: text/html;\r\n";
    $headers .= "From: " . '=?utf-8?B?'.base64_encode($senderName).'?=' . " <" . $senderEmail . ">\r\n";
    $headers .= "To: '" . $email . "'<" . $email . ">\r\n";
    $subject = $senderSubject;
    $message = '
    Welcome to Cool With It™…!<br>
    Thanks for setting up your account and for letting us be a part of helping you find that special someone!<br>
    Your username:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT') . '">' . $_POST['email'] . '</a>.<br>
    Your Password: ' . $password . '.<br>
    You can reset your password at any time here:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT') .'" >Reset Password</a>.<br>
    Our goal is to create a community whose members are 100% cool with who they are and what they are looking for, and in turn can learn and share about new places and happenings, all while getting the chance to meet other like-minded people!<br>
    Feel free to post any great date ideas you may have had or would like to on our&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_COMMUNITY_PAGE') .'" >community page</a> and thanks again for joining.<br>
    We aim to be the best dating site and more! That you’ll ever use.<br>
    Sincerely,<br>
    The Cool with it…team.';
    $success = smtpmail($email, $subject, $message, $headers);
    echo 'success';
} else {
    echo 'exist';
    exit;
}

function createUser($age, $zip, $city, $state, $longitude, $latitude, $email, $password){
    $instance = JUser::getInstance();
    $instance->name = 'new_user';
    $instance->email = $email;
    $instance->password = JUserHelper::getCryptedPassword($password);
    $instance->birthday = date('Y') - $age;
    $instance->birthday = $instance->birthday . '-01-01';
    $instance->guest = false;
    $instance->zip = $zip;
    $instance->city = $city;
    $instance->state = $state;
    $instance->longitude = $longitude;
    $instance->latitude = $latitude;

    $db = JFactory::getDBO();
    $query = "SELECT COUNT(`id`) "
        . "FROM {$db->quoteName('#__users')} "
        . "WHERE `email` = '{$instance->email}'";
    $db->setQuery($query);
    $count_users = $db->loadResult();

    if($count_users > 0){
        return 'exist';
    } else {
        $date = date("Y-m-d H:i:s");

        $query = "INSERT "
            . "INTO {$db->quoteName('#__users')} (`name`, `username`, `email`, `password`, `birthday`, `registerDate`) "
            . "VALUES ('{$instance->name}', 'new_user', '{$instance->email}', '{$db->escape($instance->password)}', '{$instance->birthday}', '{$date}')";
        $db->setQuery($query);
        $db->query();
        $id_last = $db->insertid();

        $query = "INSERT "
            . "INTO {$db->quoteName('#__jshopping_users')} (`user_id`, `email`, `password`, `birthday`, `register_date`, `zip`, `city`, `state`, `longitude`, `latitude`) "
            . "VALUES ({$id_last}, '{$instance->email}', '{$db->escape($instance->password)}', '{$instance->birthday}', '{$date}', '{$instance->zip}', '{$instance->city}', '{$instance->state}', {$instance->longitude}, {$instance->latitude})";
        $db->setQuery($query);
        $db->query();

        $query = "INSERT "
            . "INTO `#__user_tokens` (`user_id`) "
            . "VALUES ({$id_last})";
        $db->setQuery($query);
        $db->query();

        $userDataSerialize = serialize($_POST);
        $instance->setParam('params', $userDataSerialize);

        $instance->guest = false;
        $instance->id = $id_last;
        $instance->save();

        $instance = JUser::getInstance($id_last);
        $session = JFactory::getSession();
        $instance->guest = 0;
        $instance->aid = 1;
        $session->set('user', $instance);

        $instance->setLastVisit();
        return 'success';
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