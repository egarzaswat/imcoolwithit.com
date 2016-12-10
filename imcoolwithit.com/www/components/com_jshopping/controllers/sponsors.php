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

class JshoppingControllerSponsors extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
            JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerSponsors', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $this->displayList();
    }

    function displayList(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $user_id = JRequest::getInt('user');

        if(isset($user_id) && $user_id != 0){
            $modelUser = JSFactory::getModel('user', 'jshop');
            if($modelUser->existUser($user_id) == false){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
                exit;
            }

            $modelFriends = JSFactory::getModel('friends', 'jshop');
            if(!$modelFriends->getIsFrieds($user_id)){
                header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
                exit;
            }
        }

        $meta_data = JSFactory::getMetaData('sponsors_list');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelSponsors = JSFactory::getModel('sponsors', 'jshop');
        $sponsors_categories = $modelSponsors->getSponsorsCategories(array('category_id', 'name_' . JSFactory::getLang()->lang));
        $my_id = JSFactory::getUser()->user_id;
        $modelUser = JSFactory::getModel('user', 'jshop');
        $my_zip = $modelUser->getDataUser($my_id, array('zip'));

        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $searchParams = $modelUsersList->searchParamsCurrentUser(JSFactory::getUserShop());

        $display_data = array();
        foreach($sponsors_categories as $key => $values){
            $sponsors_with_category = $modelSponsors->getSponsorsWithCategory($values['category_id'], $searchParams);

            $display_data[$key] = new stdClass();
            $display_data[$key]->category_id = $values['category_id'];
            $display_data[$key]->name = $values['name_' . JSFactory::getLang()->lang];
            $display_data[$key]->link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS') . '?category_id=' . $values['category_id'];

            $display_data[$key]->category_sponsors = array();

            foreach($sponsors_with_category as $key_s => $values_s){
                $display_data[$key]->category_sponsors[$key_s] = $values_s;
                $display_data[$key]->category_sponsors[$key_s]->image = JSFactory::existImage($jshopConfig->image_product_path_site_small, $display_data[$key]->category_sponsors[$key_s]->image);

                if( isset($user_id) && !is_null($user_id) && $user_id != '' ){
                    $display_data[$key]->category_sponsors[$key_s]->link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING') . '?user=' . JRequest::getInt('user') . '&sponsor=' . $values_s->product_id;
                } else {
                    $display_data[$key]->category_sponsors[$key_s]->link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING') . '?sponsor=' . $values_s->product_id;
//                    $display_data[$key]->category_sponsors[$key_s]->link = false;
                }
            }
        }

//        $menu = JSFactory::getContentMenu();

        $view_name = "sponsors";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('sponsors');
        $view->assign('title', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
        $view->assign('data', $display_data);
        $view->assign('searchParams', $searchParams);
        $view->assign('user', $user_id);
        $view->assign('zip', $my_zip->zip);
//        $view->assign('menu', $menu);
        $view->display();
    }

}
?>