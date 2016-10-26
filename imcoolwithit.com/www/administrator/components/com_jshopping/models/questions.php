<?php
/**
 * @version      4.1.0 13.02.2011
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelQuestions extends JModelLegacy{

    function getAllQuestions() {
        $db = JFactory::getDBO();

        $query = "SELECT q.id as question_id, q.question as question_name, q.type, a.id as answer_id, a.answer as answer_name"
                ." FROM `#__questions_earn_tokens` as q LEFT JOIN `#__answers_earn_tokens` as a ON q.id = a.id_question";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getAnswers($question_id){
        $db = JFactory::getDBO();

        $query = "SELECT id as answer_id, answer as answer_name, negative"
            ." FROM `#__answers_earn_tokens` WHERE id_question = " . $question_id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

}
?>