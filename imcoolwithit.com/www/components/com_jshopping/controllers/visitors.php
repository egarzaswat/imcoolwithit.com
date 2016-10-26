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

class JshoppingControllerVisitors extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
            JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerMessages', array(&$this));
    }

    function display($cachable = false, $urlparams = false)
    {
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('visitors');
        setMetaData($meta_data['title'], $meta_data['keyword'], $meta_data['description'], $params);

        $modelVisitors = JSFactory::getModel('visitors', 'jshop');

        $page_count_items = $conf->count_items_visitors;
        $count_visitors = $modelVisitors->getCountVisitors();

        $count_pages = 0;
        if($count_visitors > $page_count_items){
            $count_pages = ceil($count_visitors/$page_count_items);
        }

        $page = JRequest::getInt('page');
        if($page != 0){
            $visitors_list = $modelVisitors->getVisitors( ($page-1)*$page_count_items, $page_count_items );
        } else {
            $visitors_list = $modelVisitors->getVisitors(0, $page_count_items);
        }

        $pagination = JSFactory::getPagination($count_pages, JText::_('LINK_VISITORS'), $page);

        $modelUsersList = $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $modelUser = JSFactory::getModel('user', 'jshop');
        $my_geo_data = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('latitude', 'longitude'));

        $display_data = array();
        foreach($visitors_list as $key => $value){
            if($value['block'] != 0){
                $value['photosite'] = "block.jpg";
            }

            array_push($display_data, array(
                    'user_id'       => $value['user_id'],
                    'name'          => $value['u_name'],
                    'age'           => JSFactory::getAge($value['birthday']),
                    'height'        => (is_null($value['height']) || $value['height'] == '')? JText::_('UNKNOWN') : $value['height'],
                    'body'          => (is_null($value['body']) || $value['body'] == '')? JText::_('UNKNOWN') : $value['body'],
                    'status'        => (is_null($value['status']) || $value['status'] == '')? JText::_('UNKNOWN') : $value['status'],
                    'distance'      => $modelUsersList->calculateDistance($value['latitude'], $value['longitude'], $my_geo_data->latitude, $my_geo_data->longitude),
                    'sex'           => ($value['sex'] == 2) ? JText::_('MALE') : JText::_('FEMALE'),
                    'photo'         => JSFactory::existImage($conf->path_user_image_medium, $value['photosite']),
                    'last_visit'    => JSFactory::getDateDiffFormat($value['last_visit']),
                    'user_link'     => JText::_('LINK_FULL_USER_PAGE') . '?user=' . $value['user_id'],
                    'visited'       => JSFactory::getDateDiffFormat($value['date']),
                    'read'          => $value['read']
                )
            );

            if( $value['read'] != 1 ){
                JSFactory::markVisitors($value['id']);
            }
        }

        $view_name = "visitors";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('visitors');
        $view->assign('title', $meta_data['header']);
        $view->assign('visitors_list', $display_data);
        $view->assign('pagination', $pagination);
        $view->display();
    }

}
?>