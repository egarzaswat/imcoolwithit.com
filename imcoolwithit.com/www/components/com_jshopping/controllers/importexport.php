<?php
/**
* @version      4.1.0 10.10.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

class JshoppingControllerImportExport extends JControllerLegacy{
    
    function display($cachable = false, $urlparams = false){
        JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
    }

    function start(){
        $jshopConfig = JSFactory::getConfig();
        $key = JRequest::getVar("key");
        if ($key!=$jshopConfig->securitykey){
            die();
        }
        
        $_GET['noredirect'] = 1; $_POST['noredirect'] = 1; $_REQUEST['noredirect'] = 1;

        $db = JFactory::getDBO();
        $time = time();
        $query = "SELECT * FROM `#__jshopping_import_export` where `steptime`>0 and (endstart + steptime < $time)  ORDER BY id";
        $db->setQuery($query);
        $list = $db->loadObjectList();

        foreach($list as $ie){
            $alias = $ie->alias;
            if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
                print sprintf(_JSHOP_ERROR_FILE_NOT_EXIST, "/importexport/".$alias."/".$alias.".php");
                return 0;
            }
            include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php");
            $classname    = 'Ie'.$alias;
            $controller   = new $classname($ie->id);
            $controller->set('ie_id', $ie->id);
            $controller->set('alias', $alias);
            $controller->save();
            print $alias."\n";
        }
        
        die();
    }
}
?>