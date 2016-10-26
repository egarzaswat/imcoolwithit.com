<?php
defined('_JEXEC') or die('Restricted access');


class jshopMessaging{

    function __construct(){}

    function getSentMessages(){
        $db = JFactory::getDBO();
        $query = "SELECT M.message, M.date, M.read, M.sender_id, M.reciper_id, U.user_id, U.photosite, U.u_name, U.birthday, U.block FROM `#__messages_chat` AS M LEFT JOIN `#__jshopping_users` as U ON M.reciper_id=U.user_id WHERE  M.sender_id=" . JSFactory::getUser()->user_id . " ORDER BY `date` DESC";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $result = $this->getUnicalListMessages($result);

        return $result;
    }

    function getReceivedMessages(){
        $db = JFactory::getDBO();
        $query = "SELECT M.id, M.message, M.date, M.read, M.sender_id, M.reciper_id, U.user_id, U.photosite, U.u_name, U.birthday, U.block FROM `#__messages_chat` AS M LEFT JOIN `#__jshopping_users` as U ON M.sender_id=U.user_id WHERE  M.reciper_id=" . JSFactory::getUser()->user_id . " ORDER BY `date` DESC";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $result = $this->getUnicalListMessages($result);

        return $result;
    }

    function getTokens(){
        $db = JFactory::getDBO();
        $query = "SELECT M.id, M.date, M.confirmation, M.read, U.user_id, U.photosite, U.u_name, U.birthday, U.block FROM `#__messages_accept_tokens` AS M LEFT JOIN `#__jshopping_users` as U ON M.sender=U.user_id WHERE M.reciper=" . JSFactory::getUser()->user_id . "  ORDER BY `date` DESC";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }

    function getSentMeetUp(){
        $db = JFactory::getDBO();
        $query = "SELECT M.meet_up_id, M.date, M.sponsor, M.confirmation, M.new_meet_up, U.u_name, U.user_id, U.photosite, U.birthday, U.longitude, U.latitude, U.block FROM `#__messages_meet_up` AS M LEFT JOIN `#__jshopping_users` as U ON M.recipient=U.user_id where M.sender=" . JSFactory::getUser()->user_id . " and M.new_meet_up=1";
        $db->setQuery($query);
        $result= $db->loadObjectList();

        return $result;
    }

    function getReceivedMeetUp(){
        $db = JFactory::getDBO();
        $query = "SELECT R.occurred, M.id, M.meet_up_id, M.date, M.read, M.sponsor, M.confirmation, M.new_meet_up, U.u_name, U.user_id, U.photosite, U.birthday, U.longitude, U.latitude, U.block, P.`name_en-GB` FROM `#__messages_meet_up` AS M LEFT JOIN `#__jshopping_users` as U ON M.sender=U.user_id LEFT JOIN `#__jshopping_products` as P ON P.product_id=M.sponsor LEFT JOIN `#__meet_up` as R ON R.id=M.meet_up_id where M.recipient=" . JSFactory::getUser()->user_id . " UNION
        SELECT R.occurred, M.id, M.meet_up_id, M.date, M.read, M.sponsor, M.confirmation, M.new_meet_up, U.u_name, U.user_id, U.photosite, U.birthday, U.longitude, U.latitude, U.block, P.`name_en-GB` FROM `#__messages_meet_up` AS M LEFT JOIN `#__jshopping_users` as U ON M.recipient=U.user_id LEFT JOIN `#__jshopping_products` as P ON P.product_id=M.sponsor LEFT JOIN `#__meet_up` as R ON R.id=M.meet_up_id where M.sender=" . JSFactory::getUser()->user_id . " and M.new_meet_up=0 and M.confirmation=1";

        $db->setQuery($query);
        $result= $db->loadAssocList();

        return $result;
    }

    function getUnicalListMessages($list_messages){
        $unical_list = array();
        foreach($list_messages as $key=>$value){
            $tmp = array(
                'sender'    => $value->sender_id,
                'reciper'   => $value->reciper_id,
            );

            if(in_array($tmp, $unical_list)){
                unset($list_messages[$key]);
            } else {
                array_push($unical_list, $tmp);
            }
        }

        return $list_messages;
    }

