<?php
defined('_JEXEC') or die('Restricted access');


class jshopUser{

    function __construct(){}

    function getDataUser($id, $fields = array(), $get_info = false){
        $db = JFactory::getDBO();

        $query = "select ";
        if(count($fields) > 0){
            foreach($fields as $key => $value){
                if($key == 0){
                    $query .= "U.`" . $value . "`";
                } else {
                    $query .= ", U.`" . $value . "`";
                }
            }
        } else {
            $query .= "*";
        }

        if($get_info){
            if(count($fields) > 0){
                $query .= ", I.`height`, I.`status`, I.`ethnicity`, I.`body`, I.`profession`, I.`religion`, I.`kids`, I.`user_about`, I.`look_qualites`, I.`recommend`, I.`few_places`, I.`relationship_type`";
            }
            $query .= " from `#__jshopping_users` AS U LEFT JOIN `#__user_info` AS I ON U.user_id = I.user_id where U.user_id = " . $id;
        } else {
            $query .= " from `#__jshopping_users` AS U where U.`user_id` = " . $id;
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows[0];
    }

    function existUser($id){
        $db = JFactory::getDBO();

        $query = "select `u_name` from `#__jshopping_users` where `user_id` = " . $id;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if( count($rows) > 0 ){
            return true;
        } else {
            return false;
        }
    }

    function isBlockUser($id){
        $db = JFactory::getDBO();

        $query = "select `u_name` from `#__jshopping_users` where `user_id` = " . $id . " and `block` = 1";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if( count($rows) > 0 ){
            return true;
        } else {
            return false;
        }
    }

    function isVerification($id){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(`id`) FROM `#__verification` WHERE `user_id` = " . $id . ' AND `active` = 1';
        $db->setQuery($query);
        $result = $db->loadResult();
        if( $result > 0 ){
            return true;
        } else {
            return false;
        }
    }

    function isVerifyUser($id, $hash){
        $db = JFactory::getDBO();
        $query = "SELECT `active` FROM `#__verification` WHERE `user_id` = " . $id . " AND `hash` = '" . $hash . "'";
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function verifyEmail($user_id, $email, $hash, $count_tokens){
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(id) " . "FROM `jproject_verification` " . "WHERE `user_id` = " . $user_id ." AND `email` = '" . $email . "' AND `hash` = '" . $hash . "' AND active = 0";
        $db->setQuery($query);
        $result = $db->loadResult();

        if($result > 0){
            $query = "UPDATE `jproject_verification` " . "SET active = 1, date = '" . date("Y-m-d H:i:s") . "' WHERE user_id = " . $user_id;
            $db->setQuery($query);
            $db->query();

            $query = "UPDATE `jproject_user_tokens` SET `count` = (`count` + " . $count_tokens . ") WHERE `user_id` = " . $user_id;
            $db->setQuery($query);
            $db->query();
        }
    }

    function getCountUserTokens($user_id){
        $db = JFactory::getDBO();
        $query = "SELECT `count` FROM `#__user_tokens` WHERE `user_id` = " . $user_id;
        $db->setQuery($query);
        $result = $db->loadResult();

        if($result > 99){
            $query = "UPDATE `#__user_tokens` SET `count`  = 99 WHERE `user_id` = " . $user_id;
            $db->setQuery($query);
            $db->query();
            $result = 99;
        }

        return $result;
    }

    function getUserAlbum($user_id){
        $db = JFactory::getDBO();
        $query = "select `photo`, `private` from `#__users_photos` where `user_id` = " . $user_id;
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    function getImagesCount($user_id){
        $db = JFactory::getDBO();
        $query = "select COUNT(id) from `#__users_photos` where `user_id` = " . $user_id;
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function getUserAlbumProfile($user_id){
        $db = JFactory::getDBO();
        $query = "select `id`, `photo`, `private`, `avatar` from `#__users_photos` where `user_id` = " . $user_id . " and `private` = 0 ORDER BY `avatar` DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows;
    }

    function getUserAndLikes($user_id){
        $db = JFactory::getDBO();
        $query = "select PH.`id`, LI.`id` as `like`, PH.`photo`, PH.`private`, PH.`avatar` from `#__users_photos` as `PH` LEFT JOIN  `#__likes` AS `LI` ON PH.`id` = LI.`photo_id` AND LI.`user_id` = " . JSFactory::getUser()->user_id . " where PH.`user_id` = " . $user_id . " and PH.`private` = 0 ORDER BY `avatar` DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows;
    }

    function getUserAlbumPrivate($user_id){
        $db = JFactory::getDBO();
        $query = "select `photo` from `#__users_photos` where `user_id` = " . $user_id . " and `private` = 1";
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    function getUserBlock($user_id){
        $db = JFactory::getDBO();
        $query = "select `block` from `#__jshopping_users` where `user_id` = " . $user_id;
        $db->setQuery($query);
        $block = $db->loadResult();
        if($block == 0){
            return false;
        } else {
            return true;
        }
    }

    function getMembershipType($user_id){
        $db = JFactory::getDBO();
        $query = "select `relationship_type` from `#__user_info` where `user_id` = " . $user_id;
        $db->setQuery($query);
        return $db->loadResult();
    }

    function setMembershipType($user_id, $type){
        $db = JFactory::getDBO();
        $query = "UPDATE `#__user_info` SET `relationship_type` = '" . $type . "' WHERE `user_id` = " . $user_id;
        $db->setQuery($query);
        $db->query();
    }

    function setUserActive($user_id, $active){
        $db = JFactory::getDBO();
        $query = "UPDATE `#__jshopping_users` SET `block` = '" . $active . "' WHERE `user_id` = " . $user_id;
        $db->setQuery($query);
        $db->query();
        return true;
    }

    function setLikePhoto($photo_id){
        $db = JFactory::getDBO();
        $query = "INSERT INTO `#__likes` (`user_id`, `photo_id`) VALUES (" . JSFactory::getUser()->user_id . ", " . $photo_id . ")";
        $db->setQuery($query);
        $db->query();
        return true;
    }
}
?>