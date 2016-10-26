<?php
/**
* @version      4.10.4 23.09.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerDeliveryTimes extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("deliverytimes");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.deliverytimes";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "name", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $_deliveryTimes = JSFactory::getModel("deliveryTimes");
        $rows = $_deliveryTimes->getDeliveryTimes($filter_order, $filter_order_Dir);
        $view=$this->getView("deliverytimes", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows); 
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayDeliveryTimes', array(&$view));
        $view->displayList();
    }
	
	function edit() {
		$id = JRequest::getInt("id");
		$deliveryTimes = JSFactory::getTable('deliveryTimes', 'jshop');
		$deliveryTimes->load($id);
		$edit = ($id)?(1):(0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        JFilterOutput::objectHTMLSafe( $deliveryTimes, ENT_QUOTES);

		$view=$this->getView("deliverytimes", 'html');
        $view->setLayout("edit");
        $view->assign('deliveryTimes', $deliveryTimes);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditDeliverytimes', array(&$view));
		$view->displayEdit();
	}
	
	function save() {
	    $mainframe = JFactory::getApplication();
		$id = JRequest::getInt("id");
		$deliveryTimes = JSFactory::getTable('deliveryTimes', 'jshop');
        $post = JRequest::get("post");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveDeliveryTime', array(&$post) );
        
		if (!$deliveryTimes->bind($post)) {
			JError::raiseWarning("",_JSHOP_ERROR_BIND);
			$this->setRedirect("index.php?option=com_jshopping&controller=deliverytimes");
			return 0;
		}
	
		if (!$deliveryTimes->store()) {
			JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
			$this->setRedirect("index.php?option=com_jshopping&controller=deliverytimes");
			return 0;
		}
        
        $dispatcher->trigger( 'onAfterSaveDeliveryTime', array(&$deliveryTimes) );
		
		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=deliverytimes&task=edit&id=".$deliveryTimes->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=deliverytimes");
        }	
	}
	
	function remove() {
		$db = JFactory::getDBO();
		$text = array();
		$cid = JRequest::getVar("cid");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveDeliveryTime', array(&$cid) );
        
		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_delivery_times` WHERE `id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if ($db->query())
				$text[] = _JSHOP_DELIVERY_TIME_DELETED."<br>";
			else
				$text[] = _JSHOP_DELIVERY_TIME_DELETED_ERROR_DELETED."<br>";
		}
        
        $dispatcher->trigger( 'onAfterRemoveDeliveryTime', array(&$cid) );
        
		$this->setRedirect("index.php?option=com_jshopping&controller=deliverytimes", implode("</li><li>", $text));
	} 
    
    
}

?>