    function setReadMessages($table, $id){
        $db = JFactory::getDBO();
        $query = "update `#__" . $table . "` set `read`= 1 where `id`=" . $id;
        $db->setQuery($query);
        $db->query();
    }

    function getCountNewMessages(){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(`id`) FROM `#__messages_chat` WHERE `reciper_id` = " . JSFactory::getUser()->user_id . " AND `read` = 0";
        $db->setQuery($query);
        $count_1= $db->loadResult();

        $query = "SELECT COUNT(`id`) AS count_t FROM `#__messages_accept_tokens` WHERE `reciper` = " . JSFactory::getUser()->user_id . " AND `read` = 0";
        $db->setQuery($query);
        $count_2= $db->loadResult();

        $query = "SELECT COUNT(`id`) AS count_m FROM `#__messages_meet_up` WHERE (`recipient` = " . JSFactory::getUser()->user_id . " OR (`sender` = " . JSFactory::getUser()->user_id . " AND `new_meet_up` = 0 AND `confirmation` = 1) ) AND `read` = 0";
        $db->setQuery($query);
        $count_3= $db->loadResult();

        $query = "SELECT COUNT(U.user_id) FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.sender = U.user_id WHERE F.reciper = " . JSFactory::getUser()->user_id . " and F.confirmation = 0 AND U.block = 0";
        $db->setQuery($query);
        $count_4 = $db->loadResult();

        $count = $count_1 + $count_2 + $count_3 + $count_4;

        return $count;
    }

