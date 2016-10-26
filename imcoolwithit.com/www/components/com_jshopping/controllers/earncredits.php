<?php
/**
 * @version      4.8.0 18.12.2014
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerEarnCredits extends JControllerLegacy{

    function __construct($config = array()){
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerEarnCredits', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = JSFactory::getConfig();

        switch($this->task){
            case null : $this->displayLinks(); break;
            case 'complete_profile' : $this->displayCompleteProfile(); break;
            case 'verify_email' : $this->displayVerifyEmail(); break;
            case 'verify_status' : $this->displayVerifyStatus(); break;
            case 'verification' : $this->displayVerification(); break;
            case 'refer_friend' : $this->displayReferFriend(); break;
            case 'user_meet_ups' : $this->displayUserMeetUps(); break;
            case 'sponsor_questions' : $this->displaySponsorQuestions(); break;
            case 'offers_list' : $this->displayOffersList(); break;
            case 'confirm_offer' : $this->displayConfirmOffer(); break;
            case 'offer_questions' : $this->displayOfferQuestions(); break;
            case 'honest_questions' : $this->displayHonestQuestions(); break;
            case 'honest' : $this->displayHonest(); break;

            default: header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
        }
    }

    function displayLinks(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('earn_tokens');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $links = array();
        $links[0]['link'] = JText::_('LINK_COMPLETE_PROFILE');
        $links[0]['image'] = $jshopConfig->image_earn_tokens_icons . "complete_profile.png";
        $links[0]['text'] = JText::_('EARN_TOKENS_COMPLETE');

        $links[1]['link'] = JText::_('LINK_HONEST');
        $links[1]['image'] = $jshopConfig->image_earn_tokens_icons . "honest_review.png";
        $links[1]['text'] = JText::_('EARN_TOKENS_HONEST');

        $links[2]['link'] = JText::_('LINK_VERIFY_EMAIL');
        $links[2]['image'] = $jshopConfig->image_earn_tokens_icons . "verify_email.png";
        $links[2]['text'] = JText::_('EARN_TOKENS_VERIFY_EMAIL');

        $links[3]['link'] = JText::_('LINK_OFFERS_LIST');
        $links[3]['image'] = $jshopConfig->image_earn_tokens_icons . "surveys.png";
        $links[3]['text'] = JText::_('EARN_TOKENS_SURVEYS');

        $links[4]['link'] = JText::_('LINK_USER_MEET_UP');
        $links[4]['image'] = $jshopConfig->image_earn_tokens_icons . "how_was_your_date.png";
        $links[4]['text'] = JText::_('EARN_TOKENS_DATE');

        $links[5]['link'] = JText::_('LINK_REFER_FRIEND');
        $links[5]['image'] = $jshopConfig->image_earn_tokens_icons . "refer_friend.png";
        $links[5]['text'] = JText::_('EARN_TOKENS_FRIEND');

//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('list');
        $view->assign('data', $links);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayCompleteProfile(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('complete_profile');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelMyProfile = JSFactory::getModel('myprofile', 'jshop');
        $list = $modelMyProfile->getProfileQuestions();
        $profile_answers_list = $modelMyProfile->getProfileAnswers(JSFactory::getUser()->user_id);

        $questions_list = array();

        foreach($list as $temp){
            $questions_list[$temp->question_id]['question'] = $temp->question;
            $questions_list[$temp->question_id]['question'] = $temp->question;
            $questions_list[$temp->question_id]['answers'][$temp->answer_id]['value'] = $temp->answer;

            if(in_array(array($temp->question_id, $temp->answer_id), $profile_answers_list)){
                $questions_list[$temp->question_id]['answers'][$temp->answer_id]['checked'] = 1;
            } else {
                $questions_list[$temp->question_id]['answers'][$temp->answer_id]['checked'] = 0;
            }
        }
		$completed = (count($profile_answers_list) > 0) ? true : false;

//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('complete_profile');
        $view->assign('tokens_count', $conf->count_tokens_complete_profile);
        $view->assign('questions', $questions_list);
        $view->assign('start_complete_profile', $completed);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayHonest(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('honest');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $items_count = $modelMeeting->getUserMeetUpCount_Honest();
        $items_count_per_page = $conf->count_items_honest_review;

        $pages_count = 0;
        if($items_count > $items_count_per_page){
            $pages_count = ceil($items_count/$items_count_per_page);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $items = $modelMeeting->getUserMeetUp_Honest(($page - 1) * $items_count_per_page, $items_count_per_page);
        } else {
            $items = $modelMeeting->getUserMeetUp_Honest(0, $items_count_per_page);
        }

        foreach($items as $key => $value){

            if ($value->block != 0) {
                $value->photosite = $conf->path_user_image_medium . "block.jpg";
            } else {
                $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
            }
            $value->occurred_date = JSFactory::getDateFormatMonthYearNumber($value->occurred_date);
            $value->link = JText::_('LINK_HONEST_QUESTIONS') . "?meet=" . $value->meet_up_id;
            $value->user_link = JText::_('LINK_USER_PAGE') . "?user=" . $value->user_id;
        }

        $pagination = JSFactory::getPagination($pages_count, JText::_('LINK_HONEST'), $page);
        $honest_logo = $jshopConfig->image_earn_tokens_icons . "honest_review.png";
        $tokens_count = $conf->count_tokens_honest_review;
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('honest_review');
        $view->assign('title',$meta_data['header']);
        $view->assign('pagination',$pagination);
        $view->assign('tokens_count', $tokens_count);
        $view->assign('honest_logo', $honest_logo);
        $view->assign('items', $items);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayHonestQuestions(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf= new JConfig();

        $meet_id = JRequest::getInt('meet');
        if($meet_id == 0){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('honest_questions');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelMyProfile = JSFactory::getModel('myprofile', 'jshop');
        if(!$modelMyProfile->getPermissionReview($meet_id)){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_HONEST'));
            exit;
        }

        $list_honest_questions = $modelMyProfile->getHonestQuestions();

        foreach($list_honest_questions as $temp){
            $list_questions[$temp->question_id]['question'] = $temp->question;
            $list_questions[$temp->question_id]['question'] = $temp->question;
            $list_questions[$temp->question_id]['answers'][$temp->answer_id]['value'] = $temp->answer;
            if($temp->negative == 1){
                $list_questions[$temp->question_id]['answers'][$temp->answer_id]['negative'] = 1;
            }
        }

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $meet_info = $modelMeeting->getMeetUpInfo($meet_id);
        $user_id = $meet_info->sender == JSFactory::getUser()->user_id ? $meet_info->recipient : $meet_info->sender;

        $modelUser = JSFactory::getModel('user', 'jshop');
        $user_data = $modelUser->getDataUser($user_id, array('user_id', 'u_name', 'birthday', 'photosite', 'block'));
        $user_data->age = JSFactory::getAge($user_data->birthday);
        if($user_data->block != 0){
            $user_data->photosite = $conf->path_user_image_medium . "block.jpg";
        } else {
            $user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $user_data->photosite);
        }
        $user_data->link = JText::_('LINK_USER_PAGE') . "?user=" . $user_data->user_id;

        $modelSponsor = JSFactory::getModel('sponsors', 'jshop');
        $sponsor = $modelSponsor->getSponsorData($meet_info->sponsor, array('image', 'name_' . JSFactory::getLang()->lang));
        $sponsor = $sponsor[0];
        $sponsor['image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $sponsor['image']);
        $image = $jshopConfig->image_earn_tokens_icons . "honest_review.png";
        $tokens_count = $conf->count_tokens_honest_review;
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('honest_questions');
        $view->assign('questions', $list_questions);
        $view->assign('tokens_count', $tokens_count);
        $view->assign('image', $image);
        $view->assign('sponsor', $sponsor);
        $view->assign('user_data', $user_data);
        $view->assign('link_honest', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_HONEST'));
        $view->assign('meet', $meet_id);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayUserMeetUps(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('user_meet_ups');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelMeeting = JSFactory::getModel('meeting', 'jshop');
        $items_count = $modelMeeting->getUserMeetUpCount();
        $items_count_per_page = $conf->count_items_meet_up_review;
        $pages_count = 0;
        if($items_count > $items_count_per_page){
            $pages_count = ceil($items_count/$items_count_per_page);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $list = $modelMeeting->getUserMeetUp(($page - 1) * $items_count_per_page, $items_count_per_page);
        } else {
            $list = $modelMeeting->getUserMeetUp(0, $items_count_per_page);
        }

        $display_data = array();
        foreach($list as $key => $temp){
            $display_data[$key] = $temp;
            $display_data[$key]->image = JSFactory::existImage($jshopConfig->image_product_path_site, $display_data[$key]->image);
            $display_data[$key]->link = JText::_('LINK_SPONSOR_QUESTIONS') . "?meet=" . $temp->meet_up_id;
        }

        $pagination = JSFactory::getPagination($pages_count, JText::_('LINK_USER_MEET_UP'), $page);
        $tokens_count = $conf->meet_up_review_tokens;
        $lincup_review_logo = $jshopConfig->image_earn_tokens_icons . "how_was_your_date.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('user_meet_ups');
        $view->assign('pagination',$pagination);
        $view->assign('title', $meta_data['header']);
        $view->assign('lincup_review_logo', $lincup_review_logo);
        $view->assign('tokens_count', $tokens_count);
        $view->assign('list', $display_data);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displaySponsorQuestions(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meet_up = JRequest::getInt('meet');
        if(!isset($meet_up) || $meet_up == 0){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelUserMeetUps = JSFactory::getModel('meeting', 'jshop');
        $show = $modelUserMeetUps->checkIfAnswered(JSFactory::getUser()->user_id, $meet_up);
        if($show){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_MEET_UP'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('sponsor_questions');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelUser = JSFactory::getModel('user', 'jshop');
        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');

        $current_meet_up = $modelUserMeetUps->getMeetUpInfo($meet_up);
        $sponsor_questions = $modelSponsors->getSponsorQuestions($current_meet_up->sponsor);

        $user_id = $current_meet_up->sender == JSFactory::getUser()->user_id ? $current_meet_up->recipient : $current_meet_up->sender;
        $user_data = $modelUser->getDataUser($user_id, array('user_id', 'u_name', 'birthday', 'photosite', 'block'));
        $user_data->age = JSFactory::getAge($user_data->birthday);
        if ($user_data->block != 0) {
            $user_data->photosite = $conf->path_user_image_medium . "block.jpg";
        } else {
            $user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $user_data->photosite);
        }
        $user_data->link = JText::_('LINK_USER_PAGE') . "?user=" . $user_data->user_id;

        $display_data = array();
        if ($sponsor_questions != null && !$show) {
            foreach ($sponsor_questions as $temp) {
                $display_data['sponsor_name'] = $temp->product_name;
                $display_data['sponsor_image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $temp->image);
                $display_data['attributes'][$temp->attr_id]['attr_id'] = $temp->attr_id;
                $display_data['attributes'][$temp->attr_id]['attr_name'] = $temp->attr_name;
                $display_data['attributes'][$temp->attr_id]['values'][$temp->value_id]['value_id'] = $temp->value_id;
                $display_data['attributes'][$temp->attr_id]['values'][$temp->value_id]['value_name'] = $temp->value_name;
            }
        } else {
            $sponsor_data = $modelSponsors->getSponsorData($current_meet_up->sponsor, array('product_id', 'image', 'name_' . JSFactory::getLang()->lang));
            $display_data['sponsor_id'] = $sponsor_data[0]['product_id'];
            $display_data['sponsor_name'] = $sponsor_data[0]['name_' . JSFactory::getLang()->lang];
            $display_data['sponsor_image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $sponsor_data[0]['image']);
            $display_data['attributes'] = 'no_questions';
        }

        $tokens_count = $conf->meet_up_review_tokens;
        $image = $jshopConfig->image_earn_tokens_icons . "how_was_your_date.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('sponsor_questions');
        $view->assign('meet_up', $meet_up);
        $view->assign('link_meet_ups', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_MEET_UP'));
        $view->assign('tokens_count', $tokens_count);
        $view->assign('image', $image);
        $view->assign('data', $display_data);
        $view->assign('user', $user_data);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayOffersList(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('offers_list');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $conf = new JConfig();
        $items_count_per_page = $conf->count_items_offers;

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $searchParams = $modelUsersList->searchParamsCurrentUser(JSFactory::getUserShop());
        $items_count = $modelSponsors->getOffersForUserCount($searchParams);

        $pages_count = 0;
        if($items_count > $items_count_per_page){
            $pages_count = ceil($items_count/$items_count_per_page);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $offers = $modelSponsors->getOffersForUser($searchParams, ($page - 1) * $items_count_per_page, $items_count_per_page);
        } else {
            $offers = $modelSponsors->getOffersForUser($searchParams, 0, $items_count_per_page);
        }

        $display_data = array();
        foreach($offers as $key => $value) {
            $display_data[$key] = $value;
            $display_data[$key]->image = JSFactory::existImage($jshopConfig->image_product_path_site, $display_data[$key]->image);
            $display_data[$key]->expires = JSFactory::getExpiresDays($display_data[$key]->expires);
            //$display_data[$key]->link = JText::_('LINK_CONFIRM_OFFER') . "?offer=" . $value->product_id;
            $display_data[$key]->link = JText::_('LINK_OFFER_QUESTION') . "?offer=" . $value->product_id;
        }

        $pagination = JSFactory::getPagination($pages_count, JText::_('LINK_OFFERS_LIST'), $page);
        $surveys_logo = $jshopConfig->image_earn_tokens_icons . "surveys.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('offers_list');
        $view->assign('pagination',$pagination);
        $view->assign('title', $meta_data['header']);
        $view->assign('surveys_logo', $surveys_logo);
        $view->assign('data', $display_data);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayConfirmOffer(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $offer_id = JRequest::getInt('offer');
        if(!isset($offer_id) || $offer_id == 0){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('confirm_offer');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $offers_data = $modelSponsors->getSponsorData($offer_id, array('product_id', 'image', 'name_' . JSFactory::getLang()->lang, 'tokens'));

        if(count($offers_data) == 0  || !$modelSponsors->checkOffer($offer_id)){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_OFFERS_LIST'));
            exit;
        }

        $display_data = $offers_data[0];
        $display_data['image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $display_data['image']);
        $display_data['link_out'] = JText::_('LINK_OFFERS_LIST');
        $display_data['link_in'] = JText::_('LINK_OFFER_QUESTION') . "?offer=" . $offer_id;

        $image = $jshopConfig->image_earn_tokens_icons . "surveys.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('confirm_offer');
        $view->assign('tokens_count', $display_data['tokens']);
        $view->assign('image', $image);
        $view->assign('data', $display_data);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayOfferQuestions(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $offer_id = JRequest::getInt('offer');
        if(!isset($offer_id) || $offer_id == 0){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('offer_questions');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $offer_questions = $modelSponsors->getSponsorQuestions($offer_id);
        $show = $modelSponsors->checkIfAnswered(JSFactory::getUser()->user_id, $offer_id);

        if($show){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_OFFERS_LIST'));
            exit;
        }

        $display_data = array();
        if (count($offer_questions) > 0) {
            foreach ($offer_questions as $temp) {
                $display_data['offer_name'] = $temp->product_name;
                $display_data['offer_image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $temp->image);
                $display_data['tokens'] = $temp->tokens;
                $display_data['attributes'][$temp->attr_id]['attr_id'] = $temp->attr_id;
                $display_data['attributes'][$temp->attr_id]['attr_name'] = $temp->attr_name;
                $display_data['attributes'][$temp->attr_id]['values'][$temp->value_id]['value_id'] = $temp->value_id;
                $display_data['attributes'][$temp->attr_id]['values'][$temp->value_id]['value_name'] = $temp->value_name;
            }
        } else {
            $offer_data = $modelSponsors->getSponsorData($offer_id, array('product_id', 'image', 'name_' . JSFactory::getLang()->lang, 'tokens'));

            if(count($offer_data) == 0 || !$modelSponsors->checkOffer($offer_id)){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_OFFERS_LIST'));
                exit;
            }

            $display_data['offer_id'] = $offer_data[0]['product_id'];
            $display_data['offer_name'] = $offer_data[0]['name_' . JSFactory::getLang()->lang];
            $display_data['offer_image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $offer_data[0]['image']);
            $display_data['tokens'] = $offer_data[0]['tokens'];
            $display_data['attributes'] = 'no_questions';
        }

        $image = $jshopConfig->image_earn_tokens_icons . "surveys.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('offer_questions');
        $view->assign('offer_id', $offer_id);
        $view->assign('tokens_count', $display_data['tokens']);
        $view->assign('image', $image);
        $view->assign('link_offers', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_OFFERS_LIST'));
        $view->assign('data', $display_data);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayVerifyEmail(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('verify_email');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $user_id = JSFactory::getUser()->user_id;

        $modelUser = JSFactory::getModel('user', 'jshop');
        $modelVerifyEmail = JSFactory::getModel('verify_email', 'jshop');
        $active = $modelVerifyEmail->checkIfActive($user_id);
        $email = !$active ? $modelUser->getDataUser($user_id, array('email')) : false;
        $email = $email->email;

        $image =$jshopConfig->image_earn_tokens_icons . "refer_friend.png";
        $tokens_count = $conf->verify_email_tokens_count;
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('verify_email');
        $view->assign('title', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
        $view->assign('tokens_count', $tokens_count);
        $view->assign('active', $active);
        $view->assign('email', $email);
        $view->assign('image', $image);
        $view->assign('link_verification', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_VERIFICATION'));
        $view->assign('link_error', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayVerifyStatus(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('verify_status');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $authorized = false;
        $user_id = JSFactory::getUser()->user_id;
        if($user_id != -1){
            $authorized = true;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');
        if(isset($_GET['user']) && !empty($_GET['user']) && isset($_GET['email'])&& !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['email'])){
            $modelUser->verifyEmail($_GET['user'], $_GET['email'], $_GET['hash'], $conf->verify_email_tokens_count);
        }

        $verify_status = false;
        if( isset($_GET['user']) && (int)$_GET['user'] != 0 && isset($_GET['hash']) && $_GET['hash'] != ''){
            $status = $modelUser->isVerifyUser($_GET['user'], $_GET['hash']);
            if($status == 1){
                $verify_status = true;
            }
            if($status == null){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME']);
                exit;
            }
        } else {
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME']);
            exit;
        }

        $image =$jshopConfig->image_earn_tokens_icons . "refer_friend.png";
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('verify_status');
        $view->assign('authorized', $authorized);
        $view->assign('verify_status', $verify_status);
        $view->assign('image', $image);
        $view->assign('tokens_count', $conf->verify_email_tokens_count);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayVerification(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $user_id = JSFactory::getUser()->user_id;

        $modelVerifyEmail = JSFactory::getModel('verify_email', 'jshop');
        $active = $modelVerifyEmail->checkIfActive($user_id);
        if($active){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_VERIFY_EMAIL'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('verification');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('verification');
        $view->assign('title', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
        $view->assign('link_verify_email', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_VERIFY_EMAIL'));
//        $view->assign('menu', $menu);
        $view->display();
    }

    function displayReferFriend(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('refer_friend');
        setMetaData($meta_data['title'], $meta_data['keyword'], $meta_data['description'], $params);

        $image =$jshopConfig->image_earn_tokens_icons . "refer_friend.png";
        $tokens_count = $conf->refer_friend_tokens_count;
//        $menu = JSFactory::getContentMenu();

        $view_name = "earncredits";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('refer_friend');
        $view->assign('title', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
        $view->assign('tokens_count', $tokens_count);
        $view->assign('image', $image);
        $view->assign('link_refer', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_REFER_FRIEND'));
        $view->assign('link_error', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
//        $view->assign('menu', $menu);
        $view->display();
    }

}