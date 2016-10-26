<?php
/**
* @version      4.10.0 02.11.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerUnits extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("units");
        addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
		$_units = JSFactory::getModel("units");
		$rows = $_units->getUnits();
        
		$view=$this->getView("units", 'html');
        $view->setLayout("list");		
        $view->assign('rows', $rows);
        $view->sidebar = JHtmlSidebar::render();       
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayUnits', array(&$view)); 		
		$view->displayList();
	}
	
    function edit() {
        $id = JRequest::getInt("id");
        $units = JSFactory::getTable('unit', 'jshop');
        $units->load($id);
        $edit = ($id)?(1):(0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        if (!$units->qty) $units->qty = 1;
        
        JFilterOutput::objectHTMLSafe( $units, ENT_QUOTES);

		$view=$this->getView("units", 'html');
        $view->setLayout("edit");
        $view->assign('units', $units);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditUnitss', array(&$view));
		$view->displayEdit();
	}
	
	function save() {
	    $mainframe = JFactory::getApplication();
		$id = JRequest::getInt("id");
		$units = JSFactory::getTable('unit', 'jshop');
        $post = JRequest::get("post");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveUnit', array(&$post) );        
        
		if (!$units->bind($post)) {
			JError::raiseWarning("",_JSHOP_ERROR_BIND);
			$this->setRedirect("index.php?option=com_jshopping&controller=units");
			return 0;
		}
	
		if (!$units->store()) {
			JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
			$this->setRedirect("index.php?option=com_jshopping&controller=units");
			return 0;
		}
        
        $dispatcher->trigger( 'onAfterSaveUnit', array(&$units) );
		
		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=units&task=edit&id=".$units->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=units");
        }	
	}
	
	function remove() {
		$db = JFactory::getDBO();
		$text = array();
		$cid = JRequest::getVar("cid");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveUnit', array(&$cid) );

		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_unit` WHERE `id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if($db->query()) $text[] = _JSHOP_ITEM_DELETED."<br>";			
		}
        $dispatcher->trigger( 'onAfterRemoveUnit', array(&$cid) );
        
		$this->setRedirect("index.php?option=com_jshopping&controller=units", implode("</li><li>", $text));
	} 
    
    
}

?>