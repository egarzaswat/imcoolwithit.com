<?php
/**
* @version      4.10.0 26.12.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerProductFieldValues extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("productfieldvalues");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $field_id = JRequest::getInt("field_id");
        $db = JFactory::getDBO();
        $_productfieldvalues = JSFactory::getModel("productFieldValues");
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.productfieldvalues";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $text_search = $mainframe->getUserStateFromRequest($context.'text_search', 'text_search', '');
        
        $filter = array("text_search"=>$text_search);
        
        $rows = $_productfieldvalues->getList($field_id, $filter_order, $filter_order_Dir, $filter);
        
        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('field_id', $field_id);
		$view->assign('text_search', $text_search);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductFieldValues', array(&$view));
        $view->displayList();
    }
    
    function edit(){        
        $field_id = JRequest::getInt("field_id");
        $id = JRequest::getInt("id");
        
        $productfieldvalue = JSFactory::getTable('productFieldValue', 'jshop');
        $productfieldvalue->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;    
                
        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe($productfieldvalue, ENT_QUOTES);
        $view->assign('row', $productfieldvalue);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('field_id', $field_id);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditProductFieldValues', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $id = JRequest::getInt("id");
        $field_id = JRequest::getInt("field_id");
        $productfieldvalue = JSFactory::getTable('productFieldValue', 'jshop');
        $post = JRequest::get("post");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveProductFieldValue', array(&$post) );
        
        if (!$productfieldvalue->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues");
            return 0;
        }
        
        if (!$id){
            $productfieldvalue->ordering = null;
            $productfieldvalue->ordering = $productfieldvalue->getNextOrder('field_id="'.$field_id.'"');            
        }
        
        if (!$productfieldvalue->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues");
            return 0; 
        }
        
        $dispatcher->trigger( 'onAfterSaveProductFieldValue', array(&$productfieldvalue) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&task=edit&field_id=".$field_id."&id=".$productfieldvalue->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&field_id=".$field_id);
        }
                        
    }

    function remove(){
        $field_id = JRequest::getInt("field_id");
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = array();
        foreach ($cid as $key => $value) {            
            $query = "DELETE FROM `#__jshopping_products_extra_field_values` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }    
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onAfterRemoveProductFieldValue', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&field_id=".$field_id, implode("</li><li>",$text));
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
    function order(){        
        $id = JRequest::getInt("id");
        $field_id = JRequest::getInt("field_id");
        $move = JRequest::getInt("move");        
        $productfieldvalue = JSFactory::getTable('productFieldValue', 'jshop');
        $productfieldvalue->load($id);
        $productfieldvalue->move($move, 'field_id="'.$field_id.'"');
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&field_id=".$field_id);
    }
    
    function saveorder(){
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        $field_id = JRequest::getInt("field_id");
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('productFieldValue', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = JSFactory::getTable('productFieldValue', 'jshop');
        $table->ordering = null;
        $table->reorder('field_id="'.$field_id.'"');        
                
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&field_id=".$field_id);
    }
    
}
?>		