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
$jshopConfig = JSFactory::getConfig();

$my_id = JSFactory::getUser()->user_id;
$friend_id = (int)$_POST['friend_id'];
$modelFriends = JSFactory::getModel('friends', 'jshop');
$isFriends = $modelFriends->getIsFrieds($friend_id) && !$modelFriends->getIsAccept($friend_id) && ($friend_id != $my_id);
if (!$isFriends) { exit; }

$count_load_messages = (int)$_POST['count_load_messages'];

$modelUser = JSFactory::getModel('user', 'jshop');
$friend_data = $modelUser->getDataUser($friend_id, array('user_id', 'photosite', 'block'));
$my_data = $modelUser->getDataUser($my_id, array('user_id', 'photosite', 'block'));

$conf = new JConfig();
$my_data->photosite = JSFactory::existImage($conf->path_user_image_small, $my_data->photosite);
$friend_data->photosite = JSFactory::existImage($conf->path_user_image_small, $friend_data->photosite);

$modelMessaging = JSFactory::getModel('messaging', 'jshop');

$messages = array();
if(isset($_POST['older_message'])){
    $messages = $modelMessaging->getChatMessages($my_id, $friend_id, 0, $count_load_messages);
}

if(isset($_POST['message'])){
    $modelMessaging->setChatMessage($my_id, $friend_id, $_POST['message']);
    if (JSFactory::isUserOffline($friend_id)){
        $Config = JSFactory::getConfig();
        $modelNotes = JSFactory::getModel('notifications', 'jshop');
        $modelNotes->addNote($my_id, $friend_id, $my_id, JSFactory::getUser()->u_name, $Config->notifications[0], $_POST['message']);
    }
    $messages = $modelMessaging->getChatMessages($my_id, $friend_id, 0, $count_load_messages);
}

$count_chat_messages = $modelMessaging->getCountChatMessages($my_id, $friend_id);
if($count_load_messages < $count_chat_messages){
    $echo_load_messages = false;
} else {
    $echo_load_messages = true;
}

if($echo_load_messages){
    print_r('
        <script type="text/javascript">
            jQuery(".load-old-messages").hide();
        </script>
    ');
} else {
    print_r('
        <script type="text/javascript">
            jQuery(".load-old-messages").show();
        </script>
    ');
}


foreach($messages as $key=>$value){
    if($value->sender_id == $my_data->user_id){
        ?>
        <div class="item right col-xs-12">
            <div class="message-block">
                <span class="message">
                    <?php print $modelMessaging->setSmiles($value->message); ?>
                </span>
                <span class="date"><?php echo $value->date; ?></span>
            </div>
            <div class="message-user"><img class="user-image" src="<?php echo $my_data->photosite; ?>"></div>
        </div>
    <?php } else { ?>
        <div class="item left col-xs-12">
            <div class="message-user"><img class="user-image" src="<?php echo $friend_data->photosite; ?>"></div>
            <div class="message-block">
                <span class="message">
                    <?php print $modelMessaging->setSmiles($value->message); ?>
                </span>
                <span class="date"><?php echo $value->date; ?></span>
            </div>
        </div>
    <?php }
} ?>
