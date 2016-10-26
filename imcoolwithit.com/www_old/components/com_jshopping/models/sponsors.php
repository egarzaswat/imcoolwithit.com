<?php
defined('_JEXEC') or die('Restricted access');


class jshopSponsors{

    function __construct(){}

    function getSponsorsCategories($fields = array()){
        $db = JFactory::getDBO();

        $query = "SELECT ";
        if(count($fields) > 0){
            foreach($fields as $key => $value){
                if($key == 0){
                    $query .= "`" . $value . "`";
                } else {
                    $query .= ", `" . $value . "`";
                }
            }
        } else {
            $query .= "*";
        }

        $query .= " FROM `#__jshopping_categories` WHERE category_parent_id = 2";

        $db->setQuery($query);
        return $db->loadAssocList();
    }

    function getSponsorsWithCategory($id_category, $searchParams){
        $db = JFactory::getDBO();

        $select = "SELECT P.product_id, P.image, P.`name_" . JSFactory::getLang()->lang . "` AS name, 3958.0*acos( sin(`latitude_" . JSFactory::getLang()->lang . "`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(`latitude_" . JSFactory::getLang()->lang . "`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(`longitude_" . JSFactory::getLang()->lang . "`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) AS radius ";

        $from = "FROM `#__jshopping_products_to_categories` AS PTC LEFT JOIN `#__jshopping_products` as P ON PTC.product_id = P.product_id ";

        $where = "WHERE PTC.category_id = " . $id_category . " AND ";

        $where .= "3958.0*acos( sin(`latitude_" . JSFactory::getLang()->lang . "`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(`latitude_" . JSFactory::getLang()->lang . "`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(`longitude_" . JSFactory::getLang()->lang . "`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) <= 50 ";

        $where .= "ORDER BY radius ASC limit 3";

//        $where = "WHERE PTC.category_id = " . $id_category . " ORDER BY radius ASC limit 3";

        $query = $select . $from . $where;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getSponsorData($id_sponsor, $fields = array()){
        $db = JFactory::getDBO();

        $query = "SELECT ";
        if(count($fields) > 0){
            foreach($fields as $key => $value){
                if($key == 0){
                    $query .= "`" . $value . "`";
                } else {
                    $query .= ", `" . $value . "`";
                }
            }
        } else {
            $query .= "*";
        }

        $query .= " FROM `#__jshopping_products` WHERE product_id = " .$id_sponsor;

        $db->setQuery($query);
        return $db->loadAssocList();
    }

    function existSponsor($id){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM `#__jshopping_products` WHERE `product_id` = " . $id;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if( count($rows) > 0 ){
            return true;
        } else {
            return false;
        }

        /*$query = "SELECT COUNT(*) FROM `#__jshopping_products` WHERE `product_id` = " . $id;
        $db->setQuery($query);
        return $db->loadResult() == 0 ? false : true;*/
    }

    function getOffersForUserCount($searchParams){
        $db = JFactory::getDBO();

        $select = "SELECT COUNT(P.product_id) ";

        $from = "FROM `#__jshopping_products` AS P LEFT JOIN `#__jshopping_users` AS U ON U.user_id = " . JSFactory::getUser()->user_id . " LEFT JOIN `#__products_offer_extra_options` AS EX ON EX.product_id = P.product_id LEFT JOIN `#__answered_offer_questions` AS AOQ ON AOQ.user = " . JSFactory::getUser()->user_id . " AND AOQ.offer = P.product_id ";

        $where = "WHERE ((EX.from_age < TIMESTAMPDIFF(YEAR, U.`birthday`, curdate())) AND (EX.to_age > TIMESTAMPDIFF(YEAR, U.`birthday`, curdate()))) AND ( 3958.0*acos( sin(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(P.`longitude_" . JSFactory::getLang()->lang . "`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ")) < EX.distance) AND ((EX.male = 1 AND U.sex = 2) OR (EX.female = 1 AND U.sex = 1)) AND (EX.permanent = 1 OR EX.expires > curdate()) AND AOQ.id is NULL";

        $query = $select . $from . $where;

        $db->setQuery($query);
        return $db->loadResult();
    }

    function getOffersForUser($searchParams, $start = 0, $limit = 0){
        $db = JFactory::getDBO();

        $select = "SELECT P.product_id, P.image, P.`name_" . JSFactory::getLang()->lang . "` AS name, P.tokens, EX.permanent, EX.expires, 3958.0*acos( sin(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(P.`longitude_" . JSFactory::getLang()->lang . "`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) AS radius ";

        $from = " FROM `#__jshopping_products` as P LEFT JOIN `#__jshopping_users` AS U ON U.user_id = " . JSFactory::getUser()->user_id ." LEFT JOIN `#__products_offer_extra_options` AS EX ON EX.product_id = P.product_id LEFT JOIN `#__answered_offer_questions` AS AOQ ON AOQ.user = " . JSFactory::getUser()->user_id . " AND AOQ.offer = P.product_id ";

        $where = "WHERE ((EX.from_age <= TIMESTAMPDIFF(YEAR, U.`birthday`, curdate())) AND (EX.to_age >= TIMESTAMPDIFF(YEAR, U.`birthday`, curdate()))) AND ( 3958.0*acos( sin(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951)*sin(" . ($searchParams['latitude']/57.29577951) . ") + cos(P.`latitude_" . JSFactory::getLang()->lang . "`/57.29577951 )*cos(" . ($searchParams['latitude']/57.29577951 ) . ")*cos(P.`longitude_" . JSFactory::getLang()->lang . "`/57.29577951 - " . $searchParams['longitude']/57.29577951 . ") ) < EX.distance) AND ((EX.male = 1 AND U.sex = 2) OR (EX.female = 1 AND U.sex = 1)) AND (EX.permanent = 1 OR EX.expires > curdate()) AND AOQ.id is NULL ";

        $query = $select . $from . $where . "ORDER BY radius ASC";

        $db->setQuery($query, $start, $limit);
        return $db->loadObjectList();
    }

    function getSponsorQuestions($product_id){
        $db = JFactory::getDBO();

        $select = "SELECT P.image, P.`name_" . JSFactory::getLang()->lang . "` AS product_name, P.tokens, A.attr_id, A.`name_" . JSFactory::getLang()->lang . "` AS attr_name, V.value_id, V.`name_" . JSFactory::getLang()->lang . "` AS value_name ";

        $from = "FROM `#__jshopping_products_attr2` AS PA LEFT JOIN `#__jshopping_products` AS P ON PA.product_id = P.product_id LEFT JOIN `#__jshopping_attr` AS A ON PA.attr_id = A.attr_id LEFT JOIN `#__jshopping_attr_values` AS V ON PA.attr_value_id = V.value_id ";

        $where = "WHERE PA.product_id = " . $product_id;

        $query = $select . $from . $where;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function checkIfAnswered($user_id, $product_id){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) " . "FROM `#__answered_offer_questions` WHERE offer = " . $product_id . " AND user = " . $user_id;

        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result == 0) {return false;} else {return true;}
    }

    function checkOffer($product_id){
        $db = JFactory::getDBO();

        $query = "SELECT category_id " . "FROM `#__jshopping_products_to_categories` WHERE product_id = " . $product_id;

        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result == 1) {return true;} else {return false;}
    }

}
?>