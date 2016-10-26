<?php
/**
* @version      4.9.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerError extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerError', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();

        $meta_data = JSFactory::getMetaData('error_page');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

//        $menu = JSFactory::getContentMenu();

        $view_name = "error";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("error");
//        $view->assign('menu', $menu);
        $view->display();
    }
}
?>