<?php
defined('_JEXEC') or die('Restricted access');


class jshopMeeting{

    function __construct(){}

    function getUserMeetUpCount(){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(MU.id) " . "FROM `#__meet_up` AS MU LEFT JOIN `#__jshopping_products` as P ON P.product_id = MU.sponsor LEFT JOIN `#__answered_sponsor_questions` AS ASQ ON ASQ.meet_up = MU.id AND ASQ.user = " . JSFactory::getUser()->user_id . " WHERE (MU.sender = " . JSFactory::getUser()->user_id . " OR MU.recipient = " . JSFactory::getUser()->user_id. ") AND MU.occurred = 1 AND P.product_id is not NULL AND ASQ.id is NULL";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getUserMeetUp($start = 0, $limit = 0){
        $db = JFactory::getDBO();

        $select = "SELECT U.user_id, M.id AS meet_up_id, U.u_name, U.block, M.occurred_date AS occurred_date, P.`name_" . JSFactory::getLang()->lang . "` AS sponsor_name, P.image";

        $from_recipient = " FROM `#__meet_up` AS M LEFT JOIN `#__jshopping_users` AS U ON M.recipient = U.user_id LEFT JOIN `#__jshopping_products` AS P ON P.product_id = M.sponsor LEFT JOIN `#__answered_sponsor_questions` AS ASQ ON ASQ.meet_up = M.id AND ASQ.user = " . JSFactory::getUser()->user_id;

        $where_sender = " WHERE M.sender = " . JSFactory::getUser()->user_id . " AND M.occurred = 1 AND P.product_id is not NULL AND ASQ.id is NULL ";

        $from_sender = " FROM `#__meet_up` AS M LEFT JOIN `#__jshopping_users` AS U ON M.sender = U.user_id LEFT JOIN `#__jshopping_products` AS P ON P.product_id = M.sponsor LEFT JOIN `#__answered_sponsor_questions` AS ASQ ON ASQ.meet_up = M.id AND ASQ.user = " . JSFactory::getUser()->user_id;

        $where_recipient = " WHERE M.recipient = " . JSFactory::getUser()->user_id . " AND M.occurred = 1 AND P.product_id is not NULL AND ASQ.id is NULL ";

        $query =  $select . $from_recipient . $where_sender ." UNION " . $select . $from_sender . $where_recipient ." ORDER BY occurred_date DESC";

        $db->setQuery($query, $start, $limit);
        $result = $db->loadObjectList();

        return $result;
    }

    function getUserMeetUpCount_Honest(){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(MU.id) " . "FROM `#__meet_up` AS MU LEFT JOIN `#__jshopping_products` AS P ON P.product_id = MU.sponsor LEFT JOIN `#__users_reviews` AS UR ON UR.meet = MU.id AND UR.sender = " . JSFactory::getUser()->user_id . " WHERE (MU.sender = " . JSFactory::getUser()->user_id . " OR MU.recipient = " . JSFactory::getUser()->user_id. ") AND MU.occurred = 1 AND P.product_id is not NULL AND UR.id is NULL";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getUserMeetUp_Honest($start = 0, $limit = 0){
        $db = JFactory::getDBO();
        $select = "SELECT U.user_id, M.id AS meet_up_id, U.photosite, U.u_name, U.birthday, U.block, M.occurred_date AS occurred_date, P.`name_" . JSFactory::getLang()->lang . "` AS sponsor_name";

        $from_recipient = " FROM `#__meet_up` AS M LEFT JOIN `#__jshopping_users` AS U ON M.recipient = U.user_id LEFT JOIN `#__jshopping_products` AS P ON P.product_id = M.sponsor LEFT JOIN `#__users_reviews` AS UR ON UR.meet = M.id AND UR.sender = " . JSFactory::getUser()->user_id;

        $where_sender = " WHERE M.sender = " . JSFactory::getUser()->user_id . " AND M.occurred = 1 AND P.product_id is not NULL AND UR.id is NULL ";

        $from_sender = " FROM `#__meet_up` AS M LEFT JOIN `#__jshopping_users` AS U ON M.sender = U.user_id LEFT JOIN `#__jshopping_products` AS P ON P.product_id = M.sponsor LEFT JOIN `#__users_reviews` AS UR ON UR.meet = M.id AND UR.sender = " . JSFactory::getUser()->user_id;

        $where_recipient = " WHERE M.recipient = " . JSFactory::getUser()->user_id . " AND M.occurred = 1 AND P.product_id is not NULL AND UR.id is NULL ";

        $query =  $select . $from_recipient . $where_sender ." UNION " . $select . $from_sender . $where_recipient ." ORDER BY occurred_date DESC";

        $db->setQuery($query, $start, $limit);
        $result = $db->loadObjectList();
        return $result;
    }

    function getMeetUpInfo($meet){
        $db = JFactory::getDBO();
        $query = "SELECT sender, recipient, code, sponsor, confirmation, occurred FROM `#__meet_up` where id = " . $meet . " and (sender = " . JSFactory::getUser()->user_id . " or recipient = " . JSFactory::getUser()->user_id . ")";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $return = new stdClass();

        if(count($result)>0){
            if($result[0]->occurred == 0){
                $return->occurred = 0;
                $return->sponsor = $result[0]->sponsor;
                $return->code = $result[0]->code;
                $return->sender = $result[0]->sender;
                $return->recipient = $result[0]->recipient;
                $return->confirmation = $result[0]->confirmation;
            } else {
                $return->occurred = 1;
                $return->sponsor = $result[0]->sponsor;
                $return->code = $result[0]->code;
                $return->sender = $result[0]->sender;
                $return->recipient = $result[0]->recipient;
                $return->confirmation = $result[0]->confirmation;
            }
        } else {
            return -1;
        }

        return $return;
    }

    function getPermissionMeetingsInTheSponsor($user, $friend, $sponsor){
        $conf = new JConfig();
        $count_perm = $conf->count_permission_count_meet;

        $db = JFactory::getDBO();
        $query = "SELECT id FROM `#__meet_up` WHERE sponsor = " . $sponsor . " AND ( (sender = " . $user . " AND recipient = " . $friend . ") OR (recipient = " . $user . " AND sender = " . $friend . ") )";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if( count($result) < $count_perm ){
            return true;
        } else {
            return false;
        }

    }

    function checkIfAnswered($user_id, $meet_up_id){
        $db = JFactory::getDBO();
        $query = "SELECT sponsor FROM `#__meet_up` WHERE id = " . $meet_up_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if( count($result) == 0 ){
            return true;
        }

        $query = "SELECT COUNT(*) ". "FROM `#__answered_sponsor_questions` "
            . "WHERE meet_up = " . $meet_up_id . " AND user = " .$user_id;

        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result == 0) {return false;} else {return true;}
    }

}
?>