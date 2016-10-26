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

class JshoppingModelMeet_Ups extends JModelLegacy {

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

    function getMeetUpReview($id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();

        $query = "SELECT u.u_name, aoq.date,"
            ." aoq.question, a.`".$lang->get("name")."` as question_name,"
            ." aoq.answer, v.`".$lang->get("name")."` as answer_name"
            ." FROM `#__answered_sponsor_questions` as aoq"
            ." LEFT JOIN `#__jshopping_users` as u ON aoq.user = u.user_id"
            ." LEFT JOIN `#__jshopping_attr` as a ON aoq.question = a.attr_id"
            ." LEFT JOIN `#__jshopping_attr_values` as v ON aoq.answer = v.value_id"
            ." WHERE aoq.meet_up = " . $id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>