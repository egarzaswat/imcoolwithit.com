<?php
/**
* @version      3.9.0 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerInfo extends JControllerLegacy{

    function display($cachable = false, $urlparams = false){
        checkAccessController("info");        
        addSubmenu("info");
        
        $jshopConfig = JSFactory::getConfig();        
        $data = JApplicationHelper::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");
		if ($jshopConfig->display_updates_version){
		    $update_model = JSFactory::getModel("info");
		    $update = $update_model->getUpdateObj($data['version'], $jshopConfig);
        }else{
            $update = new stdClass();
        }
        $view=$this->getView("panel", 'html');
        $view->setLayout("info");
		$view->assign("data",$data);
        $view->assign("update",$update);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayInfo', array(&$view));
        $view->displayInfo();
    }

}
?>