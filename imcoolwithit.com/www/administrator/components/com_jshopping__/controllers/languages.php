<?php
/**
* @version      4.10.0 20.11.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerLanguages extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );
        checkAccessController("languages");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){  	        		
        $languages = JSFactory::getModel("languages");
        $rows = $languages->getAllLanguages(0);
        $jshopConfig = JSFactory::getConfig();        
                
		$view=$this->getView("languages_list", 'html');		
        $view->assign('rows', $rows);
        $view->assign('default_front', $jshopConfig->getFrontLang());
        $view->assign('defaultLanguage', $jshopConfig->defaultLanguage);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayLanguage', array(&$view));
		$view->display(); 
        
    }
    
    function publish(){
        $this->publishLanguage(1);
    }
    
    function unpublish(){
        $this->publishLanguage(0);
    }

    function publishLanguage($flag) {
        $db = JFactory::getDBO();
        $cid = JRequest::getVar("cid");
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__jshopping_languages` SET `publish` = '" . $db->escape($flag) . "' WHERE `id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=languages");
    }
        
}
?>