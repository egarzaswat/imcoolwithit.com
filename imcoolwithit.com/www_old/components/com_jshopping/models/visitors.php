<?php
defined('_JEXEC') or die('Restricted access');


class jshopVisitors{

    function __construct(){}

    function getVisitors($start = 0, $limit = 0){
        $db	= JFactory::getDbo();
        $query	= "SELECT V.id, V.date, V.read, U.user_id, U.u_name, U.birthday, U.photosite, U.longitude, U.latitude, U.sex, U.last_visit, U.block, I.height, I.body, I.status FROM `#__visitors` AS V LEFT JOIN `#__jshopping_users` as U ON V.visitor_id=U.user_id LEFT JOIN `#__user_info` AS I ON U.user_id = I.user_id WHERE V.owner_id = " . JSFactory::getUser()->user_id . " AND U.block = 0 ORDER BY V.date DESC";
        $db->setQuery($query, $start, $limit);
        $result = $db->loadAssocList();

        return $result;
    }

    function getCountVisitors(){
        $db = JFactory::getDBO();
        $query	= "SELECT COUNT(V.id) FROM `#__visitors` AS V LEFT JOIN `#__jshopping_users` as U ON V.visitor_id=U.user_id WHERE V.owner_id = " . JSFactory::getUser()->user_id . " AND U.block = 0";
        $db->setQuery($query, 0, 0);
        $count_visitors = $db->loadResult();

        return $count_visitors;
    }

    function getProfileCountNewVisitors(){
        $db = JFactory::getDBO();
        $query	= "SELECT COUNT(V.id) FROM `#__visitors` AS V LEFT JOIN `#__jshopping_users` as U ON V.visitor_id=U.user_id WHERE V.owner_id = " . JSFactory::getUser()->user_id . " AND V.read = 0 AND U.block = 0";
        $db->setQuery($query, 0, 0);
        $count_visitors = $db->loadResult();

        return $count_visitors;
    }

    function addVisitor($id){
        $db = JFactory::getDBO();

        $query = "select * from  `#__visitors` where `visitor_id`=" . JSFactory::getUser()->user_id . " and
        `owner_id`=" . $id;
        $db->setQuery($query);
        $last_my_visit = $db->loadObjectList();
        $last_my_visit = $last_my_visit[0];

        if($last_my_visit == null){
            $query = "insert into `#__visitors` SET
                    `visitor_id`=" . JSFactory::getUser()->user_id . "," .
                " `owner_id`=".$id . "," .
                " `date`='" . date("Y-m-d H:i:s") . "'";
        } else {
            $query = "update `#__visitors` set `date`='" . date("Y-m-d H:i:s") . "', `read`=0 where `id`=" . $last_my_visit->id;
        }

        $db->setQuery($query);
        $db->query();
    }

}
?>