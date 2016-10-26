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

class JshoppingModelAnswers extends JModelLegacy {

    function getAllAnswers(){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        //$query = "SELECT * FROM `#__answered_offer_questions`";

        $query = "SELECT aoq.date, aoq.question, a.`".$lang->get("name")."` as question_name,"
                ." aoq.answer, v.`".$lang->get("name")."` as answer_name,"
                ." aoq.offer, p.`".$lang->get("name")."` as offer_name"
                ." FROM `#__answered_offer_questions` as aoq"
                ." LEFT JOIN `#__jshopping_attr` as a ON aoq.question = a.attr_id"
                ." LEFT JOIN `#__jshopping_attr_values` as v ON aoq.answer = v.value_id"
                ." LEFT JOIN `#__jshopping_products` as p ON aoq.offer = p.product_id";

        $db->setQuery($query);
        $list = $db->loadObjectList();

        return $list;
    }

    function getAllUserAnswers($offer, $date_from, $date_to){
        $db = JFactory::getDBO();

        $query = "SELECT `user` FROM `#__answered_offer_questions` ";

        if($offer !=0 || $date_from != 0 || $date_to != 0){
            $query .= 'WHERE ';
        }

        $bool = false;
        if($offer != 0){
            $query .= ' `offer` = ' . $offer . ' ';
            $bool = true;
        }

        $ddd = date_parse($date_from);

        if($date_from != 0){
            if(!$bool){

                $query .= " `date` >= '" . date("Y-m-d", strtotime($date_from)) . "' ";
            } else {
                $query .= " AND `date` >= '" . date("Y-m-d", strtotime($date_from)) . "' ";
            }
            $bool = true;
        }

        if($date_to != 0){
            if(!$bool){

                $query .= " `date` <= '" . date("Y-m-d H:M:S", strtotime($date_to . ' 23:59:59')) . "' ";
            } else {
                $query .= " AND `date` <= '" . date("Y-m-d H:M:S", strtotime($date_to . ' 23:59:59')) . "' ";
            }
            $bool = true;
        }

        $query .= ' GROUP BY `user`';

        $db->setQuery($query);
        $result = $db->loadObjectList();
        return count($result);
    }

    function getAllOffers(){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT p.product_id as offer, p.`".$lang->get("name")."` as offer_name"
                ." FROM `#__jshopping_products_to_categories` as ptc"
                ." LEFT JOIN `#__jshopping_products` as p ON ptc.product_id = p.product_id"
                ." WHERE category_id = 1";

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>