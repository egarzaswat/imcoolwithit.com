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

class JshoppingControllerPanel extends JControllerLegacy{
    function display($cachable = false, $urlparams = false){
        checkAccessController("panel");
        addSubmenu("");
		$view=$this->getView("panel", 'html');
        $view->setLayout("home");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayHomePanel', array(&$view));
		$view->displayHome(); 
    }
}
?>		