    function getIsNewMessage($user_id, $my_id){
        if((int)$user_id >0 && (int)$my_id > 0){
            $db = JFactory::getDBO();
            $query = "select COUNT(`id`) from `#__messages_chat` where (`reciper_id` = " . $my_id . " AND `sender_id` = " . $user_id . ") AND `read` = 0";
            $db->setQuery($query, 0, 0);
            $count = $db->loadResult();
            if($count == 0){
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function getCountChatMessages($sender, $reciper){
        $db	= JFactory::getDBO();
        $query = 'SELECT COUNT(`id`) FROM `#__messages_chat` WHERE `delete`=0 AND ( (`sender_id`=' . $sender . ' AND `reciper_id`=' . $reciper . ') OR (`sender_id`=' . $reciper . ' AND `reciper_id`=' . $sender . ') )';
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getChatMessages($my_id, $friend_id, $offset, $limit){
        $db	= JFactory::getDbo();
        $query = 'SELECT `id`, `message`, `date`, `sender_id`, `read` FROM `#__messages_chat` WHERE `delete`=0 AND ((`sender_id`=' . $my_id . ' AND `reciper_id`=' . $friend_id . ') OR (`sender_id`=' . $friend_id . ' AND `reciper_id`=' . $my_id . ')) ORDER BY `date` DESC';

        $db->setQuery($query, $offset, $limit);
        $result = $db->loadObjectList();

//        foreach($result as $key => $value){
//            $result[$result[$key]->date] = $value;
//            unset($result[$key]);
//        }

        function cmp($a, $b) {
            if ($a->date == $b->date) {
                return 0;
            }
            return ($a->date < $b->date) ? -1 : 1;
        }

        uasort($result, 'cmp');
        $result = array_values($result);

        foreach($result as $key => $value){
            $result[$key]->date = JSFactory::getDateDiffFormat($value->date, JText::_('DATE_FORMAT_JUST_NOW'));
            if( ($value->sender_id != $my_id) && ($value->read != 1) ){
                $query = "update `#__messages_chat` set `read`= 1 where `id`=" . $value->id;
                $db->setQuery($query);
                $db->query();
            }
        }

        return $result;
    }

    function setChatMessage($user_my, $user_friend, $message){
        $db = JFactory::getDbo();

        $message = htmlspecialchars(strip_tags($message), ENT_QUOTES);
        $message = str_replace('\\', '\\\\', $message);

        $query = $db->getQuery(true);
        $columns = array('sender_id', 'reciper_id', 'message', 'date');
        $values = array((int)$user_my, (int)$user_friend, "'{$db->escape($message)}'", "'" . date("Y-m-d H:i:s") . "'");

        $query
            ->insert($db->quoteName('#__messages_chat'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $db->setQuery($query);

        if($db->execute()){
            return true;
        } else {
            return false;
        }
    }

    function setSmiles($message, $pruning = false, $pruning_length = 60){
        $smiles = array(
            ":)"        => "sm_smile",
            ":-)"       => "sm_bigsmile",
            ":("        => "sm_sad",
            ";)"        => "sm_wink",
            ":’("       => "sm_cry",
            ":-’("      => "sm_crying",
            ":-*"       => "sm_kissed",
            ":*"        => "sm_kiss",
            htmlspecialchars("@->--")     => "sm_rose",
            htmlspecialchars("]:->")      => "sm_furious",
            "=-O"       => "sm_surprise",
            htmlspecialchars("<:-(")      => "sm_sorry",
            ":-||"      => "sm_frustrated",
            htmlspecialchars(":-<")       => "sm_expressionless",
            "8-)"       => "sm_cool",
            htmlspecialchars(">:-[")      => "sm_boring",
            htmlspecialchars(">:-(")      => "sm_disappointment",
            ":-["       => "sm_frown",
            "(:I"       => "sm_yawn",
            "8-|"       => "sm_nerd",
            "0:-)"      => "sm_sweetangel",
            "0:)"       => "sm_sweat",
            "(Х_х)"     => "sm_beaten",
            "(?_?)"     => "sm_question",
            '($_$)'     => "sm_easymoney",
            "(@_@)"     => "sm_hypnotic",
            "(♥_♥)"    => "sm_adore",
            "(☆_☆)"   => "sm_stars",
            "(*0_0*)"   => "sm_pudently",
            "8-O"       => "sm_shock",
            ":-Z"       => "sm_angry",
            "}-Z"       => "sm_beuptonogood",
            ":-`|"      => "sm_cold",
            ":-~("      => "sm_snotty",
            "%-}"       => "sm_dizzy",
            ":)-"       => "sm_happy",
            htmlspecialchars(":D<")       => "sm_hug",
            ":0"        => "sm_hungry",
            ":-D"       => "sm_laugh",
            "P-("       => "sm_pirate",
            ":-(*)"     => "sm_sick",
            ":-o"       => "sm_singing",
            "|-I"       => "sm_sleep",
            ":-i"       => "sm_smoking",
            htmlspecialchars(':-"')       => "sm_whistling",
            "@="        => "sm_bomb",
            ":-c"       => "sm_call",
            "DX"        => "sm_disgust",
            ":@"        => "sm_exclamation",
            "XD"        => "sm_lol",
            htmlspecialchars("<:o)")      => "sm_party",
            "*-)"       => "sm_thinking",
            "(n)"       => "sm_thumbsdown",
            "(y)"       => "sm_thumbsup",
            ":-w"       => "sm_waiting",
            ":[-"       => "sm_badly",
            ":-X"       => "sm_stop",
            ".{|"       => "sm_monocle",
            "[hi]"          => "sm_hi",
            "[beer]"        => "sm_beer",
            "[cocktail]"    => "sm_cocktail",
            "[coffee]"      => "sm_coffee",
            "[ninja]"       => "sm_ninja",
            "[study]"       => "sm_study",
            "[movie]"       => "sm_movie",
            "[music]"       => "sm_music",
            "[idea]"        => "sm_idea",
            "[aggressive]"  => "sm_aggressive",
            "[satisfied]"   => "sm_satisfied",
            "[scared]"      => "sm_scared",
            "[stressed]"    => "sm_stressed",
            "[struggle]"    => "sm_struggle",
            "[giggle]"      => "sm_giggle",
            "[despair]"     => "sm_despair",
            "[hysterical]"  => "sm_hysterical",
            "[facepalm]"    => "sm_facepalm",
            "[impish]"      => "sm_impish",
            "[rage]"        => "sm_rage",
            "[woo]"         => "sm_woo",
            "[whacked]"     => "sm_wornout"
        );

        if($pruning){
            $message_old = $message;
            $message = substr($message, 0, $pruning_length);
            $string_end = substr($message, 48, 60);
            $pos = strripos($string_end, ' ');
            if($pos){
                $message = substr($message, 0, 48) . substr($string_end, 0, $pos);
            }
            if(strlen($message_old) != strlen($message)){
                $message .= '...';
            }
        }

        foreach($smiles as $k => &$v) {
            $v = sprintf('&nbsp;<b class="%s smile" alt="%s"></b>&nbsp;', $v, $k);
        }

        return strtr($message, $smiles);
    }

    function getSmile($class){
        $smiles = array(
            ":)"        => "sm_smile",
            ":-)"       => "sm_bigsmile",
            ":("        => "sm_sad",
            ";)"        => "sm_wink",
            ":’("       => "sm_cry",
            ":-’("      => "sm_crying",
            ":-*"       => "sm_kissed",
            ":*"        => "sm_kiss",
            "@->--"     => "sm_rose",
            "]:->"      => "sm_furious",
            "=-O"       => "sm_surprise",
            "<:-("      => "sm_sorry",
            ":-||"      => "sm_frustrated",
            ":-<"       => "sm_expressionless",
            "8-)"       => "sm_cool",
            ">:-["      => "sm_boring",
            ">:-("      => "sm_disappointment",
            ":-["       => "sm_frown",
            "(:I"       => "sm_yawn",
            "8-|"       => "sm_nerd",
            "0:-)"      => "sm_sweetangel",
            "0:)"       => "sm_sweat",
            "(Х_х)"     => "sm_beaten",
            "(?_?)"     => "sm_question",
            '($_$)'     => "sm_easymoney",
            "(@_@)"     => "sm_hypnotic",
            "(♥_♥)"    => "sm_adore",
            "(☆_☆)"   => "sm_stars",
            "(*0_0*)"   => "sm_pudently",
            "8-O"       => "sm_shock",
            ":-Z"       => "sm_angry",
            "}-Z"       => "sm_beuptonogood",
            ":-`|"      => "sm_cold",
            ":-~("      => "sm_snotty",
            "%-}"       => "sm_dizzy",
            ":)-"       => "sm_happy",
            ":D<"       => "sm_hug",
            ":0"        => "sm_hungry",
            ":-D"       => "sm_laugh",
            "P-("       => "sm_pirate",
            ":-(*)"     => "sm_sick",
            ":-o"       => "sm_singing",
            "|-I"       => "sm_sleep",
            ":-i"       => "sm_smoking",
            ':-"'       => "sm_whistling",
            "@="        => "sm_bomb",
            ":-c"       => "sm_call",
            "DX"        => "sm_disgust",
            ":@"        => "sm_exclamation",
            "XD"        => "sm_lol",
            "<:o)"      => "sm_party",
            "*-)"       => "sm_thinking",
            "(n)"       => "sm_thumbsdown",
            "(y)"       => "sm_thumbsup",
            ":-w"       => "sm_waiting",
            ":[-"       => "sm_badly",
            ":-X"       => "sm_stop",
            ".{|"       => "sm_monocle",
            "[hi]"          => "sm_hi",
            "[beer]"        => "sm_beer",
            "[cocktail]"    => "sm_cocktail",
            "[coffee]"      => "sm_coffee",
            "[ninja]"       => "sm_ninja",
            "[study]"       => "sm_study",
            "[movie]"       => "sm_movie",
            "[music]"       => "sm_music",
            "[idea]"        => "sm_idea",
            "[aggressive]"  => "sm_aggressive",
            "[satisfied]"   => "sm_satisfied",
            "[scared]"      => "sm_scared",
            "[stressed]"    => "sm_stressed",
            "[struggle]"    => "sm_struggle",
            "[giggle]"      => "sm_giggle",
            "[despair]"     => "sm_despair",
            "[hysterical]"  => "sm_hysterical",
            "[facepalm]"    => "sm_facepalm",
            "[impish]"      => "sm_impish",
            "[rage]"        => "sm_rage",
            "[woo]"         => "sm_woo",
            "[whacked]"     => "sm_wornout"
        );
        $smile = "";
        foreach($smiles as $k => $v) {
            if($class == $v){
                $smile = $k;
            }
        }

        return " " . $smile . " ";
    }
}