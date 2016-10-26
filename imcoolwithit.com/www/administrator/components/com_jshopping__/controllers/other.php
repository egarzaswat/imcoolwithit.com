<?php
/**
* @version      4.10.0 10.08.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerOther extends JControllerLegacy{

    function display($cachable = false, $urlparams = false){
        checkAccessController("other");
        addSubmenu("other");
        $view=$this->getView("panel", 'html');
        $view->setLayout("options");
        $view->sidebar = JHtmlSidebar::render();
                
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayOptionsPanel', array(&$view));
        $view->displayOptions();
    }
    
}
?>		