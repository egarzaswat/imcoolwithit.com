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

class JshoppingControllerInfo extends JControllerLegacy
{

    function __construct($config = array())
    {
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerInfo', array(&$this));
    }

    function display($cachable = false, $urlparams = false)
    {
        switch($this->task){
            case 'community'        : $this->community(); break;
            case 'partners'         : $this->partners(); break;
            case 'privacy'          : $this->info('privacy'); break;
            case 'terms'            : $this->info('terms'); break;
            case null               : $this->support(); break;
            default                 : header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
        }
    }

    function support() {
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('support');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);
//        $menu = JSFactory::getContentMenu();

        $view_name = "info";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('support');
        $view->assign('header', $meta_data['header']);
//        $view->assign('menu', $menu);

        $view->display();
    }

    function partners() {
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('partners');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $view_name = "info";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('partners');
        $view->assign('header', $meta_data['header']);

        $view->display();
    }

    function info($page){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData($page);
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);
//        $menu = JSFactory::getContentMenu();

        $view_name = "info";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('info');
        $view->assign('header', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
//        $view->assign('menu', $menu);

        $view->display();
    }

    function community(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $meta_data = JSFactory::getMetaData('community');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);
//        $menu = JSFactory::getContentMenu();

        $view_name = "info";
        $view_config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('community');
        $view->assign('header', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
//        $view->assign('menu', $menu);

        $view->display();
    }

}