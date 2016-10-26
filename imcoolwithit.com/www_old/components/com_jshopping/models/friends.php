<?php
defined('_JEXEC') or die('Restricted access');


class jshopFriends{

    function __construct(){}

    function getCountFriends(){
        $db = JFactory::getDBO();

        $user_id = JSFactory::getUser()->user_id;
        $query = "SELECT COUNT(`sender`) FROM `#__friends` WHERE (`sender` = " . $user_id . " or `reciper` = " . $user_id. ") and `confirmation`=1";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getFriends($start = 0, $limit = 0){
        $db = JFactory::getDBO();

        $user_id = JSFactory::getUser()->user_id;
        $query = "SELECT F.date_confirm, U.user_id, U.u_name, U.birthday, U.photosite, U.longitude, U.latitude, U.sex, U.last_visit, U.block, I.height, I.body, I.status FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.reciper = U.user_id LEFT JOIN `#__user_info` AS I ON U.`user_id` = I.`user_id` WHERE F.sender = " . $user_id . " and F.confirmation = 1
                    UNION
                  SELECT F.date_confirm, U.user_id, U.u_name, U.birthday, U.photosite, U.longitude, U.latitude, U.sex, U.last_visit, U.block, I.height, I.body, I.status FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.sender = U.user_id LEFT JOIN `#__user_info` AS I ON U.`user_id` = I.`user_id` WHERE F.reciper = " . $user_id . " and F.confirmation = 1 ORDER BY date_confirm DESC";
        $db->setQuery($query, $start, $limit);
        return $db->loadObjectList();
    }

    function getCountSentTokens(){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(`sender`) FROM `#__friends` WHERE `sender` = " . JSFactory::getUser()->user_id . " and `confirmation`=0";
        $db->setQuery($query);

        return $db->loadResult();
    }

    function getSentTokens($start = 0, $limit = 0){
        $db = JFactory::getDBO();
        $query = "SELECT U.user_id, U.block, U.photosite, U.u_name, U.birthday, U.block, F.date_send FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.reciper = U.user_id WHERE F.sender = " . JSFactory::getUser()->user_id . " and F.confirmation = 0 ORDER BY F.date_send DESC";
        $db->setQuery($query, $start, $limit);

        return $db->loadObjectList();
    }

    function getCountReceivedTokens(){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(U.user_id) FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.sender = U.user_id WHERE F.reciper = " . JSFactory::getUser()->user_id . " and F.confirmation = 0 AND U.block = 0";
//        $query = "SELECT COUNT(`sender`) FROM `#__friends` WHERE `reciper` = " . JSFactory::getUser()->user_id . " and `confirmation`=0";
        $db->setQuery($query);

        return $db->loadResult();
    }

    function getReceivedTokens($start = 0, $limit = 0){
        $db = JFactory::getDBO();
        $query = "SELECT U.user_id, U.photosite, U.u_name, U.birthday, U.block, F.date_send FROM `#__friends` AS F LEFT JOIN `#__jshopping_users` AS U ON F.sender = U.user_id WHERE F.reciper = " . JSFactory::getUser()->user_id . " and F.confirmation = 0 AND U.block = 0 ORDER BY F.date_send DESC";
        $db->setQuery($query, $start, $limit);

        return $db->loadObjectList();
    }

    function getIsAccept($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT `id` FROM `#__friends` WHERE `reciper` = " .  JSFactory::getUser()->user_id . " and `sender` = " . $user_id . " and `confirmation`=0";
        $db->setQuery($query);

        if(count($db->loadObjectList()) > 0){
            return true;
        }

        return false;
    }

    function getIsFrieds($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT `id` FROM `#__friends` WHERE ( (`reciper` = " .  JSFactory::getUser()->user_id . " and `sender` = " . $user_id . ") or (`reciper` = " .  $user_id . " and `sender` = " . JSFactory::getUser()->user_id . ") ) and `confirmation`=1";
        $db->setQuery($query);

        if(count($db->loadObjectList()) > 0){
            return true;
        }

        return false;
    }

    function getIsIFiledClaim($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT `id` FROM `#__friends` WHERE `reciper` = " .  $user_id . " and `sender` = " . JSFactory::getUser()->user_id . " and `confirmation`=0";
        $db->setQuery($query);

        if(count($db->loadObjectList()) > 0){
            return true;
        }

        return false;
    }

    function getReferrer($userId){
        $db = JFactory::getDBO();
        $query = "SELECT referrer FROM `#__friends_refer` WHERE ((referrer = " . $userId . " AND recipient = " . JSFactory::getUser()->user_id . ") OR (referrer = " . JSFactory::getUser()->user_id . " AND recipient = " . $userId . "))";
        $db->setQuery($query);
        $referrer = $db->loadResult();
        if(isset($referrer) && $referrer != 0){
            return $referrer;
        } else {
            return false;
        }
    }



    function getReferrerEmail($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT `email` FROM `#__verification` WHERE `user_id` = " . $user_id;
        $db->setQuery($query);
        $email = $db->loadResult();

        if(!$email){
            $query = "SELECT `email` FROM `#__jshopping_users` WHERE `user_id` = " . $user_id;
            $db->setQuery($query);
            $email = $db->loadResult();
        }

        return $email;
    }
}
?>