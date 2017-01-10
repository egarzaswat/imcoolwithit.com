<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/models/meeting.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? removeDirectory($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}

$users_list = array(
    463,
    406,
);
$db = JFactory::getDbo();

foreach($users_list as $value){
    removeDirectory(JPATH_BASE . '/images/albums/user_' . $value);

    $query = "SELECT `photosite` FROM `jproject_jshopping_users` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $photo_name = $db->loadRow();
    $photo_name = $photo_name[0];

    unlink(JPATH_BASE . '/images/user_photo/' . $photo_name);
    unlink(JPATH_BASE . '/images/user_photo/medium/' . $photo_name);
    unlink(JPATH_BASE . '/images/user_photo/small/' . $photo_name);


    $query = "DELETE FROM `jproject_answered_offer_questions` WHERE `user` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_answered_sponsor_questions` WHERE `user` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_forgot` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_user_info` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_user_questions_answers` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_user_tokens` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_users_photos` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_verification` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_bookmarks` WHERE `sender` = " . $value . " OR `reciper` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_friends` WHERE `sender` = " . $value . " OR `reciper` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_friends_refer` WHERE `referrer` = " . $value . " OR `recipient` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_meet_up` WHERE `sender` = " . $value . " OR `recipient` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_messages_accept_tokens` WHERE `sender` = " . $value . " OR `reciper` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_messages_chat` WHERE `sender_id` = " . $value . " OR `reciper_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_messages_meet_up` WHERE `sender` = " . $value . " OR `recipient` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_rejected_users` WHERE `id_user_active` = " . $value . " OR `id_user_guest` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_users_reviews` WHERE `sender` = " . $value . " OR `recipient` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_visitors` WHERE `visitor_id` = " . $value . " OR `owner_id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_users` WHERE `id` = " . $value;
    $db->setQuery($query);
    $db->execute();

    $query = "DELETE FROM `jproject_jshopping_users` WHERE `user_id` = " . $value;
    $db->setQuery($query);
    $db->execute();
}