<?php
defined('_JEXEC') or die('Restricted access');
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));
require_once ( JPATH_BASE .'/mail_send/index.php' );

class jshopNotifications{

    private $site_url = 'http://coolwithit.com';
    function __construct(){}


//$m_not->addNote($value['product_id'], $user_value['user_id'], $value['product_id'], $value['name'], $Config->notifications[9]);

    function addNote($sender, $recipient, $event_id, $name, $type, $message_text = null){
        $Config = JSFactory::getConfig();

        $db = JFactory::getDbo();
        $query	= $db->getQuery(true)
            ->select($db->quoteName(array('id')))
            ->from($db->quoteName('#__emails_notifications'))
            ->where("`type` = '{$type}'")
            ->where("`sender` = {$sender}")
            ->where("`recipient` = {$recipient}")
            ->where("`event_id` = {$event_id}");
        $db->setQuery($query);
        $list = $db->loadAssocList();

        if(!count($list) || count($list) === 0){

            if($type == $Config->notifications[12]){
                $conf = new JConfig();
                $email = $conf->mailfrom;
            } else {
                $db = JFactory::getDbo();
                $query	= $db->getQuery(true)
                    ->select($db->quoteName(array('u_name', 'email')))
                    ->from($db->quoteName('#__jshopping_users'))
                    ->where("`user_id` = {$recipient}");
                $db->setQuery($query);
                $user = $db->loadAssocList();
                $username = $user[0]['u_name'];
                $email = $user[0]['email'];
            }

            $message = $this->constructMessage($type, $event_id, $username, $name, $message_text);


            if ($type == $Config->notifications[8] || $type == $Config->notifications[9] || $type == $Config->notifications[10]) {
                $query = "INSERT "
                    . "INTO {$db->quoteName('#__emails_notifications')} (`sender`, `recipient`, `message`, `event_id`, `type`, `brand_name`, `email`) "
                    . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}')";
                $db->setQuery($query);
                $db->execute();
            } elseif ($type == $Config->notifications[7]){
                $query = "SELECT COUNT(id) "
                    . "FROM {$db->quoteName('#__emails_notifications')} "
                    . "WHERE (`sender` = {$sender} and `recipient` = {$recipient} and `type` = '{$type}')";
                $db->setQuery($query);
                $count = $db->loadResult();
                if ($count < 1) {
                    $query = "INSERT "
                        . "INTO {$db->quoteName('#__emails_notifications')} (`sender`, `recipient`, `message`, `event_id`, `type`, `member_name`, `email`) "
                        . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}') ";
                    $db->setQuery($query);
                    $db->execute();
                }
            } elseif (in_array($type, $Config->notifications)) {
                $query = "INSERT "
                    . "INTO {$db->quoteName('#__emails_notifications')} (`sender`, `recipient`, `message`, `event_id`, `type`, `member_name`, `email`) "
                    . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}')";
                $db->setQuery($query);
                $db->execute();
            }
        }

    }

    function addTestNote($sender, $recipient, $event_id, $name, $type, $message_text = null){
        $Config = JSFactory::getConfig();

        $db = JFactory::getDbo();
        $query	= $db->getQuery(true)
            ->select($db->quoteName(array('id')))
            ->from($db->quoteName('#__emails_notifications_test'))
            ->where("`type` = '{$type}'")
            ->where("`sender` = {$sender}")
            ->where("`recipient` = {$recipient}")
            ->where("`event_id` = {$event_id}");
        $db->setQuery($query);
        $list = $db->loadAssocList();

        if(!count($list) || count($list) === 0){

            if($type == $Config->notifications[12]){
                $conf = new JConfig();
                $email = $conf->mailfrom;
            } else {
                $db = JFactory::getDbo();
                $query	= $db->getQuery(true)
                    ->select($db->quoteName(array('u_name', 'email')))
                    ->from($db->quoteName('#__jshopping_users'))
                    ->where("`user_id` = {$recipient}");
                $db->setQuery($query);
                $user = $db->loadAssocList();
                $username = $user[0]['u_name'];
                $email = $user[0]['email'];
            }

            $message = $this->constructMessage($type, $event_id, $username, $name, $message_text);


            if ($type == $Config->notifications[8] || $type == $Config->notifications[9] || $type == $Config->notifications[10]) {
                $query = "INSERT "
                    . "INTO {$db->quoteName('#__emails_notifications_test')} (`sender`, `recipient`, `message`, `event_id`, `type`, `brand_name`, `email`) "
                    . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}')";
                $db->setQuery($query);
                $db->execute();
            } elseif ($type == $Config->notifications[7]){
                $query = "SELECT COUNT(id) "
                    . "FROM {$db->quoteName('#__emails_notifications_test')} "
                    . "WHERE (`sender` = {$sender} and `recipient` = {$recipient} and `type` = '{$type}')";
                $db->setQuery($query);
                $count = $db->loadResult();
                if ($count < 1) {
                    $query = "INSERT "
                        . "INTO {$db->quoteName('#__emails_notifications_test')} (`sender`, `recipient`, `message`, `event_id`, `type`, `member_name`, `email`) "
                        . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}') ";
                    $db->setQuery($query);
                    $db->execute();
                }
            } elseif (in_array($type, $Config->notifications)) {
                $query = "INSERT "
                    . "INTO {$db->quoteName('#__emails_notifications_test')} (`sender`, `recipient`, `message`, `event_id`, `type`, `member_name`, `email`) "
                    . "VALUES ({$sender}, {$recipient}, '{$db->escape($message)}', {$event_id}, '{$type}', '{$db->escape($name)}', '{$email}')";
                $db->setQuery($query);
                $db->execute();
            }
        }

    }

    function sendNotification($message, $email, $subject){
        $conf = new JConfig();
        $senderName = 'Cool';
        $senderSubject = $subject;
        $senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
        $recipientEmail = isset( $email )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $email ) : "";

        if ($senderName) {
            $message = "". $message;

            $mail = new sendMail();
            $mail->setSubject($senderSubject);
            $mail->setMessage($message);
//            $mail->setTo('vanyatsurkan@gmail.com');
            $mail->setTo($recipientEmail);
            $success = $mail->Send();
        }
    }

    private function constructMessage($type, $event_id, $user, $name, $message_text = null){

        $Config = JSFactory::getConfig();
        $message = '';
        switch($type){
            case $Config->notifications[0] :
                $link = $this->site_url . "/" . JText::_('LINK_MESSAGING_VIEW') . "?friend=" . $event_id;
                $message_text = $this->prepareForEmail($message_text, true);
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new message. `' . strip_tags($message_text) . '` <a href="' . $link . '">View message</a>.'; break;
            case $Config->notifications[1] :
                $link = $this->site_url . "/" . JText::_('LINK_MEETING_VIEW_INVITE') . "?meet=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new Linc Up invite. <a href="' . $link . '">Click to view invite</a>.'; break;
            case $Config->notifications[2] :
                $link = $this->site_url . "/" . JText::_('LINK_MEETING_CONFIRMATION') . "?meet=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . '’s <a href="' . $link . '">Linc Up</a> invite will expire soon.'; break;
            case $Config->notifications[3] :
                $link = $this->site_url . "/" . JText::_('LINK_MESSAGING_RECEIVED');
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new friend request. <a href="' . $link . '">Click to view</a>.'; break;
            case $Config->notifications[4] :
                $link = $this->site_url . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' accepted your friend request! <a href="' . $link . '">View their profile</a>.'; break;
            case $Config->notifications[5] :
                $link = $this->site_url . "/" . JText::_('LINK_USERS_LIST');
                $message = 'Hi, ' . $user . '. ' . $name . ' declined your friends request. Sorry it didn’t work out, but get back out there! <a href="' . $link . '">Search Now!</a>'; break;
            case $Config->notifications[6] :
                $link = $this->site_url . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . ',<br>' . $name . ' just gave you a positive Honesty Review on Cool With It. Nice job. <a href="' . $link . '">View</a>.'; break;
            case $Config->notifications[7] :
                $link = $this->site_url . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' just viewed your profile. <a href="' . $link . '">View their profile.</a>'; break;
            case $Config->notifications[8] :
                $link = $this->site_url . "/" . JText::_('LINK_OFFER_QUESTION') . "?offer=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new R U Interested survey offer. <a href="' . $link . '">Click to view offer</a>.'; break;
            case $Config->notifications[9] :
                $link = $this->site_url . "/" . JText::_('LINK_MEETING') . "?sponsor=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new Linc Up offer. <a href="' . $link . '">Click to view offer</a>.'; break;
            case $Config->notifications[10] :
                $link = $this->site_url . "/" . JText::_('LINK_MEETING_COUPON_INFO') . "?meet=" . $event_id;
                $message = 'Congratulations, <a href="' . $link . '">' . $name . '</a> accepted your Linc Up invite!'; break;
            case $Config->notifications[12] :
                $link = $this->site_url . "/" . JText::_('LINK_MY_ACCOUNT');

                $message = 'Hi, This email is to notify you that a <a href="' . $link . '">Coolwithit.com</a>" Linc Up offer has just been redeemed at your venue.<br>
                            Please email us <a href="mailto:support@coolwithit.com">@support@coolwithit.com</a> if you have  any issues or concerns.<br>
                            Please email <a href="mailto:support@coolwithit.com">support@coolwithit.com</a> if you have any issues or concerns. Warmest regards';
                break;
        }
        return $message;
    }
