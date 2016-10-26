<?php
/**
* @version      4.9.0 10.08.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerSearch extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerSearch', array(&$this));
    }
    
    function display($cachable = false, $urlparams = false){
        checkUserLogin();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('search_users');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $page_count_items = $conf->count_items_search;

        $currentUser = JSFactory::getUserShop();
        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $searchParams = $modelUsersList->searchParamsCurrentUser($currentUser);
        $count_items = $modelUsersList->getCountItems($searchParams);

        $count_pages = 0;
        if($count_items > $page_count_items){
            $count_pages = ceil($count_items/$page_count_items);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $usersList = $modelUsersList->usersList($searchParams, ($page-1)*$page_count_items, $page_count_items, array('user_id', 'u_name', 'photosite', 'longitude', 'latitude', 'sex', 'last_visit', 'birthday'), array('height', 'body', 'status'));
        } else {
            $usersList = $modelUsersList->usersList($searchParams, 0, $page_count_items, array('user_id', 'u_name', 'photosite', 'longitude', 'latitude', 'sex', 'last_visit', 'birthday'), array('height', 'body', 'status'));
        }

        $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_USERS_LIST'), $page);

        $user_display_data = array();
        foreach($usersList as $value){
            $value->photosite = JSFactory::existImage($conf->path_user_image_medium, $value->photosite);
            array_push($user_display_data, array(
                'user_id'   => $value->user_id,
                'name'      => $value->u_name,
                'distance'  => $modelUsersList->calculateDistance($value->latitude, $value->longitude, $searchParams['latitude'], $searchParams['longitude']),
                'sex'       => ($value->sex == 2)?JText::_('MALE'):JText::_('FEMALE'),
                'photo'     => $value->photosite,
                'last_visit'=> JSFactory::getDateDiffFormat($value->last_visit),
                'age'       => JSFactory::getAge($value->birthday),
                'height'    => (is_null($value->height) || $value->height == '')? JText::_('UNKNOWN') : $value->height,
                'body'      => (is_null($value->body) || $value->body == '')? JText::_('UNKNOWN') : $value->body,
                'status'    => (is_null($value->status) || $value->status == '')? JText::_('UNKNOWN') : $value->status,
                'user_link' => JText::_('LINK_FULL_USER_PAGE') . "?user=" . $value->user_id
            ));
        }


        $q1 = date("Y-m-d H:i:s");
        $q2 = $currentUser->invisible_to;


        $user_visible = ($currentUser->invisible_to > date("Y-m-d H:i:s")) ? 0 : 1;

        $view_name = "search";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("result");
        $view->assign('config',$jshopConfig);
        $view->assign('currentUser',$currentUser);
        $view->assign('user_visible',$user_visible);
        $view->assign('usersList',$user_display_data);
        $view->assign('pagination',$pagination);
        $view->display();
    }

}
?>