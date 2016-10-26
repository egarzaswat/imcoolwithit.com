<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;
$conf = new JConfig();
$tokens_count = $conf->meet_up_review_tokens;

function addUserAnswers($user, $meet_up, $question, $answer){
    $db = JFactory::getDBO();
    $date = date("Y-m-d H:i:s");

    $query = "INSERT "
           . "INTO {$db->quoteName('#__answered_sponsor_questions')} (user, meet_up, question, answer, date) "
           . "VALUES ({$user}, {$db->escape($meet_up)}, {$db->escape($question)}, {$db->escape($answer)}, '{$date}')";

    $db->setQuery($query);
    $db->query();
}

function addTokens($user, $tokens_count){
    $db = JFactory::getDBO();
    $query = "UPDATE {$db->quoteName('#__user_tokens')} U "
           . "SET U.`count` = U.`count` + {$tokens_count} "
           . "WHERE U.`user_id` = {$user}";
    $db->setQuery($query);
    $db->query();
}

function existsLincUp($lincup_id, $user_id){
    $db = JFactory::getDBO();
    $query = "SELECT COUNT(id) "
        . "FROM {$db->quoteName('#__meet_up')} "
        . "WHERE (sender = {$user_id} or recipient = {$user_id}) and id = {$db->escape($lincup_id)} and occurred = 1";
    $db->setQuery($query);
    $result = $db->loadResult();
    return $result == 1 ? true : false;
}

function isAnswered($lincup_id, $user_id){
    $db = JFactory::getDBO();
    $query = "SELECT COUNT(id) "
        . "FROM {$db->quoteName('#__answered_sponsor_questions')} "
        . "WHERE meet_up = {$db->escape($lincup_id)} and user = {$db->escape($user_id)}";
    $db->setQuery($query);
    $result = $db->loadResult();
    return $result == 1 ? true : false;
}

function getAvailableAnswers($lincup_id){
    $db = JFactory::getDBO();
    $query = "SELECT pa.attr_id as id_question, pa.attr_value_id as id_answer "
        . "FROM {$db->quoteName('#__meet_up')} AS mp "
        . "LEFT JOIN {$db->quoteName('#__jshopping_products_attr2')} AS pa ON mp.sponsor = pa.product_id "
        . "WHERE mp.id = {$lincup_id}";
    $db->setQuery($query);
    return $db->loadAssocList();
}

if (isset($_POST['answers']) && existsLincUp((int)$_POST['meet_up'], $current_user)) {
    $meet_up = (int)$_POST['meet_up'];
    $available_answers = getAvailableAnswers($meet_up);
    $available_answers_array = array();
    foreach ($available_answers as $temp) {
        $available_answers_array[$temp['id_question']][] = $temp['id_answer'];
    }
    $answers = array();
    parse_str($_POST['answers'], $answers);

    if (!isAnswered($meet_up, $current_user)) {
        foreach ($answers as $question => $answer) {
            if (in_array($answer, $available_answers_array[$question])) {
                addUserAnswers($current_user, $meet_up, $question, $answer);
            }
        }
        addTokens($current_user, $tokens_count);
        echo 'success';
    }
} else {
    echo 'data_error';
}