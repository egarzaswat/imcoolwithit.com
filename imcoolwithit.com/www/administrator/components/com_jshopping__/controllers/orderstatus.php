<?php
/**
* @version      4.10.4 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerOrderStatus extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("orderstatus");
        addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.orderstatus";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "status_id", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$_order = JSFactory::getModel("orders");
		$rows = $_order->getAllOrderStatus($filter_order, $filter_order_Dir);

		$view=$this->getView("orderstatus", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);        
		$view->sidebar = JHtmlSidebar::render();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayOrderStatus', array(&$view));
		$view->displayList();
	}
	
	function edit(){
		$status_id = JRequest::getInt("status_id");
		$order_status = JSFactory::getTable('orderStatus', 'jshop');
		$order_status->load($status_id);
		$edit = ($status_id)?($edit = 1):($edit = 0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        JFilterOutput::objectHTMLSafe( $order_status, ENT_QUOTES);

		$view=$this->getView("orderstatus", 'html');
        $view->setLayout("edit");		
        $view->assign('order_status', $order_status);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('etemplatevar', '');
        $view->assign('multilang', $multilang);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditOrderStatus', array(&$view));
		$view->displayEdit();
	}
	
	function save() {
	    $mainframe = JFactory::getApplication();
		$status_id = JRequest::getInt("status_id");
		$order_status = JSFactory::getTable('orderStatus', 'jshop');
        $post = JRequest::get("post");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveOrderStatus', array(&$post) );
		if (!$order_status->bind($post)) {
			JError::raiseWarning("",_JSHOP_ERROR_BIND);
			$this->setRedirect("index.php?option=com_jshopping&controller=orderstatus");
			return 0;
		}
	
		if (!$order_status->store()) {
			JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
			$this->setRedirect("index.php?option=com_jshopping&controller=orderstatus");
			return 0;
		}
        
        $dispatcher->trigger( 'onAfterSaveOrderStatus', array(&$order_status) );
		
		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=orderstatus&task=edit&status_id=".$order_status->status_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=orderstatus");
        }
		
	}
	
	function remove() {
		$db = JFactory::getDBO();
		$text = '';
		$query = '';
		$cid = JRequest::getVar("cid");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveOrderStatus', array(&$cid));
		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_order_status` WHERE `status_id` = '".$db->escape($value)."'";
			
			$db->setQuery($query);
			if ($db->query())
				$text .= _JSHOP_ORDER_STATUS_DELETED."<br>";
			else
				$text .= _JSHOP_ORDER_STATUS_ERROR_DELETED."<br>";
		}
        
        $dispatcher->trigger( 'onAfterRemoveOrderStatus', array(&$cid));
        
		$this->setRedirect("index.php?option=com_jshopping&controller=orderstatus", $text);
	} 
    
    
}

?>