<?php
defined('_JEXEC') or die('Restricted access');

class jshopNotifications{

    function __construct(){}

    function addNote($sender, $recipient, $event_id, $name, $type, $message_text = null){
        $Config = JSFactory::getConfig();
        $db = JFactory::getDbo();
        $query	= $db->getQuery(true)
            ->select($db->quoteName(array('u_name', 'email')))
            ->from($db->quoteName('#__jshopping_users'))
            ->where("`user_id` = {$recipient}");
        $db->setQuery($query);
        $user = $db->loadAssocList();
        $username = $user[0]['u_name'];
        $email = $user[0]['email'];
        $message = $this->constructMessage($type, $event_id, $username, $name, $message_text);


        if ($type == $Config->notifications[8] || $type == $Config->notifications[9]) {
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

    private function server_parse($socket, $response, $line = __LINE__) {
        $config['smtp_debug']   = true;
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

    private function smtpmail($mail_to, $subject, $message, $headers='') {
        $conf = new JConfig();
        $config['smtp_username'] = $conf->smtpuser;
        $config['smtp_port']     = $conf->smtpport;
        $config['smtp_host']     = $conf->smtphost;
        $config['smtp_password'] = $conf->smtppass;
        $config['smtp_debug']   = true;
        $config['smtp_charset']  = 'UTF-8';
        $config['smtp_from']     = 'Cool';

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

        if (!$this->server_parse($socket, "220", __LINE__)) return false;

        fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>I can not send HELO!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "AUTH LOGIN\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>I can not find the answer to an authorization request.</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Login authorization was not accepted by the server!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
        if (!$this->server_parse($socket, "235", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Password was not accepted by the server as a faithful! Authorization Error!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>I can not send command MAIL FROM:</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>I can not send command RCPT TO:</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "DATA\r\n");

        if (!$this->server_parse($socket, "354", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>I can not send command DATA</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, $SEND."\r\n.\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Unable to send the message body. The letter has been sent not!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return TRUE;
    }

    function sendNotification($message, $email, $subject){
        $conf = new JConfig();
        $senderName = 'Cool';
        $senderSubject = $subject;
        $senderEmail = isset( $conf->mailfrom )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $conf->mailfrom ) : "";
        $recipientEmail = isset( $email )? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $email ) : "";

        if ($senderName) {
            $headers = "Content-Type: text/html;\r\n";
            $headers .= "From: " . '=?utf-8?B?'.base64_encode($senderName).'?=' . " <" . $senderEmail . ">\r\n";
            $headers .= "To: '" . $recipientEmail . "'<" . $recipientEmail . ">\r\n";
            $this->smtpmail($recipientEmail, $senderSubject, $message, $headers);
        }
    }

    private function constructMessage($type, $event_id, $user, $name, $message_text = null){

        $Config = JSFactory::getConfig();
        $message = '';
        switch($type){
            case $Config->notifications[0] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_MESSAGING_VIEW') . "?friend=" . $event_id;
                $message_text = $this->prepareForEmail($message_text, true);
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new message. <a href="' . $link . '">' . strip_tags($message_text) . ' Click to view message</a>.'; break;
            case $Config->notifications[1] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_MEETING_VIEW_INVITE') . "?meet=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new LincUp™ invite. <a href="' . $link . '">Click to view invite</a>.'; break;
            case $Config->notifications[2] :
                $message = 'Hi, ' . $user . '. ' . $name . '’s LincUp™ invite will expire soon.'; break;
            case $Config->notifications[3] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new friend request. <a href="' . $link . '">Click to view</a>.'; break;
            case $Config->notifications[4] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' accepted your friend request! <a href="' . $link . '">View their profile</a>.'; break;
            case $Config->notifications[5] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_USERS_LIST');
                $message = 'Hi, ' . $user . '. ' . $name . ' declined your friends request. Sorry it didn’t work out, but get back out there! <a href="' . $link . '">Search Now!</a>'; break;
            case $Config->notifications[6] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' just gave you a Honest Review. Thanks for being cool. <a href="' . $link . '">View their profile</a>.'; break;
            case $Config->notifications[7] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_FULL_USER_PAGE') . "?user=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' just viewed your profile. <a href="' . $link . '">View their profile</a>.'; break;
            case $Config->notifications[8] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_OFFER_QUESTION') . "?offer=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new R U Interested™ survey offer. <a href="' . $link . '">Click to view offer</a>.'; break;
            case $Config->notifications[9] :
                $link = "http://" . $_SERVER['SERVER_NAME'] . "/" . JText::_('LINK_MEETING') . "?sponsor=" . $event_id;
                $message = 'Hi, ' . $user . '. ' . $name . ' has sent you a new LincUp™ offer. <a href="' . $link . '">Click to view offer</a>.'; break;
        }
        return $message;
    }

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
            $v = sprintf('&nbsp;<img src="http://' . $_SERVER['SERVER_NAME'] . '/images/smiles/' . '%s" />&nbsp;', $v, $k);
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