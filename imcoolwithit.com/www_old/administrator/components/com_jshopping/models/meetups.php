<?php
/**
 * @version      4.8.0 04.06.2011
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class JshoppingModelMeetUps extends JModelLegacy {

    function getAllSponsors(){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT p.product_id as sponsor, p.`".$lang->get("name")."` as sponsor_name"
            ." FROM `#__jshopping_products_to_categories` as ptc"
            ." LEFT JOIN `#__jshopping_products` as p ON ptc.product_id = p.product_id"
            ." LEFT JOIN `#__jshopping_categories` as c ON ptc.category_id = c.category_id"
            ." WHERE c.category_parent_id = 2";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getOccurredMeetUps($sponsor = null){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT m.id, s.u_name as sender_name, r.u_name as recipient_name, p.product_id as sponsor,"
            ." p.`".$lang->get("name")."` as sponsor_name, m.occurred_date FROM `#__meet_up` as m"
            ." LEFT JOIN `#__jshopping_users` as s ON m.sender = s.user_id"
            ." LEFT JOIN `#__jshopping_users` as r ON m.recipient = r.user_id"
            ." LEFT JOIN `#__jshopping_products` as p ON m.sponsor = p.product_id"
            ." WHERE m.occurred = 1";

        if($sponsor != null) { $query .= " AND m.sponsor = " . $sponsor;}

        $query .=" ORDER BY m.occurred_date DESC";

        $db->setQuery($query);
        $list = $db->loadObjectList();

        return $list;
    }

    function getMeetUpReview($id = 0){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT u.u_name, aoq.date,"
            ." aoq.question, a.`".$lang->get("name")."` as question_name,"
            ." aoq.answer, v.`".$lang->get("name")."` as answer_name"
            ." FROM `#__answered_sponsor_questions` as aoq"
            ." LEFT JOIN `#__jshopping_users` as u ON aoq.user = u.user_id"
            ." LEFT JOIN `#__jshopping_attr` as a ON aoq.question = a.attr_id"
            ." LEFT JOIN `#__jshopping_attr_values` as v ON aoq.answer = v.value_id";

        if($id != 0) {
            $query .= " WHERE aoq.meet_up = " . $id;
        }

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getStatisticsMeetUpReview($id_sponsor = 0, $date_from = 0, $date_to = 0){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT spons.product_id AS sponsor_id, spons.`" . $lang->get("name") . "` as sponsor, asq.date, asq.question, a.`".$lang->get("name")."` as question_name, asq.answer, v.`".$lang->get("name")."` as answer_name FROM `#__answered_sponsor_questions` as asq LEFT JOIN `#__jshopping_attr` as a ON asq.question = a.attr_id LEFT JOIN `#__jshopping_attr_values` as v ON asq.answer = v.value_id LEFT JOIN `#__meet_up` as met ON asq.meet_up = met.id LEFT JOIN `#__jshopping_products` AS spons ON spons.product_id = met.sponsor";

        $bool = false;
        if($id_sponsor != 0 || $date_from != 0 || $date_to != 0){
            $query .= " WHERE ";
        }
        if($id_sponsor != 0){
            $query .= "met.sponsor = " . $id_sponsor;
            $bool = true;
        }
        if($date_from != 0){
            if($bool){
                $query .= " AND '" . date("y-m-d", strtotime($date_from)) . "' <= asq.date";
            } else {
                $query .= "'" . date("y-m-d", strtotime($date_from)) . "' <= asq.date";
                $bool = true;
            }
        }

        if($date_to != 0){
            if($bool){
                $query .= " AND '" . date("y-m-d", strtotime($date_to . ' 23:59:59')) . "' >= asq.date";
            } else {
                $query .= "'" . date("y-m-d", strtotime($date_to . ' 23:59:59')) . "' >= asq.date";
            }
        }

        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getAllUserMeetAnswers($offer, $date_from, $date_to){
        $db = JFactory::getDBO();
        $query = "SELECT asq.`user` FROM `#__answered_sponsor_questions` AS asq LEFT JOIN `#__meet_up` as met ON `asq`.meet_up = `met`.id LEFT JOIN `#__jshopping_products` AS spons ON `spons`.product_id = met.`sponsor` ";

        if($offer !=0 || $date_from != 0 || $date_to != 0){
            $query .= ' WHERE ';
        }

        $bool = false;
        if($offer != 0){
            $query .= ' spons.`product_id` = ' . $offer . ' ';
            $bool = true;
        }

        $ddd = date_parse($date_from);

        if($date_from != 0){
            if(!$bool){

                $query .= " asq.`date` >= '" . date("Y-m-d", strtotime($date_from)) . "' ";
            } else {
                $query .= " AND asq.`date` >= '" . date("Y-m-d", strtotime($date_from)) . "' ";
            }
            $bool = true;
        }

        if($date_to != 0){
            if(!$bool){

                $query .= " asq.`date` <= '" . date("Y-m-d H:M:S", strtotime($date_to . ' 23:59:59')) . "' ";
            } else {
                $query .= " AND asq.`date` <= '" . date("Y-m-d H:M:S", strtotime($date_to . ' 23:59:59')) . "' ";
            }
            $bool = true;
        }

        $query .= ' GROUP BY asq.`user`';

        $db->setQuery($query);
        $result = $db->loadObjectList();
        return count($result);
    }
}
?>