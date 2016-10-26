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

class JshoppingControllerFriends extends JControllerLegacy
{

    function __construct($config = array())
    {
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerFriends', array(&$this));
    }

    function display($cachable = false, $urlparams = false)
    {
        $this->displayList();
    }

    function displayList(){

        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('friend_list');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelFriends = JSFactory::getModel('friends', 'jshop');

        $page_count_items = $conf->count_items_friends;
        $count_friends = $modelFriends->getCountFriends();

        $count_pages = 0;
        if($count_friends > $page_count_items){
            $count_pages = ceil($count_friends/$page_count_items);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $friends_list = $modelFriends->getFriends( ($page-1)*$page_count_items, $page_count_items );
        } else {
            $friends_list = $modelFriends->getFriends(0, $page_count_items);
        }

        $modelUsersList = $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $modelUser = JSFactory::getModel('user', 'jshop');
        $my_geo_data = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('latitude', 'longitude'));

        $display_data = array();

        foreach ($friends_list as $key => $value) {
            if($value->block != 0){
                $value->photosite = "block.jpg";
            }
            $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);

            $display_data[$key]['user_id']      = $value->user_id;
            $display_data[$key]['name']         = $value->u_name;
            $display_data[$key]['age']          = JSFactory::getAge($value->birthday);
            $display_data[$key]['height']       = (is_null($value->height) || $value->height == '')? JText::_('UNKNOWN') : $value->height;
            $display_data[$key]['body']         = (is_null($value->body) || $value->body == '')? JText::_('UNKNOWN') : $value->body;
            $display_data[$key]['status']       = (is_null($value->status) || $value->status == '')? JText::_('UNKNOWN') : $value->status;
            $display_data[$key]['distance']     = $modelUsersList->calculateDistance($value->latitude, $value->longitude, $my_geo_data->latitude, $my_geo_data->longitude);
            $display_data[$key]['sex']          = ($value->sex == 2)?JText::_('MALE'):JText::_('FEMALE');
            $display_data[$key]['photo']        = $value->photosite;
            $display_data[$key]['last_visit']   = JSFactory::getDateDiffFormat($value->last_visit);
            $display_data[$key]['user_link']    = JText::_('LINK_FULL_USER_PAGE') . "?user=" . $value->user_id;

/*            if( strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING')) && ((int)$_GET['sponsor'] != 0) ){
                $display_data[$key]['meet_link'] = JText::_('LINK_MEETING') . '?user=' . $value->user_id . '&sponsor=' . $_GET['sponsor'];
            } else {
                $display_data[$key]['meet_link'] = JText::_('LINK_SPONSORS') . "?user=" . $value->user_id;
            }*/
            $display_data[$key]['block'] = $value->block;

            if(isset($_GET['sponsor']) ){
                $display_data[$key]['user_link'] = JText::_('LINK_MEETING') . '?user=' . $value->user_id . '&sponsor=' . $_GET['sponsor'];
                $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_FRIENDS') . '?sponsor=' . $_GET['sponsor'], $page);
            } else {
                $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_FRIENDS'), $page);
            }
        }

        $view_name = "friends";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('list');
        $view->assign('title', $meta_data['header']);
        $view->assign('data', $display_data);
        $view->assign('pagination', $pagination);
        $view->display();
    }

}