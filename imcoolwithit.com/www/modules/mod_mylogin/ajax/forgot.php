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


            $message = '
                Hi, please click this &nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_LOGIN') . '?u=' . $user_id . '&h=' . $hash . '">link</a> to reset your password.<br>
                Thanks,<br><br>
                The Cool With It team.';
            $senderSubject = 'Forgot Password';
            $conf = new JConfig();

            $mail = new sendMail();
            $mail->setSubject($senderSubject);
            $mail->setMessage($message);
//            $mail->setTo('vanyatsurkan@gmail.com');
            $mail->setTo($_POST['email']);
            $success = $mail->Send();

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