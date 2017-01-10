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
$email = trim($_POST['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'error email';
    exit;
}

if (strlen($_POST['password']) >= 6) { $password = $_POST['password']; } else { echo 'error password'; exit; }

$creating_message = createUser($birthday, $postal_code, $city, $state, $longitude, $latitude, $email, $password);

if ($creating_message == 'success') {
    $success = false;
    $senderSubject = 'Welcome to Cool With It';
    $message = '
    Welcome to Cool With It&trade;...!<br>
    Thanks for setting up your account.<br>
    Here’s your info:<br>
    Your account email is:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT') . '">' . $email . '</a>.<br>
    You can reset your password at any time here:&nbsp;<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT') .'" >Reset Password</a>.<br>
    Have fun and enjoy your dating experience..<br><br>
    The Cool With It&trade; team.';

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