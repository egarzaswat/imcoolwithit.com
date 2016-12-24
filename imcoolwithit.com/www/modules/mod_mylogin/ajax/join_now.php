<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/mail_send/index.php' );
JFactory::getApplication('site')->initialise();

$conf = new JConfig();
$config['smtp_username'] = $conf->smtpuser;
$config['smtp_port']     = $conf->smtpport;
$config['smtp_host']     = $conf->smtphost;
$config['smtp_password'] = $conf->smtppass;
$config['smtp_debug']   = true;
$config['smtp_charset']  = 'UTF-8';
$config['smtp_from']     = 'Cool';


$email = trim($_POST['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'error email';
    exit;
}

if (strlen($_POST['password']) >= 6) { $password = $_POST['password']; } else { echo 'error password'; exit; }

$creating_message = createUser($email, $password);

if ($creating_message == 'success') {
    $success = false;
    $senderSubject = 'Welcome to Cool With It';
    $message = '
    Welcome to Cool With It&trade;...!<br>
    Thanks for setting up your account and for letting us be a part of helping you find that special someone!<br>
    Your account email is:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT') . '">' . $email . '</a>.<br>
    You can reset your password at any time here:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT') .'" >Reset Password</a>.<br>
    Our goal is to create a community whose members are 100% cool with who they are and what they are looking for, and in turn can learn and share about new places and happenings, all while getting the chance to meet other like-minded people!<br><br>
    The Cool With It&trade; team.';

    $message = "Sender: " . $senderName . "\r\nE-mail: " . $senderEmail . "\r\nMessage: ". $message;

    $mail = new sendMail();
    $mail->setFrom($conf->smtpuser, 'Support');
    $mail->setSubject($senderSubject);
    $mail->setMessage($message);
//    $mail->setTo('vanyatsurkan@gmail.com');
    $mail->setTo($email);
    $success = $mail->Send();

    echo 'success';
} else {
    echo 'exist';
    exit;
}

function createUser($email, $password){
    $instance = JUser::getInstance();
    $instance->name = 'new_user';
    $instance->email = $email;
    $instance->password = JUserHelper::getCryptedPassword($password);
    $instance->guest = false;

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
            . "INTO {$db->quoteName('#__users')} (`name`, `username`, `email`, `password`, `registerDate`) "
            . "VALUES ('{$instance->name}', 'new_user', '{$instance->email}', '{$db->escape($instance->password)}', '{$date}')";
        $db->setQuery($query);
        $db->query();
        $id_last = $db->insertid();

        $query = "INSERT "
            . "INTO {$db->quoteName('#__jshopping_users')} (`user_id`, `email`, `password`,  `register_date`) "
            . "VALUES ({$id_last}, '{$instance->email}', '{$db->escape($instance->password)}', '{$date}')";
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