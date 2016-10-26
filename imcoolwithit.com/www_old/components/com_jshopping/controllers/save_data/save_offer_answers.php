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

function addUserAnswers($user, $offer, $question, $answer){
    $db = JFactory::getDBO();
    $date = date("Y-m-d H:i:s");
    $query = "INSERT "
           . "INTO {$db->quoteName('#__answered_offer_questions')} (`user`, `offer`, `question`, `answer`, `date`) "
           . "VALUES ({$user}, {$db->escape($offer)}, {$db->escape($question)}, {$db->escape($answer)}, '{$date}')";
    $db->setQuery($query);
    $db->query();
}

function addTokens($user, $tokens_count){
    $db = JFactory::getDBO();
    $query = "UPDATE {$db->quoteName('#__user_tokens')} U " . "SET U.`count` = U.`count` + {$tokens_count} " . "WHERE U.`user_id` = {$user}";
    $db->setQuery($query);
    $db->query();
}

function getAvailableAnswers($product_id){
    $db = JFactory::getDBO();
    $query = "SELECT attr_id as id_question, attr_value_id as id_answer "
           . "FROM {$db->quoteName('#__jshopping_products_attr2')} "
           . "WHERE product_id = {$product_id}";
    $db->setQuery($query);
    return $db->loadAssocList();
}

$modelSponsor = JSFactory::getModel('sponsors', 'jshop');
if ($modelSponsor->existSponsor((int)$_POST['offer_id']) && !$modelSponsor->checkIfAnswered($current_user, (int)$_POST['offer_id'])) {
    $offer_id = (int)$_POST['offer_id'];
    $available_answers = getAvailableAnswers($offer_id);
    $available_answers_array = array();
    foreach ($available_answers as $temp) {
        $available_answers_array[$temp['id_question']][] = $temp['id_answer'];
    }
    $tokens_count = $modelSponsor->getSponsorData($offer_id, array('tokens'));
    $answers = array();
    parse_str($_POST['answers'], $answers);
    foreach ($answers as $question => $answer) {
        if (in_array($answer, $available_answers_array[$question])) {
            addUserAnswers($current_user, $offer_id, $question, $answer);
        } else {
            echo 'data_error';
            exit;
        }
    }
    addTokens($current_user, (int)$tokens_count[0]['tokens']);
    echo 'success';

} else {
    echo 'data_error';
    exit;
}