//Hi, new_user. ruths chris has sent you a new R U Interested survey offer. <a href="http:///earncredits/offer_questions?offer=23">Click to view offer</a>.

    private function prepareForEmail($message, $pruning = false, $pruning_length = 60){

        $smiles = array(
            ":)"        => "Smile.png",
            ":-)"       => "BigSmile.png",
            ":("        => "Sad.png",
            ";)"        => "Wink.png",
            ":’("       => "Cry.png",
            ":-’("      => "Crying.png",
            ":-*"       => "Kissed.png",
            ":*"        => "Kiss.png",
            "@->--"     => "Rose.png",
            "]:->"      => "Furious.png",
            "=-O"       => "Surprise.png",
            "<:-("      => "Sorry.png",
            ":-||"      => "Frustrated.png",
            ":-<"       => "Expressionless.png",
            "8-)"       => "Cool.png",
            ">:-["      => "Boring.png",
            ">:-("      => "Disappointment.png",
            ":-["       => "Frown.png",
            "(:I"       => "Yawn.png",
            "8-|"       => "Nerd.png",
            "0:-)"      => "SweetAngel.png",
            "0:)"       => "Sweat.png",
            "(Х_х)"     => "Beaten.png",
            "(?_?)"     => "Question.png",
            '($_$)'     => "EasyMoney.png",
            "(@_@)"     => "Hypnotic.png",
            "(♥_♥)"    => "Adore.png",
            "(☆_☆)"   => "Stars.png",
            "(*0_0*)"   => "Pudently.png",
            "8-O"       => "Shock.png",
            ":-Z"       => "Angry.png",
            "}-Z"       => "BeUpToNoGood.png",
            ":-`|"      => "Cold.png",
            ":-~("      => "Snotty.png",
            "%-}"       => "Dizzy.png",
            ":)-"       => "Happy.png",
            ":D<"       => "Hug.png",
            ":0"        => "Hungry.png",
            ":-D"       => "Laugh.png",
            "P-("       => "Pirate.png",
            ":-(*)"     => "Sick.png",
            ":-o"       => "Singing.png",
            "|-I"       => "Sleep.png",
            ":-i"       => "Smoking.png",
            ':-"'       => "Whistling.png",
            "@="        => "Bomb.png",
            ":-c"       => "Call.png",
            "DX"        => "Disgust.png",
            ":@"        => "Exclamation.png",
            "XD"        => "LOL.png",
            "<:o)"      => "Party.png",
            "*-)"       => "Thinking.png",
            "(n)"       => "ThumbsDown.png",
            "(y)"       => "ThumbsUp.png",
            ":-w"       => "Waiting.png",
            ":[-"       => "Badly.png",
            ":-X"       => "Stop.png",
            ".{|"       => "Monocle.png",
            "[hi]"          => "Hi.png",
            "[beer]"        => "Beer.png",
            "[cocktail]"    => "Cocktail.png",
            "[coffee]"      => "Coffee.png",
            "[ninja]"       => "Ninja.png",
            "[study]"       => "Study.png",
            "[movie]"       => "Movie.png",
            "[music]"       => "Music.png",
            "[idea]"        => "Idea.png",
            "[aggressive]"  => "Aggressive.png",
            "[satisfied]"   => "Satisfied.png",
            "[scared]"      => "Scared.png",
            "[stressed]"    => "Stressed.png",
            "[struggle]"    => "Struggle.png",
            "[giggle]"      => "Giggle.png",
            "[despair]"     => "Despair.png",
            "[hysterical]"  => "Hysterical.png",
            "[facepalm]"    => "Facepalm.png",
            "[impish]"      => "Impish.png",
            "[rage]"        => "Rage.png",
            "[woo]"         => "Woo.png",
            "[whacked]"     => "WornOut.png"
        );

        if($pruning){
            $message_old = $message;
            $message = substr($message, 0, $pruning_length);
            $string_end = substr($message_old, $pruning_length, $pruning_length+12);
            $pos = strripos($string_end, ' ');
            if($pos){
                $message .= substr($string_end, 0, $pos);
            }
            if(strlen($message_old) != strlen($message)){
                $message .= '...';
            }
        }

        foreach($smiles as $k => &$v) {
            $v = sprintf('&nbsp;<img src="' . $this->site_url . '/images/smiles/' . '%s" />&nbsp;', $v, $k);
        }

        return strtr($message, $smiles);
    }

    function getNotes(){
        $db = JFactory::getDBO();
        $query = "SELECT * "
            . "FROM {$db->quoteName('#__emails_notifications')} limit 20";
        $db->setQuery($query);
        $result = $db->loadAssocList();
        return $result;
    }

    function deleteNotes($id_arr = array()){
        $db = JFactory::getDBO();
        $query = "DELETE "
            . "FROM {$db->quoteName('#__emails_notifications')} "
        . "WHERE id IN (" . implode(",",$id_arr) . ")";

        $db->setQuery($query);
        $db->execute();
        return true;
    }
}