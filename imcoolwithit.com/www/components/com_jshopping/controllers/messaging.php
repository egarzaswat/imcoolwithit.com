<?php
/**
* @version      4.7.0 10.10.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerMessaging extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerMessaging', array(&$this));

    }

    function display($cachable = false, $urlparams = false)
    {
        $this->received();
    }

    function view(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $modelUser = JSFactory::getModel('user', 'jshop');
        $friend_id = JRequest::getInt('friend');


        $meta_data = JSFactory::getMetaData('messages_read');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $friend_data = $modelUser->getDataUser($friend_id, array('user_id','u_name', 'photosite', 'block'));
        if($friend_data->block != 0){
            $friend_data->photosite = "block.jpg";
        }
        $friend_data->link = JText::_('LINK_FULL_USER_PAGE') . '?user=' . $friend_id;
        $friend_data->photo = JSFactory::existImage($conf->path_user_image_medium, $friend_data->photosite);
        $friend_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $friend_data->photosite);

        $modelFriends = JSFactory::getModel('friends', 'jshop');
        if($modelFriends->getIsFrieds($friend_id) == false || $friend_data->block != 0){
            $friend_data->permission_write = false;
        } else {
            $friend_data->permission_write = true;
        }

        $my_id = JSFactory::getUser()->user_id;
        $my_account_data = $modelUser->getDataUser($my_id, array('photosite', 'block'));
        $my_account_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $my_account_data->photosite);

        $user_link = JText::_('LINK_FULL_USER_PAGE') . '?user=';

//        $menu = JSFactory::getContentMenu();

        $view_name = "messaging";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('read');
        $view->assign('title', $meta_data['header']);
        $view->assign('friend_data', $friend_data);
        $view->assign('my_account_data', $my_account_data);
        $view->assign('my_id', $my_id);
        $view->assign('user_link', $user_link);
//        $view->assign('menu', $menu);
        $view->assign('ajax_link', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_AJAX_SEND_UPDATE_MESSAGE'));
        $view->assign('link_get_count_mess', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_AJAX_GET_COUNT_MESSAGES'));
        $view->display();
    }

    function sent(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('message_sent');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $page_count_items = $conf->count_items_chat_messages;
        $ft = JRequest::getBool('tokens');

        $page = JRequest::getInt('page');
        if($page != 0){
            $data = $this->getSentInbox( ($page-1)*$page_count_items, $page_count_items, $ft );
        } else {
            $data = $this->getSentInbox(0, $page_count_items, $ft);
        }

        $count_messages = $data->count_items;
        $data_display = $data->items;

        $count_pages = 0;
        if($count_messages > $page_count_items){
            $count_pages = ceil($count_messages/$page_count_items);
        }

        $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_MESSAGING_SENT'), $page);
        $link_received = JText::_('LINK_MESSAGING_RECEIVED');

        $view_name = "messaging";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('sent');
        $view->assign('inbox_list', $data_display);
        $view->assign('ft', $ft);
        $view->assign('title', $meta_data['header']);
        $view->assign('link_received', $link_received);
        $view->assign('pagination', $pagination);
        $view->display();
    }

    function received(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('message_received');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);
        $ft = JRequest::getBool('tokens');

        $page_count_items = $conf->count_items_chat_messages;
        $page = JRequest::getInt('page');
        if($page != 0){
            $data = $this->getReceivedInbox( ($page-1)*$page_count_items, $page_count_items, $ft );
        } else {
            $data = $this->getReceivedInbox(0, $page_count_items, $ft);
        }

        $count_messages = $data->count_items;
        $data_display = $data->items;

        $count_pages = 0;
        if($count_messages > $page_count_items){
            $count_pages = ceil($count_messages/$page_count_items);
        }

        $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_MESSAGING_RECEIVED'), $page);

        $link_sent = JText::_('LINK_MESSAGING_SENT');

        $view_name = "messaging";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('received');

        $view->assign('inbox_list', $data_display);
        $view->assign('ft', $ft);
        $view->assign('title', $meta_data['header']);
        $view->assign('link_sent', $link_sent);
        $view->assign('pagination', $pagination);
        $view->display();
    }

    function getSentInbox($start = 0, $limit = 0, $ft = false){
        $result = array();
        $conf = new JConfig();
        $modelMessaging = JSFactory::getModel('messaging', 'jshop');
        $modelFriends = JSFactory::getModel('friends', 'jshop');

        $sent_tokens = $modelFriends->getSentTokens();

        if(!$ft){
            $messages = $modelMessaging->getSentMessages();
            foreach($messages as $key => $value){
                if($value->block != 0){
                    $value->photosite = "block.jpg";
                }
                $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);

                array_push($result, array(
                    'date_original'     => $value->date,
                    'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                    'photo'             => $value->photosite,
                    'name'              => $value->u_name,
                    'age'               => JSFactory::getAge($value->birthday),
                    'date'              => JSFactory::getDateFormatMonthYearNumber($value->date),
                    'message'           => $modelMessaging->setSmiles($value->message, true),
                    'message_expires'   => false,
                    'distance'          => false,
                    'button'            => array(
                        'name'          => JText::_('BUTTON_READ'),
                        'link'          => JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $value->reciper_id
                    ),
                    'block'             => $value->block
                ));
            }
        }

        foreach($sent_tokens as $key => $value){
            if($value->block != 0){
                $value->photosite = "block.jpg";
            }
            $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
            $tokens_expires = JSFactory::getExpires($value->date_send, $conf->day_expires_add_to_friends);
            $tokens_expires_text = $tokens_expires == 0 ? JText::_('EXPIRES_TODAY') : ($tokens_expires == 1 ? JText::_('EXPIRES_TOMORROW') : JText::sprintf('EXPIRES', $tokens_expires));

            array_push($result, array(
                'message_id'        => false,
                'table'             => false,
                'date_original'     => $value->date_send,
                'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                'photo'             => $value->photosite,
                'name'              => $value->u_name,
                'age'               => JSFactory::getAge($value->birthday),
                'date'              => JSFactory::getDateTimeDiffFormat($value->date_send),
                'message'           => JText::_('INTEREST_TOKEN_SEND'),
                'message_expires'   => $tokens_expires_text,
                'distance'          => false,
                'sr_tokens'         => true,
                'button'            => array(
                    'name'          => JText::_('BUTTON_READ'),
                    'link'          => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id
                ),
                'read'              => true,
                'block'             => $value->block
            ));
        }

        if(!$ft){
            $meet_up= $modelMessaging->getSentMeetUp();
            foreach($meet_up as $key => $value){
                if($value->block != 0){
                    $value->photosite = "block.jpg";
                }
                $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
                array_push($result, array(
                    'date_original'     => $value->date,
                    'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                    'photo'             => $value->photosite,
                    'name'              => $value->u_name,
                    'age'               => JSFactory::getAge($value->birthday),
                    'date'              => JSFactory::getDateFormatMonthYearNumber($value->date),
                    'message'           => JText::_('MEET_UP_INVITE_SENT'),
                    'message_expires'   => false,
                    'distance'          => false,
                    'button'            => array(
                        'name'          => JText::_('VIEW_INVITE'),
                        'link'          => JText::_('LINK_MEETING_SENT') . '?meet=' . $value->meet_up_id
                    ),
                    'block'             => $value->block
                ));
            }
        }

        // Функция сравнения
        function cmp($a, $b) {
            if ($a['date_original'] == $b['date_original']) {
                return 0;
            }
            return ($a['date_original'] > $b['date_original']) ? -1 : 1;
        }
        // Сортируем и выводим получившийся массив

        uasort($result, 'cmp');

        $result = array_values($result);

        $return_result = new stdClass();
        $return_result->count_items = count($result);

        if( $start == 0 && $limit == 0 ){
            $return_result->items = $result;
        } else {
            $return_result->items = array();

            for($i=$start; $i<$start+$limit; $i++){
                if(isset($result[$i])){
                    array_push($return_result->items, $result[$i]);
                }
            }
        }

        return $return_result;
    }

    function getReceivedInbox($start = 0, $limit = 0, $ft = false){
        $result = array();

        $conf = new JConfig();
        $modelMessaging = JSFactory::getModel('messaging', 'jshop');
        $modelFriends = JSFactory::getModel('friends', 'jshop');

        $received_tokens = $modelFriends->getReceivedTokens();

        $my_id = JSFactory::getUser()->user_id;

        if(!$ft){
            $messages = $modelMessaging->getReceivedMessages();
            foreach($messages as $key => $value){
                $is_new = $modelMessaging->getIsNewMessage($value->user_id, $my_id);

                if($value->block != 0){
                    $value->photosite = "block.jpg";
                }
                $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
                array_push($result, array(
                    'message_id'        => $value->id,
                    'table'             => 'messages_chat',
                    'date_original'     => $value->date,
                    'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                    'photo'             => $value->photosite,
                    'name'              => $value->u_name,
                    'age'               => JSFactory::getAge($value->birthday),
                    'date'              => JSFactory::getDateFormatMonthYearNumber($value->date),
                    'message'           => $modelMessaging->setSmiles($value->message, true),
                    'message_expires'   => false,
                    'distance'          => false,
                    'button'            => array(
                        'name'  => JText::_('BUTTON_READ'),
                        'link'  => JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $value->sender_id
                    ),
                    'accept'            => false,
                    'decline'           => false,
                    'read'              => $is_new,
                    'block'             => $value->block
                ));
            }
        }

        if(!$ft){
            $tokens = $modelMessaging->getTokens();
            foreach($tokens as $key => $value){
                if($value->block != 0){
                    $value->photosite = "block.jpg";
                }
                $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
                if($value->confirmation == 1){
                    array_push($result, array(
                        'message_id'        => $value->id,
                        'table'             => 'messages_accept_tokens',
                        'date_original'     => $value->date,
                        'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                        'photo'             => $value->photosite,
                        'name'              => $value->u_name,
                        'age'               => JSFactory::getAge($value->birthday),
                        'date'              => JSFactory::getDateFormatMonthYearNumber($value->date),
                        'message'           => JText::sprintf('INVITATION_TO_FRIENDS_CONFIRMED', $value->u_name),
                        'message_expires'   => false,
                        'distance'          => false,
                        'button'            => array(
                            'name'          => JText::_('BUTTON_VIEW'),
                            'link'          => JText::_('LINK_USER_ACCEPT') . '?user=' . $value->user_id
                        ),
                        'accept'            => false,
                        'decline'           => false,
                        'read'              => $value->read,
                        'block'             => $value->block
                    ));
                } else {
                    array_push($result, array(
                        'message_id'        => $value->id,
                        'table'             => 'messages_accept_tokens',
                        'date_original'     => $value->date,
                        'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                        'photo'             => $conf->path_user_image_medium . $value->photosite,
                        'name'              => $value->u_name,
                        'age'               => JSFactory::getAge($value->birthday),
                        'date'              => JSFactory::getDateFormatMonthYearNumber($value->date),
                        'message'           => JText::_('INVITATION_TO_FRIENDS_NO_CONFIRMED'),
                        'message_expires'   => false,
                        'distance'          => false,
                        'button'            => false,
                        'accept'            => false,
                        'decline'           => false,
                        'read'              => $value->read,
                        'block'             => $value->block
                    ));
                }
            }
        }

        foreach($received_tokens as $key => $value){
            if($value->block != 0){
                $value->photosite = "block.jpg";
            }
            $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
            $tokens_expires = JSFactory::getExpires($value->date_send, $conf->day_expires_add_to_friends);
            $tokens_expires_text = $tokens_expires == 0 ? JText::_('EXPIRES_TODAY') : ($tokens_expires == 1 ? JText::_('EXPIRES_TOMORROW') : JText::sprintf('EXPIRES', $tokens_expires));

            array_push($result, array(
                'message_id'        => false,
                'table'             => false,
                'date_original'     => $value->date_send,
                'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id,
                'photo'             => $value->photosite,
                'name'              => $value->u_name,
                'age'               => JSFactory::getAge($value->birthday),
                'date'              => JSFactory::getDateTimeDiffFormat($value->date_send),
                'message'           => JText::sprintf('TOKEN_RECEIVER', $value->u_name),
                'message_expires'   => $tokens_expires_text,
                'distance'          => false,
                'sr_tokens'         => true,
                'button'            => array(
                    'name'          => JText::_('BUTTON_VIEW'),
                    'link'          => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value->user_id
                ),
                'accept'            => array(
                    'name'  => JText::_('ACCEPT'),
                    'value' => $value->user_id
                ),
                'decline'           => array(
                    'name'  => JText::_('DECLINE'),
                    'value' => $value->user_id
                ),
                'read'              => false,
                'block'             => $value->block
            ));
        }

        if(!$ft){
            $modelUser = JSFactory::getModel('user', 'jshop');
            $my_geo = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('longitude', 'latitude'));

            $meet_up= $modelMessaging->getReceivedMeetUp();
            foreach($meet_up as $key => $value){
                if( $value['new_meet_up'] == 0 && $value['confirmation'] == 0 && $value['occurred'] == 0 ){
                    if($value['block'] != 0){
                        $value['photosite'] = "block.jpg";
                    }
                    $value['photosite'] = JSFactory::existImage($conf->path_user_image_medium, $value['photosite']);
                    array_push($result, array(
                        'message_id'        => $value['id'],
                        'table'             => 'messages_meet_up',
                        'date_original'     => $value['date'],
                        'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value['user_id'],
                        'photo'             => $value['photosite'],
                        'name'              => $value['u_name'],
                        'age'               => JSFactory::getAge($value['birthday']),
                        'date'              => JSFactory::getDateFormatMonthYearNumber($value['date']),
                        'message'           => JText::_('MEET_NOT_CONFIRMED'),
                        'message_expires'   => $value['name_en-GB'],
                        'distance'          => false,
                        'button'            => false,
                        'accept'            => false,
                        'decline'           => false,
                        'read'              => $value['read'],
                        'block'             => $value['block']
                    ));
                }

                if( $value['new_meet_up'] == 0 && $value['confirmation'] == 1  && $value['occurred'] == 0 ){
                    if($value['block'] != 0){
                        $value['photosite'] = "block.jpg";
                    }
                    $value['photosite'] = JSFactory::existImage($conf->path_user_image_medium, $value['photosite']);
                    array_push($result, array(
                        'message_id'        => $value['id'],
                        'table'             => 'messages_meet_up',
                        'date_original'     => $value['date'],
                        'user_link'         => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value['user_id'],
                        'photo'             => $value['photosite'],
                        'name'              => $value['u_name'],
                        'age'               => JSFactory::getAge($value['birthday']),
                        'date'              => JSFactory::getDateFormatMonthYearNumber($value['date']),
                        'message'           => JText::_('MEET_CONFIRMED'),
                        'message_expires'   => JText::_('REDEMPTION_INFO'),
                        'distance'          => JSFactory::calculateDistance($value['latitude'], $value['longitude'], $my_geo->latitude, $my_geo->longitude),
                        'button'            => array(
                            'name'          => JText::_('COUPON_INFO_LINK'),
                            'link'          => JText::_('LINK_MEETING_COUPON_INFO') . '?meet=' . $value['meet_up_id']
                        ),
                        'accept'            => false,
                        'decline'           => false,
                        'read'              => $value['read'],
                        'block'             => $value['block']
                    ));
                }


                if( $value['new_meet_up'] == 1 && $value['confirmation'] == 0 && $value['occurred'] == 0 ){
                    if($value['block'] != 0){
                        $value['photosite'] = "block.jpg";
                    }
                    $value['photosite'] = JSFactory::existImage($conf->path_user_image_medium, $value['photosite']);
                    $meet_up_expires = JSFactory::getExpires($value['date'], $conf->day_expires_meet_up);
                    $meet_up_expires_text = $meet_up_expires == 0 ? JText::_('EXPIRES_TODAY') : ($meet_up_expires == 1 ? JText::_('EXPIRES_TOMORROW') : JText::sprintf('EXPIRES', $meet_up_expires));
                    array_push($result, array(
                        'message_id'        => $value['id'],
                        'table'             => 'messages_meet_up',
                        'date_original'     => $value['date'],
                        'user_link'         => JText::_('LINK_FULL_USER_PAGE'). '?user=' . $value['user_id'],
                        'photo'             => $value['photosite'],
                        'name'              => $value['u_name'],
                        'age'               => JSFactory::getAge($value['birthday']),
                        'date'              => JSFactory::getDateFormatMonthYearNumber($value['date']),
                        'message'           => JText::_('NEW_MEET_UP_INVITE'),
                        'message_expires'   => $meet_up_expires_text,
                        'distance'          => JSFactory::calculateDistance($value['latitude'], $value['longitude'], $my_geo->latitude, $my_geo->longitude),
                        'button'            => array(
                            'name'          => JText::_('VIEW_INVITE'),
                            'link'          => JText::_('LINK_MEETING_VIEW_INVITE') . '?meet=' . $value['meet_up_id']
                        ),
                        'accept'            => false,
                        'decline'           => false,
                        'read'              => $value['read'],
                        'block'             => $value['block']
                    ));
                }
            }
        }

        function cmp($a, $b) {
            if ($a['date_original'] == $b['date_original']) {
                return 0;
            }
            return ($a['date_original'] > $b['date_original']) ? -1 : 1;
        }
        uasort($result, 'cmp');

        $result = array_values($result);

        $return_result = new stdClass();
        $return_result->count_items = count($result);

        if( $start == 0 && $limit == 0 ){
            $return_result->items = $result;
        } else {
            $return_result->items = array();

            for($i=$start; $i<$start+$limit; $i++){
                if(isset($result[$i])){
                    array_push($return_result->items, $result[$i]);
                    if($result[$i]['read'] != 1 && $result[$i]['table'] != 'messages_chat' && $result[$i]['table'] != false){
                        $modelMessaging->setReadMessages($result[$i]['table'], $result[$i]['message_id']);
                    }
                }
            }
        }

        return $return_result;
    }
}
?>