<?php
/**
* @version      4.10.0 16.12.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

class JshoppingControllerImportExport extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );        
        checkAccessController("importexport");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        if ($this->getTask()!="" && $this->getTask()!="backtolistie" && JRequest::getInt("ie_id")){
            $this->view();
            return 1;
        }
    	$importexport = JSFactory::getModel("importexport");    	
        
		$rows = $importexport->getList();		
        $view=$this->getView("import_export_list", 'html');
		$view->assign('rows', $rows);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayImportExport', array(&$view));
        $view->display();
    }
    
    function remove() {        
        $cid = JRequest::getInt("cid");        
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($cid);        
        $_importexport->delete();        
        $this->setRedirect('index.php?option=com_jshopping&controller=importexport', _JSHOP_ITEM_DELETED);
    }
    
    function setautomaticexecution(){
        $cid = JRequest::getInt("cid");        
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($cid);
        if ($_importexport->steptime > 0){
            $_importexport->steptime = 0;
        }else{
            $_importexport->steptime = 1;
        }
        $_importexport->store();
        $this->setRedirect('index.php?option=com_jshopping&controller=importexport');
    }
    
    function view(){
        $ie_id = JRequest::getInt("ie_id");
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
            JError::raiseWarning("", sprintf(_JSHOP_ERROR_FILE_NOT_EXIST, "/importexport/".$alias."/".$alias.".php"));
            return 0;
        }
        
        include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php");
        
        $classname    = 'Ie'.$alias;
        $controller   = new $classname($ie_id);
        $controller->set('ie_id', $ie_id);
        $controller->set('alias', $alias);
        $controller->execute( JRequest::getVar( 'task' ) );        
    }
		      
}
?>