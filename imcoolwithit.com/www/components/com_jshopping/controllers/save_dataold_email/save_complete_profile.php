<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php');
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;

function getUserAnswer($user_id){
    $db = JFactory::getDBO();
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__user_questions_answers'))
        ->where($db->quoteName('user_id') . ' = ' . $db->escape($user_id));
    $db->setQuery($query);
    $result = $db->loadObjectList();

    $return_array = array();
    foreach($result as $value){
        array_push($return_array, $value->question_id);
    }

    return $return_array;
}

function addUserAnswers($data, $user_id){
    $db = JFactory::getDBO();

    if(count($data->insert) > 0){
        $query = "INSERT " . "INTO `#__user_questions_answers` (`user_id`, `question_id`, `answer_id`, `date`) VALUES ";
        foreach($data->insert as $key => $value){
            if($key == 0){
                $query .= "({$user_id}, {$value['question']}, {$value['answer']}, '" . date("Y-m-d H:i:s"). "')";
            } else {
                $query .= ", ({$user_id}, {$value['question']}, {$value['answer']}, '" . date("Y-m-d H:i:s"). "')";
            }
        }

        $db->setQuery($query);
        $db->query();
    }

    if(count($data->update) > 0){
        $query = "UPDATE `#__user_questions_answers` " . "SET `answer_id`= CASE ";
        foreach($data->update as $key => $value){
            $query .= " WHEN `question_id` = {$value['question']} THEN {$value['answer']}";
        }
        $query .= " END WHERE `user_id` = {$user_id}";

        $db->setQuery($query);
        $db->query();
    }

    $user_answers = getUserAnswer($user_id);

    $db = JFactory::getDBO();
    $query = "SELECT * " . "FROM `#__questions_earn_tokens` " . "WHERE `type` = 'complete_profile'";
    $db->setQuery($query);
    $result = $db->loadObjectList();

    $conf = new JConfig();
    $count_tokens = $conf->count_tokens_complete_profile;

    if(count($user_answers) == count($result)){
        $query = "SELECT `complete_profile` " . "FROM `#__jshopping_users` " . "WHERE `user_id` = {$user_id}";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if($result[0]->complete_profile == 0){
            $query = "UPDATE `#__user_tokens` U " . "SET U.`count` = (U.`count` + {$count_tokens}) WHERE U.`user_id` = {$user_id}";
            $db->setQuery($query);
            $db->query();

            $query = "UPDATE `#__jshopping_users` " . "SET `complete_profile` = 1, `date_complete` = '" . date("Y-m-d H:i:s") . "' WHERE `user_id` = {$user_id}";
            $db->setQuery($query);
            $db->query();

            return "add_tokens";
        }
    }

    return "success";
}

function getAvailableAnswers(){
    $db = JFactory::getDBO();
    $query = "SELECT aet.id as id_answer, aet.id_question "
           . "FROM `#__questions_earn_tokens` AS qet "
           . "LEFT JOIN `#__answers_earn_tokens` AS aet ON qet.id = aet.id_question "
           . "WHERE qet.`type` = 'complete_profile'";
    $db->setQuery($query);
    return $db->loadAssocList();
}

$available_answers = getAvailableAnswers();
$available_answers_array = array();
foreach ($available_answers as $temp) {
    $available_answers_array[$temp['id_question']][] = $temp['id_answer'];
}

$user_answers = $_POST;
$data = array();
foreach ($user_answers as $question => $answer) {
    if (in_array($answer, $available_answers_array[$question])) {
        array_push($data, array(
            'question'  => $question,
            'answer'    => $answer
        ));
    }
}

if (count($data) > 0) {
    $base_user_answer = getUserAnswer($current_user);
    $data_sql = new stdClass();
    $data_sql->insert = array();
    $data_sql->update = array();

    if (count($base_user_answer) == 0) {
        $data_sql->insert = $data;
    } else {
        foreach($data as $key => $value){
            if(in_array($value['question'], $base_user_answer)){
                array_push($data_sql->update, $value);
            } else {
                array_push($data_sql->insert, $value);
                array_push($data_sql->update, $value);
            }
        }
    }
    echo addUserAnswers($data_sql, $current_user);
}

exit;