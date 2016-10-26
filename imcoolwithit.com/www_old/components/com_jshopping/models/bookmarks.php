<?php
defined('_JEXEC') or die('Restricted access');


class jshopBookmarks{

    function __construct(){}

    function getCountBookmarks($userId){
        $db = JFactory::getDBO();

        $query = " SELECT COUNT(`reciper`) FROM `#__bookmarks` WHERE `sender` = ".$userId." ORDER BY `date` DESC ";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllMyBookmarks($userId, $start = 0, $limit = 0){
        $db = JFactory::getDBO();

        $query = " SELECT B.date, B.reciper, U.user_id, U.u_name, U.birthday, U.photosite, U.longitude, U.latitude, U.sex, U.last_visit, U.block, I.height, I.body, I.status FROM `#__bookmarks` AS B LEFT JOIN `#__jshopping_users` AS U ON B.reciper = U.user_id LEFT JOIN `#__user_info` AS I ON U.user_id = I.user_id WHERE B.sender = ".$userId." ORDER BY B.date DESC ";
        $db->setQuery($query, $start, $limit);
        return $db->loadObjectList();
    }

    function removeBookmark($currentUserId, $userIdRemoveToBookmarks){
        $db = JFactory::getDBO();

        $query = " DELETE FROM `#__bookmarks` WHERE `sender`='".$currentUserId."' AND `recieper`='".$userIdRemoveToBookmarks."' ";
        $db->setQuery($query);
        $db->query();
    }

    function addUserToBookmark($userId, $userIdAddToBookmarks){
        $db = JFactory::getDBO();
        $date = date("Y-m-d H:i:s");
        $bookmarkList = $this->getAllMyBookmarks($userId);

        foreach($bookmarkList as $bookmark):
            if($bookmark->recieper == $userIdAddToBookmarks) return 0;
        endforeach;

        $query = "INSERT INTO `#__bookmarks` SET
                      `sender`='".$db->escape($userId)."',
                      `reciper`='".$db->escape($userIdAddToBookmarks)."',
                      `date`='".$db->escape($date)."'";

        $db->setQuery($query);
        $db->query();
        return 1;
    }

}
?>