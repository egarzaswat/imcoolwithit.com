<?php
defined('_JEXEC') or die('Restricted access');


class jshopVerify_email{

    function __construct(){}

    function checkIfActive($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT active " . "FROM `#__verification` WHERE user_id = " .$user_id;

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result == 1 ? true : false;
    }

    function checkIfExists($user_id){
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) " . "FROM `#__verification` WHERE user_id = " .$user_id;

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result == 1 ? true : false;
    }

    function saveVerification($user_id, $email, $hash){
        $db = JFactory::getDBO();

        $query = "INSERT INTO `#__verification` (user_id, email, hash, active) VALUES ('$user_id', '$email', '$hash', 0)";

        $db->setQuery($query);
        $db->query();
    }

    function updateVerification($user_id, $email, $hash){
        $db = JFactory::getDBO();

        $query = "UPDATE `#__verification` SET email = '$email', hash = '$hash' WHERE user_id = " .$user_id;

        $db->setQuery($query);
        $db->query();
    }

    function activate($user_id){
        $db = JFactory::getDBO();

        $query = "UPDATE `#__verification` " . "SET active = true WHERE user_id = " . $user_id;

        $db->setQuery($query);
        $db->query();
    }

}
?>