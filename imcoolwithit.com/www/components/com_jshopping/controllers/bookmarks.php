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

class JshoppingControllerBookmarks extends JControllerLegacy
{

    function __construct($config = array())
    {
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerBookmarks', array(&$this));
    }

    function display($cachable = false, $urlparams = false)
    {
        $this->displayList();
    }

    function displayList()
    {
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('bookmarks_list');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelBookmarksList = JSFactory::getModel('bookmarks', 'jshop');
        $page_items_count = $conf->count_items_bookmarks;
        $bookmarks_count = $modelBookmarksList->getCountBookmarks(JSFactory::getUser()->user_id);

        $pages_count = 0;
        if($bookmarks_count > $page_items_count){
            $pages_count = ceil($bookmarks_count/$page_items_count);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $bookmarks_list = $modelBookmarksList->getAllMyBookmarks(JSFactory::getUser()->user_id, ($page-1)*$page_items_count, $page_items_count);
        } else {
            $bookmarks_list = $modelBookmarksList->getAllMyBookmarks(JSFactory::getUser()->user_id, 0, $page_items_count);
        }
        $pagination = JSFactory::getPagination($pages_count, JText::_('LINK_MY_BOOKMARKS'), $page);

        $modelUsersList = $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $modelUser = JSFactory::getModel('user', 'jshop');
        $my_geo_data = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('latitude', 'longitude'));

        $display_data = array();
        foreach ($bookmarks_list as $key => $value) {
            if($value->block != 0){
                $value->photosite = "block.jpg";
            }

            $display_data[$key]['user_id']      = $value->user_id;
            $display_data[$key]['name']         = $value->u_name;
            $display_data[$key]['age']          = JSFactory::getAge($value->birthday);
            $display_data[$key]['height']       = (is_null($value->height) || $value->height == '')? JText::_('UNKNOWN') : $value->height;
            $display_data[$key]['body']         = (is_null($value->body) || $value->body == '')? JText::_('UNKNOWN') : $value->body;
            $display_data[$key]['status']       = (is_null($value->status) || $value->status == '')? JText::_('UNKNOWN') : $value->status;
            $display_data[$key]['distance']     = $modelUsersList->calculateDistance($value->latitude, $value->longitude, $my_geo_data->latitude, $my_geo_data->longitude);
            $display_data[$key]['sex']          = ($value->sex == 2)?JText::_('MALE'):JText::_('FEMALE');
            $display_data[$key]['photo']        = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
            $display_data[$key]['last_visit']   = JSFactory::getDateDiffFormat($value->last_visit);
            $display_data[$key]['user_link']    = JText::_('LINK_USER_PAGE') . "?user=" . $value->user_id;
            $display_data[$key]['date']         = JSFactory::getDateDiffFormat($value->date);
        }




        $view_name = "bookmarks";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('list');
        $view->assign('title', $meta_data['header']);
        $view->assign('my_user_id', JSFactory::getUser()->user_id);
        $view->assign('data', $display_data);
        $view->assign('pagination', $pagination);
        $view->display();
    }

}