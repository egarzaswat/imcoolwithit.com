<?php
defined('_JEXEC') or die('Restricted access');


class jshopUsersList{

    function __construct(){}

    function getRejectUsers(){
        $db = JFactory::getDBO();
        $query = "SELECT `id_user_guest` FROM `#__rejected_users` where `id_user_active` = " . JSFactory::getUser()->user_id;
        $db->setQuery($query);
        $result = array();
        foreach($db->loadObjectList() as $key => $value){
            $result[$key] = $value->id_user_guest;
        }

        return $result;
    }

    function getConditionSearch($searchParams, $quickConnect){
        $condition = " WHERE ";
        if($searchParams['looking_for'] == 3){
            $condition .= "(`sex` = 1 or `sex` = 2)";
        } else {
            $condition .= "`sex` = " . $searchParams['looking_for'];
        }

        $condition .= " and TIMESTAMPDIFF(YEAR,U.`birthday`,curdate()) > " . $searchParams['age_look_from'] . " and TIMESTAMPDIFF(YEAR,U.`birthday`,curdate()) < " . $searchParams['age_look_to'];
        $condition .= " and ( 3958.0*acos( sin(U.`latitude`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(U.`latitude`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(U.`longitude`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) < " . $searchParams['distance'] . ")";
        $condition .= " and U.`user_id` <> " . JSFactory::getUser()->user_id;
        $condition .= " and U.`block` = 0 and U.`register_activate` = 1";
        if($quickConnect){
            $condition .= " and F.`id` is null";
        }

        return $condition;
    }

    function getCountItems($searchParams, $quickConnect = false){
        $db = JFactory::getDBO();
        $adv_user = JSFactory::getUser()->user_id;

        $where = $this->getConditionSearch($searchParams, $quickConnect);
        if($quickConnect){
            $query = "SELECT COUNT(U.`user_id`) FROM `#__jshopping_users` AS U LEFT JOIN `#__friends` as F ON (F.sender=U.user_id OR F.reciper=U.user_id) AND F.confirmation = 1 AND (F.reciper = " . $adv_user . " OR F.sender = " . $adv_user . ")" . $where;
        } else {
            $query = "SELECT COUNT(U.`user_id`) FROM `#__jshopping_users` AS U" . $where;
        }
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count;
    }

    function usersList($searchParams, $start = 0, $limit = 0, $fields_arr = array(), $info_fields_array = array(), $quickConnect = false){
        $db = JFactory::getDBO();
        $adv_user = JSFactory::getUser()->user_id;

        if(count($fields_arr)>0 || count($info_fields_array)>0){
            $fields = ' ';
            $cf = false;

            foreach($fields_arr as $key => $value){
                if($key == 0){
                    $fields .= 'U.`' . $value . '`';
                    $cf = true;
                } else {
                    $fields .= ', U.`' . $value . '`';
                    $cf = true;
                }
            }

            foreach($info_fields_array as $key1 => $value){
                if($key1 == 0 && !$cf){
                    $fields .= 'I.`' . $value . '`';
                } else {
                    $fields .= ', I.`' . $value . '`';
                }
            }

            $fields .= ", 3958.0*acos( sin(U.`latitude`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(U.`latitude`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(U.`longitude`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) AS radius ";
        } else {
            $fields = "
            U.`user_id`,
            U.`fb_id`,
            U.`title`,
            U.`f_name`,
            U.`l_name`,
            U.`u_name`,
            U.`birthday`,
            U.`photo`,
            U.`photosite`,
            U.`zip`,
            U.`longitude`,
            U.`latitude`,
            U.`last_visit`,
              3958.0*acos( sin(U.`latitude`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(U.`latitude`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(U.`longitude`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) AS radius ";
        }

        $where = $this->getConditionSearch($searchParams, $quickConnect);

        if($quickConnect){
            $query = "SELECT " . $fields . " FROM `#__jshopping_users` AS U LEFT JOIN `#__friends` as F ON (F.sender=U.user_id OR F.reciper=U.user_id) AND (F.reciper = " . $adv_user . " OR F.sender = " . $adv_user . ")" . $where . " ORDER BY `radius` ASC";
        } else {
            $query = "SELECT " . $fields . " FROM `#__jshopping_users` AS U LEFT JOIN `#__user_info` AS I ON U.`user_id` = I.`user_id`" . $where . " ORDER BY `radius` ASC";
        }

        $db->setQuery($query);
        $result_all_users = $db->loadObjectList();

        $reject_users = $this->getRejectUsers();

        $result_reject_users = array();
        foreach($result_all_users as $key => $value){
            if(in_array($value->user_id, $reject_users)){
                array_push($result_reject_users, $value);
                unset($result_all_users[$key]);
            }
        }

        $return_result = array();
        $result = array();
        foreach($result_all_users as $value){
            array_push($result, $value);
        }
        foreach($result_reject_users as $value){
            array_push($result, $value);
        }

        if($start != -1 && $limit != -1){
            for($i=$start; $i<$start+$limit; $i++){
                if(isset($result[$i])){
                    array_push($return_result, $result[$i]);
                }
            }
            return $return_result;
        } else{
            return $result;
        }
    }

    function calculateDistance($lat1, $lon1, $lat2, $lon2){
        $dist = 3958.0*acos( sin($lat1/57.29577951)*sin($lat2/57.29577951) + cos($lat1/57.29577951)*cos($lat2/57.29577951)*cos($lon1/57.29577951 - $lon2/57.29577951) );

        return round($dist);
    }

    function searchParamsCurrentUser($currentUser){
        $searchParams = array(
            'looking_for' => $currentUser->looking_for,
            'age_look_from' => $currentUser->age_look_from,
            'age_look_to' => $currentUser->age_look_to,
            'distance' => $currentUser->distance,
            'zip' => $currentUser->zip,
            'longitude' => $currentUser->longitude,
            'latitude' => $currentUser->latitude
        );

        return $searchParams;

    }

}
?>