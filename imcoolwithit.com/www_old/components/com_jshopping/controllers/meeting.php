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

class JshoppingControllerMeeting extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerMeeting', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        if($this->task == 'sent'){
            $this->displaySent();
        } else if($this->task == 'view_invite'){
            $this->displayViewInvite();
        } else if($this->task == 'coupon_info') {
            $this->displayCouponInfo();
        } else if($this->task == 'confirmation') {
            $this->displayConfirmation();
        } else if($this->task == 'confirmed_info'){
            $this->displayConfirmedInfo();
        } else {
            $this->displayMeeting();
        }
    }

    function displayMeeting()
    {
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('meeting');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelUser = JSFactory::getModel('user', 'jshop');
        $friend_id = JRequest::getInt('user');

        $sponsor_id = JRequest::getInt('sponsor');

        if( !isset($sponsor_id) || $sponsor_id == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        if(!$modelSponsors->existSponsor($sponsor_id)){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        if($friend_id != 0){
            if($modelUser->existUser($friend_id) == false || $modelUser->isBlockUser($friend_id) == true){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
                exit;
            }

            $modelFriends = JSFactory::getModel('friends', 'jshop');
            if(!$modelFriends->getIsFrieds($friend_id)){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $friend_id);
                exit;
            }

            $modelMeeting = JSFactory::getModel('meeting', 'jshop');
            $permission_meeting = $modelMeeting->getPermissionMeetingsInTheSponsor(JSFactory::getUser()->user_id, $friend_id, $sponsor_id);

            $modelUser = JSFactory::getModel('user', 'jshop');
            $user_data = $modelUser->getDataUser($friend_id, array('u_name', 'photosite', 'birthday'));
            $user_data->name = $user_data->u_name;
            $user_data->age = JSFactory::getAge($user_data->birthday);
            $user_data->link = JText::_('LINK_USER_PAGE') . "?user=" . $friend_id;
            $user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $user_data->photosite);

            $my_data = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('u_name', 'photosite', 'birthday'));
            $my_data->name = $my_data->u_name;
            $my_data->age = JSFactory::getAge($my_data->birthday);
            $my_data->link = JText::_('LINK_USER_PAGE') . "?user=" . JSFactory::getUser()->user_id;
            $my_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $my_data->photosite);
        }

        $permission_meeting = isset($permission_meeting)?$permission_meeting:true;

        $sponsor_data = $modelSponsors->getSponsorData($sponsor_id, array('title_en-GB', 'name_en-GB', 'image', 'short_description_en-GB', 'city_en-GB', 'state_en-GB', 'product_ean', 'tokens'));
        $sponsor_data = $sponsor_data[0];
        $sponsor_data['name'] = $sponsor_data['name_en-GB'];
        $sponsor_data['title'] = $sponsor_data['title_en-GB'];
        $sponsor_data['description'] = $sponsor_data['short_description_en-GB'];
        $sponsor_data['city'] = $sponsor_data['city_en-GB'];
        $sponsor_data['state'] = $sponsor_data['state_en-GB'];
        $sponsor_data['image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $sponsor_data['image']);

        $submit_data = array(
            'friend'        => $friend_id,
            'sponsor'       => $sponsor_id,
            'my_id'         => JSFactory::getUser()->user_id,
            'count_tokens'   => $sponsor_data['tokens'],
            'meet_code'   => $sponsor_data['product_ean'],
            'link'          => 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING_SENT') . "?meet="
        );

        $count_my_tokens = $modelUser->getCountUserTokens(JSFactory::getUser()->user_id);
        if($count_my_tokens < $sponsor_data['tokens']){
            $isset_tokens_send = false;
        } else {
            $isset_tokens_send = true;
        }
        $link_not_your_date = JText::_('LINK_FRIENDS') . '?sponsor=' . $sponsor_id;
        $link_another_sponsor = ($friend_id == 0) ? JText::_('LINK_SPONSORS') : JText::_('LINK_SPONSORS') . "?user=" . $friend_id;
        $link_earn_tokens = JText::_('LINK_EARN_TOKENS');

//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('meeting');
        if(isset($user_data) && isset($my_data)){
            $view->assign('user_data', $user_data);
            $view->assign('my_data', $my_data);
        }
        $view->assign('permission_meeting', $permission_meeting);
        $view->assign('sponsor_data', $sponsor_data);
        $view->assign('submit_data', $submit_data);
        $view->assign('link_not_your_date', $link_not_your_date);
        $view->assign('link_another_sponsor', $link_another_sponsor);
        $view->assign('link_earn_tokens', $link_earn_tokens);
        $view->assign('isset_tokens_send', $isset_tokens_send);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displaySent(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('meeting_sent');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $meet = JRequest::getInt('meet');
        if( !isset($meet) || is_null($meet) || $meet == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_data = $modelMeeting->getMeetUpInfo($meet);

        if($meet_data == -1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');
        $username = $modelUser->getDataUser($meet_data->recipient, array('u_name'));

//        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
//        $sponsor_image = $modelSponsors->getSponsorData($meet_data->sponsor , array('image'));

//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('sent');
        $view->assign('username', $username->u_name);
//        $view->assign('image', JSFactory::existImage($jshopConfig->image_product_path_site, $sponsor_image[0]['image']));
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayViewInvite(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meet = JRequest::getInt('meet');
        if( !isset($meet) || is_null($meet) || $meet == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_data = $modelMeeting->getMeetUpInfo($meet);

        if($meet_data == -1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        if($meet_data->sender == JSFactory::getUser()->user_id){
            $friend_id = $meet_data->recipient;
        } else {
            $friend_id = $meet_data->sender;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');

        if($modelUser->isBlockUser($friend_id)){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('meeting_view_invite');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $display_data = new stdClass();
        $display_data->user_data = $modelUser->getDataUser($friend_id, array('u_name', 'birthday', 'photosite', 'sex', 'city', 'state', 'latitude', 'longitude'));

        $display_data->user_data->sex = ($display_data->user_data->sex == 2) ? JText::_('MALE') : JText::_('FEMALE');
        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $currentUser = JSFactory::getUserShop();
        $searchParams = $modelUsersList->searchParamsCurrentUser($currentUser);
        $distance = $modelUsersList->calculateDistance($searchParams['latitude'], $searchParams['longitude'], $display_data->user_data->latitude, $display_data->user_data->longitude);
        $display_data->user_data->distance = $distance;

        $display_data->user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $display_data->user_data->photosite);

        $display_data->user_data->age = JSFactory::getAge($display_data->user_data->birthday);
        $display_data->user_data->link = JText::_('LINK_USER_PAGE') . '?user=' . $friend_id;

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $display_data->sponsor_data = $modelSponsors->getSponsorData($meet_data->sponsor, array('title_en-GB', 'name_en-GB', 'image', 'short_description_en-GB', 'city_en-GB', 'state_en-GB', 'product_ean', 'tokens'));
        $display_data->sponsor_data = $display_data->sponsor_data[0];
        $display_data->sponsor_data['image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $display_data->sponsor_data['image']);
        $display_data->sponsor_data['name'] = $display_data->sponsor_data['name_en-GB'];
        $display_data->sponsor_data['title'] = $display_data->sponsor_data['title_en-GB'];
        $display_data->sponsor_data['description'] = $display_data->sponsor_data['short_description_en-GB'];
        $display_data->sponsor_data['city'] = $display_data->sponsor_data['city_en-GB'];
        $display_data->sponsor_data['state'] = $display_data->sponsor_data['state_en-GB'];

        $display_data->sponsor_data['count_tokens'] = $display_data->sponsor_data['tokens'];

        $count_my_tokens = $modelUser->getCountUserTokens(JSFactory::getUser()->user_id);
        if($count_my_tokens < $display_data->sponsor_data['tokens']){
            $isset_tokens_accept = false;
        } else {
            $isset_tokens_accept = true;
        }

//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('view_invite');
        $view->assign('data', $display_data);
        $view->assign('accept', $meet_data->confirmation);
        $view->assign('meet', $meet);
        $view->assign('user', JSFactory::getUser()->user_id);
        $view->assign('friend', $friend_id);
        $view->assign('sponsor', $meet_data->sponsor);
        $view->assign('isset_tokens_accept', $isset_tokens_accept);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayCouponInfo(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meet_id = JRequest::getInt('meet');
        if( !isset($meet_id) || $meet_id == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_info = $modelMeeting->getMeetUpInfo($meet_id);

        if($meet_info == -1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        if($meet_info->occurred == 1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED'));
            exit;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');
        $user_id = $meet_info->sender == JSFactory::getUser()->user_id ? $meet_info->recipient : $meet_info->sender;

        $me = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('u_name', 'photosite'));
        $my_photo = JSFactory::existImage($conf->path_user_image_medium, $me->photosite);
        $my_link = '/';

        $user = $modelUser->getDataUser($user_id, array('u_name', 'photosite'));
        $user_photo = JSFactory::existImage($conf->path_user_image_medium, $user->photosite);
        $user_link = JText::_('LINK_USER_PAGE') . '?user=' . $user_id;

        $meta_data = JSFactory::getMetaData('meeting_coupon_info');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $sponsor_data = $modelSponsors->getSponsorData($meet_info->sponsor, array('image', 'short_description_' . JSFactory::getLang()->lang, 'tokens'));
        $message = $meet_info->sender == JSFactory::getUser()->user_id ? JText::sprintf('LINCUP_MATCH_ACCEPTED', $user->u_name) : JText::sprintf('LINCUP_MATCH_ACCEPTED_YOU', $user->u_name);
        $photo = JSFactory::existImage($jshopConfig->image_product_path_site, $sponsor_data[0]['image']);
        $description = $sponsor_data[0]['short_description_' . JSFactory::getLang()->lang];
        $message_link = JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $user_id;
        $confirm_link = JText::_('LINK_MEETING_CONFIRMATION') . '?meet=' . $meet_id;
        $count_tokens = $sponsor_data[0]['tokens'];

        $expires_tokens = $conf->day_expires_meet_up;
//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('coupon_info');
        $view->assign('message',$message);
        $view->assign('my_name',$me->u_name);
        $view->assign('my_photo',$my_photo);
        $view->assign('my_link',$my_link);
        $view->assign('user_name',$user->u_name);
        $view->assign('user_photo',$user_photo);
        $view->assign('user_link',$user_link);
        $view->assign('count_tokens', $count_tokens);
        $view->assign('expires_tokens', $expires_tokens);
        $view->assign('my_id', JSFactory::getUser()->user_id);
        $view->assign('photo', $photo);
        $view->assign('description', $description);
        $view->assign('message_link', $message_link);
        $view->assign('confirm_link', $confirm_link);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayConfirmation(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meet_id = JRequest::getInt('meet');
        if( !isset($meet_id) || $meet_id == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_info = $modelMeeting->getMeetUpInfo($meet_id);

        if($meet_info == -1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        if($meet_info->occurred == 1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('meeting_confirmation');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);
        $friend_id = $meet_info->sender == JSFactory::getUser()->user_id ? $meet_info->recipient : $meet_info->sender;

        $modelUser = JSFactory::getModel('user', 'jshop');
        $user_data = $modelUser->getDataUser($friend_id, array('u_name', 'photosite', 'birthday'));
        $user_data->name = $user_data->u_name;
        $user_data->age = JSFactory::getAge($user_data->birthday);
        $user_data->link = JText::_('LINK_USER_PAGE') . "?user=" . $friend_id;
        $user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $user_data->photosite);

        $my_data = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('u_name', 'photosite', 'birthday'));
        $my_data->name = $my_data->u_name;
        $my_data->age = JSFactory::getAge($my_data->birthday);
        $my_data->link = JText::_('LINK_USER_PAGE') . "?user=" . JSFactory::getUser()->user_id;
        $my_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $my_data->photosite);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $sponsor_data = $modelSponsors->getSponsorData($meet_info->sponsor, array('image', 'short_description_' . JSFactory::getLang()->lang, 'title_' . JSFactory::getLang()->lang, 'tokens', 'name_' . JSFactory::getLang()->lang));
        $sponsor_data = $sponsor_data[0];
        $sponsor_data['image'] = JSFactory::existImage($jshopConfig->image_product_path_site_medium, $sponsor_data['image']);
        $link_info = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING_CONFIRMED_INFO') . '?meet=' . $meet_id;

//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('confirmation');
        $view->assign('user_data', $user_data);
        $view->assign('my_data', $my_data);
        $view->assign('sponsor_data', $sponsor_data);
        $view->assign('code', $meet_info->code);
        $view->assign('meet', $meet_id);
        $view->assign('link_info', $link_info);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayConfirmedInfo(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meet_id = JRequest::getInt('meet');
        if( !isset($meet_id) || $meet_id == 0 ){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_info = $modelMeeting->getMeetUpInfo($meet_id);
        if($meet_info->occurred != 1){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('meeting_confirm_info');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $sponsor_data = $modelSponsors->getSponsorData($meet_info->sponsor, array('image', 'tokens'));
        $sponsor_image = JSFactory::existImage($jshopConfig->image_product_path_site_medium, $sponsor_data[0]['image']);
        $count_tokens = ($meet_info->sender == JSFactory::getUser()->user_id) ? $sponsor_data[0]['tokens'] : 0;
        $link_close = "/";

//        $menu = JSFactory::getContentMenu();

        $view_name = "meeting";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('confirmed_info');
        $view->assign('image', $sponsor_image);
        $view->assign('tokens', $count_tokens);
        $view->assign('link_close', $link_close);
//        $view->assign('menu', $menu);
        $view->display();
    }
}
?>