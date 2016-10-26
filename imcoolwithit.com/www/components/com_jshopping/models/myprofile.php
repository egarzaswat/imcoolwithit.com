<?php
/**
* @version      4.9.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class jshopMyProFile{

    function __construct(){}

    function getProfileQuestions(){
        $db = JFactory::getDBO();

        $query = "SELECT QET.id AS question_id, QET.question, AET.id AS answer_id, AET.answer " . "  FROM `#__questions_earn_tokens` AS QET LEFT JOIN `#__answers_earn_tokens` AS AET ON AET.id_question = QET.id WHERE AET.id_question = QET.id AND QET.type = 'complete_profile'";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getProfileAnswers($user_id){
        $db = JFactory::getDBO();
        $query = "SELECT * " . "FROM `#__user_questions_answers` WHERE user_id = " . $user_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        $return_array = array();

        foreach($result as $value){
            array_push($return_array, array($value->question_id, $value->answer_id));
        }

        return $return_array;
    }

    function getHonestQuestions(){
        $db = JFactory::getDBO();

        $query = "SELECT QET.id AS question_id, QET.question, AET.id as answer_id, AET.answer, AET.negative "
            . "FROM `#__questions_earn_tokens` AS QET LEFT JOIN `#__answers_earn_tokens` AS AET ON AET.id_question = QET.id "
            . "WHERE AET.id_question = QET.id AND QET.type = 'honest_review'";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getPermissionReview($meet_up_id){
        $db = JFactory::getDBO();
        $query = "SELECT * FROM `#__users_reviews` WHERE meet = " . $meet_up_id . " and sender = " . JSFactory::getUser()->user_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if( count($result) > 0 ){
            return false;
        }

        $db = JFactory::getDBO();
        $query = "SELECT sponsor FROM `#__meet_up` WHERE id = " . $meet_up_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if( count($result) == 0 ){
            return false;
        }

        return true;
    }
}
?>