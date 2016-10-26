<?php
/**
* @version      4.10.0 20.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerShippingExtPrice extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask('orderup', 'reorder');
        $this->registerTask('orderdown', 'reorder');
        $this->registerTask('publish', 'republish');
        $this->registerTask('unpublish', 'republish');
        checkAccessController("shippingextprice");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
		$shippings = JSFactory::getModel("shippingExtPrice");
		$rows = $shippings->getList();
        
		$view = $this->getView("shippingext", 'html');
        $view->setLayout("list");
		$view->assign('rows', $rows);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayShippingExtPrices', array(&$view));
		$view->displayList();
	}
	
	function edit() {
		$id = JRequest::getInt("id");
        $row = JSFactory::getTable('shippingExt', 'jshop');
        $row->load($id);
        
        if (!$row->exec) {
            JError::raiseError( 404, "Error load ShippingExt");
        }
        
        $shippings_conects = $row->getShippingMethod();        
        
        $shippings = JSFactory::getModel("shippings");
        $list_shippings = $shippings->getAllShippings(0);        
        
        $nofilter = array("params", "shipping_method");
        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, $nofilter);
        
        $view = $this->getView("shippingext", 'html');
        $view->setLayout("edit");
        $view->assign('row', $row);
        $view->assign('list_shippings', $list_shippings);
        $view->assign('shippings_conects', $shippings_conects);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditShippingExtPrice', array(&$view));
        $view->displayEdit();
	}
	
	function save() {
		$id = JRequest::getInt("id", 0);		
        $post = JRequest::get("post");
        $row = JSFactory::getTable('shippingExt', 'jshop');        
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveShippingExtCalc', array(&$post));
        
        $row->bind($post);        
        $row->setShippingMethod($post['shipping']);       
        $row->setParams($post['params']);		
		$row->store();
        
        $dispatcher->trigger( 'onAfterSaveShippingExtCalc', array(&$row) );        		
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice");
	}
	
	function republish() {
		$cid = JRequest::getVar("cid");
        $flag = ($this->getTask() == 'publish') ? 1 : 0;
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishShippingExtPrice', array(&$cid,&$flag) );
        $obj = JSFactory::getTable('shippingExt', 'jshop');
        $obj->publish($cid, $flag);
        $dispatcher->trigger('onAfterPublishShippingExtPrice', array(&$cid,&$flag) );        
		$this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice");
	}
  
    function reorder(){
        $ids = JRequest::getVar('cid', null, 'post', 'array');        
        $move = ($this->getTask() == 'orderup') ? -1 : +1;
        $obj = JSFactory::getTable('shippingExt', 'jshop');
        $obj->load($ids[0]);
        $obj->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice");
    }
    
    function remove(){
        $id = JRequest::getInt("id");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveShippingExtPrice', array(&$id) );
        $obj = JSFactory::getTable('shippingExt', 'jshop');        
        $obj->delete($id);
        $dispatcher->trigger( 'onAfterRemoveShippingExtPrice', array(&$id) );        
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice", _JSHOP_ITEM_DELETED);
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
    }

}
?>