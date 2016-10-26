<?php
/**
* @version      4.10.0 10.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerProductFieldGroups extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("productfieldgroups");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){        
        $db = JFactory::getDBO();
        $_productfieldgroups = JSFactory::getModel("productFieldGroups");
        $rows = $_productfieldgroups->getList();
        
        $view = $this->getView("product_field_groups", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->sidebar = JHtmlSidebar::render();    
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductsFieldGroups', array(&$view));		
        $view->displayList();
    }
    
    function edit(){        
        $id = JRequest::getInt("id");
        $productfieldgroup = JSFactory::getTable('productFieldGroup', 'jshop');
        $productfieldgroup->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;    
                
        $view = $this->getView("product_field_groups", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe($productfieldgroup, ENT_QUOTES);
        $view->assign('row', $productfieldgroup);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditProductFieldGroups', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $productfieldgroup = JSFactory::getTable('productFieldGroup', 'jshop');
        $post = JRequest::get("post");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveProductFieldGroup', array(&$post) );
        
        if (!$productfieldgroup->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
            return 0;
        }
        
        if (!$id){
            $productfieldgroup->ordering = null;
            $productfieldgroup->ordering = $productfieldgroup->getNextOrder();
        }
        
        if (!$productfieldgroup->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
            return 0; 
        }
        
        $dispatcher->trigger( 'onAfterSaveProductFieldGroup', array(&$productfieldgroup) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups&task=edit&id=".$productfieldgroup->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
        }
                        
    }

    function remove(){        
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = array();
        foreach ($cid as $key => $value) {            
            $query = "DELETE FROM `#__jshopping_products_extra_field_groups` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }    
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onAfterRemoveProductFieldGroup', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups", implode("</li><li>",$text));
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
    function order(){        
        $id = JRequest::getInt("id");
        $move = JRequest::getInt("move");        
        $productfieldvalue = JSFactory::getTable('productFieldGroup', 'jshop');
        $productfieldvalue->load($id);
        $productfieldvalue->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
    }
    
    function saveorder(){
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );        
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('productFieldGroup', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = JSFactory::getTable('productFieldGroup', 'jshop');
        $table->ordering = null;
        $table->reorder();
                
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
    }
    
}
?>		